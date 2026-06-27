<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Campaign;
use App\Models\Package;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        $coupons = Coupon::with(['campaign', 'usages'])->latest()->paginate(15);
        $campaigns = Campaign::where('status', 'active')->get();
        
        // Basic Stats
        $stats = [
            'total' => Coupon::count(),
            'active' => Coupon::where('status', 'active')->count(),
            'expired' => Coupon::where('status', 'expired')->count(),
            'scheduled' => Coupon::where('status', 'scheduled')->count(),
        ];

        return Inertia::render('Admin/Coupons/Index', [
            'coupons' => $coupons,
            'campaigns' => $campaigns,
            'stats' => $stats,
        ]);
    }

    public function create()
    {
        $campaigns = Campaign::where('status', 'active')->get();
        $packages = Package::where('is_active', true)->get();

        return Inertia::render('Admin/Coupons/Create', [
            'campaigns' => $campaigns,
            'packages' => $packages,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // Step 1: Basic
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:coupons,code',
            'description' => 'nullable|string',
            'campaign_id' => 'nullable|exists:campaigns,id',
            // Step 2: Discount Rules
            'discount_type' => 'required|in:percentage,fixed,free_trial,upgrade,free_addon,custom_price',
            'discount_value' => 'required|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            // Step 3: Context & Packages
            'subscription_type' => 'required|in:new_subscription,renewal,upgrade,downgrade,all',
            'minimum_purchase' => 'nullable|numeric|min:0',
            'packages' => 'nullable|array',
            'packages.*' => 'exists:packages,id',
            'business_types' => 'nullable|array',
            'business_types.*' => 'string',
            // Step 4: Limits
            'start_date' => 'nullable|date',
            'expire_date' => 'nullable|date|after_or_equal:start_date',
            'max_uses_total' => 'nullable|integer|min:1',
            'max_uses_per_company' => 'nullable|integer|min:1',
            'is_auto_apply' => 'boolean',
            'is_hidden' => 'boolean',
            'status' => 'required|in:active,scheduled,expired,disabled',
        ]);

        DB::transaction(function () use ($validated) {
            $coupon = Coupon::create([
                'name' => $validated['name'],
                'code' => strtoupper($validated['code']),
                'description' => $validated['description'] ?? null,
                'campaign_id' => $validated['campaign_id'] ?? null,
                'discount_type' => $validated['discount_type'],
                'discount_value' => $validated['discount_value'],
                'max_discount_amount' => $validated['max_discount_amount'] ?? null,
                'subscription_type' => $validated['subscription_type'],
                'minimum_purchase' => $validated['minimum_purchase'] ?? null,
                'start_date' => $validated['start_date'] ?? null,
                'expire_date' => $validated['expire_date'] ?? null,
                'max_uses_total' => $validated['max_uses_total'] ?? null,
                'max_uses_per_company' => $validated['max_uses_per_company'] ?? null,
                'is_auto_apply' => $validated['is_auto_apply'] ?? false,
                'is_hidden' => $validated['is_hidden'] ?? false,
                'status' => $validated['status'],
            ]);

            if (!empty($validated['packages'])) {
                $coupon->packages()->sync($validated['packages']);
            }

            if (!empty($validated['business_types'])) {
                foreach ($validated['business_types'] as $bt) {
                    $coupon->businessTypes()->create(['business_type' => $bt]);
                }
            }
        });

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon created successfully.');
    }
}
