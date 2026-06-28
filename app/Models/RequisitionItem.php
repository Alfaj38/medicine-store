<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequisitionItem extends Model
{
    protected $guarded = [];

    public function requisition()
    {
        return $this->belongsTo(Requisition::class);
    }

    public function item()
    {
        return $this->belongsTo(Medicine::class, 'item_id');
    }

    public function unit()
    {
        return $this->belongsTo(ItemUnit::class);
    }
}
