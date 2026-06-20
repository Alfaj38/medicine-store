<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $companies = Company::query()
            ->when($request->status, fn ($q, $status) => $q->where('registration_status', $status))
            // ->when($request->plan, fn ($q, $plan) => $q->where('subscription_plan', $plan)) // subscription is now relation
            ->withCount(['branches', 'users'])
            ->with(['subscription' => function ($q) {
                $q->with('plan');
            }])
            ->latest()
            ->paginate(15);

        return Inertia::render('Admin/Companies/Index', [
            'companies' => $companies,
            'filters' => $request->only(['status', 'plan'])
        ]);
    }

    public function show(Company $company)
    {
        $company->load(['branches', 'users', 'subscription.plan']);

        $totalRevenue = DB::table('subscription_payments')
            ->where('company_id', $company->id)
            ->where('status', 'paid')
            ->sum('net_amount');

        $ticketsCount = DB::table('support_tickets')
            ->where('company_id', $company->id)
            ->where('status', '!=', 'closed')
            ->count();

        $lastLogin = $company->users()->max('last_login_at');

        return Inertia::render('Admin/Companies/Show', [
            'company' => $company,
            'stats' => [
                'total_revenue' => round($totalRevenue, 2),
                'open_tickets'  => $ticketsCount,
                'total_branches'=> $company->branches->count(),
                'total_users'   => $company->users->count(),
                'last_login'    => $lastLogin,
            ],
            'tickets' => DB::table('support_tickets')
                ->where('company_id', $company->id)
                ->latest()
                ->take(5)
                ->get(),
            'payments' => DB::table('subscription_payments')
                ->where('company_id', $company->id)
                ->latest()
                ->take(5)
                ->get(),
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

    public function impersonate(Company $company)
    {
        // Find the company owner or first user
        $user = $company->users()->where('is_company_owner', true)->first() ?? $company->users()->first();

        if (!$user) {
            return back()->with('error', 'Company has no users to impersonate.');
        }

        // Store current super admin id
        session(['impersonating_from' => auth()->id()]);
        
        Auth::login($user);

        return redirect()->route('dashboard')->with('success', "You are now logged in as {$company->name}");
    }
}
