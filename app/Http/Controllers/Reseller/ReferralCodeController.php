<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\ReferralCode;
use App\Services\PromoCodeService;

class ReferralCodeController extends Controller
{
    public function index()
    {
        $reseller = Auth::guard('reseller')->user();
        $codes = ReferralCode::where('reseller_id', $reseller->id)
            ->withCount('referrals')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($code) {
                $code->total_companies = \App\Models\Referral::where('referral_code_id', $code->id)
                    ->whereHas('company', function($q) { $q->where('registration_status', 'active'); })
                    ->count();
                $code->total_commission = \App\Models\Commission::whereHas('referral', function($q) use ($code) {
                    $q->where('referral_code_id', $code->id);
                })->sum('commission_amount');
                return $code;
            });

        return Inertia::render('Reseller/ReferralCodes/Index', [
            'codes' => $codes
        ]);
    }

    public function store(Request $request, PromoCodeService $service)
    {
        $validated = $request->validate([
            'label' => 'nullable|string|max:100',
        ]);

        $reseller = Auth::guard('reseller')->user();
        $service->generateCode($reseller, $validated['label']);

        return back()->with('success', 'Referral code generated successfully.');
    }

    public function toggle($id)
    {
        $reseller = Auth::guard('reseller')->user();
        $code = ReferralCode::where('reseller_id', $reseller->id)->findOrFail($id);
        
        $code->update([
            'status' => $code->status === 'active' ? 'inactive' : 'active'
        ]);

        return back()->with('success', 'Referral code status updated.');
    }
}
