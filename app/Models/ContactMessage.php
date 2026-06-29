<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    use HasFactory, BelongsToTenant;

    protected $guarded = [];

    /**
     * Get the company that owns the contact message.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
