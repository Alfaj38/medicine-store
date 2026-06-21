<?php

namespace App\Services;

use App\Models\SubscriptionPayment;
use App\Models\Company;
use App\Models\Commission;
use Illuminate\Support\Facades\DB;

class CommissionService
{
    protected WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    public function handle(SubscriptionPayment $payment, string $eventType): ?Commission
    {
        $company = Company::with('referral.reseller', 'referral.referralCode')->find($payment->company_id);
        
        if (!$company || !$company->referral || !$company->referral->reseller || !$company->referral->referralCode) {
            return null; // Not a referred company
        }

        $reseller = $company->referral->reseller;
        $referralCode = $company->referral->referralCode;

        if (!$reseller->isActive()) {
            return null; // Reseller is suspended/rejected, no commission
        }

        // Check for duplicate commission for this specific payment
        if (Commission::where('subscription_payment_id', $payment->id)->exists()) {
            return null;
        }

        return DB::transaction(function () use ($payment, $eventType, $company, $reseller, $referralCode) {
            $rate = $reseller->commission_rate;
            $amount = round($payment->amount * $rate / 100, 2);

            $commission = Commission::create([
                'reseller_id' => $reseller->id,
                'referral_id' => $company->referral->id,
                'company_id' => $company->id,
                'subscription_payment_id' => $payment->id,
                'event_type' => $eventType,
                'payment_amount' => $payment->amount,
                'commission_rate' => $rate,
                'commission_amount' => $amount,
                'status' => 'pending', // Will be marked as paid when reseller withdraws
            ]);

            // Update Wallet
            $description = ucfirst($eventType) . " subscription commission for {$company->name}";
            $this->walletService->credit($reseller, $amount, $description, 'commission', $commission->id);

            // Update Referral Code denormalized totals
            $referralCode->increment('total_revenue', $payment->amount);
            $referralCode->increment('total_commission', $amount);

            return $commission;
        });
    }

    public function reverseCommission(Commission $commission): void
    {
        if ($commission->status === 'reversed') {
            return;
        }

        DB::transaction(function () use ($commission) {
            $reseller = $commission->reseller;
            $amount = $commission->commission_amount;
            
            $commission->update([
                'status' => 'reversed',
                'reversed_at' => now(),
            ]);

            $description = "Commission reversal due to refund for {$commission->company->name}";
            $this->walletService->debit($reseller, $amount, $description, 'reversal', $commission->id);

            // Decrement Referral Code denormalized totals
            $referralCode = $commission->referral->referralCode;
            $referralCode->decrement('total_revenue', $commission->payment_amount);
            $referralCode->decrement('total_commission', $amount);
        });
    }
}
