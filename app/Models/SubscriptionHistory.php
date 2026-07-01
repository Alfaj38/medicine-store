<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionHistory extends Model
{
    protected $guarded = [];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'credit_used' => 'decimal:2',
        'wallet_used' => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function oldPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'old_plan_id');
    }

    public function newPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'new_plan_id');
    }
}
