<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemType;
use App\Models\Uom;
use App\Models\Manufacturer;
use App\Models\PharmaceuticalIndustry;
use App\Models\Supplier;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $items = Item::query()
            ->with(['itemType', 'category', 'manufacturer', 'medicineDetails', 'prices', 'units'])
            ->where('company_id', auth()->user()->company_id)
            ->when($search, function ($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%")
                      ->orWhere('barcode', 'like', "%{$search}%")
                      ->orWhereHas('manufacturer', function($m) use ($search) {
                          $m->where('name', 'like', "%{$search}%");
                      });
                });
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Items/Index', [
            'items' => $items,
            'filters' => ['search' => $search],
        ]);
    }

    public function create()
    {
        return Inertia::render('Items/Form', [
            'itemTypes' => ItemType::orderBy('name')->get(),
            'categories' => ItemCategory::where('is_active', true)->orderBy('name')->get(),
            'uoms' => Uom::orderBy('name')->get(),
            'pharmaceuticalIndustries' => PharmaceuticalIndustry::orderBy('name')->get(),
            'suppliers' => Supplier::where('company_id', auth()->user()->company_id)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_type_id' => 'required|exists:item_types,id',
            'name' => 'required|string|max:191',
            'barcode' => 'nullable|string|unique:items,barcode',
            'sku' => 'nullable|string|max:191',
            'category_id' => 'nullable|exists:item_categories,id',
            'uom_id' => 'required|exists:uoms,id',
            'manufacturer_name' => 'nullable|string|max:191',
            'buy_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            
            // Inventory behavior
            'inventory_type' => 'required|string|in:Stock Item,Non-Stock Item,Service,Asset',
            'track_batch' => 'boolean',
            'track_expiry' => 'boolean',
            'track_serial' => 'boolean',
            'is_active' => 'boolean',

            // Medicine specific
            'generic_name' => 'nullable|string|max:191',
            'strength' => 'nullable|string|max:100',
            'dosage_form' => 'nullable|string|max:100',
            
            // Procurement & Sourcing
            'preferred_supplier_id' => 'nullable|exists:suppliers,id',
            'alternate_supplier_id' => 'nullable|exists:suppliers,id',
            'reorder_level' => 'nullable|integer|min:0',
            'reorder_qty' => 'nullable|integer|min:0',
            'safety_stock' => 'nullable|integer|min:0',
            'lead_time_days' => 'nullable|integer|min:0',

            // Units
            'units' => 'required|array|min:1',
            'units.*.unit_name' => 'required|string|max:100',
            'units.*.factor' => 'required|integer|min:1',
            'units.*.is_base_unit' => 'boolean',
            'units.*.is_purchase_unit' => 'boolean',
            'units.*.is_sales_unit' => 'boolean',
        ]);

        return DB::transaction(function () use ($validated) {
            $manufacturerId = null;
            if (!empty($validated['manufacturer_name'])) {
                $manufacturer = Manufacturer::firstOrCreate(
                    ['name' => $validated['manufacturer_name'], 'company_id' => auth()->user()->company_id]
                );
                $manufacturerId = $manufacturer->id;
            }

            $item = Item::create([
                'company_id' => auth()->user()->company_id,
                'item_code' => 'ITM-' . strtoupper(uniqid()),
                'name' => $validated['name'],
                'barcode' => $validated['barcode'],
                'sku' => $validated['sku'],
                'item_type_id' => $validated['item_type_id'],
                'category_id' => $validated['category_id'],
                'uom_id' => $validated['uom_id'],
                'manufacturer_id' => $manufacturerId,
                'inventory_type' => $validated['inventory_type'],
                'track_batch' => $validated['track_batch'] ?? false,
                'track_expiry' => $validated['track_expiry'] ?? false,
                'track_serial' => $validated['track_serial'] ?? false,
                'is_active' => $validated['is_active'] ?? true,
                'preferred_supplier_id' => $validated['preferred_supplier_id'] ?? null,
                'alternate_supplier_id' => $validated['alternate_supplier_id'] ?? null,
                'reorder_level' => $validated['reorder_level'] ?? 0,
                'reorder_qty' => $validated['reorder_qty'] ?? 0,
                'safety_stock' => $validated['safety_stock'] ?? 0,
                'lead_time_days' => $validated['lead_time_days'] ?? 0,
            ]);

            // Save Prices
            if ($validated['buy_price'] > 0) {
                $item->prices()->create([
                    'price_type' => 'Purchase',
                    'price' => $validated['buy_price'],
                    'effective_from' => now(),
                ]);
            }
            if ($validated['sell_price'] > 0) {
                $item->prices()->create([
                    'price_type' => 'Retail',
                    'price' => $validated['sell_price'],
                    'effective_from' => now(),
                ]);
            }

            // Save Medicine Details
            $medicineType = ItemType::where('name', 'Medicine')->first();
            if ($medicineType && $validated['item_type_id'] == $medicineType->id) {
                $item->medicineDetails()->create([
                    'generic_name' => $validated['generic_name'],
                    'strength' => $validated['strength'],
                    'dosage_form' => $validated['dosage_form'],
                ]);
            }

            // Save Units
            foreach ($validated['units'] as $unit) {
                $item->units()->create([
                    'unit_name' => $unit['unit_name'],
                    'factor' => $unit['factor'],
                    'is_base_unit' => $unit['is_base_unit'] ?? false,
                    'is_purchase_unit' => $unit['is_purchase_unit'] ?? false,
                    'is_sales_unit' => $unit['is_sales_unit'] ?? false,
                ]);
            }

            return redirect()->route('items.index')->with('success', 'Item added successfully.');
        });
    }

    public function edit(Item $item)
    {
        if ($item->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $item->load(['medicineDetails', 'prices', 'manufacturer', 'units']);

        return Inertia::render('Items/Form', [
            'item' => $item,
            'itemTypes' => ItemType::orderBy('name')->get(),
            'categories' => ItemCategory::where('is_active', true)->orderBy('name')->get(),
            'uoms' => Uom::orderBy('name')->get(),
            'pharmaceuticalIndustries' => PharmaceuticalIndustry::orderBy('name')->get(),
            'suppliers' => Supplier::where('company_id', auth()->user()->company_id)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Item $item)
    {
        if ($item->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $validated = $request->validate([
            'item_type_id' => 'required|exists:item_types,id',
            'name' => 'required|string|max:191',
            'barcode' => 'nullable|string|unique:items,barcode,' . $item->id,
            'sku' => 'nullable|string|max:191',
            'category_id' => 'nullable|exists:item_categories,id',
            'uom_id' => 'required|exists:uoms,id',
            'manufacturer_name' => 'nullable|string|max:191',
            'buy_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            
            'inventory_type' => 'required|string|in:Stock Item,Non-Stock Item,Service,Asset',
            'track_batch' => 'boolean',
            'track_expiry' => 'boolean',
            'track_serial' => 'boolean',
            'is_active' => 'boolean',

            'generic_name' => 'nullable|string|max:191',
            'strength' => 'nullable|string|max:100',
            'dosage_form' => 'nullable|string|max:100',

            // Procurement & Sourcing
            'preferred_supplier_id' => 'nullable|exists:suppliers,id',
            'alternate_supplier_id' => 'nullable|exists:suppliers,id',
            'reorder_level' => 'nullable|integer|min:0',
            'reorder_qty' => 'nullable|integer|min:0',
            'safety_stock' => 'nullable|integer|min:0',
            'lead_time_days' => 'nullable|integer|min:0',

            // Units
            'units' => 'required|array|min:1',
            'units.*.id' => 'nullable|integer',
            'units.*.unit_name' => 'required|string|max:100',
            'units.*.factor' => 'required|integer|min:1',
            'units.*.is_base_unit' => 'boolean',
            'units.*.is_purchase_unit' => 'boolean',
            'units.*.is_sales_unit' => 'boolean',
        ]);

        return DB::transaction(function () use ($validated, $item) {
            $manufacturerId = null;
            if (!empty($validated['manufacturer_name'])) {
                $manufacturer = Manufacturer::firstOrCreate(
                    ['name' => $validated['manufacturer_name'], 'company_id' => auth()->user()->company_id]
                );
                $manufacturerId = $manufacturer->id;
            }

            $item->update([
                'name' => $validated['name'],
                'barcode' => $validated['barcode'],
                'sku' => $validated['sku'],
                'item_type_id' => $validated['item_type_id'],
                'category_id' => $validated['category_id'],
                'uom_id' => $validated['uom_id'],
                'manufacturer_id' => $manufacturerId,
                'inventory_type' => $validated['inventory_type'],
                'track_batch' => $validated['track_batch'] ?? false,
                'track_expiry' => $validated['track_expiry'] ?? false,
                'track_serial' => $validated['track_serial'] ?? false,
                'is_active' => $validated['is_active'] ?? true,
                'preferred_supplier_id' => $validated['preferred_supplier_id'] ?? null,
                'alternate_supplier_id' => $validated['alternate_supplier_id'] ?? null,
                'reorder_level' => $validated['reorder_level'] ?? 0,
                'reorder_qty' => $validated['reorder_qty'] ?? 0,
                'safety_stock' => $validated['safety_stock'] ?? 0,
                'lead_time_days' => $validated['lead_time_days'] ?? 0,
            ]);

            // Update Prices (simplified)
            $item->prices()->where('price_type', 'Purchase')->update(['price' => $validated['buy_price']]);
            $item->prices()->where('price_type', 'Retail')->update(['price' => $validated['sell_price']]);

            // Save Medicine Details
            $medicineType = ItemType::where('name', 'Medicine')->first();
            if ($medicineType && $validated['item_type_id'] == $medicineType->id) {
                $item->medicineDetails()->updateOrCreate(
                    ['item_id' => $item->id],
                    [
                        'generic_name' => $validated['generic_name'],
                        'strength' => $validated['strength'],
                        'dosage_form' => $validated['dosage_form'],
                    ]
                );
            } else {
                $item->medicineDetails()->delete();
            }

            // Update Units
            $existingUnitIds = $item->units->pluck('id')->toArray();
            $updatedUnitIds = [];

            foreach ($validated['units'] as $unit) {
                $unitData = [
                    'unit_name' => $unit['unit_name'],
                    'factor' => $unit['factor'],
                    'is_base_unit' => $unit['is_base_unit'] ?? false,
                    'is_purchase_unit' => $unit['is_purchase_unit'] ?? false,
                    'is_sales_unit' => $unit['is_sales_unit'] ?? false,
                ];

                if (!empty($unit['id'])) {
                    $itemUnit = $item->units()->find($unit['id']);
                    if ($itemUnit) {
                        $itemUnit->update($unitData);
                        $updatedUnitIds[] = $itemUnit->id;
                    }
                } else {
                    $newUnit = $item->units()->create($unitData);
                    $updatedUnitIds[] = $newUnit->id;
                }
            }

            // delete missing
            $missing = array_diff($existingUnitIds, $updatedUnitIds);
            if (count($missing) > 0) {
                $item->units()->whereIn('id', $missing)->delete();
            }

            return redirect()->route('items.index')->with('success', 'Item updated successfully.');
        });
    }

    public function destroy(Item $item)
    {
        if ($item->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $item->delete();

        return redirect()->route('items.index')->with('success', 'Item deleted successfully.');
    }
}
