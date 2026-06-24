<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;


#[Fillable(['company_id', 'branch_id', 'area_id', 'name', 'email', 'phone', 'avatar', 'password', 'is_active', 'user_type', 'data_scope', 'is_company_owner'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->user_type === 'management' && $this->data_scope === 'platform';
    }

    public function isManagement(): bool
    {
        return $this->user_type === 'management';
    }

    public function isCompanyOwner(): bool
    {
        return $this->is_company_owner;
    }

    public function isCompanyUser(): bool
    {
        return $this->user_type === 'company';
    }

    public function getDataScope(): string
    {
        return $this->data_scope;
    }

    public function getAllowedBranchIds(): array
    {
        if (in_array($this->data_scope, ['own', 'branch'])) {
            return $this->branch_id ? [$this->branch_id] : [];
        }
        if ($this->data_scope === 'area' && $this->area) {
            return $this->area->branches()->pluck('branches.id')->toArray();
        }
        if ($this->data_scope === 'company' && $this->company) {
            return $this->company->branches()->pluck('id')->toArray();
        }
        if (in_array($this->data_scope, ['platform', 'readonly_platform'])) {
            return Branch::pluck('id')->toArray();
        }
        return [];
    }
}
