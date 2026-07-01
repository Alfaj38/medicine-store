<?php

namespace App\Services;

use App\Models\Company;
use App\Models\CompanyWallet;
use App\Models\CompanyWalletTransaction;
use Illuminate\Support\Facades\DB;

class CompanyWalletService
{
    /**
     * Credit the wallet.
     */
    public function credit(Company $company, float $amount, string $type, string $reference = null, string $note = null): CompanyWalletTransaction
    {
        return DB::transaction(function () use ($company, $amount, $type, $reference, $note) {
            $wallet = $company->wallet()->firstOrCreate([]);
            
            $wallet->balance += $amount;
            $wallet->save();

            return $company->walletTransactions()->create([
                'credit' => $amount,
                'debit' => 0,
                'balance_after' => $wallet->balance,
                'type' => $type,
                'reference' => $reference,
                'note' => $note,
            ]);
        });
    }

    /**
     * Debit the wallet.
     */
    public function debit(Company $company, float $amount, string $type, string $reference = null, string $note = null): CompanyWalletTransaction
    {
        return DB::transaction(function () use ($company, $amount, $type, $reference, $note) {
            $wallet = $company->wallet()->firstOrCreate([]);
            
            if ($wallet->balance < $amount) {
                throw new \Exception('Insufficient wallet balance.');
            }

            $wallet->balance -= $amount;
            $wallet->save();

            return $company->walletTransactions()->create([
                'credit' => 0,
                'debit' => $amount,
                'balance_after' => $wallet->balance,
                'type' => $type,
                'reference' => $reference,
                'note' => $note,
            ]);
        });
    }
}
