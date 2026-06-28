<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\PharmaceuticalIndustry;
use App\Models\ItemType;
use App\Models\ItemCategory;

class FetchBangladeshPharma extends Command
{
    protected $signature = 'fetch:bangladesh-pharma
                            {--type=* : Force association to specific ItemType IDs}
                            {--category=* : Force association to specific Category IDs}
                            {--source=opencorporates : data source (opencorporates|csv)}
                            {--csv_path= : Path to CSV file when using csv source}
                            {--fallback : Generate placeholder names if source fails}';

    protected $description = 'Fetch Bangladeshi pharmaceutical company names and map them to item types & categories without modifying existing data.';

    /** Default keyword maps – can be overridden via options */
    protected $typeKeywords = [
        'tablet'   => 'Tablet',
        'syrup'    => 'Syrup',
        'ointment' => 'Ointment',
        'capsule'  => 'Capsule',
        'injection'=> 'Injection',
    ];

    protected $categoryKeywords = [
        'antibiotic' => 'Antibiotic',
        'analgesic'  => 'Analgesic',
        'vitamin'    => 'Vitamin',
        'antiseptic' => 'Antiseptic',
        'diaper' => 'Baby Care/Toiletries',
        'baby diaper' => 'Baby Care/Toiletries',
    ];

    public function handle()
    {
        $companies = $this->fetchCompanies();
        if (empty($companies) && $this->option('fallback')) {
            $this->info('Source returned no data – generating placeholder names.');
            $companies = $this->generatePlaceholders();
        }

        if (empty($companies)) {
            $this->warn('No company names retrieved. Exiting.');
            return 0;
        }

        // ----- Insert companies (idempotent) -----
        $newCompanyIds = [];
        foreach ($companies as $name) {
            $clean = trim($name);
            $clean = substr($clean, 0, 255);
            if (empty($clean)) continue;
            $company = PharmaceuticalIndustry::firstOrCreate(['name' => $clean]);
            $newCompanyIds[] = $company->id;
        }
        $this->info('Inserted/verified '.count($newCompanyIds).' companies.');

        // ----- Build pivot data -----
        $itemPivotRows = [];
        $catPivotRows  = [];

        foreach ($newCompanyIds as $cid) {
            $company = PharmaceuticalIndustry::find($cid);
            $lowerName = strtolower($company->name);

            // ItemType association
            $typeId = null;
            if ($this->option('type')) {
                $typeId = $this->option('type')[0];
            } else {
                foreach ($this->typeKeywords as $kw => $typeName) {
                    if (stripos($lowerName, $kw) !== false) {
                        $typeId = ItemType::where('name', $typeName)->value('id');
                        break;
                    }
                }
            }
            if ($typeId) {
                $itemPivotRows[] = [
                    'pharmaceutical_industry_id' => $cid,
                    'item_type_id'               => $typeId,
                ];
            }

            // Category association
            $catId = null;
            if ($this->option('category')) {
                $catId = $this->option('category')[0];
            } else {
                foreach ($this->categoryKeywords as $kw => $catName) {
                    if (stripos($lowerName, $kw) !== false) {
                        $catId = ItemCategory::where('name', $catName)->value('id');
                        break;
                    }
                }
            }
            if ($catId) {
                $catPivotRows[] = [
                    'pharmaceutical_industry_id' => $cid,
                    'category_id'                => $catId,
                ];
            }
        }

        // ----- Insert pivots safely (avoid duplicates) -----
        if (!empty($itemPivotRows)) {
            foreach ($itemPivotRows as $row) {
                DB::table('company_item_type')->updateOrInsert(
                    [
                        'pharmaceutical_industry_id' => $row['pharmaceutical_industry_id'],
                        'item_type_id'               => $row['item_type_id'],
                    ],
                    $row
                );
            }
            $this->info('Item‑type associations created/updated: '.count($itemPivotRows));
        }

        if (!empty($catPivotRows)) {
            foreach ($catPivotRows as $row) {
                DB::table('company_category')->updateOrInsert(
                    [
                        'pharmaceutical_industry_id' => $row['pharmaceutical_industry_id'],
                        'category_id'                => $row['category_id'],
                    ],
                    $row
                );
            }
            $this->info('Category associations created/updated: '.count($catPivotRows));
        }

        $this->info('Fetch & mapping finished successfully.');
        return 0;
    }

    /** -------------------------------------------------------------- */
    protected function fetchCompanies(): array
    {
        $source = $this->option('source');
        if ($source === 'csv') {
            $path = $this->option('csv_path');
            if (!$path || !file_exists(base_path($path))) {
                $this->error('CSV path not provided or file does not exist.');
                return [];
            }
            $lines = array_map('str_getcsv', file(base_path($path)));
            return array_column($lines, 0);
        }

        // Default: OpenCorporates API (public, no auth needed for basic queries)
        $baseUrl = 'https://api.opencorporates.com/v0.4/companies/search';
        $params = [
            'jurisdiction_code' => 'bd', // Bangladesh
            'q'                 => 'pharma',
            'per_page'          => 100,
        ];
        $page = 1;
        $names = [];
        do {
            $params['page'] = $page;
            $response = Http::withoutVerifying()->get($baseUrl, $params);
            if (! $response->ok()) {
                $this->error('OpenCorporates request failed ('.$response->status().')');
                break;
            }
            $data = $response->json();
            $list = $data['results']['companies'] ?? [];
            foreach ($list as $c) {
                $name = $c['company']['name'] ?? null;
                if ($name) $names[] = $name;
            }
            $page++;
        } while (!empty($list) && $page <= ($data['results']['total_pages'] ?? $page));

        return $names;
    }

    /** -------------------------------------------------------------- */
    protected function generatePlaceholders(): array
    {
        $types = ItemType::pluck('name')->toArray();
        $placeholders = [];
        foreach ($types as $t) {
            $placeholders[] = $t . ' Pharmaceuticals Ltd.';
        }
        return $placeholders;
    }
}
?>
