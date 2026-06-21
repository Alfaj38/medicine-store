<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdditionalCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = DB::table('item_types')->get()->keyBy('name');

        $categories = [
            'Consumer Good' => [
                'Baby Care', 'Skin Care', 'Oral Care', 'Personal Hygiene', 
                'Nutrition & Supplements', 'First Aid', 'Family Planning', 'Toiletries'
            ],
            'Surgical' => [
                'Bandages & Dressings', 'Syringes & Needles', 'Surgical Gloves', 
                'Sutures', 'Catheters', 'Surgical Instruments', 'Orthopedic Supports'
            ],
            'Medical Device' => [
                'Blood Pressure Monitors', 'Thermometers', 'Glucometers', 
                'Pulse Oximeters', 'Nebulizers', 'Mobility Aids', 'Weighing Scales'
            ],
            'Asset' => [
                'Computers & IT', 'Furniture', 'Refrigerators', 
                'Air Conditioners', 'Store Fixtures', 'Medical Equipment'
            ],
            'Service' => [
                'Delivery Fee', 'Home Checkup', 'Blood Sugar Test', 
                'Blood Pressure Check', 'Consultation', 'Injection Administration'
            ]
        ];

        foreach ($categories as $typeName => $cats) {
            if (!isset($types[$typeName])) continue;

            $typeId = $types[$typeName]->id;

            foreach ($cats as $cat) {
                DB::table('item_categories')->updateOrInsert(
                    ['name' => $cat, 'item_type_id' => $typeId],
                    [
                        'slug' => Str::slug($cat) . '-' . uniqid(),
                        'is_active' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}
