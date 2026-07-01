<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPayment extends Model
{
    use \App\Models\Traits\BelongsToTenant;

    protected $guarded = [];

    protected $casts = [
        'period_starts_at' => 'datetime',
        'period_ends_at'   => 'datetime',
        'paid_at'          => 'datetime',
        'gateway_response' => 'array',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function commission()
    {
        return $this->hasOne(Commission::class, 'subscription_payment_id');
    }

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }
}
