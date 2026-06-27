<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\PackageFeature;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::with('features')->orderBy('sort_order')->get();

        return Inertia::render('Admin/Packages/Index', [
            'packages' => $packages
        ]);
    }

    public function update(Request $request, Package $package)
    {
        $request->validate([
            'name' => 'required|string',
            'monthly_price' => 'required|numeric',
            'yearly_price' => 'required|numeric',
            'is_active' => 'required|boolean',
            'features' => 'array',
            'features.*.feature_code' => 'required|string',
            'features.*.is_enabled' => 'required|boolean',
            'features.*.limit' => 'nullable|integer',
        ]);

        DB::transaction(function () use ($request, $package) {
            $package->update([
                'name' => $request->name,
                'monthly_price' => $request->monthly_price,
                'yearly_price' => $request->yearly_price,
                'is_active' => $request->is_active,
            ]);

            if ($request->has('features')) {
                foreach ($request->features as $featureData) {
                    $package->features()->updateOrCreate(
                        ['feature_code' => $featureData['feature_code']],
                        [
                            'is_enabled' => $featureData['is_enabled'],
                            'limit' => $featureData['limit']
                        ]
                    );
                }
            }
        });

        return back()->with('success', 'Package updated successfully.');
    }
}
