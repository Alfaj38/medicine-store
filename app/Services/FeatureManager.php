<?php

namespace App\Services;

use App\Models\Company;
use App\Models\PackageFeature;

class FeatureManager
{
    /**
     * Get the current active package for a given company (or currently authenticated company).
     */
    protected function getActivePackage($companyId = null)
    {
        if (!$companyId) {
            $company = auth()->user()?->company;
        } else {
            $company = Company::find($companyId);
        }

        if (!$company) {
            return null;
        }

        $subscription = $company->subscription;
        if (!$subscription || !$subscription->package_id) {
            return null;
        }

        return $subscription->package;
    }

    /**
     * Check if a feature is enabled.
     */
    public function isEnabled(string $featureCode, $companyId = null): bool
    {
        $package = $this->getActivePackage($companyId);
        
        if (!$package) {
            return false;
        }

        $feature = $package->features()->where('feature_code', $featureCode)->first();
        
        return $feature ? $feature->is_enabled : false;
    }

    /**
     * Check if a feature has reached its limit.
     * 
     * @param string $featureCode
     * @param int $currentCount The current number of items (e.g., number of branches currently created)
     * @param int|null $companyId
     * @return bool True if they can add more, false if they have hit the limit.
     */
    public function canAddMore(string $featureCode, int $currentCount, $companyId = null): bool
    {
        $package = $this->getActivePackage($companyId);
        
        if (!$package) {
            return false;
        }

        $feature = $package->features()->where('feature_code', $featureCode)->first();

        if (!$feature || !$feature->is_enabled) {
            return false; // Not enabled at all
        }

        if (is_null($feature->limit)) {
            return true; // Unlimited
        }

        return $currentCount < $feature->limit;
    }
}
