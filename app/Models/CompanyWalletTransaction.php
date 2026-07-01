<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyWalletTransaction extends Model
{
    protected $guarded = [];

    protected $casts = [
        'credit' => 'decimal:2',
        'debit' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
