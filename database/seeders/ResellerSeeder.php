<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ResellerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reseller = \App\Models\Reseller::create([
            'reseller_code' => 'RSL-00001',
            'name' => 'Demo Reseller',
            'email' => 'reseller@medisaas.com',
            'phone' => '01700000000',
            'business_name' => 'Tech Solutions Ltd.',
            'status' => 'approved',
            'commission_rate' => 10.00,
            'wallet_balance' => 0.00,
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
        ]);

        \App\Models\ReferralCode::create([
            'reseller_id' => $reseller->id,
            'code' => 'DEMO10',
            'label' => 'Standard Demo Promo',
            'status' => 'active',
        ]);
    }
}
