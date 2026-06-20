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
        
        $roles = Role::where('company_id', $companyId)
            ->withCount('users')
            ->get();

        return Inertia::render('Company/Roles/Index', [
            'roles' => $roles
        ]);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Role::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
            'company_id' => auth()->user()->company_id
        ]);

        return back()->with('success', 'Role created successfully.');
    }

    public function update(Request $request, Role $role)
    {
        if ($role->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $role->update([
            'name' => $validated['name']
        ]);

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
