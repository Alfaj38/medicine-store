<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$suppliers = App\Models\Supplier::all();
foreach($suppliers as $s) {
    $c = is_string($s->companies) ? json_decode($s->companies, true) : $s->companies;
    $mCount = App\Models\Manufacturer::whereIn('name', $c ?? [])->count();
    echo "Supplier ID {$s->id} - {$s->name} - Companies array length: " . count($c ?? []) . " - Mapped Manufacturers: {$mCount}\n";
}
