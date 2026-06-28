<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Company;
use App\Models\Branch;
use App\Models\User;
use App\Models\Package;
use App\Models\Subscription;
use App\Models\ReferralCode;
use App\Models\Referral;

class CompanyRegistrationController extends Controller
{
    public function create()
    {
        $packages = Package::where('is_active', true)
            ->with('features')
            ->orderBy('sort_order')
            ->get();

        return Inertia::render('Auth/Register', [
            'packages' => $packages,
            'referral_code' => session('referral_code')
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // Step 1: Business Profile
            'company_name' => 'required|string|max:255',
            'company_type' => 'required|string',
            'branch_count' => 'required|string',
            // Step 2: Company Setup
            'company_country' => 'required|string',
            'company_currency' => 'required|string',
            'company_timezone' => 'required|string',
            'company_address' => 'required|string',
            'company_phone' => 'required|string|max:20',
            // Step 3: Admin Account
            'owner_name' => 'required|string|max:255',
            'owner_email' => 'required|email|unique:users,email',
            'owner_phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            // Step 4: Subscription
            'package_id' => 'required|exists:packages,id',
            'billing_cycle' => 'required|in:monthly,yearly',
            'referral_code' => 'nullable|string',
            'referral_source' => 'nullable|string',
            'coupon_code' => 'nullable|string',
        ]);

        $user = DB::transaction(function () use ($validated) {
            $package = Package::findOrFail($validated['package_id']);

            $referralCode = null;
            $coupon = null;
            $couponService = app(\App\Services\CouponService::class);
            $extraTrialDays = 0;

            if (!empty($validated['referral_code'])) {
                $codeToFind = $validated['referral_code'];
                
                $referralCode = ReferralCode::where(function($q) use ($codeToFind) {
                        $q->where('code', $codeToFind)->orWhere('label', $codeToFind);
                    })
                    ->where('status', 'active')
                    ->first();
                
                if ($referralCode && $referralCode->expires_at && $referralCode->expires_at->isPast()) {
                    $referralCode = null;
                }
            }

            if (!empty($validated['coupon_code'])) {
                $codeToFind = $validated['coupon_code'];
                $dummyCompany = new Company(['id' => 0]);
                $validation = $couponService->validateCoupon($codeToFind, $dummyCompany, $package->id, 0, 'new_subscription');
                if ($validation['valid']) {
                    $coupon = $validation['coupon'];
                    if ($coupon->discount_type === 'free_trial') {
                        $extraTrialDays = (int)$coupon->discount_value;
                    }
                }
            }

            // Generate a unique slug
            $originalSlug = \Illuminate\Support\Str::slug($validated['company_name']);
            $slug = $originalSlug;
            $counter = 1;
            while (Company::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            // We are creating a trial account.
            $company = Company::create([
                'name' => $validated['company_name'],
                'slug' => $slug,
                'phone' => $validated['company_phone'],
                'address' => $validated['company_address'],
                'referral_code_id' => $referralCode ? $referralCode->id : null,
                'registration_status' => 'active',
                'is_active' => true,
            ]);

            $branch = Branch::create([
                'company_id' => $company->id,
                'name' => 'Main Branch',
                'address' => $validated['company_address'],
                'phone' => $validated['company_phone'],
                'is_default' => true,
                'approval_status' => 'approved',
            ]);

            $user = User::create([
                'company_id' => $company->id,
                'branch_id' => $branch->id,
                'name' => $validated['owner_name'],
                'email' => $validated['owner_email'],
                'phone' => $validated['owner_phone'],
                'password' => Hash::make($validated['password']),
                'user_type' => 'company',
                'data_scope' => 'company',
                'is_company_owner' => true,
                'is_active' => true,
            ]);

            $totalTrialDays = $package->trial_days + $extraTrialDays;

            $subscription = Subscription::create([
                'company_id' => $company->id,
                'package_id' => $package->id,
                'billing_cycle' => $validated['billing_cycle'],
                'cycle_years' => 1,
                'amount_paid' => 0, // Free Trial
                'starts_at' => now(),
                'expires_at' => now()->addDays($totalTrialDays),
                'trial_ends_at' => now()->addDays($totalTrialDays),
                'grace_period_days' => 5,
                'status' => 'trial',
            ]);

            if ($referralCode) {
                Referral::create([
                    'reseller_id' => $referralCode->reseller_id,
                    'referral_code_id' => $referralCode->id,
                    'company_id' => $company->id,
                    'source' => session()->has('referral_code') && session('referral_code') === $referralCode->code ? 'link' : 'code',
                ]);
                
                $referralCode->increment('total_companies');
                if (session()->has('referral_code')) {
                    session()->forget('referral_code');
                }
            }

            if ($coupon) {
                $couponService->applyCoupon($coupon, $company, 0, null, $subscription->id);
            }

            return $user;
        });

        event(new \Illuminate\Auth\Events\Registered($user));
        \Illuminate\Support\Facades\Auth::login($user);

        return redirect()->route('verification.notice');
    }
}
