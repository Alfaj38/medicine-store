<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\WithdrawalRequest;
use App\Services\WalletService;

class WithdrawalController extends Controller
{
    public function index()
    {
        $reseller = Auth::guard('reseller')->user();
        
        $withdrawals = WithdrawalRequest::where('reseller_id', $reseller->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return Inertia::render('Reseller/Withdrawals/Index', [
            'withdrawals' => $withdrawals,
            'balance' => $reseller->wallet_balance,
            'bank_info' => $reseller->bank_info,
        ]);
    }

    public function store(Request $request, WalletService $walletService)
    {
        $reseller = Auth::guard('reseller')->user();

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:500', 'max:' . $reseller->wallet_balance],
            'payment_method' => ['required', 'in:bank,bkash,nagad,rocket'],
        ]);

        if ($reseller->wallet_balance < $validated['amount']) {
            return back()->withErrors(['amount' => 'Insufficient balance.']);
        }

        // Debit the wallet immediately so they can't double spend
        $walletService->debit(
            $reseller, 
            $validated['amount'], 
            "Withdrawal request pending via " . ucfirst($validated['payment_method']), 
            'withdrawal', 
            0 // Will update below
        );

        $withdrawal = WithdrawalRequest::create([
            'reseller_id' => $reseller->id,
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'payment_details' => $reseller->bank_info, // Snapshot their current bank info
            'status' => 'pending'
        ]);

        // Update the wallet transaction reference
        $reseller->walletTransactions()->latest()->first()->update(['reference_id' => $withdrawal->id]);

        return back()->with('success', 'Withdrawal request submitted successfully.');
    }
}
