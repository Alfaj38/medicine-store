<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$items = \App\Models\Item::withoutGlobalScope(\App\Models\Scopes\TenantScope::class)->where('name', 'like', '%Napa%')->take(5)->get();
foreach ($items as $item) {
    $mfg = \App\Models\Manufacturer::find($item->manufacturer_id);
    echo "- " . $item->name . " (Mfg: " . ($mfg ? $mfg->name : 'None') . ")\n";
}
