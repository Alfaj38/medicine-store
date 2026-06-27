<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Company;
use Carbon\Carbon;
use Exception;

class CouponService
{
    /**
     * Validate if a coupon can be applied.
     * 
     * @param string $code
     * @param Company $company
     * @param int|null $packageId
     * @param float $cartTotal
     * @param string $subscriptionType (new_subscription, renewal, upgrade, downgrade)
     * @return array ['valid' => bool, 'message' => string, 'discount_amount' => float, 'coupon' => Coupon|null]
     */
    public function validateCoupon(
        string $code,
        Company $company,
        ?int $packageId = null,
        float $cartTotal = 0,
        string $subscriptionType = 'new_subscription'
    ): array {
        $coupon = Coupon::where('code', strtoupper($code))
            ->where('status', 'active')
            ->first();

        if (!$coupon) {
            return $this->error('Invalid or inactive coupon code.');
        }

        // 1. Time Check
        $now = Carbon::now();
        if ($coupon->start_date && $now->lt($coupon->start_date)) {
            return $this->error('This coupon is not active yet.');
        }
        if ($coupon->expire_date && $now->gt($coupon->expire_date)) {
            return $this->error('This coupon has expired.');
        }

        // 2. Global Usage Check
        if ($coupon->max_uses_total !== null && $coupon->total_uses >= $coupon->max_uses_total) {
            return $this->error('This coupon usage limit has been reached.');
        }

        // 3. Per-Company Usage Check
        if ($coupon->max_uses_per_company !== null) {
            $companyUsage = CouponUsage::where('coupon_id', $coupon->id)
                ->where('company_id', $company->id)
                ->count();
            if ($companyUsage >= $coupon->max_uses_per_company) {
                return $this->error('You have already used this coupon the maximum allowed times.');
            }
        }

        // 4. Minimum Purchase Check
        if ($coupon->minimum_purchase !== null && $cartTotal < $coupon->minimum_purchase) {
            return $this->error("Minimum purchase of ৳{$coupon->minimum_purchase} required.");
        }

        // 5. Package Eligibility Check
        if ($packageId) {
            // If the coupon has related packages, ensure $packageId is in them.
            // If it has NO related packages, we assume it's for 'All' packages.
            $validPackages = $coupon->packages()->pluck('packages.id')->toArray();
            if (!empty($validPackages) && !in_array($packageId, $validPackages)) {
                return $this->error('This coupon is not valid for the selected package.');
            }
        }

        // 6. Subscription Type Check
        if ($coupon->subscription_type !== 'all' && $coupon->subscription_type !== $subscriptionType) {
            $formattedType = str_replace('_', ' ', $coupon->subscription_type);
            return $this->error("This coupon is only valid for {$formattedType}s.");
        }
        
        // 7. Business Type Check
        // If the coupon has specific business types, check if the company's business type matches
        // (Assuming company type might be stored in extended profile or if known at checkout)
        // Since we don't have company_type directly on Company model yet, we can skip or implement if needed.

        // Calculate Discount
        $discountAmount = $this->calculateDiscount($coupon, $cartTotal);

        return [
            'valid' => true,
            'message' => 'Coupon applied successfully.',
            'discount_amount' => $discountAmount,
            'coupon' => $coupon,
        ];
    }

    /**
     * Calculate the discount amount.
     */
    protected function calculateDiscount(Coupon $coupon, float $cartTotal): float
    {
        $discountAmount = 0;

        if ($coupon->discount_type === 'percentage') {
            $discountAmount = ($cartTotal * $coupon->discount_value) / 100;
            if ($coupon->max_discount_amount !== null && $discountAmount > $coupon->max_discount_amount) {
                $discountAmount = $coupon->max_discount_amount;
            }
        } elseif ($coupon->discount_type === 'fixed') {
            $discountAmount = $coupon->discount_value;
        } elseif ($coupon->discount_type === 'custom_price') {
            // Set the price exactly to discount_value
            $discountAmount = $cartTotal - $coupon->discount_value;
        }

        // Ensure we don't discount more than the cart total
        if ($discountAmount > $cartTotal) {
            $discountAmount = $cartTotal;
        }

        return round($discountAmount, 2);
    }

    /**
     * Apply the coupon and record usage.
     */
    public function applyCoupon(Coupon $coupon, Company $company, float $discountAmount, ?int $invoiceId = null, ?int $subscriptionId = null): void
    {
        CouponUsage::create([
            'coupon_id' => $coupon->id,
            'company_id' => $company->id,
            'invoice_id' => $invoiceId,
            'subscription_id' => $subscriptionId,
            'discount_amount_applied' => $discountAmount,
            'used_at' => Carbon::now(),
        ]);

        $coupon->increment('total_uses');
    }

    protected function error(string $message): array
    {
        return [
            'valid' => false,
            'message' => $message,
            'discount_amount' => 0,
            'coupon' => null,
        ];
    }
}
