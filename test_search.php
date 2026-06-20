<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$supplier = App\Models\Supplier::first();
if ($supplier) {
    echo "Supplier Companies:\n";
    $companies = is_string($supplier->companies) ? json_decode($supplier->companies, true) : $supplier->companies;
    print_r($companies);
    
    echo "Manufacturer IDs:\n";
    $mIds = App\Models\Manufacturer::whereIn('name', $companies)->pluck('id');
    print_r($mIds->toArray());

    echo "Items for these manufacturers:\n";
    $items = App\Models\Item::withoutGlobalScope(\App\Models\Scopes\TenantScope::class)->whereIn('manufacturer_id', $mIds)->take(5)->get();
    print_r($items->pluck('name')->toArray());
} else {
    echo "No supplier found.\n";
}
