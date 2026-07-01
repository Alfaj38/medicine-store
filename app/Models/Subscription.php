<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use \App\Models\Traits\BelongsToTenant;

    protected $guarded = [];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'next_renewal_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'auto_renew' => 'boolean',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    public function payments()
    {
        return $this->hasMany(SubscriptionPayment::class);
    }
}
