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

    public function create(Request $request)
    {
        $po_id = $request->query('po_id');
        $prefillPo = null;

        if ($po_id) {
            $prefillPo = \App\Models\PurchaseOrder::with('items.item')->find($po_id);
        }

        return Inertia::render('Purchases/Form', [
            'suppliers' => Supplier::where('is_active', true)->orderBy('name')->get(),
            'medicines' => [], // Will be loaded asynchronously via search
            'reference_no' => 'GRN-' . date('Ymd') . '-' . rand(1000, 9999),
            'prefillPo' => $prefillPo
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
        
        $baseNames = array_map(function($c) {
            return trim(str_ireplace([' Ltd.', ' Ltd', ' Limited', ' PLC', ' Plc', ' PLC.', '.'], '', $c));
        }, $companies);

        $items = \App\Models\Medicine::where('is_active', true)
            ->where(function($q) use ($baseNames) {
                foreach ($baseNames as $baseName) {
                    $q->orWhere('company_name', 'like', "%{$baseName}%");
                }
            })
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('barcode', 'like', "%{$query}%")
                  ->orWhere('generic_name', 'like', "%{$query}%");
            })
            ->with(['category:id,name', 'secondaryUnit:id,name', 'unit:id,name'])
            ->select(['id', 'name', 'generic_name', 'mrp', 'unit_id', 'secondary_unit_id', 'conversion_factor', 'category_id'])
            ->take(15)
            ->get();

        // Map to expected format for the frontend
        $mapped = $items->map(function ($item) {
            // Create units array dynamically from Medicine schema
            $units = collect();
            if ($item->unit) {
                $units->push((object)[
                    'id' => $item->unit_id,
                    'unit_name' => $item->unit->name,
                    'factor' => 1,
                    'is_base_unit' => true,
                    'is_purchase_unit' => true,
                    'is_sales_unit' => true
                ]);
            } else {
                $units->push((object)[
                    'id' => 'fallback',
                    'unit_name' => 'Unit',
                    'factor' => 1,
                    'is_base_unit' => true,
                    'is_purchase_unit' => true,
                    'is_sales_unit' => true
                ]);
            }
            if ($item->secondaryUnit && $item->conversion_factor > 1) {
                $units->push((object)[
                    'id' => $item->secondary_unit_id,
                    'unit_name' => $item->secondaryUnit->name,
                    'factor' => $item->conversion_factor,
                    'is_base_unit' => false,
                    'is_purchase_unit' => true,
                    'is_sales_unit' => true
                ]);
            }

            $purchaseUnit = $units->first();
            $baseUnit = $units->first();
            
            return [
                'id' => $item->id,
                'name' => $item->name,
                'generic_name' => $item->generic_name,
                'mrp' => $item->mrp,
                'sell_price' => $item->mrp, // Map sell_price to mrp for any trailing frontend logic
                'units' => $units,
                'default_unit' => $purchaseUnit,
                'base_unit' => $baseUnit,
            ];
        });

        return response()->json($mapped);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'reference_no' => 'required|string|unique:purchases,reference_no',
            'purchase_date' => 'required|date',
            'discount' => 'numeric|min:0',
            'tax' => 'numeric|min:0',
            'paid_amount' => 'numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.purchase_order_item_id' => 'nullable|exists:purchase_order_items,id',
            'items.*.medicine_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.mrp' => 'required|numeric|min:0',
            'items.*.bonus_quantity' => 'nullable|integer|min:0',
            'items.*.trade_discount_percent' => 'nullable|numeric|min:0|max:100',
            'items.*.batch_no' => 'nullable|string',
            'items.*.expiry_date' => 'nullable|date',
            'items.*.unit_id' => 'nullable|exists:item_units,id',
            'items.*.unit_factor' => 'nullable|integer|min:1',
        ]);

        DB::transaction(function () use ($validated) {
            $subtotal = 0;
            $itemsData = [];

            foreach ($validated['items'] as $item) {
                $itemSubtotal = $item['quantity'] * $item['unit_price'];
                $subtotal += $itemSubtotal;
                $itemsData[] = [
                    'medicine_id' => $item['medicine_id'],
                    'purchase_order_item_id' => $item['purchase_order_item_id'] ?? null,
                    'unit_id' => $item['unit_id'] ?? null,
                    'unit_factor' => $item['unit_factor'] ?? 1,
                    'batch_no' => $item['batch_no'] ?? null,
                    'expiry_date' => $item['expiry_date'] ?? null,
                    'quantity' => $item['quantity'],
                    'bonus_quantity' => $item['bonus_quantity'] ?? 0,
                    'unit_price' => $item['unit_price'],
                    'mrp' => $item['mrp'],
                    'trade_discount_percent' => $item['trade_discount_percent'] ?? 0,
                    'subtotal' => $itemSubtotal,
                ];
                
                // Update PurchaseOrderItem received quantity
                if (!empty($item['purchase_order_item_id'])) {
                    $poItem = \App\Models\PurchaseOrderItem::find($item['purchase_order_item_id']);
                    if ($poItem) {
                        $poItem->increment('received_qty', $item['quantity']);
                    }
                }
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
                'purchase_order_id' => $validated['purchase_order_id'] ?? null,
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

            // Update PurchaseOrder status
            if (!empty($validated['purchase_order_id'])) {
                $po = \App\Models\PurchaseOrder::with('items')->find($validated['purchase_order_id']);
                if ($po) {
                    $allReceived = true;
                    $anyReceived = false;
                    foreach ($po->items as $poItem) {
                        if ($poItem->received_qty < $poItem->ordered_qty) {
                            $allReceived = false;
                        }
                        if ($poItem->received_qty > 0) {
                            $anyReceived = true;
                        }
                    }
                    $po->update(['status' => $allReceived ? 'Completed' : ($anyReceived ? 'Partial' : 'Pending')]);
                }
            }

            foreach ($itemsData as $itemData) {
                $purchase->items()->create($itemData);

                $factor = $itemData['unit_factor'] ?: 1;
                $baseQuantityAdded = ($itemData['quantity'] + ($itemData['bonus_quantity'] ?? 0)) * $factor;
                $baseTradePrice = $itemData['unit_price'] / $factor;
                $baseMrp = $itemData['mrp'] / $factor;

                // Update Inventory
                $inventory = Inventory::firstOrCreate(
                    [
                        'company_id' => auth()->user()->company_id,
                        'branch_id' => auth()->user()->branch_id,
                        'medicine_id' => $itemData['medicine_id'],
                        'batch_no' => $itemData['batch_no'],
                    ],
                    [
                        'quantity' => 0, 
                        'expiry_date' => $itemData['expiry_date'], 
                        'mrp' => $baseMrp,
                        'trade_price' => $baseTradePrice
                    ]
                );

                $inventory->update([
                    'mrp' => $baseMrp,
                    'trade_price' => $baseTradePrice
                ]);
                
                $inventory->increment('quantity', $baseQuantityAdded);

                // Create Stock Ledger
                StockLedger::create([
                    'medicine_id' => $itemData['medicine_id'],
                    'batch_no' => $itemData['batch_no'],
                    'reference_type' => Purchase::class,
                    'reference_id' => $purchase->id,
                    'type' => 'in',
                    'quantity' => $baseQuantityAdded,
                    'balance_after' => $inventory->fresh()->quantity,
                    'notes' => 'Purchase ' . $purchase->reference_no . (($itemData['bonus_quantity'] > 0) ? ' (includes ' . $itemData['bonus_quantity'] . ' bonus ' . ($itemData['unit_name'] ?? 'units') . ')' : ''),
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
