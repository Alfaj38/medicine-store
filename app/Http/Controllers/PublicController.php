<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\SubscriptionPlan;

class PublicController extends Controller
{

    public function home(Request $request)
    {
        $query = \App\Models\Company::where('is_active', true)
            ->where('registration_status', 'active');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        if ($request->filled('location') && $request->location !== 'all') {
            $location = $request->location;
            $query->where('address', 'like', "%{$location}%");
        }

        if ($request->boolean('top_rated')) {
            $query->orderByDesc('id'); // Mock sorting for top rated
        } else {
            $query->orderBy('id', 'desc'); // Default ordering
        }

        $pharmacies = $query->select('id', 'name', 'slug', 'address', 'logo')
            ->limit(12) // show up to 12
            ->get();

        $seoService = new \App\Services\SeoService();
        $appName = config('app.name', 'SaaSMedi');
        $seo = $seoService->generate([
            'site_name' => $appName,
            'title' => $appName . ' - Pharmacy POS & ERP Software',
            'description' => 'The ultimate cloud-based Pharmacy POS & ERP software in Bangladesh. Manage inventory, billing, and online orders effortlessly.',
            'url' => route('home'),
            'image' => asset('images/medicine_sample.png'),
            'schema' => [
                [
                    '@context' => 'https://schema.org',
                    '@type' => 'Organization',
                    'name' => $appName,
                    'url' => route('home'),
                    'logo' => asset('images/medicine_sample.png'),
                    'description' => 'Comprehensive Pharmacy Management Software and Medicine Delivery Platform.',
                    'contactPoint' => [
                        '@type' => 'ContactPoint',
                        'telephone' => '+880-1234-567890',
                        'contactType' => 'customer service'
                    ]
                ],
                [
                    '@context' => 'https://schema.org',
                    '@type' => 'SoftwareApplication',
                    'name' => $appName . ' POS & ERP',
                    'applicationCategory' => 'BusinessApplication',
                    'operatingSystem' => 'All',
                    'offers' => [
                        '@type' => 'Offer',
                        'price' => '0',
                        'priceCurrency' => 'BDT'
                    ]
                ]
            ]
        ]);

        return Inertia::render('Public/Home', [
            'pharmacies' => $pharmacies,
            'filters' => [
                'search' => $request->search,
                'location' => $request->location ?? 'all',
                'top_rated' => $request->boolean('top_rated')
            ],
            'seo' => $seo
        ]);
    }

    public function features()
    {
        $seoService = new \App\Services\SeoService();
        $seo = $seoService->generate([
            'site_name' => config('app.name', 'SaaSMedi'),
            'title' => 'Features - ' . config('app.name', 'SaaSMedi'),
            'description' => 'Explore the powerful features of our Pharmacy POS and ERP software including inventory tracking, billing, and reporting.',
            'url' => route('features'),
            'image' => asset('images/medicine_sample.png')
        ]);
        return Inertia::render('Public/Features', ['seo' => $seo]);
    }

    public function pricing()
    {
        // Fetch active packages, hiding the redundant standalone free trial
        $packages = \App\Models\Package::where('is_active', true)
            ->where('code', '!=', 'free_trial')
            ->with('features')
            ->orderBy('sort_order')
            ->get()
            ->map(function($plan) {
                return [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'slug' => $plan->code,
                    'description' => 'Perfect for ' . strtolower(str_replace('_', ' ', $plan->code)) . ' pharmacies',
                    'features' => $plan->features->filter(fn($f) => $f->is_enabled || $f->limit !== null)->map(function($f) {
                         $name = str_replace('_', ' ', $f->feature_code);
                         $limit = $f->limit ? $f->limit . ' ' : '';
                         return ucwords($limit . $name);
                    })->values()->toArray(),
                    'is_popular' => $plan->code === 'professional',
                    'monthly_price' => $plan->monthly_price,
                    'yearly_price' => $plan->yearly_price,
                ];
            });

        $seoService = new \App\Services\SeoService();
        $seo = $seoService->generate([
            'site_name' => config('app.name', 'SaaSMedi'),
            'title' => 'Pricing - ' . config('app.name', 'SaaSMedi'),
            'description' => 'Affordable and scalable pricing plans for pharmacies of all sizes. Start your free trial today.',
            'url' => route('pricing'),
            'image' => asset('images/medicine_sample.png')
        ]);

        return Inertia::render('Public/Pricing', [
            'plans' => $packages,
            'seo' => $seo
        ]);
    }

    public function contact()
    {
        $seoService = new \App\Services\SeoService();
        $seo = $seoService->generate([
            'site_name' => config('app.name', 'SaaSMedi'),
            'title' => 'Contact Us - ' . config('app.name', 'SaaSMedi'),
            'description' => 'Get in touch with our team for support, sales, or partnership inquiries.',
            'url' => route('contact'),
            'image' => asset('images/medicine_sample.png')
        ]);
        return Inertia::render('Public/Contact', ['seo' => $seo]);
    }

    public function partner()
    {
        $seoService = new \App\Services\SeoService();
        $seo = $seoService->generate([
            'site_name' => config('app.name', 'SaaSMedi'),
            'title' => 'Partner Program - ' . config('app.name', 'SaaSMedi'),
            'description' => 'Join our affiliate partner program and earn high commissions by referring pharmacies.',
            'url' => route('partner'),
            'image' => asset('images/medicine_sample.png')
        ]);
        return Inertia::render('Public/Partner', ['seo' => $seo]);
    }

    public function bookDemo()
    {
        return Inertia::render('Public/BookDemo');
    }

    public function demoCenter()
    {
        return Inertia::render('Public/DemoCenter');
    }

    public function success(Request $request)
    {
        return Inertia::render('Public/Success', [
            'type' => $request->query('type', 'demo'),
            'name' => $request->query('name', ''),
        ]);
    }

    public function trackOrderPage()
    {
        return Inertia::render('Public/TrackOrder');
    }

    public function trackOrder(Request $request)
    {
        $request->validate([
            'tracking_number' => 'required|string',
        ]);

        $order = \App\Models\OnlineOrder::with('company')
            ->where('tracking_number', $request->tracking_number)
            ->first();

        if (!$order) {
            return back()->withErrors(['tracking_number' => 'Order not found. Please check your tracking number.']);
        }

        return redirect()->route('storefront.orderSuccess', [
            'company' => $order->company->slug,
            'trackingNumber' => $order->tracking_number,
        ]);
    }
}

