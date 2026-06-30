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
        $company = auth()->user()->company;
        $subscription = $company->subscription;
        
        if ($subscription) {
            $subscription->load(['plan', 'planPrice']);
        }

        $monthlyBill = $subscription && $subscription->planPrice ? $subscription->planPrice->total_price : 0;
        
        $dueBill = 0;
        if ($subscription && $subscription->expires_at && $subscription->expires_at->isPast()) {
            $dueBill = $monthlyBill; 
        }

        $unpaidPayments = SubscriptionPayment::where('company_id', $company->id)
            ->where('status', 'pending')
            ->sum('amount');
            
        if ($unpaidPayments > 0) {
            $dueBill = $unpaidPayments;
        }

        $paymentHistory = SubscriptionPayment::where('company_id', $company->id)
            ->latest()
            ->take(10)
            ->get();
        
        return Inertia::render('Company/Subscription/Index', [
            'plans' => $plans,
            'currentSubscription' => $subscription,
            'monthlyBill' => $monthlyBill,
            'dueBill' => $dueBill,
            'paymentHistory' => $paymentHistory,
        ]);
    }

    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'price_id' => 'required|exists:subscription_plan_prices,id'
        ]);

        $plan = SubscriptionPlan::findOrFail($validated['plan_id']);
        $price = SubscriptionPlanPrice::findOrFail($validated['price_id']);
        $company = auth()->user()->company;
        
        $paymentRequests = SubscriptionPayment::where('company_id', $company->id)
            ->latest()
            ->take(10)
            ->get();

        return Inertia::render('Company/Subscription/Payment', [
            'plan' => $plan,
            'price' => $price,
            'paymentRequests' => $paymentRequests
        ]);
    }

    public function submitPayment(Request $request)
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'price_id' => 'required|exists:subscription_plan_prices,id',
            'gateway' => 'required|string',
            'sender_account_no' => 'required|string',
            'transaction_id' => 'required|string',
            'payment_date' => 'required|date',
            'payment_proof' => 'nullable|image|max:2048'
        ]);

        $company = auth()->user()->company;
        $price = SubscriptionPlanPrice::findOrFail($validated['price_id']);
        $plan = SubscriptionPlan::findOrFail($validated['plan_id']);
        
        $proofPath = null;
        if ($request->hasFile('payment_proof')) {
            $proofPath = $request->file('payment_proof')->store('payments', 'public');
        }

        DB::transaction(function () use ($company, $plan, $price, $validated, $proofPath) {
            SubscriptionPayment::create([
                'company_id' => $company->id,
                'subscription_id' => $company->subscription->id ?? 1,
                'amount' => $price->total_price,
                'net_amount' => $price->total_price,
                'gateway' => $validated['gateway'],
                'type' => 'manual',
                'status' => 'pending',
                'sender_account_no' => $validated['sender_account_no'],
                'transaction_id' => $validated['transaction_id'],
                'payment_proof_path' => $proofPath,
                'period_starts_at' => now(),
            ]);
        });

        return redirect()->route('company.subscription.index')->with('success', 'Payment details submitted successfully! Your subscription will be upgraded once verified by the authority.');
    }
}
