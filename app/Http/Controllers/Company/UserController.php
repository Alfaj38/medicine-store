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
        
        $users = User::with(['branch'])
            ->where('company_id', $companyId)
            ->when($request->search, fn ($q, $search) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
            ->latest()
            ->paginate(15);

        $branches = Branch::where('company_id', $companyId)->where('is_active', true)->get(['id', 'name']);

        return Inertia::render('Company/Users/Index', [
            'users' => $users,
            'branches' => $branches,
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
            'user_type' => 'required|string|in:Manager,Pharmacist,Cashier',
            'is_active' => 'boolean'
        ]);

        $validated['company_id'] = auth()->user()->company_id;
        $validated['password'] = Hash::make($validated['password']);
        $validated['is_company_owner'] = false;

        User::create($validated);

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
            'user_type' => 'required|string|in:Manager,Pharmacist,Cashier',
            'is_active' => 'boolean'
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

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
