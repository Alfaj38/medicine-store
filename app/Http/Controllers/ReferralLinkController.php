<?php

namespace App\Http\Controllers;

use App\Models\ReferralCode;
use Illuminate\Http\Request;

class ReferralLinkController extends Controller
{
    public function redirect($code)
    {
        $referralCode = ReferralCode::where('code', $code)
            ->where('status', 'active')
            ->first();

        if ($referralCode && $referralCode->isValid()) {
            session(['referral_code' => $referralCode->code]);
        }

        return redirect()->route('register');
    }

    public function validateCode(Request $request)
    {
        $code = $request->query('code');
        
        if (!$code) {
            return response()->json(['valid' => false, 'message' => 'No code provided.']);
        }

        $referralCode = ReferralCode::where('code', $code)->first();

        if (!$referralCode) {
            return response()->json(['valid' => false, 'message' => 'Invalid referral code.']);
        }

        if ($referralCode->status !== 'active') {
            return response()->json(['valid' => false, 'message' => 'This referral code is inactive.']);
        }

        if (!$referralCode->isValid()) {
            return response()->json(['valid' => false, 'message' => 'This referral code is suspended or invalid.']);
        }

        return response()->json([
            'valid' => true,
            'message' => 'Code applied successfully!',
            'reseller_name' => $referralCode->reseller->name ?? 'Partner'
        ]);
    }
}
