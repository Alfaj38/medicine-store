<?php

namespace App\Services;

class SeoService
{
    protected array $defaultSettings = [];

    public function __construct()
    {
        // Setup default settings (can be overridden by multi-tenant config later)
        $this->defaultSettings = [
            'site_name' => 'SaaSMedi',
            'title' => 'SaaSMedi - Complete Pharmacy Management',
            'description' => 'Order medicines online from trusted local pharmacies through SaaSMedi.',
            'image' => url('/images/medicine_sample.jpg'),
            'url' => url()->current(),
            'type' => 'website',
            'twitter_card' => 'summary_large_image',
            'schema' => null,
            'keywords' => 'pharmacy, medicine, buy medicine online'
        ];
    }

    /**
     * Generate complete SEO metadata package for Inertia/Vue pages
     *
     * @param array $data Overrides for default SEO settings
     * @return array
     */
    public function generate(array $data = []): array
    {
        // Merge provided data with defaults
        $seo = array_merge($this->defaultSettings, $data);
        
        return [
            'title' => $this->generateTitle($seo),
            'description' => $seo['description'],
            'keywords' => $seo['keywords'],
            'canonical' => $seo['url'],
            'open_graph' => $this->generateOpenGraph($seo),
            'twitter' => $this->generateTwitter($seo),
            'schema' => $this->generateSchema($seo['schema']),
            'tracking' => $seo['tracking'] ?? []
        ];
    }

    protected function generateTitle(array $seo): string
    {
        if (str_contains($seo['title'], $seo['site_name'])) {
            return $seo['title'];
        }
        return $seo['title'] . ' | ' . $seo['site_name'];
    }

    protected function generateOpenGraph(array $seo): array
    {
        return [
            'og:title' => $seo['title'],
            'og:description' => $seo['description'],
            'og:image' => $seo['image'],
            'og:url' => $seo['url'],
            'og:type' => $seo['type'],
            'og:site_name' => $seo['site_name'],
        ];
    }

    protected function generateTwitter(array $seo): array
    {
        return [
            'twitter:card' => $seo['twitter_card'],
            'twitter:title' => $seo['title'],
            'twitter:description' => $seo['description'],
            'twitter:image' => $seo['image'],
        ];
    }

    protected function generateSchema($schemaArray): ?string
    {
        if (!$schemaArray) {
            return null;
        }

        // Return JSON encoded without escaping slashes for clean output
        return json_encode($schemaArray, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
