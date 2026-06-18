<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use \App\Models\Traits\BelongsToTenant;

    protected $guarded = [];

    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'area_branches', 'area_id', 'branch_id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
