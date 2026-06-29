<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;
        
        $users = User::with(['branch', 'roles'])
            ->where('company_id', $companyId)
            ->when($request->search, fn ($q, $search) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
            ->latest()
            ->paginate(15);

        $branches = Branch::where('company_id', $companyId)->where('is_active', true)->get(['id', 'name']);
        
        $roles = \Spatie\Permission\Models\Role::where('company_id', $companyId)->get(['id', 'name']);

        return Inertia::render('Company/Users/Index', [
            'users' => $users,
            'branches' => $branches,
            'roles' => $roles,
            'filters' => $request->only(['search'])
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')],
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8',
            'branch_id' => ['nullable', Rule::exists('branches', 'id')->where('company_id', auth()->user()->company_id)],
            'role' => ['required', 'string', Rule::exists('roles', 'name')->where('company_id', auth()->user()->company_id)],
            'is_active' => 'boolean'
        ]);

        $roleName = $validated['role'];
        unset($validated['role']);

        $validated['company_id'] = auth()->user()->company_id;
        $validated['password'] = Hash::make($validated['password']);
        $validated['is_company_owner'] = false;
        $validated['user_type'] = 'company';

        setPermissionsTeamId(auth()->user()->company_id);

        $user = User::create($validated);
        
        $role = \Spatie\Permission\Models\Role::firstOrCreate([
            'name' => $roleName,
            'company_id' => auth()->user()->company_id
        ], ['guard_name' => 'web']);
        
        $user->assignRole($role);

        return back()->with('success', 'Employee created successfully.');
    }

    public function update(Request $request, User $user)
    {
        if ($user->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => 'required|string|max:20',
            'password' => 'nullable|string|min:8',
            'branch_id' => ['nullable', Rule::exists('branches', 'id')->where('company_id', auth()->user()->company_id)],
            'role' => ['required', 'string', Rule::exists('roles', 'name')->where('company_id', auth()->user()->company_id)],
            'is_active' => 'boolean'
        ]);

        $roleName = $validated['role'];
        unset($validated['role']);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        setPermissionsTeamId(auth()->user()->company_id);
        
        $role = \Spatie\Permission\Models\Role::firstOrCreate([
            'name' => $roleName,
            'company_id' => auth()->user()->company_id
        ], ['guard_name' => 'web']);
        
        $user->syncRoles([$role]);

        return back()->with('success', 'Employee updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        if ($user->is_company_owner) {
            return back()->with('error', 'Cannot delete a company owner.');
        }

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete yourself.');
        }

        $user->delete();

        return back()->with('success', 'Employee deleted successfully.');
    }
}
