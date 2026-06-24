<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Medicine;
use App\Models\MedicineCategory;
use App\Models\ItemCategory;
use App\Models\ItemType;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Inventory;
use App\Models\StockLedger;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function index()
    {
        $customers = Customer::where('is_active', true)->orderBy('name')->get(['id', 'name', 'current_balance']);
        // Load ALL item categories grouped by their item type for the sidebar
        $itemTypes = ItemType::orderBy('name')->get(['id', 'name']);
        $categories = ItemCategory::where('is_active', true)
            ->with('itemType')
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'icon', 'item_type_id']);
        $medicineCategories = MedicineCategory::where('is_active', true)->orderBy('name')->get(['id', 'name', 'slug']);
        $scope = app('data_scope');
        $saleQuery = Sale::query();
        $saleQuery = $scope->apply($saleQuery, auth()->user(), Sale::class);

        $recentSales = $saleQuery->where('sale_date', '>=', now()->startOfDay())
            ->with(['customer'])
            ->latest()
            ->take(10)
            ->get(['id', 'invoice_no', 'total_amount', 'payment_status', 'customer_id', 'sale_date']);

        return Inertia::render('Pos/Index', [
            'customers' => $customers,
            'categories' => $categories,
            'itemTypes' => $itemTypes,
            'recentSales' => $recentSales,
        ]);
    }

    public function browseMedicines(Request $request)
    {
        $categoryId = $request->get('category_id');
        $filter = $request->get('filter', 'all');
        $companyId = auth()->user()->company_id;

        // Get item IDs that have stock for this company (direct DB query, no scope issues)
        $itemIdsWithStock = \Illuminate\Support\Facades\DB::table('inventories')
            ->when($companyId, function($q) use ($companyId) {
                return $q->where('company_id', $companyId);
            })
            ->where('quantity', '>', 0)
            ->pluck('medicine_id');

        // Build the item query (bypass TenantScope since central catalog has company_id = null)
        $query = \App\Models\Item::withoutGlobalScope(\App\Models\Scopes\TenantScope::class)
            ->where('is_active', true)
            ->when($companyId, function($q) use ($companyId) {
                $q->where(function($sub) use ($companyId) {
                    $sub->whereNull('company_id')
                        ->orWhere('company_id', $companyId);
                });
            })
            ->with(['medicineDetails', 'prices' => function($q) {
                $q->where('price_type', 'Retail');
            }, 'category', 'units', 'uom']);

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($filter === 'low_stock') {
            // Items with stock > 0 but below reorder point (show all with stock for now)
            $query->whereIn('id', $itemIdsWithStock);
        } elseif ($filter === 'expiring_soon') {
            $expiringItemIds = \Illuminate\Support\Facades\DB::table('inventories')
                ->when($companyId, function($q) use ($companyId) {
                    return $q->where('company_id', $companyId);
                })
                ->where('quantity', '>', 0)
                ->where('expiry_date', '<=', now()->addDays(90))
                ->pluck('medicine_id');
            $query->whereIn('id', $expiringItemIds);
        } else {
            // All: show non-stock items (services) OR items that have stock
            $query->where(function($q) use ($itemIdsWithStock) {
                $q->where('inventory_type', '!=', 'Stock Item')
                  ->orWhereIn('id', $itemIdsWithStock);
            });
        }

        $itemIds = $query->pluck('id')->toArray();

        $items = $query->take(32)->get()->map(function($item) use ($companyId) {
            // Get stock details for frontend format
            $inventoryQuery = \App\Models\Inventory::where('medicine_id', $item->id);
            if ($companyId) {
                $inventoryQuery->where('company_id', $companyId);
            }
            $inventory = $inventoryQuery->get();
                
            $stockQty = $inventory->sum('quantity');

            $sellPrice = $item->prices->first() ? $item->prices->first()->price : 0;
            if ($sellPrice <= 0 && $inventory->count() > 0) {
                $sellPrice = $inventory->max('mrp');
            }

            // Fallback inventory mrp to sell_price if it's 0 so frontend shows correct price
            $inventory->transform(function ($inv) use ($sellPrice) {
                if ($inv->mrp <= 0) {
                    $inv->mrp = $sellPrice;
                }
                return $inv;
            });

            $units = $item->units;
            if ($units->isEmpty()) {
                $uomName = $item->uom ? $item->uom->name : 'Unit';
                $fallbackUnit = [
                    'id' => 'fallback',
                    'unit_name' => $uomName,
                    'factor' => 1,
                    'is_base_unit' => true,
                    'is_purchase_unit' => true,
                    'is_sales_unit' => true
                ];
                $units = collect([(object) $fallbackUnit]);
            }

            $salesUnit = $units->firstWhere('is_sales_unit', true) ?? $units->firstWhere('is_base_unit', true) ?? $units->first();
            $baseUnit = $units->firstWhere('is_base_unit', true) ?? $units->first();

            return [
                'id'           => $item->id,
                'name'         => $item->name,
                'generic_name' => $item->medicineDetails ? $item->medicineDetails->generic_name : '',
                'barcode'      => $item->barcode,
                'sell_price'   => $sellPrice, // Base selling price
                'category'     => $item->category,
                'stock_qty'    => (int) $stockQty, // Base stock quantity
                'inventory'    => $inventory,
                'units'        => $units,
                'default_unit' => $salesUnit,
                'base_unit'    => $baseUnit,
            ];
        });

        return response()->json($items);
    }

    public function quickCustomer(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'phone'           => 'nullable|string|max:30',
            'address'         => 'nullable|string|max:500',
            'opening_balance' => 'nullable|numeric|min:0',
        ]);

        $customer = Customer::create([
            'company_id'      => auth()->user()->company_id,
            'name'            => $validated['name'],
            'phone'           => $validated['phone'] ?? null,
            'address'         => $validated['address'] ?? null,
            'opening_balance' => $validated['opening_balance'] ?? 0,
            'current_balance' => $validated['opening_balance'] ?? 0,
            'is_active'       => true,
        ]);

        return redirect()->back()->with('newCustomer', $customer->only(['id', 'name', 'phone', 'current_balance']));
    }

    public function searchMedicines(Request $request)
    {
        $queryText = $request->get('q');
        
        if (!$queryText) {
            return response()->json([]);
        }

        $companyId = auth()->user()->company_id;

        // Get item IDs that have stock for this company
        $itemIdsWithStock = \Illuminate\Support\Facades\DB::table('inventories')
            ->when($companyId, function($q) use ($companyId) {
                return $q->where('company_id', $companyId);
            })
            ->where('quantity', '>', 0)
            ->pluck('medicine_id');

        $query = \App\Models\Item::withoutGlobalScope(\App\Models\Scopes\TenantScope::class)
            ->where('is_active', true)
            ->when($companyId, function($q) use ($companyId) {
                $q->where(function($sub) use ($companyId) {
                    $sub->whereNull('company_id')
                        ->orWhere('company_id', $companyId);
                });
            })
            ->where(function($q) use ($queryText) {
                $q->where('name', 'like', "%{$queryText}%")
                  ->orWhere('barcode', 'like', "%{$queryText}%")
                  ->orWhere('sku', 'like', "%{$queryText}%")
                  ->orWhereHas('medicineDetails', function($m) use ($queryText) {
                      $m->where('generic_name', 'like', "%{$queryText}%");
                  });
            })
            ->where(function($q) use ($itemIdsWithStock) {
                $q->where('inventory_type', '!=', 'Stock Item')
                  ->orWhereIn('id', $itemIdsWithStock);
            });

        $itemIds = $query->pluck('id')->toArray();

        $items = $query->with(['medicineDetails', 'prices' => function($q) {
                $q->where('price_type', 'Retail');
            }, 'units', 'uom'])
            ->take(15)
            ->get()->map(function($item) use ($companyId) {
                // Get stock details for frontend format
                $inventoryQuery = \App\Models\Inventory::where('medicine_id', $item->id);
                if ($companyId) {
                    $inventoryQuery->where('company_id', $companyId);
                }
                $inventory = $inventoryQuery->get();

                $sellPrice = $item->prices->first() ? $item->prices->first()->price : 0;
                if ($sellPrice <= 0 && $inventory->count() > 0) {
                    $sellPrice = $inventory->max('mrp');
                }

                $inventory->transform(function ($inv) use ($sellPrice) {
                    if ($inv->mrp <= 0) {
                        $inv->mrp = $sellPrice;
                    }
                    return $inv;
                });
                    
                $units = $item->units;
                if ($units->isEmpty()) {
                    $uomName = $item->uom ? $item->uom->name : 'Unit';
                    $fallbackUnit = [
                        'id' => 'fallback',
                        'unit_name' => $uomName,
                        'factor' => 1,
                        'is_base_unit' => true,
                        'is_purchase_unit' => true,
                        'is_sales_unit' => true
                    ];
                    $units = collect([(object) $fallbackUnit]);
                }

                $salesUnit = $units->firstWhere('is_sales_unit', true) ?? $units->firstWhere('is_base_unit', true) ?? $units->first();
                $baseUnit = $units->firstWhere('is_base_unit', true) ?? $units->first();

                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'generic_name' => $item->medicineDetails ? $item->medicineDetails->generic_name : '',
                    'barcode' => $item->barcode,
                    'sell_price' => $sellPrice,
                    'inventory' => $inventory,
                    'units' => $units,
                    'default_unit' => $salesUnit,
                    'base_unit' => $baseUnit,
                ];
            });

        return response()->json($items);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'subtotal' => 'required|numeric|min:0',
            'discount' => 'numeric|min:0',
            'tax' => 'numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.medicine_id' => 'required|exists:items,id',
            'items.*.unit_id' => 'nullable|exists:item_units,id',
            'items.*.unit_factor' => 'nullable|integer|min:1',
            'items.*.batch_no' => 'nullable|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.subtotal' => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($validated) {
            $paymentStatus = 'unpaid';
            if ($validated['paid_amount'] >= $validated['total_amount']) {
                $paymentStatus = 'paid';
            } elseif ($validated['paid_amount'] > 0) {
                $paymentStatus = 'partial';
            }

            // Generate Invoice No
            $invoiceNo = 'INV-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

            $sale = Sale::create([
                'company_id' => auth()->user()->company_id,
                'branch_id' => auth()->user()->branch_id,
                'customer_id' => $validated['customer_id'],
                'user_id' => auth()->id(),
                'invoice_no' => $invoiceNo,
                'sale_date' => now(),
                'subtotal' => $validated['subtotal'],
                'discount' => $validated['discount'] ?? 0,
                'tax' => $validated['tax'] ?? 0,
                'total_amount' => $validated['total_amount'],
                'paid_amount' => $validated['paid_amount'],
                'payment_method' => $validated['payment_method'],
                'payment_status' => $paymentStatus,
                'status' => 'completed',
            ]);

            foreach ($validated['items'] as $item) {
                $sale->items()->create([
                    'medicine_id' => $item['medicine_id'],
                    'unit_id' => $item['unit_id'] ?? null,
                    'unit_factor' => $item['unit_factor'] ?? 1,
                    'batch_no' => $item['batch_no'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['subtotal'],
                    'discount' => 0,
                    'total' => $item['subtotal'],
                ]);

                $factor = $item['unit_factor'] ?? 1;
                $deductionQuantity = $item['quantity'] * $factor;

                // Deduct Inventory
                $inventory = Inventory::where('company_id', auth()->user()->company_id)
                    ->where('medicine_id', $item['medicine_id'])
                    ->when($item['batch_no'], function($q) use ($item) {
                        return $q->where('batch_no', $item['batch_no']);
                    })
                    ->orderBy('expiry_date', 'asc') // FEFO priority
                    ->first();

                if ($inventory) {
                    $inventory->decrement('quantity', $deductionQuantity);
                    
                    // Stock Ledger
                    StockLedger::create([
                        'medicine_id' => $item['medicine_id'],
                        'batch_no' => $item['batch_no'],
                        'reference_type' => Sale::class,
                        'reference_id' => $sale->id,
                        'type' => 'out',
                        'quantity' => -$deductionQuantity,
                        'balance_after' => $inventory->fresh()->quantity,
                        'notes' => 'Sale ' . $invoiceNo,
                    ]);
                }
            }

            // Customer Balance
            if ($validated['customer_id']) {
                $due = $validated['total_amount'] - $validated['paid_amount'];
                if ($due != 0) { // Can be negative if overpaid (advance)
                    Customer::where('id', $validated['customer_id'])->increment('current_balance', $due);
                }
            }

            return redirect()->route('sales.show', $sale->id);
        });
    }
}
