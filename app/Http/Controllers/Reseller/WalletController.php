<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\WalletTransaction;

class WalletController extends Controller
{
    public function index()
    {
        $reseller = Auth::guard('reseller')->user();
        
        $transactions = WalletTransaction::where('reseller_id', $reseller->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('Reseller/Wallet/Index', [
            'transactions' => $transactions,
            'balance' => $reseller->wallet_balance
        ]);
    }
}
