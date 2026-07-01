<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyWallet extends Model
{
    protected $guarded = [];

    protected $casts = [
        'balance' => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
