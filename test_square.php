<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$items = \App\Models\Item::withoutGlobalScope(\App\Models\Scopes\TenantScope::class)
    ->whereHas('manufacturer', function($q) {
        $q->where('name', 'like', '%Square%');
    })
    ->take(5)
    ->get();

foreach ($items as $item) {
    echo "- " . $item->name . "\n";
}
