<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Services\SubscriptionService;
use App\Models\SubscriptionPayment;

class SubscriptionPaymentController extends Controller
{
    public function index(Request $request)
    {
        $payments = SubscriptionPayment::with(['company', 'package'])
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

            $subscriptionService = new SubscriptionService();
            $subscriptionService->processPayment($payment);

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
