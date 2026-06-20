<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseItem extends Model
{
    protected $guarded = [];

    protected $casts = [
        'expiry_date' => 'date',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'medicine_id');
    }

    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'medicine_id');
    }
}
