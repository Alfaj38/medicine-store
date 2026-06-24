<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    protected $guarded = [];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class)->withoutGlobalScope(\App\Models\Scopes\TenantScope::class);
    }

    public function unit()
    {
        return $this->belongsTo(ItemUnit::class);
    }
}
