<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::where('user_type', 'company')->first();
auth()->login($user);

$company = auth()->user()->company;
$subscription = $company->subscription;
if ($subscription) {
    $subscription->load(['plan', 'planPrice']);
}
$plans = \App\Models\SubscriptionPlan::with('prices')->get();
$paymentHistory = \App\Models\SubscriptionPayment::where('company_id', $company->id)->take(2)->get();

echo "Data retrieved successfully.\n";

$data = [
    'plans' => $plans,
    'currentSubscription' => $subscription,
    'paymentHistory' => $paymentHistory,
];

echo "Encoding to JSON...\n";
$json = json_encode($data);
if ($json === false) {
    echo "JSON Encode failed: " . json_last_error_msg() . "\n";
} else {
    echo "JSON Encode successful. Length: " . strlen($json) . "\n";
}
echo "Done.\n";
