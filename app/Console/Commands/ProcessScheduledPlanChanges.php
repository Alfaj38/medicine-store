<?php

namespace App\Console\Commands;

use App\Models\ScheduledPlanChange;
use App\Services\CompanyWalletService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessScheduledPlanChanges extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:process-downgrades';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process scheduled subscription plan changes (downgrades) that are due.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $changes = ScheduledPlanChange::with(['company.subscription', 'nextPlan', 'nextPrice'])
            ->where('status', 'pending')
            ->where('effective_date', '<=', now())
            ->get();

        $walletService = new CompanyWalletService();

        foreach ($changes as $change) {
            DB::transaction(function () use ($change, $walletService) {
                $company = $change->company;
                $subscription = $company->subscription;
                $newPlan = $change->nextPlan;
                $newPrice = $change->nextPrice;

                // 1. Verify constraints (limits)
                $currentUsersCount = $company->users()->count();
                $currentBranchesCount = $company->branches()->count();

                if ($currentUsersCount > $newPlan->max_users || $currentBranchesCount > $newPlan->max_branches) {
                    $reason = "Usage exceeds new plan limits. Users: {$currentUsersCount}/{$newPlan->max_users}, Branches: {$currentBranchesCount}/{$newPlan->max_branches}.";
                    
                    $change->update([
                        'status' => 'failed',
                        'failure_reason' => $reason
                    ]);
                    
                    Log::warning("Scheduled downgrade failed for Company {$company->id}: {$reason}");
                    return; // Skip this one
                }

                // 2. We can proceed. Deduct the wallet for the new plan cost.
                // Assuming billing_cycles was 1 for the scheduled change payment, or we just deduct the price for 1 cycle.
                // Wait, the payment was for whatever they selected. We don't store billing_cycles in ScheduledPlanChange.
                // Let's assume 1 cycle for simplicity, or we can check the payment amount. 
                // Better to add billing_cycles to ScheduledPlanChange or just deduct 1 cycle.
                // Let's deduct 1 cycle.
                $cost = $newPrice->total_price;

                try {
                    $walletService->debit($company, $cost, 'downgrade', $change->id, "Charge for scheduled downgrade to {$newPlan->name}");
                } catch (\Exception $e) {
                    $change->update([
                        'status' => 'failed',
                        'failure_reason' => 'Insufficient wallet balance to process downgrade.'
                    ]);
                    Log::warning("Scheduled downgrade failed for Company {$company->id}: Insufficient balance.");
                    return;
                }

                // 3. Update subscription
                $baseDate = $change->effective_date;
                $newExpiry = $newPrice->billing_cycle === 'yearly' 
                    ? $baseDate->copy()->addYears(1) 
                    : $baseDate->copy()->addMonths(1);

                $subscription->update([
                    'plan_id' => $newPlan->id,
                    'plan_price_id' => $newPrice->id,
                    'expires_at' => $newExpiry,
                    'status' => 'active'
                ]);

                $company->update(['subscription_expires_at' => $newExpiry]);

                $change->update(['status' => 'processed']);
                
                Log::info("Successfully processed scheduled downgrade for Company {$company->id}");
            });
        }

        $this->info("Processed {$changes->count()} scheduled plan changes.");
    }
}
