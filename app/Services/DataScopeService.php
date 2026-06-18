<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class DataScopeService
{
    /**
     * Apply data scope to any Eloquent query builder.
     * This is called in controllers and automatically limits
     * what records each user can see.
     */
    public function apply(Builder $query, User $user, string $model): Builder
    {
        return match ($user->data_scope) {
            'own'               => $this->applyOwnScope($query, $user, $model),
            'branch'            => $this->applyBranchScope($query, $user),
            'area'              => $this->applyAreaScope($query, $user),
            'company'           => $this->applyCompanyScope($query, $user),
            'platform'          => $query,  // No restriction
            'readonly_platform' => $query,  // No restriction (write blocked at middleware)
            default             => $query->whereRaw('1 = 0'), // Failsafe: return no data
        };
    }

    private function applyOwnScope(Builder $query, User $user, string $model): Builder
    {
        // For Sales: filter by user_id
        // For Purchases: filter by user_id (who created it)
        $query->where('branch_id', $user->branch_id);

        if (in_array($model, [\App\Models\Sale::class, \App\Models\Purchase::class, \App\Models\Expense::class])) {
            $query->where('user_id', $user->id);
        }

        return $query;
    }

    private function applyBranchScope(Builder $query, User $user): Builder
    {
        return $query->where('branch_id', $user->branch_id);
    }

    private function applyAreaScope(Builder $query, User $user): Builder
    {
        $branchIds = $user->area ? $user->area->branches()->pluck('branches.id') : collect();
        return $query->whereIn('branch_id', $branchIds);
    }

    private function applyCompanyScope(Builder $query, User $user): Builder
    {
        // TenantScope already handles company isolation globally
        return $query;
    }
}
