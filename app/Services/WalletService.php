<?php

namespace App\Services;

use App\Models\Reseller;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;

class WalletService
{
    public function credit(Reseller $reseller, float $amount, string $description, string $referenceType, int $referenceId): WalletTransaction
    {
        return DB::transaction(function () use ($reseller, $amount, $description, $referenceType, $referenceId) {
            $balanceAfter = $reseller->wallet_balance + $amount;
            
            $transaction = WalletTransaction::create([
                'reseller_id' => $reseller->id,
                'type' => 'credit',
                'amount' => $amount,
                'description' => $description,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'balance_after' => $balanceAfter,
            ]);

            $reseller->update(['wallet_balance' => $balanceAfter]);

            return $transaction;
        });
    }

    public function debit(Reseller $reseller, float $amount, string $description, string $referenceType, int $referenceId): WalletTransaction
    {
        return DB::transaction(function () use ($reseller, $amount, $description, $referenceType, $referenceId) {
            $balanceAfter = $reseller->wallet_balance - $amount;
            
            $transaction = WalletTransaction::create([
                'reseller_id' => $reseller->id,
                'type' => 'debit',
                'amount' => $amount,
                'description' => $description,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'balance_after' => $balanceAfter,
            ]);

            $reseller->update(['wallet_balance' => $balanceAfter]);

            return $transaction;
        });
    }
}
