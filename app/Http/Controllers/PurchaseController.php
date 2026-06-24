<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Inventory;
use App\Models\StockLedger;
use App\Models\Supplier;
use App\Models\Medicine;
use App\Models\PurchaseReturnItem;
use App\Models\SupplierCreditNote;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $scope = app('data_scope');

        $query = Purchase::query();
        $query = $scope->apply($query, auth()->user(), Purchase::class)
            ->with(['supplier'])
            ->withCount(['returns']);

        $purchases = $query->orderBy('purchase_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15);

        return Inertia::render('Purchases/Index', [
            'purchases' => $purchases,
        ]);
    }

    public function show($id)
    {
        $scope = app('data_scope');

        $purchase = Purchase::query();
        $purchase = $scope->apply($purchase, auth()->user(), Purchase::class)
            ->with(['supplier', 'items.medicine', 'payments' => function($q) {
                $q->orderBy('payment_date', 'desc')->orderBy('created_at', 'desc');
            }, 'returns.items'])
            ->findOrFail($id);

        // Annotate each item with total returned quantity for that batch
        $purchase->items->each(function($item) use ($purchase) {
            $returned = PurchaseReturnItem::where('medicine_id', $item->medicine_id)
                ->where('batch_no', $item->batch_no)
                ->whereHas('purchaseReturn', function($q) use ($purchase) {
                    $q->where('supplier_id', $purchase->supplier_id)
                      ->whereIn('status', ['pending', 'approved']);
                })
                ->sum('quantity');
            $item->returned_quantity = (int) $returned;
        });

        // Available credit notes from returns
        $availableCredit = SupplierCreditNote::where('supplier_id', $purchase->supplier_id)
            ->where('status', 'pending')
            ->sum('amount');

        return Inertia::render('Purchases/Show', [
            'purchase' => $purchase,
            'availableCredit' => (float) $availableCredit,
        ]);
    }

    public function create()
    {
        return Inertia::render('Purchases/Form', [
            'suppliers' => Supplier::where('is_active', true)->orderBy('name')->get(),
            'medicines' => [], // Will be loaded asynchronously via search
            'reference_no' => 'PO-' . date('Ymd') . '-' . rand(1000, 9999),
        ]);
    }

    public function searchMedicines(Request $request)
    {
        $query = $request->get('q');
        $supplierId = $request->get('supplier_id');
        
        if (!$query || !$supplierId) {
            return response()->json([]);
        }

        $supplier = Supplier::find($supplierId);
        if (!$supplier) {
            return response()->json([]);
        }

        // Supplier's companies column is cast to array or stored as JSON.
        $companies = is_string($supplier->companies) ? json_decode($supplier->companies, true) : $supplier->companies;

        if (empty($companies)) {
            return response()->json([]);
        }

        // Get Manufacturer IDs that match the supplier's company names (using LIKE to handle 'Ltd.' mismatches)
        $manufacturerIds = \App\Models\Manufacturer::where(function($q) use ($companies) {
            foreach ($companies as $company) {
                $q->orWhere('name', 'like', "%{$company}%");
            }
        })->pluck('id');

        $items = \App\Models\Item::withoutGlobalScope(\App\Models\Scopes\TenantScope::class)
            ->with(['medicineDetails', 'prices' => function($q) {
                $q->where('price_type', 'Purchase');
            }])
            ->where('is_active', true)
            ->whereIn('manufacturer_id', $manufacturerIds)
            ->where(function($q) {
                $q->whereNull('company_id')
                  ->orWhere('company_id', auth()->user()->company_id);
            })
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('barcode', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%")
                  ->orWhereHas('medicineDetails', function($m) use ($query) {
                      $m->where('generic_name', 'like', "%{$query}%");
                  });
            })
            ->take(15)
            ->get();

        // Map to expected format for the frontend
        $mapped = $items->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'generic_name' => $item->medicineDetails ? $item->medicineDetails->generic_name : '',
                'buy_price' => $item->prices->first() ? $item->prices->first()->price : 0,
            ];
        });

        return response()->json($mapped);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'reference_no' => 'required|string|unique:purchases,reference_no',
            'purchase_date' => 'required|date',
            'discount' => 'numeric|min:0',
            'tax' => 'numeric|min:0',
            'paid_amount' => 'numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.medicine_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.mrp' => 'required|numeric|min:0',
            'items.*.batch_no' => 'nullable|string',
            'items.*.expiry_date' => 'nullable|date',
        ]);

        DB::transaction(function () use ($validated) {
            $subtotal = 0;
            $itemsData = [];

            foreach ($validated['items'] as $item) {
                $itemSubtotal = $item['quantity'] * $item['unit_price'];
                $subtotal += $itemSubtotal;
                $itemsData[] = array_merge($item, ['subtotal' => $itemSubtotal, 'mrp' => $item['mrp']]);
            }

            $totalAmount = $subtotal - ($validated['discount'] ?? 0) + ($validated['tax'] ?? 0);
            
            $paymentStatus = 'unpaid';
            if ($validated['paid_amount'] >= $totalAmount) {
                $paymentStatus = 'paid';
            } elseif ($validated['paid_amount'] > 0) {
                $paymentStatus = 'partial';
            }

            $purchase = Purchase::create([
                'supplier_id' => $validated['supplier_id'],
                'reference_no' => $validated['reference_no'],
                'purchase_date' => $validated['purchase_date'],
                'subtotal' => $subtotal,
                'discount' => $validated['discount'] ?? 0,
                'tax' => $validated['tax'] ?? 0,
                'total_amount' => $totalAmount,
                'paid_amount' => $validated['paid_amount'] ?? 0,
                'status' => 'received',
                'payment_status' => $paymentStatus,
                'notes' => $validated['notes'],
            ]);

            foreach ($itemsData as $itemData) {
                $purchase->items()->create($itemData);

                // Update Inventory
                $inventory = Inventory::firstOrCreate(
                    [
                        'company_id' => auth()->user()->company_id,
                        'branch_id' => auth()->user()->branch_id,
                        'medicine_id' => $itemData['medicine_id'],
                        'batch_no' => $itemData['batch_no'],
                    ],
                    ['quantity' => 0, 'expiry_date' => $itemData['expiry_date'], 'mrp' => $itemData['mrp']]
                );

                $inventory->update(['mrp' => $itemData['mrp']]);
                $inventory->increment('quantity', $itemData['quantity']);

                // Create Stock Ledger
                StockLedger::create([
                    'medicine_id' => $itemData['medicine_id'],
                    'batch_no' => $itemData['batch_no'],
                    'reference_type' => Purchase::class,
                    'reference_id' => $purchase->id,
                    'type' => 'in',
                    'quantity' => $itemData['quantity'],
                    'balance_after' => $inventory->fresh()->quantity,
                    'notes' => 'Purchase ' . $purchase->reference_no,
                ]);
            }

            // Update Supplier Balance
            $dueAmount = $totalAmount - ($validated['paid_amount'] ?? 0);
            if ($dueAmount > 0) {
                Supplier::where('id', $validated['supplier_id'])->increment('current_balance', $dueAmount);
            }
        });

        return redirect()->route('purchases.index')->with('success', 'Purchase created successfully.');
    }
}
