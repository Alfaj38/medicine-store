<?php

namespace App\Observers;

use App\Models\SubscriptionPayment;
use App\Services\CommissionService;

class SubscriptionPaymentObserver
{
    protected CommissionService $commissionService;

    public function __construct(CommissionService $commissionService)
    {
        $this->commissionService = $commissionService;
    }

    /**
     * Handle the SubscriptionPayment "created" event.
     */
    public function created(SubscriptionPayment $subscriptionPayment): void
    {
        // Ignore unpaid payments initially if they are created in 'pending' state
        // Assuming 'paid' is the successful status. If it's created as 'paid' right away:
        if ($subscriptionPayment->status === 'paid') {
            $eventType = $this->determineEventType($subscriptionPayment);
            $this->commissionService->handle($subscriptionPayment, $eventType);
        }
    }

    /**
     * Handle the SubscriptionPayment "updated" event.
     */
    public function updated(SubscriptionPayment $subscriptionPayment): void
    {
        if ($subscriptionPayment->wasChanged('status')) {
            if ($subscriptionPayment->status === 'paid') {
                $eventType = $this->determineEventType($subscriptionPayment);
                $this->commissionService->handle($subscriptionPayment, $eventType);
            } elseif ($subscriptionPayment->status === 'refunded') {
                if ($subscriptionPayment->commission) {
                    $this->commissionService->reverseCommission($subscriptionPayment->commission);
                }
            }
        }
    }

    /**
     * Determine if this payment is new, renewal, upgrade, or downgrade.
     */
    protected function determineEventType(SubscriptionPayment $payment): string
    {
        $previousPaymentsCount = SubscriptionPayment::where('company_id', $payment->company_id)
            ->where('id', '<', $payment->id)
            ->where('status', 'paid')
            ->count();

        if ($previousPaymentsCount === 0) {
            return 'new';
        }

        // Simplistic check for upgrade/downgrade based on amount compared to last payment
        $lastPayment = SubscriptionPayment::where('company_id', $payment->company_id)
            ->where('id', '<', $payment->id)
            ->where('status', 'paid')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastPayment) {
            if ($payment->amount > $lastPayment->amount) {
                return 'upgrade';
            } elseif ($payment->amount < $lastPayment->amount) {
                return 'downgrade';
            }
        }

        return 'renewal';
    }

    /**
     * Handle the SubscriptionPayment "deleted" event.
     */
    public function deleted(SubscriptionPayment $subscriptionPayment): void
    {
        // Reversals on delete can be tricky, but if needed:
        if ($subscriptionPayment->commission) {
            $this->commissionService->reverseCommission($subscriptionPayment->commission);
        }
    }

    /**
     * Handle the SubscriptionPayment "restored" event.
     */
    public function restored(SubscriptionPayment $subscriptionPayment): void
    {
        //
    }

    /**
     * Handle the SubscriptionPayment "force deleted" event.
     */
    public function forceDeleted(SubscriptionPayment $subscriptionPayment): void
    {
        //
    }
}
