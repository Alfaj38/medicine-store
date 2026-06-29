<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $reseller = Auth::guard('reseller')->user();
        
        // Date Filtering
        $period = $request->input('period', '30'); // Default 30 days
        $startDate = now()->subDays((int)$period)->startOfDay();
        $endDate = now()->endOfDay();

        // Base Queries
        $commissionsQuery = \App\Models\Commission::where('reseller_id', $reseller->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'reversed');
            
        $referralsQuery = \App\Models\Referral::where('reseller_id', $reseller->id)
            ->whereBetween('created_at', [$startDate, $endDate]);

        // Key Performance Indicators (KPIs)
        $totalRevenue = (clone $commissionsQuery)->sum('commission_amount');
        $totalReferrals = (clone $referralsQuery)->count();
        $avgCommission = $totalReferrals > 0 ? $totalRevenue / $totalReferrals : 0;

        // Daily Earnings Trend Chart Data
        $dailyEarnings = (clone $commissionsQuery)
            ->selectRaw('DATE(created_at) as date, SUM(commission_amount) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        // Fill missing days with zero
        $dates = collect();
        $earningsData = collect();
        
        for ($i = (int)$period; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dates->push(now()->subDays($i)->format('M d'));
            $earningsData->push((float)($dailyEarnings[$date] ?? 0));
        }

        // Campaign Breakdown (Top Codes)
        $campaigns = \App\Models\ReferralCode::where('reseller_id', $reseller->id)
            ->get()
            ->map(function ($code) use ($startDate, $endDate) {
                $refs = \App\Models\Referral::where('referral_code_id', $code->id)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count();
                    
                $earned = \App\Models\Commission::whereHas('referral', function($q) use ($code) {
                        $q->where('referral_code_id', $code->id);
                    })
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->where('status', '!=', 'reversed')
                    ->sum('commission_amount');

                return [
                    'code' => $code->code,
                    'label' => $code->label ?? 'Campaign',
                    'referrals' => $refs,
                    'earnings' => $earned
                ];
            })
            ->filter(fn($c) => $c['referrals'] > 0 || $c['earnings'] > 0)
            ->sortByDesc('earnings')
            ->values();

        return Inertia::render('Reseller/Reports/Index', [
            'filters' => [
                'period' => $period
            ],
            'kpis' => [
                'total_revenue' => $totalRevenue,
                'total_referrals' => $totalReferrals,
                'avg_commission' => $avgCommission,
            ],
            'chartData' => [
                'labels' => $dates,
                'series' => [
                    [
                        'name' => 'Earnings',
                        'data' => $earningsData
                    ]
                ]
            ],
            'campaigns' => $campaigns
        ]);
    }
}
