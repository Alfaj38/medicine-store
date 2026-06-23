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

        return Inertia::render('Public/Home', [
            'pharmacies' => $pharmacies,
            'filters' => [
                'search' => $request->search,
                'location' => $request->location ?? 'all',
                'top_rated' => $request->boolean('top_rated')
            ]
        ]);
    }

    public function features()
    {
        return Inertia::render('Public/Features');
    }

    public function pricing()
    {
        // Fetch active subscription plans with their prices
        $plans = SubscriptionPlan::where('is_active', true)
            ->with(['prices' => function($q) {
                $q->orderBy('billing_cycle');
            }])
            ->orderBy('max_branches')
            ->get()
            ->map(function($plan) {
                return [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'slug' => $plan->slug,
                    'description' => $plan->description,
                    'features' => is_array($plan->features) ? $plan->features : json_decode($plan->features, true) ?? [],
                    'is_popular' => $plan->slug === 'professional', // Default styling logic
                    'prices' => $plan->prices->map(function($price) {
                        return [
                            'id' => $price->id,
                            'price' => $price->price_per_month,
                            'billing_cycle' => $price->billing_cycle,
                        ];
                    }),
                ];
            });

        return Inertia::render('Public/Pricing', [
            'plans' => $plans
        ]);
    }

    public function contact()
    {
        return Inertia::render('Public/Contact');
    }

    public function partner()
    {
        return Inertia::render('Public/Partner');
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

