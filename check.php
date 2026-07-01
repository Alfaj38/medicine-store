<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$company = App\Models\Company::first();
$subscription = $company->subscription;
$package = $subscription->package;

echo "Company: " . $company->name . "\n";
echo "Package: " . $package->name . " (ID: " . $package->id . ")\n";
echo "Monthly Price: " . $package->monthly_price . "\n";
echo "Cycle: " . $subscription->billing_cycle . "\n";
echo "Expires: " . $subscription->expires_at . "\n";

$remainingDays = now()->diffInDays($subscription->expires_at, false);
echo "Remaining Days Float: " . $remainingDays . "\n";
echo "Remaining Days Rounded: " . (int) ceil($remainingDays) . "\n";

$oldDaily = $package->monthly_price / 30;
$unusedCredit = ((int) ceil($remainingDays)) * $oldDaily;

echo "Unused Credit Calc: " . $unusedCredit . "\n";
