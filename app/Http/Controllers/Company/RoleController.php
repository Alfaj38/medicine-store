<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Inertia\Inertia;

class RoleController extends Controller
{
    public function index()
    {
        $companyId = auth()->user()->company_id;
        
        $roles = Role::where('company_id', $companyId)
            ->withCount('users')
            ->with(['permissions', 'users' => function($query) {
                $query->select('id', 'name', 'email', 'avatar');
            }])
            ->get();
            
        // Load branch access for each role (raw query for now since no relationship is defined on Spatie's Role)
        $branchRoles = \DB::table('branch_role')->whereIn('role_id', $roles->pluck('id'))->get();
        foreach($roles as $role) {
            $role->branch_ids = $branchRoles->where('role_id', $role->id)->pluck('branch_id')->toArray();
        }

        $allPermissions = \Spatie\Permission\Models\Permission::all();
        $permissionsByModule = [];
        foreach ($allPermissions as $perm) {
            $parts = explode('.', $perm->name);
            $module = $parts[0];
            $action = $parts[1] ?? 'other';
            
            // Format module name for display
            $moduleName = ucwords(str_replace('-', ' ', $module));
            
            if (!isset($permissionsByModule[$moduleName])) {
                $permissionsByModule[$moduleName] = [];
            }
            $permissionsByModule[$moduleName][] = [
                'id' => $perm->id,
                'name' => $perm->name,
                'action' => $action
            ];
        }
        
        // Get branches for this company
        $branches = \App\Models\Branch::where('company_id', $companyId)->get(['id', 'name', 'area_id']);

        return Inertia::render('Company/Roles/Index', [
            'roles' => $roles,
            'permissionsByModule' => $permissionsByModule,
            'branches' => $branches
        ]);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->where(function ($query) {
                    return $query->where('company_id', auth()->user()->company_id);
                })
            ],
            'permissions' => 'nullable|array',
            'branches' => 'nullable|array'
        ]);

        $role = Role::query()->create([
            'name' => $validated['name'],
            'guard_name' => 'web',
            'company_id' => auth()->user()->company_id
        ]);
        
        if (isset($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }
        
        if (isset($validated['branches'])) {
            $branchData = [];
            foreach ($validated['branches'] as $branchId) {
                $branchData[] = ['role_id' => $role->id, 'branch_id' => $branchId, 'created_at' => now(), 'updated_at' => now()];
            }
            \DB::table('branch_role')->insert($branchData);
        }

        return back()->with('success', 'Role created successfully.');
    }

    public function update(Request $request, Role $role)
    {
        if ($role->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->where(function ($query) {
                    return $query->where('company_id', auth()->user()->company_id);
                })->ignore($role->id)
            ],
            'permissions' => 'nullable|array',
            'branches' => 'nullable|array'
        ]);

        $role->update([
            'name' => $validated['name']
        ]);
        
        if (isset($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        } else {
            $role->syncPermissions([]);
        }
        
        \DB::table('branch_role')->where('role_id', $role->id)->delete();
        if (isset($validated['branches'])) {
            $branchData = [];
            foreach ($validated['branches'] as $branchId) {
                $branchData[] = ['role_id' => $role->id, 'branch_id' => $branchId, 'created_at' => now(), 'updated_at' => now()];
            }
            \DB::table('branch_role')->insert($branchData);
        }

        // Phase 10: Permission Optimization - Clear User Cache when permissions change
        $userIds = \DB::table('model_has_roles')->where('role_id', $role->id)->pluck('model_id');
        foreach ($userIds as $userId) {
            \Illuminate\Support\Facades\Cache::forget("user_{$userId}_auth_data");
        }

        return back()->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        if ($role->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        if ($role->users()->count() > 0) {
            return back()->with('error', 'Cannot delete role assigned to users.');
        }

        $role->delete();

        return back()->with('success', 'Role deleted successfully.');
    }
}
