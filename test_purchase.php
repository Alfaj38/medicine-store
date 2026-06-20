<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$schema = Illuminate\Support\Facades\Schema::getColumnListing('purchase_items');
print_r($schema);

// check a row
$row = Illuminate\Support\Facades\DB::table('purchase_items')->first();
print_r($row);
