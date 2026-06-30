<?php

namespace App\Services;

use App\Models\Company;

class SubscriptionService
{
    /**
     * Check if a company's subscription is active (not expired).
     */
    public function isSubscriptionActive(Company $company): bool
    {
        $subscription = $company->subscription;
        if (!$subscription || !$subscription->expires_at) {
            return false;
        }
        return $subscription->expires_at->isFuture();
    }

    /**
     * Check if a company can add more users based on their plan.
     */
    public function canAddUser(Company $company): bool
    {
        $plan = $this->getCurrentPlan($company);
        if (!$plan) return false;

        $currentUsersCount = $company->users()->count();
        return $currentUsersCount < $plan->max_users;
    }

    /**
     * Check if a company can add more branches based on their plan.
     */
    public function canAddBranch(Company $company): bool
    {
        $plan = $this->getCurrentPlan($company);
        if (!$plan) return false;

        $currentBranchesCount = $company->branches()->count();
        return $currentBranchesCount < $plan->max_branches;
    }

    /**
     * Get the max allowed users for the company.
     */
    public function getMaxUsers(Company $company): int
    {
        $plan = $this->getCurrentPlan($company);
        return $plan ? $plan->max_users : 0;
    }

    /**
     * Get the max allowed branches for the company.
     */
    public function getMaxBranches(Company $company): int
    {
        $plan = $this->getCurrentPlan($company);
        return $plan ? $plan->max_branches : 0;
    }

    /**
     * Retrieve the active subscription plan for a company.
     */
    protected function getCurrentPlan(Company $company)
    {
        // Get the latest subscription (e.g. from relations or directly from the company object if loaded)
        $subscription = $company->subscription;
        if (!$subscription || !$subscription->plan) {
            return null;
        }
        return $subscription->plan;
    }
}
