<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BankBranch extends Model
{
    use HasFactory;

    protected $fillable = ['bank_id', 'name', 'routing_number', 'is_active'];

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
}
