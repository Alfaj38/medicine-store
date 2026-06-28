<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Requisition;
use App\Models\RequisitionItem;
use App\Models\Item;
use App\Models\Inventory;
use App\Models\Supplier;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class RequisitionController extends Controller
{
    public function index(Request $request)
    {
        $requisitions = Requisition::where('company_id', auth()->user()->company_id)
            ->with(['creator', 'branch'])
            ->withCount('items')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return Inertia::render('Requisitions/Index', [
            'requisitions' => $requisitions
        ]);
    }

    public function create()
    {
        return Inertia::render('Requisitions/Form', [
            'suppliers' => Supplier::where('company_id', auth()->user()->company_id)->orderBy('name')->get(),
        ]);
    }

    public function searchMedicines(Request $request)
    {
        $queryText = $request->get('q');
        $supplierId = $request->get('supplier_id');
        
        if (!$queryText) {
            return response()->json([]);
        }

        $companyId = auth()->user()->company_id;

        $items = \App\Models\Medicine::where('is_active', true)
            ->when($companyId, function($q) use ($companyId) {
                $q->where(function($sub) use ($companyId) {
                    $sub->whereNull('company_id')
                        ->orWhere('company_id', $companyId);
                });
            })
            ->when($supplierId, function($q) use ($supplierId) {
                $supplier = \App\Models\Supplier::find($supplierId);
                
                $q->where(function($subQ) use ($supplier, $supplierId) {
                    if ($supplier && is_array($supplier->companies) && count($supplier->companies) > 0) {
                        $baseNames = array_map(function($c) {
                            return trim(str_ireplace([' Ltd.', ' Ltd', ' Limited', ' PLC', ' Plc', ' PLC.', '.'], '', $c));
                        }, $supplier->companies);

                        $subQ->where(function($mq) use ($baseNames) {
                            foreach ($baseNames as $bn) {
                                $mq->orWhere('company_name', 'like', "%{$bn}%");
                            }
                        });
                    }
                });
            })
            ->where(function($q) use ($queryText) {
                $q->where('name', 'like', "%{$queryText}%")
                  ->orWhere('barcode', 'like', "%{$queryText}%")
                  ->orWhere('generic_name', 'like', "%{$queryText}%");
            })
            ->with([
                'category:id,name', 
                'unit:id,name', 
                'secondaryUnit:id,name',
                'inventory' => function($q) use ($companyId) {
                    if ($companyId) {
                        $q->where('company_id', $companyId);
                    }
                }
            ])
            ->select(['id', 'name', 'generic_name', 'barcode', 'mrp', 'unit_id', 'secondary_unit_id', 'category_id'])
            ->take(15)
            ->get()->map(function($item) {
                $inventory = $item->inventory;
                
                $uomName = $item->unit ? $item->unit->name : 'Unit';
                
                $sellPrice = $item->mrp ?: 0;
                if ($sellPrice <= 0 && $inventory->count() > 0) {
                    $sellPrice = $inventory->max('mrp');
                }

                return [
                    'id'           => $item->id,
                    'name'         => $item->name,
                    'generic_name' => $item->generic_name,
                    'inventory'    => $inventory,
                    'uom'          => ['name' => $uomName],
                    'mrp'          => $sellPrice,
                    'sell_price'   => $sellPrice
                ];
            });

        return response()->json($items);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:Purchase,Transfer,Emergency,Auto',
            'priority' => 'required|in:Low,Normal,High,Urgent',
            'required_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.requested_qty' => 'required|integer|min:1',
            'items.*.current_stock' => 'required|integer',
        ]);

        return DB::transaction(function () use ($validated) {
            $requisition = Requisition::create([
                'company_id' => auth()->user()->company_id,
                'requisition_no' => 'REQ-' . strtoupper(uniqid()),
                'type' => $validated['type'],
                'priority' => $validated['priority'],
                'required_date' => $validated['required_date'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'status' => 'Draft',
                'created_by' => auth()->id()
            ]);

            foreach ($validated['items'] as $item) {
                $requisition->items()->create([
                    'item_id' => $item['item_id'],
                    'current_stock' => $item['current_stock'],
                    'requested_qty' => $item['requested_qty'],
                    'approved_qty' => $item['requested_qty'] // auto approve initial qty for now
                ]);
            }

            return redirect()->route('requisitions.show', $requisition->id)->with('success', 'Requisition created successfully.');
        });
    }

    public function show(Requisition $requisition)
    {
        if ($requisition->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $requisition->load(['items.item', 'creator', 'branch']);

        return Inertia::render('Requisitions/Show', [
            'requisition' => $requisition
        ]);
    }

    public function updateStatus(Request $request, Requisition $requisition)
    {
        if ($requisition->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:Draft,Submitted,Store Approved,Accounts Approved,Owner Approved,PO Generated,Cancelled'
        ]);

        return DB::transaction(function () use ($validated, $requisition) {
            $requisition->update(['status' => $validated['status']]);

            // If the status is PO Generated, we need to create Purchase Orders grouped by Supplier
            if ($validated['status'] === 'PO Generated') {
                $requisition->load('items.item');
                
                // Group items by preferred supplier
                $supplierItems = [];
                foreach ($requisition->items as $reqItem) {
                    // For now, if no preferred supplier, group them under a '0' key to handle later or assign to a default
                    $supplierId = $reqItem->item->preferred_supplier_id ?? 0;
                    if (!isset($supplierItems[$supplierId])) {
                        $supplierItems[$supplierId] = [];
                    }
                    $supplierItems[$supplierId][] = $reqItem;
                }

                foreach ($supplierItems as $supplierId => $items) {
                    if ($supplierId === 0) continue; // Skip items without a supplier for now, or handle them differently

                    $po = \App\Models\PurchaseOrder::create([
                        'company_id' => $requisition->company_id,
                        'supplier_id' => $supplierId,
                        'requisition_id' => $requisition->id,
                        'po_no' => 'PO-' . strtoupper(uniqid()),
                        'status' => 'Pending',
                        'expected_delivery_date' => $requisition->required_date,
                        'created_by' => auth()->id()
                    ]);

                    $totalAmount = 0;
                    foreach ($items as $item) {
                        $unitPrice = $item->item->buy_price ?? 0;
                        $subtotal = $unitPrice * $item->approved_qty;
                        
                        $po->items()->create([
                            'item_id' => $item->item_id,
                            'ordered_qty' => $item->approved_qty,
                            'unit_price' => $unitPrice,
                            'subtotal' => $subtotal
                        ]);
                        
                        $totalAmount += $subtotal;
                    }

                    $po->update(['total_amount' => $totalAmount]);
                }
            }

            return redirect()->back()->with('success', 'Requisition status updated to ' . $validated['status'] . ($validated['status'] === 'PO Generated' ? ' and Purchase Orders created.' : ''));
        });
    }

    public function autoGenerate()
    {
        $companyId = auth()->user()->company_id;
        
        $items = Item::where('company_id', $companyId)
            ->where('inventory_type', 'Stock Item')
            ->where('is_active', true)
            ->where('reorder_level', '>', 0)
            ->with(['medicineDetails', 'uom'])
            ->get();

        $suggestedItems = [];

        foreach ($items as $item) {
            $currentStock = Inventory::where('company_id', $companyId)
                ->where('medicine_id', $item->id)
                ->sum('quantity');
            
            // Auto Requisition Logic
            if ($currentStock <= $item->reorder_level) {
                
                // If Reorder Qty is set, use it. Otherwise calculate based on level and safety stock
                $suggestedQty = $item->reorder_qty > 0 ? 
                                $item->reorder_qty : 
                                (($item->reorder_level - $currentStock) + $item->safety_stock);
                
                // Ensure we request at least 1
                if ($suggestedQty < 1) $suggestedQty = 1;

                $suggestedItems[] = [
                    'item_id' => $item->id,
                    'name' => $item->name,
                    'generic_name' => $item->medicineDetails ? $item->medicineDetails->generic_name : '',
                    'uom' => $item->uom ? $item->uom->name : 'Unit',
                    'current_stock' => $currentStock,
                    'suggested_qty' => $suggestedQty,
                    'preferred_supplier_id' => $item->preferred_supplier_id
                ];
            }
        }

        return response()->json($suggestedItems);
    }
}
