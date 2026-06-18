<?php

namespace App\Http\Controllers;

use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use App\Models\ReturnReason;
use App\Models\Supplier;
use App\Models\Medicine;
use App\Models\PurchaseItem;
use App\Models\Inventory;
use App\Models\SupplierCreditNote;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class PurchaseReturnController extends Controller
{
    public function index()
    {
        $scope = app('data_scope');

        $query = PurchaseReturn::query();
        $query = $scope->apply($query, auth()->user(), PurchaseReturn::class)
            ->with(['supplier', 'items.medicine']);

        $returns = $query->latest()
            ->paginate(10);
            
        return Inertia::render('Purchases/Returns/Index', [
            'returns' => $returns
        ]);
    }

    public function create()
    {
        return Inertia::render('Purchases/Returns/Create', [
            'suppliers' => Supplier::where('is_active', true)->get(['id', 'name', 'companies']),
            'reasons' => ReturnReason::all()
        ]);
    }

    public function getMedicines(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
        ]);

        $supplierId = $request->supplier_id;

        $medicines = Medicine::whereHas('purchaseItems.purchase', function($q) use ($supplierId) {
                $q->where('supplier_id', $supplierId);
            })
            ->whereHas('inventory', function($q) {
                $q->where('quantity', '>', 0);
            })
            ->select(['id', 'name', 'generic_name'])
            ->get();

        return response()->json($medicines);
    }

    public function getBatches(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'medicine_id' => 'required|exists:medicines,id',
        ]);

        $supplierId = $request->supplier_id;
        $medicineId = $request->medicine_id;

        $purchasedBatches = PurchaseItem::whereHas('purchase', function($q) use ($supplierId) {
                $q->where('supplier_id', $supplierId);
            })
            ->where('medicine_id', $medicineId)
            ->select('batch_no', 'expiry_date', 'unit_price', DB::raw('SUM(quantity) as total_purchased'))
            ->groupBy('batch_no', 'expiry_date', 'unit_price')
            ->get();
            
        $results = [];
        foreach($purchasedBatches as $batch) {
            // Need tenant awareness for inventory
            $inventory = Inventory::where('medicine_id', $medicineId)
                ->where('batch_no', $batch->batch_no)
                ->first();
            
            $availableQty = $inventory ? $inventory->quantity : 0;
            
            if ($availableQty > 0) {
                // Ensure unique batch entry in results
                $existing = array_filter($results, function($r) use ($batch) {
                    return $r['batch_no'] === $batch->batch_no;
                });
                
                if(empty($existing)) {
                    $results[] = [
                        'batch_no' => $batch->batch_no,
                        'expiry_date' => $batch->expiry_date,
                        'unit_price' => $batch->unit_price,
                        'available_qty' => $availableQty,
                    ];
                }
            }
        }
        
        // Sort by FEFO (First Expiry First Out)
        usort($results, function($a, $b) {
            return strtotime($a['expiry_date']) - strtotime($b['expiry_date']);
        });

        return response()->json($results);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'return_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.medicine_id' => 'required|exists:medicines,id',
            'items.*.batch_no' => 'required|string',
            'items.*.return_qty' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.return_reason_id' => 'required|exists:return_reasons,id',
        ]);

        DB::beginTransaction();
        try {
            // Calculate total
            $totalAmount = 0;
            foreach ($validated['items'] as $item) {
                $totalAmount += ($item['return_qty'] * $item['unit_price']);
                
                // Validate stock again
                $inventory = Inventory::where('medicine_id', $item['medicine_id'])
                    ->where('batch_no', $item['batch_no'])
                    ->lockForUpdate()
                    ->first();
                    
                if (!$inventory || $inventory->quantity < $item['return_qty']) {
                    throw new \Exception("Insufficient stock for batch {$item['batch_no']}");
                }
            }

            // Create Return Record
            $purchaseReturn = PurchaseReturn::create([
                'tenant_id' => auth()->user()->tenant_id ?? 1,
                'supplier_id' => $validated['supplier_id'],
                'reference_no' => 'RET-' . date('Ymd') . '-' . rand(1000, 9999),
                'return_date' => $validated['return_date'],
                'total_amount' => $totalAmount,
                'status' => 'approved', // Assuming auto-approve for Phase 1
                'notes' => $validated['notes'],
                'created_by' => auth()->id(),
                'approved_by' => auth()->id(),
            ]);

            // Process Items
            foreach ($validated['items'] as $item) {
                PurchaseReturnItem::create([
                    'purchase_return_id' => $purchaseReturn->id,
                    'medicine_id' => $item['medicine_id'],
                    'return_reason_id' => $item['return_reason_id'],
                    'batch_no' => $item['batch_no'],
                    'quantity' => $item['return_qty'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['return_qty'] * $item['unit_price'],
                ]);
                
                // Deduct Inventory
                $inventory = Inventory::where('medicine_id', $item['medicine_id'])
                    ->where('batch_no', $item['batch_no'])
                    ->first();
                $inventory->quantity -= $item['return_qty'];
                $inventory->save();
                
                // Also deduct from Medicine master total stock
                $medicine = Medicine::find($item['medicine_id']);
                $medicine->stock -= $item['return_qty'];
                $medicine->save();
            }

            // Generate Credit Note
            SupplierCreditNote::create([
                'tenant_id' => auth()->user()->tenant_id ?? 1,
                'credit_note_no' => 'CN-' . date('Y') . '-' . rand(1000, 9999),
                'supplier_id' => $validated['supplier_id'],
                'purchase_return_id' => $purchaseReturn->id,
                'amount' => $totalAmount,
                'status' => 'pending'
            ]);

            DB::commit();
            return redirect()->route('purchase-returns.index')->with('success', 'Return created and Credit Note generated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        $scope = app('data_scope');

        $purchaseReturn = PurchaseReturn::query();
        $purchaseReturn = $scope->apply($purchaseReturn, auth()->user(), PurchaseReturn::class)
            ->with(['supplier', 'items.medicine', 'items.returnReason', 'creditNotes'])
            ->findOrFail($id);
            
        return Inertia::render('Purchases/Returns/Show', [
            'returnRecord' => $purchaseReturn
        ]);
    }
}
