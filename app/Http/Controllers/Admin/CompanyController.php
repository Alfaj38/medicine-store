<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;
use Inertia\Inertia;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $companies = Company::query()
            ->when($request->status, fn ($q, $status) => $q->where('registration_status', $status))
            ->when($request->plan, fn ($q, $plan) => $q->where('subscription_plan', $plan))
            ->withCount(['branches', 'users'])
            ->latest()
            ->paginate(15);

        return Inertia::render('Admin/Companies/Index', [
            'companies' => $companies,
            'filters' => $request->only(['status', 'plan'])
        ]);
    }

    public function show(Company $company)
    {
        $company->load(['branches', 'users']);
        // Load subscriptions if they exist
        // $company->load('subscriptions');

        return Inertia::render('Admin/Companies/Show', [
            'company' => $company
        ]);
    }

    public function approve(Company $company)
    {
        $company->update([
            'registration_status' => 'active',
            'approved_by' => auth()->id()
        ]);

        return back()->with('success', 'Company approved successfully.');
    }

    public function suspend(Company $company)
    {
        $company->update([
            'registration_status' => 'suspended'
        ]);

        return back()->with('success', 'Company suspended successfully.');
    }
}
