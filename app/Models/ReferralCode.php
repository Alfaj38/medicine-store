<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralCode extends Model
{
    protected $guarded = [];

    protected $casts = [
        'expires_at' => 'datetime',
        'total_revenue' => 'decimal:2',
        'total_commission' => 'decimal:2',
    ];

    public function reseller()
    {
        return $this->belongsTo(Reseller::class);
    }

    public function companies()
    {
        return $this->hasMany(Company::class, 'referral_code_id');
    }

    public function referrals()
    {
        return $this->hasMany(Referral::class, 'referral_code_id');
    }

    public function commissions()
    {
        return $this->hasMany(Commission::class, 'referral_code_id', 'id');
    }

    public function isValid(): bool
    {
        return $this->status === 'active' && 
               (!$this->expires_at || $this->expires_at->isFuture());
    }
}
