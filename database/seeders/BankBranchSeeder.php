<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BankBranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branches = [
            'Sonali Bank' => [
                ['name' => 'Motijheel Local Office', 'routing_number' => '200273767'],
                ['name' => 'Gulshan Branch', 'routing_number' => '200270405'],
                ['name' => 'Dhanmondi Branch', 'routing_number' => '200270258'],
            ],
            'BRAC Bank' => [
                ['name' => 'Asad Gate Branch', 'routing_number' => '060260405'],
                ['name' => 'Banani Branch', 'routing_number' => '060260533'],
                ['name' => 'Mirpur Branch', 'routing_number' => '060262650'],
            ],
            'Dutch-Bangla Bank' => [
                ['name' => 'Motijheel Foreign Exchange', 'routing_number' => '090274092'],
                ['name' => 'Uttara Branch', 'routing_number' => '090276634'],
                ['name' => 'Kawran Bazar Branch', 'routing_number' => '090273873'],
            ]
        ];

        foreach ($branches as $bankName => $bankBranches) {
            $bank = \App\Models\Bank::where('name', $bankName)->first();
            if ($bank) {
                foreach ($bankBranches as $branch) {
                    \App\Models\BankBranch::firstOrCreate([
                        'routing_number' => $branch['routing_number']
                    ], [
                        'bank_id' => $bank->id,
                        'name' => $branch['name'],
                        'is_active' => true
                    ]);
                }
            }
        }
    }
}
