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
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionPlanPrice;
use App\Models\Subscription;
use App\Models\ReferralCode;
use App\Models\Referral;

class CompanyRegistrationController extends Controller
{
    public function create()
    {
        $plans = SubscriptionPlan::where('is_active', true)->with(['prices' => function($q) {
            $q->where('is_active', true);
        }])->get();

        return Inertia::render('Auth/Register', [
            'plans' => $plans,
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
            'plan_id' => 'required|exists:subscription_plans,id',
            'billing_cycle' => 'required|in:monthly,yearly',
            'referral_code' => 'nullable|string',
            'referral_source' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            $plan = SubscriptionPlan::findOrFail($validated['plan_id']);
            $price = SubscriptionPlanPrice::where('plan_id', $plan->id)
                ->where('billing_cycle', $validated['billing_cycle'])
                ->firstOrFail();

            $referralCode = null;
            if (!empty($validated['referral_code'])) {
                $referralCode = ReferralCode::where('code', $validated['referral_code'])
                    ->where('status', 'active')
                    ->first();
                
                if ($referralCode && $referralCode->expires_at && $referralCode->expires_at->isPast()) {
                    $referralCode = null;
                }
            }

            // We are creating a trial account.
            $company = Company::create([
                'name' => $validated['company_name'],
                'slug' => \Illuminate\Support\Str::slug($validated['company_name']),
                'phone' => $validated['company_phone'],
                'address' => $validated['company_address'],
                'referral_code_id' => $referralCode ? $referralCode->id : null,
                // Store additional setup context temporarily in address or add columns later if needed
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

            Subscription::create([
                'company_id' => $company->id,
                'plan_id' => $plan->id,
                'plan_price_id' => $price->id,
                'billing_cycle' => $validated['billing_cycle'],
                'cycle_years' => $price->cycle_years,
                'amount_paid' => 0, // Free Trial
                'starts_at' => now(),
                'expires_at' => now()->addDays(14), // 14-Day Free Trial
                'status' => 'trial',
            ]);

            if ($referralCode) {
                Referral::create([
                    'reseller_id' => $referralCode->reseller_id,
                    'referral_code_id' => $referralCode->id,
                    'company_id' => $company->id,
                    // If it was auto-filled from session vs manual
                    'source' => session()->has('referral_code') && session('referral_code') === $referralCode->code ? 'link' : 'code',
                ]);
                
                $referralCode->increment('total_companies');
                // Clear session if used
                if (session()->has('referral_code')) {
                    session()->forget('referral_code');
                }
            }
        });

        // Redirect to success page for trial
        return redirect()->route('success', ['type' => 'trial', 'name' => $validated['owner_name']]);
    }
}
