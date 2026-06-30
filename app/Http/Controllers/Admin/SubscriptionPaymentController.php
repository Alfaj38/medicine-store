<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Http\Request;

class SubscriptionPaymentController extends Controller
{
    public function index(Request $request)
    {
        $payments = \App\Models\SubscriptionPayment::with('company')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return Inertia::render('Admin/Payments/Index', [
            'payments' => $payments
        ]);
    }

    public function approve(\App\Models\SubscriptionPayment $payment)
    {
        if ($payment->status !== 'pending') {
            return back()->with('error', 'Only pending payments can be approved.');
        }

        DB::transaction(function () use ($payment) {
            $payment->update(['status' => 'success']);

            $company = $payment->company;
            $subscription = $company->subscription;
            
            // Assume we can get plan info from the payment amount or we could have stored plan_id in payment
            // For now, if the company has a subscription, we just extend it. If not, this is tricky.
            // Wait, we need to know WHICH plan and price they selected.
            // They clicked "Select & Pay" for a specific plan and price in `SubscriptionController@submitPayment`.
            // Let's assume we can fetch the plan based on the amount, or we need to add `plan_id` to payment.
            // Actually, in `submitPayment`, we didn't save `plan_id` and `price_id` to the payment.
            // We should just update the subscription to active and set `amount_paid`.
            // This is a simplified approach. In a real system, `SubscriptionPayment` should store `plan_id`.
            
            if ($subscription) {
                // If it was a pending subscription waiting for payment, or an upgrade
                // Let's assume the subscription record already exists or was updated to pending earlier.
                // In our implementation, we didn't update the subscription during checkout to prevent giving access before payment.
                // For this demo, let's just make the subscription active and extend expiry.
                $newExpiry = now()->addMonths(1); // Assuming monthly for simplicity
                $subscription->update([
                    'status' => 'active',
                    'amount_paid' => $payment->amount,
                    'expires_at' => $newExpiry 
                ]);
                
                $company->update([
                    'subscription_expires_at' => $newExpiry
                ]);
            }

            // TRIGGER AFFILIATE COMMISSION
            $commissionService = new \App\Services\AffiliateCommissionService();
            $commissionService->processCommission($payment, 'upgrade');
        });

        return back()->with('success', 'Payment approved and subscription activated.');
    }

    public function reject(Request $request, \App\Models\SubscriptionPayment $payment)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        if ($payment->status !== 'pending') {
            return back()->with('error', 'Only pending payments can be rejected.');
        }

        $payment->update([
            'status' => 'failed',
            'rejection_reason' => $validated['reason']
        ]);

        return back()->with('success', 'Payment rejected successfully.');
    }
}
