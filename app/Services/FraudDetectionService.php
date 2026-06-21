<?php

namespace App\Services;

use App\Models\Reseller;
use App\Models\Company;

class FraudDetectionService
{
    public function isSelfReferral(Reseller $reseller, array $companyData): bool
    {
        // Simple check: matches email or phone
        if (isset($companyData['email']) && strtolower($companyData['email']) === strtolower($reseller->email)) {
            return true;
        }

        if (isset($companyData['phone']) && $this->normalizePhone($companyData['phone']) === $this->normalizePhone($reseller->phone)) {
            return true;
        }

        return false;
    }

    public function isAlreadyReferred(Company $company): bool
    {
        return !is_null($company->referral_code_id) || $company->referral()->exists();
    }

    protected function normalizePhone(?string $phone): string
    {
        if (!$phone) return '';
        // Remove everything except numbers
        $normalized = preg_replace('/[^0-9]/', '', $phone);
        // Take last 10 digits to ignore country code variations for matching
        return substr($normalized, -10);
    }
}
