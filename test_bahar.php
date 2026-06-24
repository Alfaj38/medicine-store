<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$supplier = \App\Models\Supplier::where('name', 'like', '%Bahar%')->first();
if ($supplier) {
    echo "Supplier Name: " . $supplier->name . "\n";
    $companies = is_string($supplier->companies) ? json_decode($supplier->companies, true) : $supplier->companies;
    echo "Supplier Companies: " . json_encode($companies) . "\n";
} else {
    echo "Supplier Bahar not found\n";
}
