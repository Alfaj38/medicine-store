<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Item;
use App\Models\ItemCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class StorefrontController extends Controller
{
    public function show(Request $request, Company $company)
    {
        if (!$company->isActive()) {
            abort(404);
        }

        $company->load(['branches']);
        
        $categories = ItemCategory::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'icon', 'item_type_id']);

        $categoryId = $request->get('category');
        $queryText = $request->get('q');
        $companyId = $company->id;

        // Get item IDs that have stock for this company
        $itemIdsWithStock = DB::table('inventories')
            ->where('company_id', $companyId)
            ->where('quantity', '>', 0)
            ->pluck('medicine_id');

        $query = Item::withoutGlobalScope(\App\Models\Scopes\TenantScope::class)
            ->where('is_active', true)
            ->where(function($q) use ($companyId) {
                $q->whereNull('company_id')
                  ->orWhere('company_id', $companyId);
            })
            ->with(['medicineDetails', 'prices' => function($q) {
                $q->where('price_type', 'Retail');
            }, 'category']);

        if ($categoryId) {
            // Find category by slug
            $cat = ItemCategory::where('slug', $categoryId)->first();
            if ($cat) {
                $query->where('category_id', $cat->id);
            }
        }

        if ($queryText) {
            $query->where(function($q) use ($queryText) {
                $q->where('name', 'like', "%{$queryText}%")
                  ->orWhere('barcode', 'like', "%{$queryText}%")
                  ->orWhere('sku', 'like', "%{$queryText}%")
                  ->orWhereHas('medicineDetails', function($m) use ($queryText) {
                      $m->where('generic_name', 'like', "%{$queryText}%");
                  });
            });
        }

        // Only show items with stock or non-stock items
        $query->where(function($q) use ($itemIdsWithStock) {
            $q->where('inventory_type', '!=', 'Stock Item')
              ->orWhereIn('id', $itemIdsWithStock);
        });

        // Get paginated items
        $paginator = $query->paginate(24)->withQueryString();
        
        $itemIds = collect($paginator->items())->pluck('id')->toArray();
        $fallbackPrices = [];
        if (!empty($itemIds)) {
            $fallbackPrices = DB::table('purchase_items')
                ->whereIn('medicine_id', $itemIds)
                ->groupBy('medicine_id')
                ->select('medicine_id', DB::raw('MAX(unit_price) as max_price'))
                ->pluck('max_price', 'medicine_id')
                ->toArray();
        }

        $items = collect($paginator->items())->map(function($item) use ($companyId, $fallbackPrices) {
            // Get stock details for frontend format
            $inventory = \App\Models\Inventory::where('medicine_id', $item->id)
                ->where('company_id', $companyId)
                ->get();

            $sellPrice = $item->prices->first() ? $item->prices->first()->price : 0;
            if ($sellPrice <= 0 && isset($fallbackPrices[$item->id])) {
                $sellPrice = $fallbackPrices[$item->id];
            }

            $inventory->transform(function ($inv) use ($sellPrice) {
                if ($inv->mrp <= 0) {
                    $inv->mrp = $sellPrice;
                }
                return $inv;
            });
                
            return [
                'id' => $item->id,
                'name' => $item->name,
                'generic_name' => $item->medicineDetails ? $item->medicineDetails->generic_name : '',
                'barcode' => $item->barcode,
                'sell_price' => $sellPrice,
                'inventory' => $inventory,
                'image' => $item->image,
                'category' => $item->category ? $item->category->name : null,
            ];
        });

        return Inertia::render('Storefront/Show', [
            'company' => $company,
            'categories' => $categories,
            'medicines' => [
                'data' => $items,
                'links' => $paginator->linkCollection()->toArray(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'total' => $paginator->total(),
            ],
            'filters' => [
                'q' => $queryText,
                'category' => $categoryId,
            ]
        ]);
    }

    public function medicines(Request $request, Company $company)
    {
        if (!$company->isActive()) {
            abort(404);
        }

        $company->load(['branches']);
        
        $categories = ItemCategory::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'icon', 'item_type_id']);

        $categoryId = $request->get('category');
        $queryText = $request->get('q');
        $companyId = $company->id;

        // Get item IDs that have stock for this company
        $itemIdsWithStock = \Illuminate\Support\Facades\DB::table('inventories')
            ->where('company_id', $companyId)
            ->where('quantity', '>', 0)
            ->pluck('medicine_id');

        $query = Item::withoutGlobalScope(\App\Models\Scopes\TenantScope::class)
            ->where('is_active', true)
            ->where(function($q) use ($companyId) {
                $q->whereNull('company_id')
                  ->orWhere('company_id', $companyId);
            })
            ->with(['medicineDetails', 'prices' => function($q) {
                $q->where('price_type', 'Retail');
            }, 'category']);

        if ($categoryId) {
            $cat = ItemCategory::where('slug', $categoryId)->first();
            if ($cat) {
                $query->where('category_id', $cat->id);
            }
        }

        if ($queryText) {
            $query->where(function($q) use ($queryText) {
                $q->where('name', 'like', "%{$queryText}%")
                  ->orWhere('barcode', 'like', "%{$queryText}%")
                  ->orWhere('sku', 'like', "%{$queryText}%")
                  ->orWhereHas('medicineDetails', function($m) use ($queryText) {
                      $m->where('generic_name', 'like', "%{$queryText}%");
                  });
            });
        }

        $query->where(function($q) use ($itemIdsWithStock) {
            $q->where('inventory_type', '!=', 'Stock Item')
              ->orWhereIn('id', $itemIdsWithStock);
        });

        $paginator = $query->paginate(24)->withQueryString();
        
        $itemIds = collect($paginator->items())->pluck('id')->toArray();
        $fallbackPrices = [];
        if (!empty($itemIds)) {
            $fallbackPrices = \Illuminate\Support\Facades\DB::table('purchase_items')
                ->whereIn('medicine_id', $itemIds)
                ->groupBy('medicine_id')
                ->select('medicine_id', \Illuminate\Support\Facades\DB::raw('MAX(unit_price) as max_price'))
                ->pluck('max_price', 'medicine_id')
                ->toArray();
        }

        $items = collect($paginator->items())->map(function($item) use ($companyId, $fallbackPrices) {
            $inventory = \App\Models\Inventory::where('medicine_id', $item->id)
                ->where('company_id', $companyId)
                ->get();

            $sellPrice = $item->prices->first() ? $item->prices->first()->price : 0;
            if ($sellPrice <= 0 && isset($fallbackPrices[$item->id])) {
                $sellPrice = $fallbackPrices[$item->id];
            }

            $inventory->transform(function ($inv) use ($sellPrice) {
                if ($inv->mrp <= 0) {
                    $inv->mrp = $sellPrice;
                }
                return $inv;
            });
                
            return [
                'id' => $item->id,
                'name' => $item->name,
                'generic_name' => $item->medicineDetails ? $item->medicineDetails->generic_name : '',
                'barcode' => $item->barcode,
                'sell_price' => $sellPrice,
                'inventory' => $inventory,
                'image' => $item->image,
                'category' => $item->category ? $item->category->name : null,
            ];
        });

        return Inertia::render('Storefront/Medicines/Index', [
            'company' => $company,
            'categories' => $categories,
            'medicines' => [
                'data' => $items,
                'links' => $paginator->linkCollection()->toArray(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'total' => $paginator->total(),
            ],
            'filters' => [
                'q' => $queryText,
                'category' => $categoryId,
            ]
        ]);
    }

    public function checkout(Company $company)
    {
        if (!$company->isActive()) {
            abort(404);
        }

        $company->load(['branches']);

        return Inertia::render('Storefront/Checkout', [
            'company' => $company,
        ]);
    }

    public function placeOrder(Request $request, Company $company)
    {
        if (!$company->isActive()) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'delivery_method' => 'required|in:home,pickup',
            'city' => 'nullable|string|max:255',
            'address' => 'required|string',
            'payment_method' => 'required|string',
            'prescription' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'cart' => 'required|array',
            'cart.*.id' => 'required|exists:items,id',
            'cart.*.quantity' => 'required|integer|min:1',
            'cart.*.sell_price' => 'required|numeric|min:0',
            'cart.*.name' => 'required|string',
        ]);

        $prescriptionPath = null;
        if ($request->hasFile('prescription')) {
            $prescriptionPath = $request->file('prescription')->store('prescriptions', 'public');
        }

        $subtotal = 0;
        foreach ($validated['cart'] as $item) {
            $subtotal += ($item['sell_price'] * $item['quantity']);
        }

        $deliveryFee = $validated['delivery_method'] === 'home' ? 60 : 0;
        $totalAmount = $subtotal + $deliveryFee;

        // Create tracking number using pharmacy slug prefix
        $prefix = strtoupper(preg_replace('/[^A-Za-z]/', '', substr($company->slug, 0, 3)));
        if (strlen($prefix) < 3) $prefix = 'ORD';
        $trackingNumber = $prefix . '-' . strtoupper(substr(uniqid(), -6));

        $order = \App\Models\OnlineOrder::create([
            'company_id' => $company->id,
            'customer_name' => $validated['name'],
            'customer_phone' => $validated['phone'],
            'customer_email' => $validated['email'],
            'delivery_method' => $validated['delivery_method'],
            'city' => $validated['city'] ?? null,
            'delivery_address' => $validated['address'],
            'subtotal' => $subtotal,
            'delivery_fee' => $deliveryFee,
            'total_amount' => $totalAmount,
            'prescription_path' => $prescriptionPath,
            'payment_method' => $validated['payment_method'],
            'status' => 'pending',
            'tracking_number' => $trackingNumber,
        ]);

        foreach ($validated['cart'] as $item) {
            \App\Models\OnlineOrderItem::create([
                'online_order_id' => $order->id,
                'item_id' => $item['id'],
                'item_name' => $item['name'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['sell_price'],
                'total_price' => $item['sell_price'] * $item['quantity'],
            ]);
        }

        return redirect()->route('storefront.orderSuccess', [
            'company' => $company->slug,
            'trackingNumber' => $trackingNumber
        ]);
    }

    public function orderSuccess(Company $company, $trackingNumber)
    {
        if (!$company->isActive()) {
            abort(404);
        }

        $order = \App\Models\OnlineOrder::with('items')
            ->where('company_id', $company->id)
            ->where('tracking_number', $trackingNumber)
            ->firstOrFail();

        return Inertia::render('Storefront/OrderSuccess', [
            'company' => $company,
            'order' => $order,
        ]);
    }
}
