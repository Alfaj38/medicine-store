<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PharmaceuticalIndustry;

class DiaperCompaniesSeeder extends Seeder
{
    /**
     * Run the seeder.
     */
    public function run(): void
    {
        $companies = [
            'Pampers Bangladesh Ltd.',
            'Huggies Bangladesh Ltd.',
            'Molfix Bangladesh Ltd.',
            'DryBaby Bangladesh Ltd.',
            'BabyCare Diapers Ltd.',
            'Kawash Baby Diaper Co.',
            'Avonee Baby Diaper', // already inserted, will be ignored
        ];

        foreach ($companies as $name) {
            PharmaceuticalIndustry::firstOrCreate(['name' => $name]);
        }
    }
}
?>
