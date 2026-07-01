<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Package;
use App\Models\SubscriptionPayment;
use App\Services\AffiliateCommissionService;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;

class SubscriptionController extends Controller
{
    public function index()
    {
        $packages = Package::with('features')
            ->where('is_active', true)
            ->where('code', '!=', 'free_trial')   // hide free trial from upgrade list
            ->where('monthly_price', '>', 0)        // hide any other zero-price packages
            ->orderBy('sort_order')
            ->get();
        $company = auth()->user()->company;
        $subscription = $company->subscription;
        
        if ($subscription) {
            $subscription->load(['package']);
        }

        $monthlyBill = 0;
        if ($subscription && $subscription->package) {
            $monthlyBill = $subscription->billing_cycle === 'yearly' 
                ? $subscription->package->yearly_price 
                : $subscription->package->monthly_price;
        }
        
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
            'plans' => $packages,
            'currentSubscription' => $subscription,
            'monthlyBill' => $monthlyBill,
            'dueBill' => $dueBill,
            'paymentHistory' => $paymentHistory,
        ]);
    }

    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'package_id' => 'required|exists:packages,id',
            'billing_cycle' => 'required|in:monthly,yearly',
        ]);

        $package = Package::findOrFail($validated['package_id']);
        $company = auth()->user()->company;
        
        $paymentRequests = SubscriptionPayment::where('company_id', $company->id)
            ->latest()
            ->take(10)
            ->get();

        return Inertia::render('Company/Subscription/Payment', [
            'package' => $package,
            'billing_cycle' => $validated['billing_cycle'],
            'paymentRequests' => $paymentRequests,
            'currentSubscription' => $company->subscription
        ]);
    }

    public function submitPayment(Request $request)
    {
        $validated = $request->validate([
            'package_id' => 'required|exists:packages,id',
            'billing_cycle' => 'required|in:monthly,yearly',
            'billing_cycles' => 'required|integer|min:1|max:60',
            'gateway' => 'required|string',
            'sender_account_no' => 'required|string',
            'transaction_id' => 'required|string',
            'payment_date' => 'required|date',
            'payment_proof' => 'nullable|image|max:2048'
        ]);

        $company = auth()->user()->company;
        $package = Package::findOrFail($validated['package_id']);
        $billingCycle = $validated['billing_cycle'];
        
        $proofPath = null;
        if ($request->hasFile('payment_proof')) {
            $proofPath = $request->file('payment_proof')->store('payments', 'public');
        }

        $oldPackage = $company->subscription ? $company->subscription->package : null;
        $oldCycle = $company->subscription ? $company->subscription->billing_cycle : null;

        $unusedCredit = 0;
        if ($oldPackage && $oldPackage->id !== $package->id) {
            $oldMonthly = $oldCycle === 'yearly' ? $oldPackage->yearly_price / 12 : $oldPackage->monthly_price;
            $newMonthly = $billingCycle === 'yearly' ? $package->yearly_price / 12 : $package->monthly_price;
            
            if ($newMonthly >= $oldMonthly && $company->subscription->expires_at && $company->subscription->expires_at->isFuture()) {
                $remainingDays = (int) ceil(now()->diffInDays($company->subscription->expires_at, false));
                if ($remainingDays > 0) {
                    $oldDaily = $oldCycle === 'yearly' ? $oldPackage->yearly_price / 365 : $oldPackage->monthly_price / 30;
                    $unusedCredit = $remainingDays * $oldDaily;
                }
            }
        }

        DB::transaction(function () use ($company, $package, $billingCycle, $validated, $proofPath, $unusedCredit) {
            $cycles = (int) $validated['billing_cycles'];
            $pricePerCycle = $billingCycle === 'yearly' ? $package->yearly_price : $package->monthly_price;
            $totalAmount = $pricePerCycle * $cycles;
            $payableAmount = max(0, $totalAmount - $unusedCredit);

            SubscriptionPayment::create([
                'company_id'       => $company->id,
                'subscription_id'  => $company->subscription->id ?? 1,
                'package_id'       => $package->id,
                'billing_cycle'    => $billingCycle,
                'billing_cycles'   => $cycles,
                'amount'           => $payableAmount,
                'net_amount'       => $payableAmount,
                'gateway'          => $validated['gateway'],
                'type'             => 'manual',
                'status'           => 'pending',
                'sender_account_no' => $validated['sender_account_no'],
                'transaction_id'   => $validated['transaction_id'],
                'payment_proof_path' => $proofPath,
                'period_starts_at' => now(),
            ]);
        });

        return redirect()->route('company.subscription.index')->with('success', 'Payment details submitted successfully! Your subscription will be upgraded once verified by the authority.');
    }

    public function downloadInvoice(SubscriptionPayment $payment)
    {
        $company = auth()->user()->company;

        if ($payment->company_id !== $company->id) {
            abort(403);
        }

        $pdf = Pdf::loadView('pdf.subscription_invoice', compact('payment', 'company'));
        
        return $pdf->download('invoice-' . str_pad($payment->id, 6, '0', STR_PAD_LEFT) . '.pdf');
    }

    public function calculateProration(Request $request)
    {
        $validated = $request->validate([
            'package_id' => 'required|exists:packages,id',
            'billing_cycle' => 'required|in:monthly,yearly',
            'billing_cycles' => 'required|integer|min:1'
        ]);

        $company = auth()->user()->company;
        $subscription = $company->subscription;
        
        if (!$subscription) {
            return response()->json(['error' => 'No active subscription found.'], 400);
        }

        $newPackage = Package::findOrFail($validated['package_id']);
        $newCycle = $validated['billing_cycle'];
        
        $oldPackage = $subscription->package;
        $oldCycle = $subscription->billing_cycle;

        if (!$oldPackage) {
            $oldPackage = $newPackage;
            $oldCycle = 'monthly';
        }

        $oldMonthly = $oldCycle === 'yearly' ? $oldPackage->yearly_price / 12 : $oldPackage->monthly_price;
        $newMonthly = $newCycle === 'yearly' ? $newPackage->yearly_price / 12 : $newPackage->monthly_price;
        $type = $newMonthly >= $oldMonthly ? 'upgrade' : 'downgrade';
        if ($oldPackage->id === $newPackage->id && $oldCycle === $newCycle) {
            $type = 'renew';
        }

        $cycles = $validated['billing_cycles'];
        $baseDate = ($subscription->expires_at && $subscription->expires_at->isFuture()) 
            ? $subscription->expires_at 
            : now();

        $remainingDays = 0;
        $unusedCredit = 0;
        $effectiveDate = null;
        
        $pricePerCycle = $newCycle === 'yearly' ? $newPackage->yearly_price : $newPackage->monthly_price;
        $payableAmount = $pricePerCycle * $cycles;
        $newCost = $payableAmount;

        if ($type === 'upgrade') {
            if ($subscription->expires_at && $subscription->expires_at->isFuture()) {
                $remainingDays = (int) ceil(now()->diffInDays($subscription->expires_at, false));
                if ($remainingDays > 0) {
                    $oldDaily = $oldCycle === 'yearly' ? $oldPackage->yearly_price / 365 : $oldPackage->monthly_price / 30;
                    $unusedCredit = $remainingDays * $oldDaily;
                }
            }
            $payableAmount = max(0, $newCost - $unusedCredit);
            $effectiveDate = now()->toDateTimeString();
        } elseif ($type === 'downgrade') {
            $effectiveDate = $baseDate->toDateTimeString();
        } else {
            $effectiveDate = $baseDate->toDateTimeString();
        }

        return response()->json([
            'type' => $type,
            'remaining_days' => $remainingDays,
            'unused_credit' => round($unusedCredit, 2),
            'new_cost' => round($newCost, 2),
            'payable_amount' => round($payableAmount, 2),
            'effective_date' => $effectiveDate
        ]);
    }
}
