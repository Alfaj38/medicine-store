<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseReturnItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_return_id',
        'medicine_id',
        'return_reason_id',
        'batch_no',
        'expiry_date',
        'quantity',
        'unit_price',
        'subtotal',
    ];

    public function purchaseReturn()
    {
        return $this->belongsTo(PurchaseReturn::class);
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }

    public function returnReason()
    {
        return $this->belongsTo(ReturnReason::class);
    }
}
