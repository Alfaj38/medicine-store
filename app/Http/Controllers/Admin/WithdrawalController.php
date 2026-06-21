<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\WithdrawalRequest;
use App\Services\WalletService;

class WithdrawalController extends Controller
{
    public function index()
    {
        $withdrawals = WithdrawalRequest::with('reseller')
            ->orderByRaw("FIELD(status, 'pending') DESC")
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return Inertia::render('Admin/Affiliate/Withdrawals/Index', [
            'withdrawals' => $withdrawals
        ]);
    }

    public function approve($id)
    {
        $withdrawal = WithdrawalRequest::findOrFail($id);
        
        if ($withdrawal->status !== 'pending') {
            return back()->withErrors(['message' => 'Only pending requests can be approved.']);
        }

        $withdrawal->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now()
        ]);

        return back()->with('success', 'Withdrawal request approved and ready for payment.');
    }

    public function pay(Request $request, $id)
    {
        $validated = $request->validate([
            'admin_note' => 'nullable|string'
        ]);

        $withdrawal = WithdrawalRequest::findOrFail($id);
        
        if ($withdrawal->status !== 'approved') {
            return back()->withErrors(['message' => 'Request must be approved before paying.']);
        }

        $withdrawal->update([
            'status' => 'paid',
            'paid_at' => now(),
            'admin_note' => $validated['admin_note'] ?? null
        ]);

        // Note: Wallet was debited at the time of request, so we don't need to touch the balance.
        // We just update the status of the request.

        return back()->with('success', 'Withdrawal marked as paid.');
    }
    
    public function reject(Request $request, $id, WalletService $walletService)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $withdrawal = WithdrawalRequest::findOrFail($id);
        
        if ($withdrawal->status !== 'pending') {
            return back()->withErrors(['message' => 'Only pending requests can be rejected.']);
        }

        $withdrawal->update([
            'status' => 'rejected',
            'admin_note' => $validated['reason']
        ]);

        // Refund the wallet
        $walletService->credit(
            $withdrawal->reseller,
            $withdrawal->amount,
            "Refund for rejected withdrawal request",
            'reversal',
            $withdrawal->id
        );

        return back()->with('success', 'Withdrawal request rejected and funds returned to wallet.');
    }
}
