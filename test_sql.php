<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::whereNotNull('company_id')->first();
auth()->login($user);

$query = 'Preservin';
$supplierId = 1;
$supplier = \App\Models\Supplier::find($supplierId);
$companies = is_string($supplier->companies) ? json_decode($supplier->companies, true) : $supplier->companies;
$manufacturerIds = \App\Models\Manufacturer::whereIn('name', $companies)->pluck('id');

$q = \App\Models\Item::withoutGlobalScope(\App\Models\Scopes\TenantScope::class)
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
    ->take(15);

echo $q->toSql() . "\n";
print_r($q->getBindings());

$items = $q->get();
echo "Items found: " . count($items) . "\n";
