<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Branch;
use Inertia\Inertia;

class BranchController extends Controller
{
    public function index()
    {
        $companyId = auth()->user()->company_id;
        
        $branches = Branch::where('company_id', $companyId)
            ->withCount('users')
            ->latest()
            ->get();

        return Inertia::render('Company/Branches/Index', [
            'branches' => $branches
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'is_default' => 'boolean',
            'is_active' => 'boolean'
        ]);

        $company = auth()->user()->company;
        $subscriptionService = app(\App\Services\SubscriptionService::class);
        
        if (!$subscriptionService->canAddBranch($company)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'name' => 'You have reached the maximum number of branches for your current plan. Please upgrade to add more.'
            ]);
        }

        $validated['company_id'] = auth()->user()->company_id;
        $validated['approval_status'] = 'approved'; // Self-managed

        if ($request->is_default) {
            Branch::where('company_id', auth()->user()->company_id)
                ->update(['is_default' => false]);
        }

        Branch::create($validated);

        return back()->with('success', 'Branch created successfully.');
    }

    public function update(Request $request, Branch $branch)
    {
        if ($branch->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'is_default' => 'boolean',
            'is_active' => 'boolean'
        ]);

        if ($request->is_default) {
            Branch::where('company_id', auth()->user()->company_id)
                ->where('id', '!=', $branch->id)
                ->update(['is_default' => false]);
        }

        $branch->update($validated);

        return back()->with('success', 'Branch updated successfully.');
    }

    public function destroy(Branch $branch)
    {
        if ($branch->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        if ($branch->users()->count() > 0) {
            return back()->with('error', 'Cannot delete a branch that has users assigned.');
        }

        $branch->delete();

        return back()->with('success', 'Branch deleted successfully.');
    }
}
