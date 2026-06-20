<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$items = App\Models\Item::withoutGlobalScopes()->whereIn('id', [1186, 15178])->get(['id', 'inventory_type']);
print_r($items->toArray());
