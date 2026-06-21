<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    protected $guarded = [];

    protected $casts = [
        'payment_amount' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'reversed_at' => 'datetime',
    ];

    public function reseller()
    {
        return $this->belongsTo(Reseller::class);
    }

    public function referral()
    {
        return $this->belongsTo(Referral::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function subscriptionPayment()
    {
        return $this->belongsTo(SubscriptionPayment::class);
    }
}
