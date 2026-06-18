<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyBankAccount extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
