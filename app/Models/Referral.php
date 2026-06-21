<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    protected $guarded = [];

    protected $casts = [
        'registered_at' => 'datetime',
    ];

    public function reseller()
    {
        return $this->belongsTo(Reseller::class);
    }

    public function referralCode()
    {
        return $this->belongsTo(ReferralCode::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
