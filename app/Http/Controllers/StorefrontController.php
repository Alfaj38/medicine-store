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
    protected function getTrackingData(Company $company)
    {
        $settings = $company->settings['seo'] ?? [];
        $gsc = $settings['google_search_console_html'] ?? null;
        if ($gsc && preg_match('/content="([^"]+)"/', $gsc, $matches)) {
            $gsc = $matches[1];
        } elseif ($gsc && preg_match("/content='([^']+)'/", $gsc, $matches)) {
            $gsc = $matches[1];
        }

        return [
            'ga' => $settings['google_analytics_id'] ?? null,
            'pixel' => $settings['facebook_pixel_id'] ?? null,
            'gsc' => $gsc
        ];
    }

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

        $company->load(['areas']);
        $areaNames = $company->areas->pluck('name')->toArray();
        $deliveryAreas = !empty($areaNames) ? implode(', ', $areaNames) : $company->address;

        $localSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'Pharmacy',
            'name' => $company->name,
            'image' => asset('images/medicine_sample.jpg'), // Placeholder or company logo
            '@id' => route('storefront.show', $company->slug),
            'url' => route('storefront.show', $company->slug),
            'telephone' => $company->phone,
            'priceRange' => '৳৳',
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $company->address,
                'addressCountry' => 'BD'
            ],
            'areaServed' => [
                '@type' => 'GeoCircle',
                'geoMidpoint' => [
                    '@type' => 'GeoCoordinates',
                    'latitude' => 23.8103, // Dummy default, ideally from company settings
                    'longitude' => 90.4125
                ],
                'geoRadius' => '5000'
            ],
            'description' => "Order medicines online from {$company->name}. We deliver to {$deliveryAreas}.",
            'openingHoursSpecification' => [
                [
                    '@type' => 'OpeningHoursSpecification',
                    'dayOfWeek' => [
                        'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'
                    ],
                    'opens' => '08:00',
                    'closes' => '22:00'
                ]
            ]
        ];

        $seoService = new \App\Services\SeoService();
        $seo = $seoService->generate([
            'site_name' => $company->name,
            'title' => "Buy Medicines Online - {$company->name}",
            'description' => "Order from {$company->name} online. Fast delivery in your area. Serving {$deliveryAreas}.",
            'url' => route('storefront.show', $company->slug),
            'schema' => $localSchema,
            'tracking' => $this->getTrackingData($company)
        ]);

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
            ],
            'seo' => $seo
        ]);
    }

    public function deliveryArea(Request $request, Company $company, $areaSlug)
    {
        if (!$company->isActive()) {
            abort(404);
        }

        // We could look up an exact area via Slug if we added slugs to Areas,
        // but for now, we'll format the slug into a human readable string.
        $areaName = ucwords(str_replace('-', ' ', $areaSlug));

        // Use same logic as index/show to load medicines, but optimize SEO for area
        $categories = \App\Models\ItemCategory::where('is_active', true)
            ->whereNull('parent_id')
            ->where(function($q) use ($company) {
                $q->whereNull('company_id')
                  ->orWhere('company_id', $company->id);
            })
            ->get();
            
        $items = collect(); // Simplified for landing page
        
        $localSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'Pharmacy',
            'name' => $company->name,
            'image' => asset('images/medicine_sample.jpg'),
            '@id' => route('storefront.show', $company->slug),
            'url' => route('storefront.deliveryArea', ['company' => $company->slug, 'area' => $areaSlug]),
            'telephone' => $company->phone,
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => $areaName,
                'addressCountry' => 'BD'
            ],
            'areaServed' => [
                '@type' => 'Place',
                'name' => $areaName
            ],
            'description' => "Fast medicine delivery in {$areaName} by {$company->name}."
        ];

        $seoService = new \App\Services\SeoService();
        $seo = $seoService->generate([
            'site_name' => $company->name,
            'title' => "Medicine Delivery in {$areaName} - {$company->name}",
            'description' => "Need medicine in {$areaName}? Order from {$company->name} online for fast local delivery.",
            'url' => route('storefront.deliveryArea', ['company' => $company->slug, 'area' => $areaSlug]),
            'schema' => $localSchema,
            'tracking' => $this->getTrackingData($company)
        ]);

        return Inertia::render('Storefront/DeliveryArea', [
            'company' => $company,
            'areaName' => $areaName,
            'seo' => $seo,
            'categories' => $categories
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

        $seoService = new \App\Services\SeoService();
        $seo = $seoService->generate([
            'site_name' => $company->name,
            'title' => "All Medicines - {$company->name}",
            'description' => "Browse all available medicines at {$company->name}.",
            'url' => route('storefront.medicines', $company->slug),
            'tracking' => $this->getTrackingData($company)
        ]);

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
            ],
            'seo' => $seo
        ]);
    }

    public function medicineDetails(Request $request, Company $company, $medicineSlug)
    {
        if (!$company->isActive()) {
            abort(404);
        }

        $company->load(['branches']);

        // Find medicine by slug or ID
        $item = Item::withoutGlobalScope(\App\Models\Scopes\TenantScope::class)
            ->where('is_active', true)
            ->where(function($q) use ($medicineSlug) {
                $q->where('slug', $medicineSlug)
                  ->orWhere('id', $medicineSlug);
            })
            ->where(function($q) use ($company) {
                $q->whereNull('company_id')
                  ->orWhere('company_id', $company->id);
            })
            ->with(['medicineDetails', 'category', 'brand', 'manufacturer', 'prices' => function($q) {
                $q->where('price_type', 'Retail');
            }])
            ->firstOrFail();

        // Get inventory for this specific branch/company
        $inventory = \App\Models\Inventory::where('medicine_id', $item->id)
            ->where('company_id', $company->id)
            ->get();
            
        $sellPrice = $item->prices->first() ? $item->prices->first()->price : 0;
        if ($sellPrice <= 0) {
            $maxPurchasePrice = \Illuminate\Support\Facades\DB::table('purchase_items')
                ->where('medicine_id', $item->id)
                ->max('unit_price');
            if ($maxPurchasePrice) {
                $sellPrice = $maxPurchasePrice;
            }
        }

        $inventory->transform(function ($inv) use ($sellPrice) {
            if ($inv->mrp <= 0) {
                $inv->mrp = $sellPrice;
            }
            return $inv;
        });

        // GEO: Relational Extractor - Find alternatives with same generic name
        $alternatives = collect();
        if ($item->medicineDetails && $item->medicineDetails->generic_name) {
            $genericName = $item->medicineDetails->generic_name;
            $alternatives = Item::withoutGlobalScope(\App\Models\Scopes\TenantScope::class)
                ->where('is_active', true)
                ->where('id', '!=', $item->id)
                ->where(function($q) use ($company) {
                    $q->whereNull('company_id')
                      ->orWhere('company_id', $company->id);
                })
                ->whereHas('medicineDetails', function($q) use ($genericName) {
                    $q->where('generic_name', $genericName);
                })
                ->with(['medicineDetails', 'manufacturer', 'prices' => function($q) {
                    $q->where('price_type', 'Retail');
                }])
                ->limit(5)
                ->get()
                ->map(function($altItem) {
                    $altPrice = $altItem->prices->first() ? $altItem->prices->first()->price : 0;
                    return [
                        'id' => $altItem->id,
                        'name' => $altItem->name,
                        'slug' => $altItem->slug,
                        'sell_price' => $altPrice,
                        'manufacturer' => $altItem->manufacturer ? $altItem->manufacturer->name : null,
                        'image' => $altItem->image,
                    ];
                });
        }

        $medicineData = [
            'id' => $item->id,
            'name' => $item->name,
            'slug' => $item->slug,
            'generic_name' => $item->medicineDetails ? $item->medicineDetails->generic_name : '',
            'barcode' => $item->barcode,
            'sell_price' => $sellPrice,
            'inventory' => $inventory,
            'image' => $item->image,
            'category' => $item->category ? $item->category->name : null,
            'manufacturer' => $item->manufacturer ? $item->manufacturer->name : null,
            'strength' => $item->medicineDetails ? $item->medicineDetails->strength : null,
            'dosage_form' => $item->medicineDetails ? $item->medicineDetails->dosage_form : null,
            'description' => $item->description,
        ];

        $productSchema = [
            '@context' => 'https://schema.org/',
            '@type' => 'Product',
            'name' => $item->name,
            'image' => $item->image ? asset("storage/{$item->image}") : asset("images/medicine_sample.jpg"),
            'description' => $item->description ?: "Buy {$item->name} online.",
            'brand' => [
                '@type' => 'Brand',
                'name' => $item->manufacturer ? $item->manufacturer->name : 'Unknown'
            ],
            'offers' => [
                '@type' => 'Offer',
                'url' => route('storefront.medicine.details', ['company' => $company->slug, 'medicine' => $medicineSlug]),
                'priceCurrency' => 'BDT',
                'price' => $sellPrice,
                'availability' => $inventory->sum('quantity') > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                'itemCondition' => 'https://schema.org/NewCondition'
            ]
        ];

        // AEO (Answer Engine Optimization) - FAQ Schema
        $faqSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => [
                [
                    '@type' => 'Question',
                    'name' => "What is the price of {$item->name} in Bangladesh?",
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => "The current price of {$item->name} is {$sellPrice} BDT at {$company->name}."
                    ]
                ]
            ]
        ];

        if ($item->medicineDetails && $item->medicineDetails->generic_name) {
            $faqSchema['mainEntity'][] = [
                '@type' => 'Question',
                'name' => "What is the generic name of {$item->name}?",
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => "The generic name of {$item->name} is {$item->medicineDetails->generic_name}."
                ]
            ];
        }

        $faqSchema['mainEntity'][] = [
            '@type' => 'Question',
            'name' => "Is {$item->name} available for home delivery from {$company->name}?",
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => "Yes, you can order {$item->name} online from {$company->name} and get fast home delivery."
            ]
        ];

        // GEO: ItemList Schema for Alternatives
        $alternativesSchema = null;
        if ($alternatives->count() > 0) {
            $alternativesSchema = [
                '@context' => 'https://schema.org',
                '@type' => 'ItemList',
                'name' => "Alternative Medicines for {$item->name} ({$item->medicineDetails?->generic_name})",
                'itemListElement' => $alternatives->map(function($alt, $index) use ($company) {
                    return [
                        '@type' => 'ListItem',
                        'position' => $index + 1,
                        'url' => route('storefront.medicine.details', ['company' => $company->slug, 'medicine' => $alt['slug'] ?? $alt['id']]),
                        'name' => $alt['name']
                    ];
                })->toArray()
            ];
            $seoSchemas = [$productSchema, $faqSchema, $alternativesSchema];
        } else {
            $seoSchemas = [$productSchema, $faqSchema];
        }

        $seoService = new \App\Services\SeoService();
        $seo = $seoService->generate([
            'site_name' => $company->name,
            'title' => "Buy {$item->name} - {$company->name}",
            'description' => "Order {$item->name} online from {$company->name}. Fast delivery in your area.",
            'url' => route('storefront.medicine.details', ['company' => $company->slug, 'medicine' => $medicineSlug]),
            'image' => route('og.medicine', ['company' => $company->slug, 'medicineSlug' => $medicineSlug]),
            'schema' => $seoSchemas,
            'tracking' => $this->getTrackingData($company)
        ]);

        return Inertia::render('Storefront/Medicines/Show', [
            'company' => $company,
            'medicine' => $medicineData,
            'alternatives' => $alternatives,
            'seo' => $seo
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
