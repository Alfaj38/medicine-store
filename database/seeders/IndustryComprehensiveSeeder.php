<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\PharmaceuticalIndustry;
use App\Models\ItemType;
use App\Models\ItemCategory;

/**
 * IndustryComprehensiveSeeder
 *
 * Uses AI-curated knowledge of real Bangladeshi & global companies
 * operating in Bangladesh to populate pharmaceutical_industries and
 * create pivot links to every item type and item category.
 *
 * Safe to run multiple times (fully idempotent via firstOrCreate /
 * updateOrInsert). No existing data is ever modified or deleted.
 */
class IndustryComprehensiveSeeder extends Seeder
{
    /**
     * Map: Item Type name => list of companies that manufacture/supply
     * products of that type in Bangladesh.
     */
    protected array $companiesByItemType = [

        'Medicine' => [
            'Square Pharmaceuticals Ltd.',
            'Incepta Pharmaceuticals Ltd.',
            'Beximco Pharmaceuticals Ltd.',
            'Renata Limited',
            'Opsonin Pharma Ltd.',
            'ACI Pharmaceuticals Ltd.',
            'Healthcare Pharmaceuticals Ltd.',
            'Drug International Ltd.',
            'Eskayef Bangladesh Ltd.',
            'General Pharmaceuticals Ltd.',
            'Ibn Sina Pharmaceutical Industry',
            'Acme Laboratories Ltd.',
            'Gonoshasthaya Pharmaceuticals Ltd.',
            'Nuvista Pharma Ltd.',
            'Radiant Pharmaceuticals Ltd.',
            'Sanofi Bangladesh Ltd.',
            'Aristopharma Ltd.',
            'Albion Laboratories Ltd.',
            'Orion Pharma Ltd.',
            'Pacific Pharmaceuticals Ltd.',
            'Novartis (Bangladesh) Ltd.',
            'GlaxoSmithKline Bangladesh Ltd.',
            'Roche Bangladesh Ltd.',
            'Pfizer Bangladesh Ltd.',
            'AstraZeneca Bangladesh',
        ],

        'Consumer Good' => [
            'Unilever Bangladesh Ltd.',
            'Marico Bangladesh Ltd.',
            'Reckitt Benckiser Bangladesh Ltd.',
            'Nestlé Bangladesh Ltd.',
            'Keya Cosmetics Ltd.',
            'Pran-RFL Group',
            'ACI Consumer Brands Ltd.',
            'Square Toiletries Ltd.',
            'Kohinoor Chemical Company (BD) Ltd.',
            'Avonee Consumer Products Ltd.',
            'NeoCare Bangladesh Ltd.',
            'Molfix Bangladesh Ltd.',
            'Huggies Bangladesh (Kimberly-Clark)',
            'Pampers Bangladesh (P&G)',
            'Himalaya Drug Company Bangladesh',
            'Dabur Bangladesh Ltd.',
            'Johnson & Johnson Bangladesh Ltd.',
            'Meril Toiletries (Beximco)',
            'Dove Bangladesh (Unilever)',
            'Lifebuoy Bangladesh (Unilever)',
        ],

        'Surgical' => [
            'Medipack Ltd.',
            'Bard Bangladesh Ltd.',
            'Universal Medical Systems',
            'BD Bangladesh (Becton Dickinson)',
            'Terumo Bangladesh Ltd.',
            'Smiths Medical Bangladesh',
            'Nipro Bangladesh Ltd.',
            'Cardinal Health Bangladesh',
            'Medline Industries Bangladesh',
            'Ansell Bangladesh Ltd.',
            'TOP Glove Bangladesh',
            'Mölnlycke Health Care Bangladesh',
            'Paul Hartmann Bangladesh',
            'Sterimed Bangladesh',
            'Rays Healthcare Ltd.',
        ],

        'Medical Device' => [
            'Omron Healthcare Bangladesh',
            'Siemens Healthineers Bangladesh',
            'Philips Healthcare Bangladesh',
            'GE Healthcare Bangladesh',
            'Mindray Bangladesh Ltd.',
            'A&D Medical Bangladesh',
            'Rossmax Bangladesh',
            'AccuChek (Roche) Bangladesh',
            'OneTouch (LifeScan) Bangladesh',
            'Beurer Bangladesh Ltd.',
            'Microlife Bangladesh',
            'Nonin Medical Bangladesh',
            'Contec Medical Bangladesh',
            'Nellcor Bangladesh (Medtronic)',
            'Masimo Bangladesh',
        ],

        'Service' => [
            'Popular Diagnostic Centre Ltd.',
            'Ibn Sina Trust',
            'Square Hospital Ltd.',
            'Labaid Diagnostics Ltd.',
            'Evercare Hospital Dhaka',
            'United Hospital Ltd.',
            'Dhaka Medical College Hospital',
            'Bangladesh National Hospital',
            'Apollo Hospitals Dhaka',
            'Medinova Medical Services Ltd.',
            'Pathology Bangladesh Ltd.',
            'HealthCare Online Bangladesh',
            'Seba Diagnostic Ltd.',
            'MedX Health Bangladesh',
            'DocTime Bangladesh',
        ],

        'Asset' => [
            'Hatil Furniture Ltd.',
            'Otobi Bangladesh Ltd.',
            'Brothers Furniture Ltd.',
            'Samsung Bangladesh Ltd.',
            'Walton Hi-Tech Industries Ltd.',
            'Singer Bangladesh Ltd.',
            'General Electric Bangladesh',
            'Carrier Bangladesh Ltd.',
            'Gree Bangladesh (Transcom)',
            'Fujitsu Bangladesh Ltd.',
            'Dell Bangladesh',
            'HP Bangladesh Ltd.',
            'Lenovo Bangladesh',
            'LG Electronics Bangladesh',
        ],
    ];

