<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchTransferItem extends Model
{
    protected $guarded = [];

    protected $casts = [
        'expiry_date' => 'date',
    ];

    public function branchTransfer()
    {
        return $this->belongsTo(BranchTransfer::class);
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }
}
