<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\Commission;

class CommissionController extends Controller
{
    public function index()
    {
        $reseller = Auth::guard('reseller')->user();
        
        $commissions = Commission::with(['company'])
            ->where('reseller_id', $reseller->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('Reseller/Commissions/Index', [
            'commissions' => $commissions
        ]);
    }
}
