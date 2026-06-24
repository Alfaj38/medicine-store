<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierQuotation extends Model
{
    protected $guarded = [];

    protected $casts = [
        'quotation_date' => 'date',
        'valid_until' => 'date',
    ];

    public function items()
    {
        return $this->hasMany(SupplierQuotationItem::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function requisition()
    {
        return $this->belongsTo(Requisition::class);
    }
}
