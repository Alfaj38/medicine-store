<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierQuotationItem extends Model
{
    protected $guarded = [];

    public function quotation()
    {
        return $this->belongsTo(SupplierQuotation::class, 'supplier_quotation_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function unit()
    {
        return $this->belongsTo(ItemUnit::class);
    }
}
