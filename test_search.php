<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$supplier = \App\Models\Supplier::first();
if (!$supplier) {
    echo "Supplier not found\n";
    exit;
}

echo "Supplier Name: " . $supplier->name . "\n";

$companies = is_string($supplier->companies) ? json_decode($supplier->companies, true) : $supplier->companies;
echo "Supplier Companies: " . json_encode($companies) . "\n";

$manufacturerIds = \App\Models\Manufacturer::whereIn('name', $companies ?: [])->pluck('id');
echo "Manufacturer IDs: " . json_encode($manufacturerIds) . "\n";

$query = 'napa';

$items = \App\Models\Item::withoutGlobalScope(\App\Models\Scopes\TenantScope::class)
    ->with(['medicineDetails', 'prices' => function($q) {
        $q->where('price_type', 'Purchase');
    }])
    ->where('is_active', true)
    ->whereIn('manufacturer_id', $manufacturerIds)
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

echo "Items found: " . count($items) . "\n";
foreach ($items as $item) {
    echo "- " . $item->name . "\n";
}
