<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemUnit extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_base_unit' => 'boolean',
        'is_purchase_unit' => 'boolean',
        'is_sales_unit' => 'boolean',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
