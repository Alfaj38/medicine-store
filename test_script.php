<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::where('user_type', 'company')->first();
if (!$user) {
    echo "No company user found.";
    exit;
}
auth()->login($user);
$response = app()->make(\App\Http\Controllers\Company\SubscriptionController::class)->index();
echo "Success\n";