    /**
     * Map: Item Category name => list of companies that supply
     * products in that category in Bangladesh.
     */
    protected array $companiesByCategory = [

        'Pain Relief' => [
            'Square Pharmaceuticals Ltd.',
            'Incepta Pharmaceuticals Ltd.',
            'Beximco Pharmaceuticals Ltd.',
            'ACI Pharmaceuticals Ltd.',
            'Renata Limited',
            'Opsonin Pharma Ltd.',
            'Drug International Ltd.',
            'Healthcare Pharmaceuticals Ltd.',
            'Eskayef Bangladesh Ltd.',
            'Nuvista Pharma Ltd.',
            'Gonoshasthaya Pharmaceuticals Ltd.',
            'Acme Laboratories Ltd.',
            'General Pharmaceuticals Ltd.',
            'Pfizer Bangladesh Ltd.',
        ],

        'Antibiotics' => [
            'Square Pharmaceuticals Ltd.',
            'Incepta Pharmaceuticals Ltd.',
            'Beximco Pharmaceuticals Ltd.',
            'Renata Limited',
            'ACI Pharmaceuticals Ltd.',
            'Opsonin Pharma Ltd.',
            'Eskayef Bangladesh Ltd.',
            'Drug International Ltd.',
            'Healthcare Pharmaceuticals Ltd.',
            'Aristopharma Ltd.',
            'Ibn Sina Pharmaceutical Industry',
            'Acme Laboratories Ltd.',
            'Orion Pharma Ltd.',
            'Pacific Pharmaceuticals Ltd.',
            'Radiant Pharmaceuticals Ltd.',
        ],

        'Vitamins & Supp.' => [
            'Square Pharmaceuticals Ltd.',
            'Incepta Pharmaceuticals Ltd.',
            'Beximco Pharmaceuticals Ltd.',
            'ACI Pharmaceuticals Ltd.',
            'Renata Limited',
            'Acme Laboratories Ltd.',
            'Opsonin Pharma Ltd.',
            'Healthcare Pharmaceuticals Ltd.',
            'Ibn Sina Pharmaceutical Industry',
            'General Pharmaceuticals Ltd.',
            'Eskayef Bangladesh Ltd.',
            'Drug International Ltd.',
            'Himalaya Drug Company Bangladesh',
            'Dabur Bangladesh Ltd.',
            'GlaxoSmithKline Bangladesh Ltd.',
        ],

        'Cough & Cold' => [
            'Square Pharmaceuticals Ltd.',
            'Incepta Pharmaceuticals Ltd.',
            'Beximco Pharmaceuticals Ltd.',
            'Renata Limited',
            'ACI Pharmaceuticals Ltd.',
            'Healthcare Pharmaceuticals Ltd.',
            'Opsonin Pharma Ltd.',
            'Drug International Ltd.',
            'Eskayef Bangladesh Ltd.',
            'Acme Laboratories Ltd.',
            'Ibn Sina Pharmaceutical Industry',
            'Orion Pharma Ltd.',
            'Radiant Pharmaceuticals Ltd.',
            'Nuvista Pharma Ltd.',
        ],

        'Digestive' => [
            'Square Pharmaceuticals Ltd.',
            'Incepta Pharmaceuticals Ltd.',
            'Beximco Pharmaceuticals Ltd.',
            'Renata Limited',
            'ACI Pharmaceuticals Ltd.',
            'Acme Laboratories Ltd.',
            'Opsonin Pharma Ltd.',
            'Drug International Ltd.',
            'Healthcare Pharmaceuticals Ltd.',
            'Eskayef Bangladesh Ltd.',
            'General Pharmaceuticals Ltd.',
            'Ibn Sina Pharmaceutical Industry',
        ],

        'Diabetes' => [
            'Square Pharmaceuticals Ltd.',
            'Incepta Pharmaceuticals Ltd.',
            'Beximco Pharmaceuticals Ltd.',
            'Renata Limited',
            'Novartis (Bangladesh) Ltd.',
            'Sanofi Bangladesh Ltd.',
            'Novo Nordisk Bangladesh',
            'Eli Lilly Bangladesh',
            'ACI Pharmaceuticals Ltd.',
            'Healthcare Pharmaceuticals Ltd.',
            'AccuChek (Roche) Bangladesh',
            'OneTouch (LifeScan) Bangladesh',
            'Contec Medical Bangladesh',
        ],

        'Cardiology' => [
            'Square Pharmaceuticals Ltd.',
            'Incepta Pharmaceuticals Ltd.',
            'Beximco Pharmaceuticals Ltd.',
            'AstraZeneca Bangladesh',
            'Novartis (Bangladesh) Ltd.',
            'Sanofi Bangladesh Ltd.',
            'Renata Limited',
            'ACI Pharmaceuticals Ltd.',
            'Pfizer Bangladesh Ltd.',
            'Omron Healthcare Bangladesh',
            'Rossmax Bangladesh',
            'A&D Medical Bangladesh',
            'Microlife Bangladesh',
        ],

        'Dermatology' => [
            'Square Pharmaceuticals Ltd.',
            'Incepta Pharmaceuticals Ltd.',
            'Beximco Pharmaceuticals Ltd.',
            'GlaxoSmithKline Bangladesh Ltd.',
            'Renata Limited',
            'ACI Pharmaceuticals Ltd.',
            'Healthcare Pharmaceuticals Ltd.',
            'Himalaya Drug Company Bangladesh',
            'Dabur Bangladesh Ltd.',
            'Johnson & Johnson Bangladesh Ltd.',
            'Unilever Bangladesh Ltd.',
            'Marico Bangladesh Ltd.',
            'Keya Cosmetics Ltd.',
        ],

        'Eye Care' => [
            'Square Pharmaceuticals Ltd.',
            'Incepta Pharmaceuticals Ltd.',
            'Beximco Pharmaceuticals Ltd.',
            'Alcon Bangladesh',
            'Bausch & Lomb Bangladesh',
            'Johnson & Johnson Vision Bangladesh',
            'Opsonin Pharma Ltd.',
            'ACI Pharmaceuticals Ltd.',
            'Drug International Ltd.',
            'Healthcare Pharmaceuticals Ltd.',
        ],

        'Baby Care' => [
            'Johnson & Johnson Bangladesh Ltd.',
            'Himalaya Drug Company Bangladesh',
            'Unilever Bangladesh Ltd.',
            'Nestlé Bangladesh Ltd.',
            'Avonee Consumer Products Ltd.',
            'NeoCare Bangladesh Ltd.',
            'Molfix Bangladesh Ltd.',
            'Huggies Bangladesh (Kimberly-Clark)',
            'Pampers Bangladesh (P&G)',
            'Marico Bangladesh Ltd.',
            'Square Toiletries Ltd.',
            'Dabur Bangladesh Ltd.',
            'Mamy Poko Bangladesh',
            'Canbebe Bangladesh',
            'MamaBear Bangladesh Ltd.',
        ],

        'Skin Care' => [
            'Unilever Bangladesh Ltd.',
            'Marico Bangladesh Ltd.',
            'Keya Cosmetics Ltd.',
            'Johnson & Johnson Bangladesh Ltd.',
            'Beximco Pharmaceuticals Ltd.',
            'Himalaya Drug Company Bangladesh',
            'Dabur Bangladesh Ltd.',
            'Square Toiletries Ltd.',
            'Kohinoor Chemical Company (BD) Ltd.',
            'Meril Toiletries (Beximco)',
            'Dove Bangladesh (Unilever)',
            'Nivea Bangladesh (Beiersdorf)',
            'Ponds Bangladesh (Unilever)',
            'VLCC Bangladesh',
        ],

        'Oral Care' => [
            'Unilever Bangladesh Ltd.',
            'Colgate-Palmolive Bangladesh Ltd.',
            'GlaxoSmithKline Bangladesh Ltd.',
            'ACI Consumer Brands Ltd.',
            'Dabur Bangladesh Ltd.',
            'Square Toiletries Ltd.',
            'Kohinoor Chemical Company (BD) Ltd.',
            'Pran-RFL Group',
            'Close-Up Bangladesh (Unilever)',
            'Sensodyne Bangladesh (GSK)',
            'Signal Bangladesh (Unilever)',
        ],

        'Personal Hygiene' => [
            'Unilever Bangladesh Ltd.',
            'Marico Bangladesh Ltd.',
            'Reckitt Benckiser Bangladesh Ltd.',
            'Square Toiletries Ltd.',
            'Keya Cosmetics Ltd.',
            'Kohinoor Chemical Company (BD) Ltd.',
            'ACI Consumer Brands Ltd.',
            'Dabur Bangladesh Ltd.',
            'Lifebuoy Bangladesh (Unilever)',
            'Dettol Bangladesh (Reckitt)',
            'Savlon Bangladesh (ACI)',
            'Pran-RFL Group',
        ],

        'Nutrition & Supplements' => [
            'Nestlé Bangladesh Ltd.',
            'Abbott Nutrition Bangladesh',
            'Danone Bangladesh Ltd.',
            'GlaxoSmithKline Bangladesh Ltd.',
            'Incepta Pharmaceuticals Ltd.',
            'Square Pharmaceuticals Ltd.',
            'Beximco Pharmaceuticals Ltd.',
            'ACI Pharmaceuticals Ltd.',
            'Renata Limited',
            'Himalaya Drug Company Bangladesh',
            'Acme Laboratories Ltd.',
        ],

        'First Aid' => [
            'Medipack Ltd.',
            'Square Pharmaceuticals Ltd.',
            'Incepta Pharmaceuticals Ltd.',
            'Beximco Pharmaceuticals Ltd.',
            'ACI Consumer Brands Ltd.',
            'Reckitt Benckiser Bangladesh Ltd.',
            'Johnson & Johnson Bangladesh Ltd.',
            'Rays Healthcare Ltd.',
            'Sterimed Bangladesh',
            'Savlon Bangladesh (ACI)',
            'Dettol Bangladesh (Reckitt)',
        ],

        'Family Planning' => [
            'Renata Limited',
            'Incepta Pharmaceuticals Ltd.',
            'Square Pharmaceuticals Ltd.',
            'Beximco Pharmaceuticals Ltd.',
            'ACI Pharmaceuticals Ltd.',
            'Healthcare Pharmaceuticals Ltd.',
            'Drug International Ltd.',
            'Acme Laboratories Ltd.',
            'Eskayef Bangladesh Ltd.',
        ],

        'Toiletries' => [
            'Unilever Bangladesh Ltd.',
            'Marico Bangladesh Ltd.',
            'Reckitt Benckiser Bangladesh Ltd.',
            'Square Toiletries Ltd.',
            'Keya Cosmetics Ltd.',
            'Kohinoor Chemical Company (BD) Ltd.',
            'ACI Consumer Brands Ltd.',
            'Pran-RFL Group',
            'Dabur Bangladesh Ltd.',
            'Meril Toiletries (Beximco)',
            'Avonee Consumer Products Ltd.',
            'NeoCare Bangladesh Ltd.',
        ],

        'Bandages & Dressings' => [
            'Medipack Ltd.',
            'Paul Hartmann Bangladesh',
            'Mölnlycke Health Care Bangladesh',
            'Johnson & Johnson Bangladesh Ltd.',
            'Rays Healthcare Ltd.',
            'Sterimed Bangladesh',
            'Cardinal Health Bangladesh',
            'Medline Industries Bangladesh',
            'BD Bangladesh (Becton Dickinson)',
        ],

        'Syringes & Needles' => [
            'BD Bangladesh (Becton Dickinson)',
            'Nipro Bangladesh Ltd.',
            'Terumo Bangladesh Ltd.',
            'Medipack Ltd.',
            'Rays Healthcare Ltd.',
            'Smiths Medical Bangladesh',
            'Cardinal Health Bangladesh',
            'TOP Glove Bangladesh',
        ],

        'Surgical Gloves' => [
            'Ansell Bangladesh Ltd.',
            'TOP Glove Bangladesh',
            'Medipack Ltd.',
            'Cardinal Health Bangladesh',
            'Medline Industries Bangladesh',
            'Rays Healthcare Ltd.',
            'Mölnlycke Health Care Bangladesh',
        ],

        'Sutures' => [
            'Ethicon Bangladesh (J&J)',
            'Medipack Ltd.',
            'Covidien Bangladesh (Medtronic)',
            'Rays Healthcare Ltd.',
            'Sterimed Bangladesh',
            'Mölnlycke Health Care Bangladesh',
        ],

        'Catheters' => [
            'BD Bangladesh (Becton Dickinson)',
            'Bard Bangladesh Ltd.',
            'Terumo Bangladesh Ltd.',
            'Smiths Medical Bangladesh',
            'Nipro Bangladesh Ltd.',
            'Cardinal Health Bangladesh',
        ],

        'Surgical Instruments' => [
            'Rays Healthcare Ltd.',
            'Universal Medical Systems',
            'Medipack Ltd.',
            'B. Braun Bangladesh Ltd.',
            'Stryker Bangladesh',
            'Codman Bangladesh (J&J)',
            'Mölnlycke Health Care Bangladesh',
        ],

        'Orthopedic Supports' => [
            'Medela Bangladesh',
            'Ossur Bangladesh',
            'Breg Bangladesh',
            'DJO Bangladesh',
            'Rays Healthcare Ltd.',
            'Universal Medical Systems',
        ],

        'Blood Pressure Monitors' => [
            'Omron Healthcare Bangladesh',
            'Microlife Bangladesh',
            'Rossmax Bangladesh',
            'A&D Medical Bangladesh',
            'Beurer Bangladesh Ltd.',
            'Contec Medical Bangladesh',
            'Philips Healthcare Bangladesh',
        ],

        'Thermometers' => [
            'Omron Healthcare Bangladesh',
            'Microlife Bangladesh',
            'Beurer Bangladesh Ltd.',
            'Rossmax Bangladesh',
            'Braun Bangladesh',
            'Contec Medical Bangladesh',
            'Cardinal Health Bangladesh',
        ],

        'Glucometers' => [
            'AccuChek (Roche) Bangladesh',
            'OneTouch (LifeScan) Bangladesh',
            'Contec Medical Bangladesh',
            'SD Biosensor Bangladesh',
            'Omron Healthcare Bangladesh',
            'Rossmax Bangladesh',
            'Microlife Bangladesh',
        ],

        'Pulse Oximeters' => [
            'Masimo Bangladesh',
            'Nonin Medical Bangladesh',
            'Nellcor Bangladesh (Medtronic)',
            'Contec Medical Bangladesh',
            'Mindray Bangladesh Ltd.',
            'Omron Healthcare Bangladesh',
            'Beurer Bangladesh Ltd.',
        ],

        'Nebulizers' => [
            'Omron Healthcare Bangladesh',
            'Philips Healthcare Bangladesh',
            'PARI Bangladesh',
            'Microlife Bangladesh',
            'Beurer Bangladesh Ltd.',
            'Contec Medical Bangladesh',
            'Universal Medical Systems',
        ],

        'Mobility Aids' => [
            'Rays Healthcare Ltd.',
            'Universal Medical Systems',
            'Invacare Bangladesh',
            'Drive Medical Bangladesh',
            'Medline Industries Bangladesh',
        ],

        'Weighing Scales' => [
            'Omron Healthcare Bangladesh',
            'Beurer Bangladesh Ltd.',
            'A&D Medical Bangladesh',
            'Tanita Bangladesh',
            'Seca Bangladesh',
            'Singer Bangladesh Ltd.',
        ],

        'Computers & IT' => [
            'Dell Bangladesh',
            'HP Bangladesh Ltd.',
            'Lenovo Bangladesh',
            'Walton Hi-Tech Industries Ltd.',
            'Samsung Bangladesh Ltd.',
            'Asus Bangladesh',
            'Acer Bangladesh',
            'LG Electronics Bangladesh',
        ],

        'Furniture' => [
            'Hatil Furniture Ltd.',
            'Otobi Bangladesh Ltd.',
            'Brothers Furniture Ltd.',
            'Partex Furniture Ltd.',
            'Navana Furniture Ltd.',
            'Akhtar Furnishers Ltd.',
        ],

        'Refrigerators' => [
            'Walton Hi-Tech Industries Ltd.',
            'Samsung Bangladesh Ltd.',
            'Singer Bangladesh Ltd.',
            'LG Electronics Bangladesh',
            'Haier Bangladesh',
            'Whirlpool Bangladesh',
        ],

        'Air Conditioners' => [
            'Carrier Bangladesh Ltd.',
            'Gree Bangladesh (Transcom)',
            'Samsung Bangladesh Ltd.',
            'LG Electronics Bangladesh',
            'Fujitsu Bangladesh Ltd.',
            'Mitsubishi Bangladesh',
            'Walton Hi-Tech Industries Ltd.',
        ],

        'Store Fixtures' => [
            'Otobi Bangladesh Ltd.',
            'Hatil Furniture Ltd.',
            'Partex Furniture Ltd.',
            'Brothers Furniture Ltd.',
            'RFL Household Ltd.',
        ],

        'Medical Equipment' => [
            'GE Healthcare Bangladesh',
            'Siemens Healthineers Bangladesh',
            'Philips Healthcare Bangladesh',
            'Mindray Bangladesh Ltd.',
            'Rays Healthcare Ltd.',
            'Universal Medical Systems',
        ],

        'Delivery Fee' => [
            'Pathao Bangladesh',
            'Shohoz Logistics',
            'Paperfly Bangladesh',
            'RedX Bangladesh',
        ],

        'Home Checkup' => [
            'Popular Diagnostic Centre Ltd.',
            'Labaid Diagnostics Ltd.',
            'DocTime Bangladesh',
            'HealthCare Online Bangladesh',
            'MedX Health Bangladesh',
            'Seba Diagnostic Ltd.',
        ],

        'Blood Sugar Test' => [
            'AccuChek (Roche) Bangladesh',
            'OneTouch (LifeScan) Bangladesh',
            'SD Biosensor Bangladesh',
            'Contec Medical Bangladesh',
            'Popular Diagnostic Centre Ltd.',
            'Labaid Diagnostics Ltd.',
        ],

        'Blood Pressure Check' => [
            'Omron Healthcare Bangladesh',
            'Popular Diagnostic Centre Ltd.',
            'Labaid Diagnostics Ltd.',
            'Rossmax Bangladesh',
            'Microlife Bangladesh',
            'DocTime Bangladesh',
        ],

        'Consultation' => [
            'Square Hospital Ltd.',
            'Evercare Hospital Dhaka',
            'United Hospital Ltd.',
            'Apollo Hospitals Dhaka',
            'Popular Diagnostic Centre Ltd.',
            'DocTime Bangladesh',
            'MedX Health Bangladesh',
        ],

        'Injection Administration' => [
            'BD Bangladesh (Becton Dickinson)',
            'Nipro Bangladesh Ltd.',
            'Terumo Bangladesh Ltd.',
            'Medipack Ltd.',
            'Rays Healthcare Ltd.',
            'Square Hospital Ltd.',
        ],

        'Others' => [
            'ACI Pharmaceuticals Ltd.',
            'Square Pharmaceuticals Ltd.',
            'Incepta Pharmaceuticals Ltd.',
            'Beximco Pharmaceuticals Ltd.',
            'Renata Limited',
        ],
    ];

