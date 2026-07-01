<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Package;
use App\Models\SubscriptionPayment;
use App\Services\CompanyWalletService;
use Illuminate\Support\Facades\Log;

class SubscriptionService
{
    /**
     * Get the current state of the subscription lifecycle.
     */
    public function getSubscriptionState(Company $company): string
    {
        $subscription = $company->subscription;
        if (!$subscription || !$subscription->expires_at) {
            return 'suspended';
        }

        $hasPendingPayment = SubscriptionPayment::where('company_id', $company->id)
            ->where('status', 'pending')
            ->exists();

        if ($hasPendingPayment) {
            return 'payment_pending';
        }

        $expiresAt = $subscription->expires_at;
        $now = now();

        if ($expiresAt->isFuture()) {
            $daysToExpiry = $now->diffInDays($expiresAt, false);
            if ($daysToExpiry <= 15) {
                return 'renewal_window';
            }
            return 'active';
        }

        $daysExpired = $now->diffInDays($expiresAt, true);

        if ($daysExpired <= 7) {
            return 'grace_period';
        }

        if ($daysExpired <= 37) {
            return 'restricted';
        }

        return 'suspended';
    }

    public function getDaysToExpiry(Company $company): int
    {
        $subscription = $company->subscription;
        if (!$subscription || !$subscription->expires_at) return 0;

        $diff = now()->diffInDays($subscription->expires_at, false);
        return $diff > 0 ? (int) $diff : 0;
    }

    public function getGraceDaysRemaining(Company $company): int
    {
        $subscription = $company->subscription;
        if (!$subscription || !$subscription->expires_at) return 0;

        if ($subscription->expires_at->isFuture()) return 7;

        $daysExpired = (int) now()->diffInDays($subscription->expires_at, true);
        $remaining = 7 - $daysExpired;

        return $remaining > 0 ? $remaining : 0;
    }

    /**
     * Check if a company's subscription allows mutating actions.
     */
    public function isSubscriptionActive(Company $company): bool
    {
        $state = $this->getSubscriptionState($company);
        return in_array($state, ['active', 'renewal_window', 'grace_period', 'payment_pending']);
    }

    /**
     * Check if a company can add more users based on their package.
     */
    public function canAddUser(Company $company): bool
    {
        $package = $this->getCurrentPackage($company);
        if (!$package) return false;

        $currentUsersCount = $company->users()->count();
        return $currentUsersCount < $package->max_users;
    }

    /**
     * Check if a company can add more branches based on their package.
     */
    public function canAddBranch(Company $company): bool
    {
        $package = $this->getCurrentPackage($company);
        if (!$package) return false;

        $currentBranchesCount = $company->branches()->count();
        return $currentBranchesCount < $package->max_branches;
    }

    /**
     * Get the max allowed users for the company.
     */
    public function getMaxUsers(Company $company): int
    {
        $package = $this->getCurrentPackage($company);
        return $package ? $package->max_users : 0;
    }

    /**
     * Get the max allowed branches for the company.
     */
    public function getMaxBranches(Company $company): int
    {
        $package = $this->getCurrentPackage($company);
        return $package ? $package->max_branches : 0;
    }

    /**
     * Retrieve the active package for a company.
     */
    protected function getCurrentPackage(Company $company): ?Package
    {
        $subscription = $company->subscription;
        if (!$subscription || !$subscription->package) {
            return null;
        }
        return $subscription->package;
    }

