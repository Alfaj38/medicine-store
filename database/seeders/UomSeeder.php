<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            'Carton', 'Box', 'Strip', 'Tablet', 'Capsule', 'Bottle',
            'Vial', 'Ampoule', 'Tube', 'Pack', 'Piece', 'Sachet',
            'ml', 'gm', 'kg'
        ];

        foreach ($units as $unit) {
            \App\Models\Uom::firstOrCreate([
                'name' => $unit
            ], [
                'short_name' => substr($unit, 0, 3)
            ]);
        }
    }
}
