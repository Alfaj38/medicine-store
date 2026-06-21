<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\SubscriptionPlan;

class PublicController extends Controller
{
    public function home()
    {
        return Inertia::render('Public/Home');
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
}