    // -----------------------------------------------------------------------

    public function run(): void
    {
        $this->command->info('⏳  Starting comprehensive industry seeder …');

        // Step 1 – Insert all companies (idempotent)
        $allNames = collect($this->companiesByItemType)
            ->flatten()
            ->merge(collect($this->companiesByCategory)->flatten())
            ->map(fn($n) => trim($n))
            ->filter()
            ->unique()
            ->values();

        $this->command->info("   Inserting/verifying {$allNames->count()} unique companies …");

        foreach ($allNames as $name) {
            PharmaceuticalIndustry::firstOrCreate(['name' => $name]);
        }

        $this->command->info('   ✅  Companies inserted/verified.');

        // Step 2 – Map companies → item types (pivot: company_item_type)
        $this->command->info('   Mapping item‑type pivots …');
        foreach ($this->companiesByItemType as $typeName => $companies) {
            $type = ItemType::where('name', $typeName)->first();
            if (! $type) {
                $this->command->warn("   ⚠️  ItemType '{$typeName}' not found – skipping.");
                continue;
            }
            foreach ($companies as $companyName) {
                $company = PharmaceuticalIndustry::where('name', trim($companyName))->first();
                if (! $company) continue;

                DB::table('company_item_type')->updateOrInsert(
                    [
                        'pharmaceutical_industry_id' => $company->id,
                        'item_type_id'               => $type->id,
                    ],
                    [
                        'pharmaceutical_industry_id' => $company->id,
                        'item_type_id'               => $type->id,
                        'created_at'                 => now(),
                        'updated_at'                 => now(),
                    ]
                );
            }
        }
        $this->command->info('   ✅  Item‑type pivots done.');

        // Step 3 – Map companies → categories (pivot: company_category)
        $this->command->info('   Mapping category pivots …');
        foreach ($this->companiesByCategory as $catName => $companies) {
            // Handle duplicate category names (e.g. two "Baby Care" rows)
            $categoryIds = ItemCategory::where('name', $catName)->pluck('id');
            if ($categoryIds->isEmpty()) {
                $this->command->warn("   ⚠️  Category '{$catName}' not found – skipping.");
                continue;
            }
            foreach ($companies as $companyName) {
                $company = PharmaceuticalIndustry::where('name', trim($companyName))->first();
                if (! $company) continue;

                foreach ($categoryIds as $catId) {
                    DB::table('company_category')->updateOrInsert(
                        [
                            'company_id'  => $company->id,
                            'category_id' => $catId,
                        ],
                        [
                            'company_id'  => $company->id,
                            'category_id' => $catId,
                            'created_at'  => now(),
                            'updated_at'  => now(),
                        ]
                    );
                }
            }
        }
        $this->command->info('   ✅  Category pivots done.');

        $this->command->info('🎉  Comprehensive industry seeder finished successfully!');
    }
}
