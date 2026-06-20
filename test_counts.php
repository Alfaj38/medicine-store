<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$inv = App\Models\Inventory::count(); 
echo 'Inventory count: ' . $inv . "\n"; 
$items = App\Models\Item::count(); 
echo 'Items count: ' . $items . "\n";
