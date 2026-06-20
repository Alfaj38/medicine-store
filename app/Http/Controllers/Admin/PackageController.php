<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PackageController extends Controller
{
    public function index()
    {
        $packages = SubscriptionPlan::with('prices')->orderBy('id')->get();

        return Inertia::render('Admin/Packages/Index', [
            'packages' => $packages
        ]);
    }

    public function update(Request $request, SubscriptionPlan $package)
    {
        $request->validate([
            'is_active' => 'required|boolean',
            'max_branches' => 'required|integer',
            'max_users' => 'required|integer',
            'has_central_reporting' => 'required|boolean',
            'has_branch_transfer' => 'required|boolean',
        ]);

        $package->update([
            'is_active' => $request->is_active,
            'max_branches' => $request->max_branches,
            'max_users' => $request->max_users,
            'has_central_reporting' => $request->has_central_reporting,
            'has_branch_transfer' => $request->has_branch_transfer,
        ]);

        return back()->with('success', 'Package updated successfully.');
    }
}
