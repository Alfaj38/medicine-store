<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Company;
use App\Models\Branch;
use App\Models\User;
use App\Models\Subscription;
use App\Models\DemoRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $monthStart = $now->copy()->startOfMonth();

        // Stats
        $stats = [
            'total_companies' => Company::count() ?: 142,
            'active_companies' => Company::where('registration_status', 'active')->count() ?: 121,
            'trial_companies' => Subscription::where('status', 'trial')->count() ?: 15,
            'expired_companies' => Subscription::where('status', 'expired')->count() ?: 6,
            'total_users' => User::count() ?: 3240,
            'total_branches' => Branch::count() ?: 497,
            
            'mrr' => 250000,
            'arr' => 3000000,
            'revenue_this_month' => 220000,
            'pending_invoices' => 30000,
            'overdue_invoices' => 12000,
        ];

        // Revenue Chart (Mock data for line chart)
        $revenueChart = [
            'series' => [
                [
                    'name' => 'Revenue',
                    'data' => [30, 40, 35, 50, 49, 60, 70, 91, 120]
                ],
                [
                    'name' => 'Previous Month',
                    'data' => [20, 25, 30, 35, 40, 45, 50, 60, 80]
                ]
            ],
            'categories' => ['1 Jun', '5 Jun', '10 Jun', '15 Jun', '20 Jun', '25 Jun', '30 Jun']
        ];

        // Trial Funnel
        $trialFunnel = [
            'series' => [
                ['name' => 'Visitors', 'data' => [1200]],
                ['name' => 'Demo Requests', 'data' => [150]],
                ['name' => 'Trials Started', 'data' => [80]],
                ['name' => 'Converted', 'data' => [25]],
            ]
        ];

        // Subscription Overview
        $subscriptionChart = [
            'series' => [20, 70, 52],
            'labels' => ['Basic', 'Professional', 'Enterprise']
        ];

        // Support Tickets
        $supportTickets = [
            'series' => [5, 3, 7, 6],
            'labels' => ['Open', 'Pending', 'In Progress', 'Resolved Today'],
            'total' => 21
        ];

        // Recent Signups
        $recentSignups = [
            ['name' => 'Pharma Point', 'plan' => 'Professional', 'branches' => 3, 'time' => '2 hours ago', 'color' => 'bg-slate-800'],
            ['name' => 'Demo Pharmacy', 'plan' => 'Basic', 'branches' => 1, 'time' => '5 hours ago', 'color' => 'bg-slate-400'],
            ['name' => 'Health Plus', 'plan' => 'Enterprise', 'branches' => 8, 'time' => '1 day ago', 'color' => 'bg-blue-500'],
            ['name' => 'MediCare', 'plan' => 'Professional', 'branches' => 2, 'time' => '2 days ago', 'color' => 'bg-orange-400'],
            ['name' => 'Wellness Pharmacy', 'plan' => 'Basic', 'branches' => 1, 'time' => '2 days ago', 'color' => 'bg-emerald-500'],
        ];

        // Recent Demo Requests
        $demoRequests = DemoRequest::latest()->take(5)->get()->map(function ($req) {
            return [
                'name' => $req->name,
                'email' => $req->email,
                'date' => $req->created_at->format('d M Y'),
                'time' => $req->created_at->format('h:i A'),
                'status' => $req->status,
                'color' => 'bg-purple-500'
            ];
        });

        // Notifications
        $notifications = [
            ['title' => 'New trial started', 'desc' => 'Health Plus Pharmacy started trial.', 'time' => '2 min ago', 'icon' => '🟢', 'bg' => 'bg-emerald-50 text-emerald-600'],
            ['title' => 'Demo request received', 'desc' => 'New demo request from MediCare Pharmacy.', 'time' => '15 min ago', 'icon' => '👤', 'bg' => 'bg-blue-50 text-blue-600'],
            ['title' => 'Payment received', 'desc' => '৳12,000 payment received from Pharma Point.', 'time' => '1 hour ago', 'icon' => '💰', 'bg' => 'bg-emerald-50 text-emerald-600'],
            ['title' => 'Subscription renewed', 'desc' => 'Wellness Pharmacy renewed their subscription.', 'time' => '5 hours ago', 'icon' => '🔄', 'bg' => 'bg-blue-50 text-blue-600'],
            ['title' => 'Support ticket escalated', 'desc' => 'Ticket #TKT-125 has been escalated.', 'time' => '8 hours ago', 'icon' => '⚠️', 'bg' => 'bg-red-50 text-red-600'],
        ];

        // System Health
        $systemHealth = [
            ['name' => 'Application', 'status' => 'Healthy', 'color' => 'text-emerald-500', 'icon' => '💻'],
            ['name' => 'Database', 'status' => 'Healthy', 'color' => 'text-emerald-500', 'icon' => '🗄️'],
            ['name' => 'Queue Workers', 'status' => 'Healthy', 'color' => 'text-emerald-500', 'icon' => '⚙️'],
            ['name' => 'Mail Service', 'status' => 'Healthy', 'color' => 'text-emerald-500', 'icon' => '📧'],
            ['name' => 'SMS Gateway', 'status' => 'Warning', 'color' => 'text-amber-500', 'icon' => '💬'],
            ['name' => 'Payment Gateway', 'status' => 'Healthy', 'color' => 'text-emerald-500', 'icon' => '💳'],
        ];

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'revenueChart' => $revenueChart,
            'trialFunnel' => $trialFunnel,
            'subscriptionChart' => $subscriptionChart,
            'supportTickets' => $supportTickets,
            'recentSignups' => $recentSignups,
            'demoRequests' => $demoRequests,
            'notifications' => $notifications,
            'systemHealth' => $systemHealth,
        ]);
    }
}
