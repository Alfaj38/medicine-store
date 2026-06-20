<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Get the latest unit_price for each medicine from purchase_items
$purchasePrices = DB::table('purchase_items')
    ->select('medicine_id', DB::raw('MAX(unit_price) as sell_price'))
    ->whereIn('medicine_id', [1186, 15178])
    ->groupBy('medicine_id')
    ->get();

echo "=== Purchase prices found ===\n";
if ($purchasePrices->isEmpty()) {
    echo "  [NONE] - checking column names...\n";
    // Show columns of purchase_items to find the right price column
    $sample = DB::table('purchase_items')->first();
    if ($sample) {
        echo "  Columns: " . implode(', ', array_keys((array)$sample)) . "\n";
    } else {
        echo "  purchase_items table is empty!\n";
    }
    exit;
}

foreach ($purchasePrices as $p) {
    echo "  medicine_id={$p->medicine_id}, sell_price={$p->sell_price}\n";

    // Update the existing item_prices row
    DB::table('item_prices')
        ->where('item_id', $p->medicine_id)
        ->where('price_type', 'Retail')
        ->update([
            'price'      => $p->sell_price,
            'updated_at' => now(),
        ]);
}

echo "\n=== Updated item_prices ===\n";
$prices = DB::table('item_prices')->get();
foreach ($prices as $pr) {
    echo "  item_id={$pr->item_id}, type={$pr->price_type}, price={$pr->price}\n";
}
