<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    protected $guarded = [];

    protected $casts = [
        'features' => 'array',
        'has_central_reporting' => 'boolean',
        'has_branch_transfer' => 'boolean',
        'has_api_access' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function prices()
    {
        return $this->hasMany(SubscriptionPlanPrice::class, 'plan_id');
    }
}
