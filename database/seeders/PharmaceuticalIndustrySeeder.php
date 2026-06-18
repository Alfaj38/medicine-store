<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PharmaceuticalIndustrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = \Illuminate\Support\Facades\DB::table('medicines')
            ->whereNotNull('company_name')
            ->where('company_name', '!=', '')
            ->distinct()
            ->pluck('company_name');

        foreach ($companies as $company) {
            // Clean up excessive whitespace and newlines
            $cleanName = preg_replace('/\s+/', ' ', trim($company));
            
            // Truncate to 255 chars just in case
            $cleanName = substr($cleanName, 0, 255);

            if (!empty($cleanName)) {
                \App\Models\PharmaceuticalIndustry::firstOrCreate([
                    'name' => $cleanName
                ]);
            }
        }
    }
}
