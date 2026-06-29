<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

auth()->login(\App\Models\User::first());

$req = Illuminate\Http\Request::create('/pos', 'POST', [
    'customer_id' => null,
    'subtotal' => 12.00,
    'discount' => 0,
    'tax' => 0,
    'total_amount' => 12.00,
    'paid_amount' => 12.00,
    'payment_method' => 'cash',
    'items' => [
        [
            'medicine_id' => 1,
            'unit_id' => null,
            'unit_factor' => 1,
            'batch_no' => null,
            'quantity' => 1,
            'unit_price' => 12.00,
            'subtotal' => 12.00,
        ]
    ]
]);

try {
    app(App\Http\Controllers\PosController::class)->store($req);
    echo "SUCCESS";
} catch (Illuminate\Validation\ValidationException $e) {
    echo json_encode($e->errors(), JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo "EXCEPTION: " . $e->getMessage();
}
