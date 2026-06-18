<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'supplier_id',
        'purchase_id',
        'reference_no',
        'return_date',
        'total_amount',
        'refund_amount',
        'status',
        'notes',
        'created_by',
        'approved_by',
    ];

    public function items()
    {
        return $this->hasMany(PurchaseReturnItem::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function creditNotes()
    {
        return $this->hasMany(SupplierCreditNote::class);
    }
}
