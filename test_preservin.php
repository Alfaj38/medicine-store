<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$item = App\Models\Item::withoutGlobalScope(App\Models\Scopes\TenantScope::class)->where('name', 'like', '%Preservin%')->first();
print_r($item->toArray());
