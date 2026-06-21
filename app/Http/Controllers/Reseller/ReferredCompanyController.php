<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\Referral;

class ReferredCompanyController extends Controller
{
    public function index()
    {
        $reseller = Auth::guard('reseller')->user();
        
        $companies = Referral::with(['company.subscription.plan', 'referralCode'])
            ->where('reseller_id', $reseller->id)
            ->orderBy('registered_at', 'desc')
            ->paginate(15);

        return Inertia::render('Reseller/Companies/Index', [
            'companies' => $companies
        ]);
    }
}
