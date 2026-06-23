<?php

namespace App\Services;

use App\Models\Referral;
use App\Models\Commission;
use App\Models\WalletTransaction;
use App\Models\SubscriptionPayment;
use Illuminate\Support\Facades\DB;
use Exception;

class AffiliateCommissionService
{
    /**
     * Process commission for a subscription payment
     * 
     * @param SubscriptionPayment $payment
     * @param string $eventType 'new' or 'renewal'
     * @return Commission|null
     */
    public function processCommission(SubscriptionPayment $payment, string $eventType = 'new'): ?Commission
    {
        return DB::transaction(function () use ($payment, $eventType) {
            // Check if the company paying is referred by a reseller
            $referral = Referral::where('company_id', $payment->company_id)->with('reseller')->first();

            if (!$referral || !$referral->reseller || $referral->reseller->status !== 'approved') {
                return null;
            }

            // Calculate commission
            $commissionRate = $referral->reseller->commission_rate;
            $commissionAmount = ($payment->amount * $commissionRate) / 100;

            if ($commissionAmount <= 0) {
                return null;
            }

            // Record Commission
            $commission = Commission::create([
                'reseller_id' => $referral->reseller_id,
                'referral_id' => $referral->id,
                'company_id' => $payment->company_id,
                'subscription_payment_id' => $payment->id,
                'event_type' => $eventType,
                'payment_amount' => $payment->amount,
                'commission_rate' => $commissionRate,
                'commission_amount' => $commissionAmount,
                'status' => 'approved', // Auto-approve for this implementation
            ]);

            // Update Referral total revenue stats
            $referral->referralCode()->increment('total_revenue', $payment->amount);
            $referral->referralCode()->increment('total_commission', $commissionAmount);

            // Add to Wallet Balance
            $balanceAfter = $referral->reseller->wallet_balance + $commissionAmount;
            
            WalletTransaction::create([
                'reseller_id' => $referral->reseller_id,
                'type' => 'credit',
                'amount' => $commissionAmount,
                'description' => 'Commission for ' . ucfirst($eventType) . ' Subscription',
                'reference_type' => 'commission',
                'reference_id' => $commission->id,
                'balance_after' => $balanceAfter,
            ]);

            $referral->reseller->update([
                'wallet_balance' => $balanceAfter
            ]);

            return $commission;
        });
    }
}
