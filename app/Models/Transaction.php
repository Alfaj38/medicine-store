<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Traits\BelongsToTenant;

class Transaction extends Model
{
    use HasFactory, BelongsToTenant;

    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2'
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
