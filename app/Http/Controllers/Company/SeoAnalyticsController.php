<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use App\Models\Company;

class SeoAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $company = $request->user()->company;

        // Fetch some basic analytics data (Mocked or DB driven)
        $totalMedicines = DB::table('items')->where('company_id', $company->id)->where('is_active', true)->count();
        $totalAreas = DB::table('areas')->where('company_id', $company->id)->count();

        return Inertia::render('Company/SeoAnalytics/Index', [
            'settings' => $company->settings ?? [],
            'stats' => [
                'total_indexed_medicines' => $totalMedicines,
                'local_landing_pages' => $totalAreas,
                'sitemap_url' => url('/sitemap.xml'),
                'store_sitemap_url' => url('/store-sitemap.xml'),
            ]
        ]);
    }

    public function update(Request $request)
    {
        $company = $request->user()->company;

        $validated = $request->validate([
            'google_analytics_id' => 'nullable|string|max:50',
            'facebook_pixel_id' => 'nullable|string|max:50',
            'google_search_console_html' => 'nullable|string|max:255',
            'meta_title_template' => 'nullable|string|max:255',
            'meta_description_template' => 'nullable|string|max:500',
        ]);

        $settings = $company->settings ?? [];
        $settings['seo'] = array_merge($settings['seo'] ?? [], $validated);

        $company->update(['settings' => $settings]);

        return redirect()->back()->with('success', 'SEO & Analytics settings updated successfully.');
    }
}
