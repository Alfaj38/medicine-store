<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        if (!auth()->check() && !app()->bound('tenant')) {
            return;
        }

        if (app()->bound('tenant')) {
            $builder->where($model->getTable() . '.company_id', app('tenant')->id);
            return;
        }

        $user = auth()->user();

        // Always enforce company isolation first for non-platform users
        if ($user->company_id) {
            $builder->where($model->getTable() . '.company_id', $user->company_id);
        }
    }
}
