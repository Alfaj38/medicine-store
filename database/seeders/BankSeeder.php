<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonPath = __DIR__ . '/bank_data.json';
        
        if (!file_exists($jsonPath)) {
            $this->command->error("bank_data.json not found.");
            return;
        }

        $data = json_decode(file_get_contents($jsonPath), true);
        
        if (!$data) {
            $this->command->error("Invalid JSON data.");
            return;
        }

        $this->command->info("Importing banks and branches...");

        foreach ($data as $bankData) {
            $bankName = ucwords(strtolower(trim($bankData['name'])));
            $bank = \App\Models\Bank::firstOrCreate(['name' => $bankName]);

            $branchesToInsert = [];
            
            foreach ($bankData['districts'] as $district) {
                foreach ($district['branches'] as $branch) {
                    $branchesToInsert[] = [
                        'bank_id' => $bank->id,
                        'name' => ucwords(strtolower(trim($branch['branch_name']))),
                        'routing_number' => $branch['routing_number'],
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            if (!empty($branchesToInsert)) {
                // Bulk insert with upsert to prevent duplicates
                \App\Models\BankBranch::upsert(
                    $branchesToInsert,
                    ['routing_number'],
                    ['name', 'bank_id']
                );
            }
        }
        
        $this->command->info("Successfully imported all banks and branches.");
    }
}
