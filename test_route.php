<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

auth()->login(\App\Models\User::first());

$request = Illuminate\Http\Request::create('/purchases/search-medicines', 'GET', [
    'q' => 'Preservin',
    'supplier_id' => 1
]);

$response = app()->handle($request);
echo "Status: " . $response->getStatusCode() . "\n";
echo "Content:\n" . $response->getContent() . "\n";
