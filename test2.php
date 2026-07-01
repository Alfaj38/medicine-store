<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::where('user_type', 'company')->first();
auth()->login($user);

$company = auth()->user()->company;
echo "Company ID: " . $company->id . "\n";

echo "Getting subscription...\n";
$sub = $company->subscription;
echo "Subscription ID: " . ($sub ? $sub->id : 'null') . "\n";
