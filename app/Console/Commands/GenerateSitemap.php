<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\Item;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate the XML sitemaps for the storefront';

    public function handle()
    {
        $this->info('Starting sitemap generation...');

        $publicPath = public_path();
        
        // 1. Generate Store Sitemap
        $this->generateStoreSitemap($publicPath);
        
        // 2. Generate Medicine Sitemap
        $this->generateMedicineSitemap($publicPath);
        
        // 3. Generate Index Sitemap
        $this->generateIndexSitemap($publicPath);

        $this->info('Sitemaps generated successfully!');
    }

    protected function generateStoreSitemap($path)
    {
        $this->info('Generating store-sitemap.xml...');
        
        $companies = Company::where('is_active', true)->get();
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
        
        foreach ($companies as $company) {
            $url = route('storefront.show', ['company' => $company->slug]);
            $xml .= "    <url>" . PHP_EOL;
            $xml .= "        <loc>{$url}</loc>" . PHP_EOL;
            $xml .= "        <lastmod>" . $company->updated_at->toAtomString() . "</lastmod>" . PHP_EOL;
            $xml .= "        <changefreq>daily</changefreq>" . PHP_EOL;
            $xml .= "        <priority>0.9</priority>" . PHP_EOL;
            $xml .= "    </url>" . PHP_EOL;
        }
        
        $xml .= '</urlset>';
        
        File::put($path . '/store-sitemap.xml', $xml);
    }

    protected function generateMedicineSitemap($path)
    {
        $this->info('Generating medicine-sitemap.xml...');
        
        $companies = Company::where('is_active', true)->get();
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
        
        // Due to potentially millions of medicines, we chunk the data. 
        // We only generate links for items assigned to active companies.
        foreach ($companies as $company) {
            Item::withoutGlobalScope(\App\Models\Scopes\TenantScope::class)
                ->where('is_active', true)
                ->where(function($q) use ($company) {
                    $q->whereNull('company_id')
                      ->orWhere('company_id', $company->id);
                })
                ->whereNotNull('slug')
                ->chunk(1000, function ($items) use (&$xml, $company) {
                    foreach ($items as $item) {
                        $url = route('storefront.medicine.details', [
                            'company' => $company->slug, 
                            'medicine' => $item->slug
                        ]);
                        $xml .= "    <url>" . PHP_EOL;
                        $xml .= "        <loc>{$url}</loc>" . PHP_EOL;
                        $xml .= "        <lastmod>" . $item->updated_at->toAtomString() . "</lastmod>" . PHP_EOL;
                        $xml .= "        <changefreq>weekly</changefreq>" . PHP_EOL;
                        $xml .= "        <priority>0.8</priority>" . PHP_EOL;
                        $xml .= "    </url>" . PHP_EOL;
                    }
                });
        }
        
        $xml .= '</urlset>';
        
        File::put($path . '/medicine-sitemap.xml', $xml);
    }

    protected function generateIndexSitemap($path)
    {
        $this->info('Generating index sitemap.xml...');
        
        $baseUrl = url('/');
        $date = now()->toAtomString();
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
        
        $sitemaps = [
            'store-sitemap.xml',
            'medicine-sitemap.xml'
        ];
        
        foreach ($sitemaps as $sitemap) {
            $xml .= "    <sitemap>" . PHP_EOL;
            $xml .= "        <loc>{$baseUrl}/{$sitemap}</loc>" . PHP_EOL;
            $xml .= "        <lastmod>{$date}</lastmod>" . PHP_EOL;
            $xml .= "    </sitemap>" . PHP_EOL;
        }
        
        $xml .= '</sitemapindex>';
        
        File::put($path . '/sitemap.xml', $xml);
    }
}
