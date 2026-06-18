<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Branch;
use App\Models\User;
use App\Models\Sale;

class DashboardController extends Controller
{
    public function index()
    {
        $companyId = auth()->user()->company_id;
        
        $stats = [
            'total_branches' => Branch::where('company_id', $companyId)->count(),
            'total_users' => User::where('company_id', $companyId)->count(),
            'monthly_sales' => Sale::where('company_id', $companyId)
                                ->whereMonth('created_at', now()->month)
                                ->sum('total_amount'),
            'today_sales' => Sale::where('company_id', $companyId)
                                ->whereDate('created_at', now()->toDateString())
                                ->sum('total_amount'),
        ];

        return Inertia::render('Company/Dashboard', [
            'stats' => $stats
        ]);
    }
}
