<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\PharmaceuticalIndustry;
use App\Models\ItemCategory;
use App\Models\Item;

class SuggestCompaniesForCategory extends Command
{
    protected $signature = 'suggest:companies-for-category
                            {category : Name of the item category (e.g., "Baby Care/Toiletries")}
                            {--source=opencorporates : data source (opencorporates|csv)}
                            {--csv_path= : Path to CSV file when using csv source}
                            {--fallback : Generate placeholder names if source fails}';

    protected $description = 'AI‑inspired: find Bangladeshi companies likely to supply the given item category based on existing item keywords.';

    /**
     * Retrieve significant keywords from existing items belonging to the given category.
     * Very simple heuristic: extract unique words longer than 3 characters.
     */
    protected function extractKeywords(string $categoryName): array
    {
        $category = ItemCategory::where('name', $categoryName)->first();
        if (! $category) {
            $this->error("Category '{$categoryName}' not found.");
            return [];
        }
        $items = Item::where('category_id', $category->id)->pluck('name')->toArray();
        $words = [];
        foreach ($items as $name) {
            foreach (preg_split('/\s+/', strtolower($name)) as $w) {
                $clean = preg_replace('/[^a-z0-9]/', '', $w);
                if (strlen($clean) > 3) {
                    $words[$clean] = true;
                }
            }
        }
        return array_keys($words);
    }

    public function handle()
    {
        $categoryName = $this->argument('category');
        $keywords = $this->extractKeywords($categoryName);
        if (empty($keywords)) {
            $this->warn('No keywords extracted – falling back to generic fetch.');
        }

        $companies = $this->fetchCompanies();
        if (empty($companies) && $this->option('fallback')) {
            $this->info('Source returned no data – generating placeholders.');
            $companies = $this->generatePlaceholders($categoryName);
        }
        if (empty($companies)) {
            $this->warn('No company names retrieved. Exiting.');
            return 0;
        }

        $newCompanyIds = [];
        foreach ($companies as $name) {
            $clean = trim($name);
            $clean = substr($clean, 0, 255);
            if (empty($clean)) continue;
            $company = PharmaceuticalIndustry::firstOrCreate(['name' => $clean]);
            $newCompanyIds[] = $company->id;
        }
        $this->info('Inserted/verified '.count($newCompanyIds).' companies.');

        // Build category pivot rows based on keyword match
        $catPivotRows = [];
        foreach ($newCompanyIds as $cid) {
            $company = PharmaceuticalIndustry::find($cid);
            $lowerName = strtolower($company->name);
            $match = false;
            foreach ($keywords as $kw) {
                if (stripos($lowerName, $kw) !== false) {
                    $match = true;
                    break;
                }
            }
            if ($match) {
                $category = ItemCategory::where('name', $categoryName)->first();
                if ($category) {
                    $catPivotRows[] = [
                        'pharmaceutical_industry_id' => $cid,
                        'category_id'                => $category->id,
                    ];
                }
            }
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
        } else {
            $this->info('No company matched the extracted keywords for this category.');
        }

        $this->info('Suggestion process finished.');
        return 0;
    }

    protected function fetchCompanies(): array
    {
        $source = $this->option('source');
        if ($source === 'csv') {
            $path = $this->option('csv_path');
            if (! $path || ! file_exists(base_path($path))) {
                $this->error('CSV path not provided or file does not exist.');
                return [];
            }
            $lines = array_map('str_getcsv', file(base_path($path)));
            return array_column($lines, 0);
        }
        // OpenCorporates default
        $baseUrl = 'https://api.opencorporates.com/v0.4/companies/search';
        $params = [
            'jurisdiction_code' => 'bd',
            'q'                 => 'baby', // narrow to baby‑related firms
            'per_page'          => 100,
        ];
        $page = 1;
        $names = [];
        do {
            $params['page'] = $page;
            $response = Http::get($baseUrl, $params);
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

    protected function generatePlaceholders(string $categoryName): array
    {
        // Simple placeholder based on the category name
        return ["{$categoryName} Supplier Ltd."];
    }
}
?>
