<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierCreditNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'credit_note_no',
        'supplier_id',
        'purchase_return_id',
        'amount',
        'status',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseReturn()
    {
        return $this->belongsTo(PurchaseReturn::class);
    }
}
