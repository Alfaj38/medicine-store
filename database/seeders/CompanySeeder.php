<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plan = \App\Models\SubscriptionPlan::firstOrCreate([
            'slug' => 'starter'
        ], [
            'name' => 'Starter',
            'description' => 'For single branch pharmacies',
            'max_branches' => 1,
            'max_users' => 5,
            'features' => ['Unlimited Sales', 'Inventory Tracking', 'Daily Reports', 'Basic Support'],
        ]);

        $priceMonthly = \App\Models\SubscriptionPlanPrice::firstOrCreate([
            'plan_id' => $plan->id,
            'billing_cycle' => 'monthly',
        ], [
            'price_per_month' => 999,
            'total_price' => 999,
        ]);

        $priceYearly = \App\Models\SubscriptionPlanPrice::firstOrCreate([
            'plan_id' => $plan->id,
            'billing_cycle' => 'yearly',
        ], [
            'price_per_month' => 900,
            'total_price' => 10800,
            'discount_percent' => 10,
        ]);

        $proPlan = \App\Models\SubscriptionPlan::firstOrCreate([
            'slug' => 'pro'
        ], [
            'name' => 'Pro',
            'description' => 'For multi-branch pharmacies',
            'max_branches' => 3,
            'max_users' => 15,
            'features' => ['Unlimited Sales', 'Inventory Tracking', 'Daily Reports', 'Priority Support', 'Multi-branch sync'],
        ]);

        \App\Models\SubscriptionPlanPrice::firstOrCreate([
            'plan_id' => $proPlan->id,
            'billing_cycle' => 'monthly',
        ], [
            'price_per_month' => 1999,
            'total_price' => 1999,
        ]);

        \App\Models\SubscriptionPlanPrice::firstOrCreate([
            'plan_id' => $proPlan->id,
            'billing_cycle' => 'yearly',
        ], [
            'price_per_month' => 1665,
            'total_price' => 19980,
            'discount_percent' => 15,
        ]);

        $enterprisePlan = \App\Models\SubscriptionPlan::firstOrCreate([
            'slug' => 'enterprise'
        ], [
            'name' => 'Enterprise',
            'description' => 'For large pharmacy chains',
            'max_branches' => 10,
            'max_users' => 50,
            'features' => ['Unlimited Sales', 'Inventory Tracking', 'Daily Reports', 'Dedicated Support', 'API Access', 'Custom Reports'],
        ]);

        \App\Models\SubscriptionPlanPrice::firstOrCreate([
            'plan_id' => $enterprisePlan->id,
            'billing_cycle' => 'monthly',
        ], [
            'price_per_month' => 4999,
            'total_price' => 4999,
        ]);

        \App\Models\SubscriptionPlanPrice::firstOrCreate([
            'plan_id' => $enterprisePlan->id,
            'billing_cycle' => 'yearly',
        ], [
            'price_per_month' => 4165,
            'total_price' => 49980,
            'discount_percent' => 15,
        ]);

        // Platform Admin
        \App\Models\User::firstOrCreate(
            ['email' => 'superadmin@saasmedi.com'],
            [
                'name' => 'Super Admin',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'user_type' => 'management',
                'data_scope' => 'platform',
                'is_active' => true,
            ]
        );

        // Sample Company
        $company = \App\Models\Company::firstOrCreate(
            ['email' => 'contact@demo.saasmedi.com'],
            [
                'name' => 'Demo Pharmacy',
                'slug' => 'demo',
                'registration_status' => 'active',
                'is_active' => true,
            ]
        );

        $branch = \App\Models\Branch::firstOrCreate(
            ['company_id' => $company->id, 'name' => 'Main Branch'],
            [
                'is_default' => true,
                'approval_status' => 'approved',
            ]
        );

        $user = \App\Models\User::firstOrCreate(
            ['email' => 'admin@saasmedi.com'],
            [
                'company_id' => $company->id,
                'branch_id' => $branch->id,
                'name' => 'Company Owner',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'user_type' => 'company',
                'data_scope' => 'company',
                'is_company_owner' => true,
                'is_active' => true,
            ]
        );

        if (!\App\Models\Subscription::where('company_id', $company->id)->exists()) {
            \App\Models\Subscription::create([
                'company_id' => $company->id,
                'plan_id' => $plan->id,
                'plan_price_id' => $priceMonthly->id,
                'billing_cycle' => 'monthly',
                'amount_paid' => 999,
                'starts_at' => now(),
                'expires_at' => now()->addMonth(),
                'status' => 'active',
            ]);
        }

        // Needs spatie role
        $role = \Spatie\Permission\Models\Role::firstOrCreate([
            'name' => 'Owner',
            'company_id' => $company->id,
            'guard_name' => 'web'
        ]);
        setPermissionsTeamId($company->id);
        $user->assignRole($role);

        // Create Default PagePermissions for Owner
        $pages = ['dashboard', 'pos', 'purchases', 'medicines', 'suppliers', 'customers', 'transfers', 'reports', 'expenses'];
        foreach ($pages as $page) {
            \App\Models\PagePermission::firstOrCreate([
                'company_id' => $company->id,
                'role_id' => $role->id,
                'page' => $page,
            ], [
                'can_view' => true,
                'can_create' => true,
                'can_edit' => true,
                'can_delete' => true,
            ]);
        }
    }
}
