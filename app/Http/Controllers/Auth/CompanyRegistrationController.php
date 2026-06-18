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

class CompanyRegistrationController extends Controller
{
    public function create()
    {
        $plans = SubscriptionPlan::where('is_active', true)->with(['prices' => function($q) {
            $q->where('is_active', true);
        }])->get();

        return Inertia::render('Auth/Register', [
            'plans' => $plans
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // Company info
            'company_name' => 'required|string|max:255',
            'company_phone' => 'required|string|max:20',
            'company_address' => 'required|string',
            // Owner info
            'owner_name' => 'required|string|max:255',
            'owner_email' => 'required|email|unique:users,email',
            'owner_phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            // Subscription
            'plan_id' => 'required|exists:subscription_plans,id',
            'billing_cycle' => 'required|in:monthly,yearly',
        ]);

        DB::transaction(function () use ($validated) {
            $plan = SubscriptionPlan::findOrFail($validated['plan_id']);
            $price = SubscriptionPlanPrice::where('plan_id', $plan->id)
                ->where('billing_cycle', $validated['billing_cycle'])
                ->firstOrFail();

            $isFree = $price->total_price == 0;

            // 1. Create Company
            $company = Company::create([
                'name' => $validated['company_name'],
                'slug' => \Illuminate\Support\Str::slug($validated['company_name']),
                'phone' => $validated['company_phone'],
                'address' => $validated['company_address'],
                'registration_status' => $isFree ? 'pending' : 'active',
                'is_active' => true,
            ]);

            // 2. Create Default Branch
            $branch = Branch::create([
                'company_id' => $company->id,
                'name' => 'Main Branch',
                'address' => $validated['company_address'],
                'phone' => $validated['company_phone'],
                'is_default' => true,
                'approval_status' => $isFree ? 'pending' : 'approved',
            ]);

            // 3. Create Owner User
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

            // Assign Owner Role
            // Need to setup roles per company later, but we can bypass for now or rely on is_company_owner

            // 4. Create Subscription
            Subscription::create([
                'company_id' => $company->id,
                'plan_id' => $plan->id,
                'plan_price_id' => $price->id,
                'billing_cycle' => $validated['billing_cycle'],
                'cycle_years' => $price->cycle_years,
                'amount_paid' => $price->total_price,
                'starts_at' => now(),
                'expires_at' => $validated['billing_cycle'] === 'yearly' ? now()->addYear() : now()->addMonth(),
                'status' => 'active',
            ]);
        });

        return redirect()->route('login')->with('success', 'Registration successful. Please log in.');
    }
}
