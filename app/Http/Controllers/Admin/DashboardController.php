<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Company;
use App\Models\Branch;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_companies' => Company::count(),
            'active_companies' => Company::where('registration_status', 'active')->count(),
            'pending_companies' => Company::where('registration_status', 'pending')->count(),
            'pending_branches' => Branch::where('approval_status', 'pending')->count(),
            'total_users' => User::count(),
            // MRR placeholder
            'mrr' => 0, 
        ];

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats
        ]);
    }
}
