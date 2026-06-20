<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = [1, 2, 3];
foreach ($users as $userId) {
    Auth::loginUsingId($userId);
    $request = Illuminate\Http\Request::create('/pos/browse', 'GET', ['filter' => 'all']);
    $controller = new App\Http\Controllers\PosController();
    $data = $controller->browseMedicines($request)->getData(true);
    echo "User $userId (Company: " . auth()->user()->company_id . ") items count: " . count($data) . "\n";
}
