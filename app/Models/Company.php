<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ItemType;
use App\Models\ItemCategory;

class Company extends Model
{
    protected $guarded = [];

    protected $casts = [
        'settings' => 'array',
        'subscription_expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function areas()
    {
        return $this->hasMany(Area::class);
    }

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    public function bankAccounts()
    {
        return $this->hasMany(CompanyBankAccount::class);
    }

    public function documents()
    {
        return $this->hasMany(CompanyDocument::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function owner()
    {
        return $this->hasOne(User::class)->where('is_company_owner', true);
    }

    public function subscription()
    {
        return $this->hasOne(Subscription::class)->latestOfMany();
    }

    public function isActive(): bool
    {
        return $this->is_active && $this->registration_status === 'active';
    }

    public function isPending(): bool
    {
        return $this->registration_status === 'pending';
    }

    public function isSuspended(): bool
    {
        return $this->registration_status === 'suspended';
    }
    /**
     * The item types this company supplies (many‑to‑many).
     */
    public function itemTypes()
    {
        return $this->belongsToMany(ItemType::class, 'company_item_type', 'pharmaceutical_industry_id', 'item_type_id');
    }

    /**
     * The item categories this company supplies (many‑to‑many).
     */
    public function categories()
    {
        return $this->belongsToMany(ItemCategory::class, 'company_category', 'pharmaceutical_industry_id', 'category_id');
    }

    public function referralCode()
    {
        return $this->belongsTo(ReferralCode::class, 'referral_code_id');
    }

    public function referral()
    {
        return $this->hasOne(Referral::class, 'company_id');
    }
}