    /**
     * Process an approved subscription payment.
     */
    public function processPayment(SubscriptionPayment $payment)
    {
        $company = $payment->company;
        $subscription = $company->subscription;
        $newPackage = $payment->package;
        $newCycle = $payment->billing_cycle ?? ($subscription->billing_cycle ?? 'monthly');

        if (!$subscription || !$newPackage) {
            Log::warning("processPayment: Missing subscription or package for payment {$payment->id}");
            return;
        }

        $oldPackage = $subscription->package;
        $oldCycle = $subscription->billing_cycle ?? 'monthly';
        $walletService = new CompanyWalletService();

        // 1. Determine Type
        $type = 'renew';
        if ($oldPackage && $oldPackage->id !== $newPackage->id) {
            $oldMonthly = $this->getMonthlyPrice($oldPackage, $oldCycle);
            $newMonthly = $this->getMonthlyPrice($newPackage, $newCycle);
            $type = $newMonthly >= $oldMonthly ? 'upgrade' : 'downgrade';
        }

        // Base Date calculation
        $baseDate = ($subscription->expires_at && $subscription->expires_at->isFuture())
            ? $subscription->expires_at
            : now();

        $cycles = $payment->billing_cycles ?? 1;

        if ($type === 'upgrade') {
            $remainingDays = 0;
            $unusedCredit = 0;

            if ($subscription->expires_at && $subscription->expires_at->isFuture()) {
                $remainingDays = (int) ceil(now()->diffInDays($subscription->expires_at, false));
                if ($remainingDays > 0) {
                    $oldDaily = $this->getDailyPrice($oldPackage, $oldCycle);
                    $unusedCredit = $remainingDays * $oldDaily;

                    if ($unusedCredit > 0) {
                        $walletService->credit($company, $unusedCredit, 'upgrade', $payment->id, "Unused credit from remaining {$remainingDays} days of {$oldPackage->name}.");
                    }
                }
            }

            $walletService->credit($company, $payment->amount, 'upgrade', $payment->id, "Payment for {$cycles} cycles of {$newPackage->name}.");

            $pricePerCycle = $newCycle === 'yearly' ? $newPackage->yearly_price : $newPackage->monthly_price;
            $totalNewCost = $pricePerCycle * $cycles;
            try {
                $walletService->debit($company, $totalNewCost, 'upgrade', $payment->id, "Charge for {$newPackage->name}");
            } catch (\Exception $e) {
                Log::warning("Company {$company->id} wallet deficit during upgrade.");
            }

            $baseDate = now();
            $newExpiry = $newCycle === 'yearly'
                ? $baseDate->copy()->addYears($cycles)
                : $baseDate->copy()->addMonths($cycles);

            $subscription->update([
                'status'       => 'active',
                'package_id'   => $newPackage->id,
                'billing_cycle' => $newCycle,
                'amount_paid'  => $totalNewCost,
                'expires_at'   => $newExpiry,
            ]);

            $company->update(['subscription_expires_at' => $newExpiry]);

            $company->subscriptionHistories()->create([
                'old_plan_id'  => $oldPackage ? $oldPackage->id : null,
                'new_plan_id'  => $newPackage->id,
                'type'         => 'upgrade',
                'amount_paid'  => $payment->amount,
                'credit_used'  => $unusedCredit,
                'wallet_used'  => 0,
            ]);

        } elseif ($type === 'downgrade') {
            $company->scheduledPlanChanges()->create([
                'current_plan_id' => $oldPackage->id,
                'next_plan_id'    => $newPackage->id,
                'next_price_id'   => null,
                'effective_date'  => $baseDate,
                'status'          => 'pending',
            ]);

            $walletService->credit($company, $payment->amount, 'downgrade', $payment->id, "Prepayment for scheduled downgrade to {$newPackage->name}");

            $company->subscriptionHistories()->create([
                'old_plan_id'  => $oldPackage->id,
                'new_plan_id'  => $newPackage->id,
                'type'         => 'downgrade',
                'amount_paid'  => $payment->amount,
                'credit_used'  => 0,
                'wallet_used'  => 0,
            ]);

        } else {
            // Normal Renewal
            $newExpiry = $newCycle === 'yearly'
                ? $baseDate->copy()->addYears($cycles)
                : $baseDate->copy()->addMonths($cycles);

            $subscription->update([
                'status'        => 'active',
                'package_id'    => $newPackage->id,
                'billing_cycle' => $newCycle,
                'amount_paid'   => $payment->amount,
                'expires_at'    => $newExpiry,
            ]);

            $company->update(['subscription_expires_at' => $newExpiry]);

            $company->subscriptionHistories()->create([
                'old_plan_id'  => $oldPackage ? $oldPackage->id : null,
                'new_plan_id'  => $newPackage->id,
                'type'         => 'renew',
                'amount_paid'  => $payment->amount,
                'credit_used'  => 0,
                'wallet_used'  => 0,
            ]);
        }
    }

    protected function getMonthlyPrice(Package $package, string $cycle): float
    {
        return $cycle === 'yearly' ? $package->yearly_price / 12 : $package->monthly_price;
    }

    protected function getDailyPrice(Package $package, string $cycle): float
    {
        return $cycle === 'yearly' ? $package->yearly_price / 365 : $package->monthly_price / 30;
    }
}
