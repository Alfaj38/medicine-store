<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Inertia\Inertia;

class RoleController extends Controller
{
    public function index()
    {
        $companyId = auth()->user()->company_id;
        
        $roles = Role::where('team_id', $companyId)
            ->withCount('users')
            ->get();

        return Inertia::render('Company/Roles/Index', [
            'roles' => $roles
        ]);
    }
}
