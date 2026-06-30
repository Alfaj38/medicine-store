<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$company = \App\Models\Company::first();
if ($company) {
    $payment = \App\Models\SubscriptionPayment::where('company_id', $company->id)->latest()->first();
    if ($payment) {
        $payment->update(['status' => 'pending']);
    }
    
    $company->update(['subscription_expires_at' => now()->subDays(1)]);
    if ($company->subscription) {
        $company->subscription->update(['status' => 'expired', 'expires_at' => now()->subDays(1)]);
    }
    echo "Reverted payment and subscription successfully.";
} else {
    echo "No company found.";
}
