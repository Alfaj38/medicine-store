<?php

namespace App\Http\Controllers;

use App\Models\ReferralCode;
use Illuminate\Http\Request;

class ReferralLinkController extends Controller
{
    public function redirect($code)
    {
        $referralCode = ReferralCode::where(function($q) use ($code) {
                $q->where('code', $code)->orWhere('label', $code);
            })
            ->where('status', 'active')
            ->first();

        if ($referralCode && $referralCode->isValid()) {
            session(['referral_code' => $referralCode->code]);
        }

        return redirect()->route('register');
    }

    public function validateCode(Request $request)
    {
        $code = $request->query('code');
        $packageId = $request->query('package_id');
        $type = $request->query('type', 'referral'); // default to referral for backwards compatibility
        
        if (!$code) {
            return response()->json(['valid' => false, 'message' => 'No code provided.']);
        }

        if ($type === 'referral') {
            // Check for Referral Code
            $referralCode = ReferralCode::where(function($q) use ($code) {
                $q->where('code', $code)->orWhere('label', $code);
            })->first();

            if ($referralCode) {
                if ($referralCode->status !== 'active' || !$referralCode->isValid()) {
                    return response()->json(['valid' => false, 'message' => 'This referral code is inactive or invalid.']);
                }
                return response()->json([
                    'valid' => true,
                    'message' => 'Referral Code applied successfully!',
                    'type' => 'referral',
                    'reseller_name' => $referralCode->reseller->name ?? 'Partner'
                ]);
            }
            
            return response()->json(['valid' => false, 'message' => 'Invalid referral code.']);
        }

        if ($type === 'coupon') {
            // Check for a Coupon
            $couponService = app(\App\Services\CouponService::class);
            
            // Mock a company for validation if this is during registration
            $dummyCompany = new \App\Models\Company(['id' => 0]); 
            
            $validationResult = $couponService->validateCoupon(
                $code, 
                $dummyCompany, 
                $packageId ? (int)$packageId : null, 
                0, // Cart total (during registration it's free trial, so 0)
                'new_subscription'
            );

        if (!$validationResult['valid']) {
            return response()->json([
                'valid' => false,
                'message' => $validationResult['message']
            ]);
        }

        $coupon = $validationResult['coupon'];
        $discountMessage = '';
        if ($coupon->discount_type === 'percentage') {
            $discountMessage = "{$coupon->discount_value}% OFF on your first invoice!";
        } elseif ($coupon->discount_type === 'fixed') {
            $discountMessage = "৳{$coupon->discount_value} OFF on your first invoice!";
        } elseif ($coupon->discount_type === 'free_trial') {
            $discountMessage = "Enjoy an extended free trial!";
        }

        return response()->json([
            'valid' => true,
            'message' => 'Coupon applied: ' . $discountMessage,
            'type' => 'coupon',
            'coupon' => $coupon
        ]);
        }

        return response()->json(['valid' => false, 'message' => 'Invalid validation type.']);
    }
}
