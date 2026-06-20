<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bank extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'is_active'];

    public function branches()
    {
        return $this->hasMany(BankBranch::class);
    }
}
