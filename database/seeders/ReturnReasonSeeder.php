<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReturnReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reasons = [
            ['code' => 'EXP', 'reason' => 'Expired'],
            ['code' => 'NEX', 'reason' => 'Near Expiry'],
            ['code' => 'DAM', 'reason' => 'Damaged'],
            ['code' => 'BRK', 'reason' => 'Broken'],
            ['code' => 'WRO', 'reason' => 'Wrong Item'],
            ['code' => 'OVR', 'reason' => 'Overstock'],
            ['code' => 'REC', 'reason' => 'Product Recall'],
            ['code' => 'DEF', 'reason' => 'Manufacturing Defect'],
        ];

        foreach ($reasons as $reason) {
            \App\Models\ReturnReason::updateOrCreate(['code' => $reason['code']], $reason);
        }
    }
}
