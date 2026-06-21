<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Reseller;
use App\Services\PromoCodeService;

class ResellerApplicationController extends Controller
{
    public function store(Request $request, PromoCodeService $promoCodeService)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'business_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:resellers,email',
            'phone' => 'required|string|max:20',
            'nid_or_business_id' => 'required|string|max:100',
            'address' => 'required|string',
            'bank_info' => 'required|array',
            'bank_info.method' => 'required|in:bank,bkash,nagad,rocket',
            'bank_info.account_name' => 'required|string',
            'bank_info.account_number' => 'required|string',
            'bank_info.bank_name' => 'nullable|string',
            'experience' => 'nullable|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $validated['reseller_code'] = $promoCodeService->generateResellerCode();
        $validated['password'] = \Illuminate\Support\Facades\Hash::make($validated['password']);

        Reseller::create($validated);

        return redirect()->route('reseller.pending');
    }
}
