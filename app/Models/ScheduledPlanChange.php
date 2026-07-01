<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduledPlanChange extends Model
{
    protected $guarded = [];

    protected $casts = [
        'effective_date' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function currentPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'current_plan_id');
    }

    public function nextPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'next_plan_id');
    }

    public function nextPrice()
    {
        return $this->belongsTo(SubscriptionPlanPrice::class, 'next_price_id');
    }
}
