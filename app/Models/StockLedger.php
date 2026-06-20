<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockLedger extends Model
{
    use BelongsToTenant;

    protected $guarded = [];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'medicine_id');
    }

    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'medicine_id');
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }
}
