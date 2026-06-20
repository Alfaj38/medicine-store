<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

auth()->login(\App\Models\User::first());

$query = 'Preservin';
$supplierId = 1;

$supplier = \App\Models\Supplier::find($supplierId);
$companies = is_string($supplier->companies) ? json_decode($supplier->companies, true) : $supplier->companies;
$manufacturerIds = \App\Models\Manufacturer::whereIn('name', $companies)->pluck('id');

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

$mapped = $items->map(function ($item) {
    return [
        'id' => $item->id,
        'name' => $item->name,
        'generic_name' => $item->medicineDetails ? $item->medicineDetails->generic_name : '',
        'buy_price' => $item->prices->first() ? $item->prices->first()->price : 0,
    ];
});

echo "Total items found: " . count($mapped) . "\n";
print_r($mapped->toArray());
