<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'start_date' => 'datetime',
        'expire_date' => 'datetime',
        'is_auto_apply' => 'boolean',
        'is_hidden' => 'boolean',
        'discount_value' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'minimum_purchase' => 'decimal:2',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(Package::class, 'coupon_packages');
    }

    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function businessTypes(): HasMany
    {
        return $this->hasMany(CouponBusinessType::class);
    }
}
