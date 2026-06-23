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
        // Create Plans
        $plan = \App\Models\SubscriptionPlan::create([
            'name' => 'Starter',
            'slug' => 'starter',
            'description' => 'For single branch pharmacies',
            'max_branches' => 1,
            'max_users' => 5,
        ]);

        $priceMonthly = \App\Models\SubscriptionPlanPrice::create([
            'plan_id' => $plan->id,
            'billing_cycle' => 'monthly',
            'price_per_month' => 1000,
            'total_price' => 1000,
        ]);

        $priceYearly = \App\Models\SubscriptionPlanPrice::create([
            'plan_id' => $plan->id,
            'billing_cycle' => 'yearly',
            'price_per_month' => 900,
            'total_price' => 10800,
            'discount_percent' => 10,
        ]);

        // Platform Admin
        \App\Models\User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@saasmedi.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'user_type' => 'management',
            'data_scope' => 'platform',
            'is_active' => true,
        ]);

        // Sample Company
        $company = \App\Models\Company::create([
            'name' => 'Demo Pharmacy',
            'slug' => 'demo',
            'email' => 'contact@demo.saasmedi.com',
            'registration_status' => 'active',
            'is_active' => true,
        ]);

        $branch = \App\Models\Branch::create([
            'company_id' => $company->id,
            'name' => 'Main Branch',
            'is_default' => true,
            'approval_status' => 'approved',
        ]);

        $user = \App\Models\User::create([
            'company_id' => $company->id,
            'branch_id' => $branch->id,
            'name' => 'Company Owner',
            'email' => 'admin@saasmedi.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'user_type' => 'company',
            'data_scope' => 'company',
            'is_company_owner' => true,
            'is_active' => true,
        ]);

        \App\Models\Subscription::create([
            'company_id' => $company->id,
            'plan_id' => $plan->id,
            'plan_price_id' => $priceMonthly->id,
            'billing_cycle' => 'monthly',
            'amount_paid' => 1000,
            'starts_at' => now(),
            'expires_at' => now()->addMonth(),
            'status' => 'active',
        ]);

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
            \App\Models\PagePermission::create([
                'company_id' => $company->id,
                'role_id' => $role->id,
                'page' => $page,
                'can_view' => true,
                'can_create' => true,
                'can_edit' => true,
                'can_delete' => true,
            ]);
        }
    }
}
