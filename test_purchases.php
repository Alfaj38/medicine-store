<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$purchases = App\Models\Purchase::with('items.item')->orderBy('id', 'desc')->take(3)->get();
foreach($purchases as $p) {
    echo "Purchase ID {$p->id} - Status: {$p->status}\n";
    foreach($p->items as $pi) {
        echo "  - Item: " . ($pi->item ? $pi->item->name : 'N/A') . " (Qty: {$pi->quantity})\n";
    }
}
