<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    use BelongsToTenant;

    protected $guarded = [];

    protected $casts = [
        'expiry_date' => 'date',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'medicine_id');
    }

    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'medicine_id');
    }
}
