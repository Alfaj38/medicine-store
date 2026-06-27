<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Package;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Free Trial',
                'code' => 'free_trial',
                'monthly_price' => 0,
                'yearly_price' => 0,
                'trial_days' => 15,
                'sort_order' => 1,
                'features' => [
                    'branches' => 1,
                    'users' => 2,
                    'products' => 300,
                    'sales_per_month' => 100,
                    'pos' => true,
                    'purchase' => true,
                    'inventory' => true,
                    'customer' => true,
                    'dashboard' => true,
                    'public_profile' => true,
                ]
            ],
            [
                'name' => 'Starter',
                'code' => 'starter',
                'monthly_price' => 999,
                'yearly_price' => 9999,
                'trial_days' => 15,
                'sort_order' => 2,
                'features' => [
                    'branches' => 1,
                    'users' => 5,
                    'products' => 5000,
                    'sales_per_month' => null, // unlimited
                    'pos' => true,
                    'purchase' => true,
                    'inventory' => true,
                    'customer' => true,
                    'dashboard' => true,
                    'public_profile' => true,
                    'barcode' => true,
                    'supplier' => true,
                    'reports' => true,
                    'medicine_search' => true,
                    'prescription_upload' => true,
                ]
            ],
            [
                'name' => 'Professional',
                'code' => 'professional',
                'monthly_price' => 2499,
                'yearly_price' => 24999,
                'trial_days' => 15,
                'sort_order' => 3,
                'features' => [
                    'branches' => 5,
                    'users' => 20,
                    'products' => null,
                    'sales_per_month' => null,
                    'pos' => true,
                    'purchase' => true,
                    'inventory' => true,
                    'customer' => true,
                    'dashboard' => true,
                    'public_profile' => true,
                    'barcode' => true,
                    'supplier' => true,
                    'reports' => true,
                    'medicine_search' => true,
                    'prescription_upload' => true,
                    'multi_branch' => true,
                    'stock_transfer' => true,
                    'online_orders' => true,
                    'delivery' => true,
                    'expense' => true,
                    'income' => true,
                    'accounting' => true,
                    'hr' => true,
                    'attendance' => true,
                    'sms_gateway' => true,
                    'whatsapp_notification' => true,
                    'medicine_marketplace' => true,
                ]
            ],
            [
                'name' => 'Business',
                'code' => 'business',
                'monthly_price' => 4999,
                'yearly_price' => 49999,
                'trial_days' => 15,
                'sort_order' => 4,
                'features' => [
                    'branches' => 20,
                    'users' => 100,
                    'products' => null,
                    'sales_per_month' => null,
                    'pos' => true,
                    'purchase' => true,
                    'inventory' => true,
                    'customer' => true,
                    'dashboard' => true,
                    'public_profile' => true,
                    'barcode' => true,
                    'supplier' => true,
                    'reports' => true,
                    'medicine_search' => true,
                    'prescription_upload' => true,
                    'multi_branch' => true,
                    'stock_transfer' => true,
                    'online_orders' => true,
                    'delivery' => true,
                    'expense' => true,
                    'income' => true,
                    'accounting' => true,
                    'hr' => true,
                    'attendance' => true,
                    'sms_gateway' => true,
                    'whatsapp_notification' => true,
                    'medicine_marketplace' => true,
                    'warehouse' => true,
                    'purchase_approval' => true,
                    'batch_management' => true,
                    'expiry_management' => true,
                    'crm' => true,
                    'loyalty' => true,
                    'coupon' => true,
                    'api' => true,
                    'mobile_app' => true,
                    'advanced_reports' => true,
                ]
            ],
            [
                'name' => 'Enterprise',
                'code' => 'enterprise',
                'monthly_price' => 0, // Custom
                'yearly_price' => 0, // Custom
                'trial_days' => 15,
                'sort_order' => 5,
                'features' => [
                    'branches' => null,
                    'users' => null,
                    'products' => null,
                    'sales_per_month' => null,
                    'pos' => true,
                    'purchase' => true,
                    'inventory' => true,
                    'customer' => true,
                    'dashboard' => true,
                    'public_profile' => true,
                    'barcode' => true,
                    'supplier' => true,
                    'reports' => true,
                    'medicine_search' => true,
                    'prescription_upload' => true,
                    'multi_branch' => true,
                    'stock_transfer' => true,
                    'online_orders' => true,
                    'delivery' => true,
                    'expense' => true,
                    'income' => true,
                    'accounting' => true,
                    'hr' => true,
                    'attendance' => true,
                    'sms_gateway' => true,
                    'whatsapp_notification' => true,
                    'medicine_marketplace' => true,
                    'warehouse' => true,
                    'purchase_approval' => true,
                    'batch_management' => true,
                    'expiry_management' => true,
                    'crm' => true,
                    'loyalty' => true,
                    'coupon' => true,
                    'api' => true,
                    'mobile_app' => true,
                    'advanced_reports' => true,
                    'white_label' => true,
                    'custom_domain' => true,
                    'priority_support' => true,
                    'dedicated_server' => true,
                    'custom_development' => true,
                    'bi_dashboard' => true,
                    'ai_forecast' => true,
                    'custom_integration' => true,
                    'expiration_management' => true,
                ]
            ],
        ];

        foreach ($packages as $pkgData) {
            $features = $pkgData['features'];
            unset($pkgData['features']);

            $package = Package::updateOrCreate(
                ['code' => $pkgData['code']],
                $pkgData
            );

            foreach ($features as $featureCode => $value) {
                if (is_bool($value)) {
                    $package->features()->updateOrCreate(
                        ['feature_code' => $featureCode],
                        ['is_enabled' => $value, 'limit' => null]
                    );
                } else {
                    $package->features()->updateOrCreate(
                        ['feature_code' => $featureCode],
                        ['is_enabled' => true, 'limit' => $value]
                    );
                }
            }
        }
    }
}
