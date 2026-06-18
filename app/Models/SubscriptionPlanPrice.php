<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlanPrice extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }
}
