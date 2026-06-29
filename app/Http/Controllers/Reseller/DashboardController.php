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
            'total_paid' => $reseller->withdrawalRequests()->where('status', 'completed')->sum('amount'),
        ];

        // Monthly chart data (last 6 months)
        $chartData = collect(range(5, 0))->map(function ($i) use ($reseller) {
            $date = now()->subMonths($i);
            $year = $date->year;
            $month = $date->month;

            $referralsCount = Referral::where('reseller_id', $reseller->id)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count();

            $activeCompaniesCount = Referral::where('reseller_id', $reseller->id)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->whereHas('company', function($q) {
                    $q->where('registration_status', 'active');
                })->count();

            return [
                'month' => $date->format('M Y'),
                'short_date' => $date->format('M d'),
                'earnings' => Commission::where('reseller_id', $reseller->id)
                    ->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->where('status', '!=', 'reversed')
                    ->sum('commission_amount'),
                'referrals' => $referralsCount,
                'active_companies' => $activeCompaniesCount
            ];
        })->values();

        // Top 5 Referral Codes
        $referralCodes = ReferralCode::where('reseller_id', $reseller->id)
            ->withCount('referrals')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($code) {
                $activeCompanies = Referral::where('referral_code_id', $code->id)
                    ->whereHas('company', function($q) { $q->where('registration_status', 'active'); })
                    ->count();
                $earned = Commission::whereHas('referral', function($q) use ($code) {
                    $q->where('referral_code_id', $code->id);
                })->where('status', '!=', 'reversed')->sum('commission_amount');

                return [
                    'id' => $code->id,
                    'code' => $code->code,
                    'campaign_label' => $code->notes ?? 'Campaign',
                    'created_at' => $code->created_at->format('M d, Y'),
                    'referrals_count' => $code->referrals_count,
                    'active_companies' => $activeCompanies,
                    'commission_earned' => $earned,
                    'status' => $code->status,
                ];
            });

        // Recent Activity
        $recentReferrals = Referral::with('company')->where('reseller_id', $reseller->id)->latest()->take(5)->get()->map(function($r) {
            return [
                'type' => 'referral',
                'title' => 'New referral registered',
                'subtitle' => $r->company->name ?? 'Unknown Company',
                'date' => $r->created_at->format('M d, Y • h:i A'),
                'timestamp' => $r->created_at->timestamp,
            ];
        });

        $recentCommissions = Commission::with('company')->where('reseller_id', $reseller->id)->latest()->take(5)->get()->map(function($c) {
            return [
                'type' => 'commission',
                'title' => 'Commission earned',
                'subtitle' => '৳' . number_format($c->commission_amount) . ' from ' . ($c->company->name ?? 'Unknown Company'),
                'date' => $c->created_at->format('M d, Y • h:i A'),
                'timestamp' => $c->created_at->timestamp,
            ];
        });

        $recentPayouts = $reseller->withdrawalRequests()->latest()->take(5)->get()->map(function($p) {
            return [
                'type' => 'payout',
                'title' => 'Payout ' . $p->status,
                'subtitle' => '৳' . number_format($p->amount) . ' via ' . $p->payment_method,
                'date' => $p->created_at->format('M d, Y • h:i A'),
                'timestamp' => $p->created_at->timestamp,
            ];
        });

        $recentActivity = collect()->concat($recentReferrals)->concat($recentCommissions)->concat($recentPayouts)
            ->sortByDesc('timestamp')->take(5)->values();

        return Inertia::render('Reseller/Dashboard', [
            'stats' => $stats,
            'chartData' => $chartData,
            'referralCodes' => $referralCodes,
            'recentActivity' => $recentActivity,
            'reseller' => $reseller,
        ]);
    }
}
