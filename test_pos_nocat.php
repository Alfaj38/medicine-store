<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

Auth::loginUsingId(3);

$request = Illuminate\Http\Request::create('/pos/browse', 'GET', ['filter' => 'all']);
$controller = new App\Http\Controllers\PosController();

try {
    $response = $controller->browseMedicines($request);
    $data = $response->getData(true);
    echo "Count: " . count($data) . "\n";
    if (count($data) == 0) {
        $companyId = auth()->user()->company_id;
        $itemIdsWithStock = \Illuminate\Support\Facades\DB::table('inventories')
            ->when($companyId, function($q) use ($companyId) {
                return $q->where('company_id', $companyId);
            })
            ->where('quantity', '>', 0)
            ->pluck('medicine_id');
        echo "Item IDs with stock: " . implode(',', $itemIdsWithStock->toArray()) . "\n";
    }
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n" . $e->getTraceAsString();
}
