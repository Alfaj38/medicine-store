<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\SubscriptionPlan;
use App\Models\SubscriptionPlanPrice;
use App\Models\SubscriptionPayment;
use App\Services\AffiliateCommissionService;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class SubscriptionController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::with('prices')->get();
        $subscription = auth()->user()->company->subscription;
        
        return Inertia::render('Company/Subscription/Index', [
            'plans' => $plans,
            'currentSubscription' => $subscription
        ]);
    }

    public function upgrade(Request $request)
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'price_id' => 'required|exists:subscription_plan_prices,id'
        ]);

        $company = auth()->user()->company;
        $plan = SubscriptionPlan::findOrFail($validated['plan_id']);
        $price = SubscriptionPlanPrice::findOrFail($validated['price_id']);

        DB::transaction(function () use ($company, $plan, $price) {
            // Mock payment creation
            $payment = SubscriptionPayment::create([
                'company_id' => $company->id,
                'subscription_id' => $company->subscription->id ?? 1,
                'amount' => $price->price,
                'payment_method' => 'card',
                'status' => 'completed',
                'transaction_id' => 'MOCK_' . strtoupper(uniqid()),
            ]);

            // Update Subscription
            if ($company->subscription) {
                $company->subscription->update([
                    'plan_id' => $plan->id,
                    'plan_price_id' => $price->id,
                    'billing_cycle' => $price->billing_cycle,
                    'amount_paid' => $price->price,
                    'status' => 'active',
                    'expires_at' => now()->addMonths($price->billing_cycle === 'yearly' ? 12 : 1)
                ]);
            }

            // TRIGGER AFFILIATE COMMISSION (THIS IS THE CORE GOAL)
            $commissionService = new AffiliateCommissionService();
            $commissionService->processCommission($payment, 'upgrade');
        });

        return back()->with('success', 'Subscription upgraded successfully! Commission processed if applicable.');
    }
}
