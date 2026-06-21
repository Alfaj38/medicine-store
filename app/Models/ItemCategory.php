<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

use App\Models\Item;
use App\Models\ItemType;
use App\Models\Company;
use App\Models\PharmaceuticalIndustry;

class ItemCategory extends Model
{
    protected $guarded = [];

    public function itemType()
    {
        return $this->belongsTo(ItemType::class);
    }

    public function items()
    {
        return $this->hasMany(Item::class, 'category_id');
    }
    /**
    * Companies that supply this category (many‑to‑many).
    */
    public function companies()
    {
        return $this->belongsToMany(PharmaceuticalIndustry::class, 'company_category', 'category_id', 'pharmaceutical_industry_id');
    }
}

