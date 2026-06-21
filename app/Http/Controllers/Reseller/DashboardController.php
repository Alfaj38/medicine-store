<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\Referral;
use App\Models\ReferralCode;
use App\Models\Commission;

class DashboardController extends Controller
{
    public function index()
    {
        $reseller = Auth::guard('reseller')->user();

        $stats = [
            'total_companies' => Referral::where('reseller_id', $reseller->id)->count(),
            'active_codes' => ReferralCode::where('reseller_id', $reseller->id)->where('status', 'active')->count(),
            'monthly_earnings' => Commission::where('reseller_id', $reseller->id)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->where('status', '!=', 'reversed')
                ->sum('commission_amount'),
            'lifetime_earnings' => Commission::where('reseller_id', $reseller->id)
                ->where('status', '!=', 'reversed')
                ->sum('commission_amount'),
            'wallet_balance' => $reseller->wallet_balance,
            'pending_withdrawal' => $reseller->withdrawalRequests()->where('status', 'pending')->sum('amount'),
        ];

        // Monthly chart data (last 6 months)
        $chartData = collect(range(5, 0))->map(function ($i) use ($reseller) {
            $date = now()->subMonths($i);
            return [
                'month' => $date->format('M Y'),
                'earnings' => Commission::where('reseller_id', $reseller->id)
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->where('status', '!=', 'reversed')
                    ->sum('commission_amount')
            ];
        })->values();

        return Inertia::render('Reseller/Dashboard', [
            'stats' => $stats,
            'chartData' => $chartData,
            'reseller' => $reseller,
        ]);
    }
}
