<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ManagementUserSeeder extends Seeder
{
    public function run()
    {
        // Create a platform management user (admin panel)
        User::create([
            'company_id' => null,
            'branch_id' => null,
            'name' => 'Platform Admin',
            'email' => 'admin@medisaas.com',
            'password' => Hash::make('password'),
            'user_type' => 'management',
            'data_scope' => 'platform',
            'is_company_owner' => false,
            'is_active' => true,
        ]);
    }
}
