<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AccountController extends Controller
{
    public function index()
    {
        $scope = app('data_scope');
        $query = Account::query();
        $query = $scope->apply($query, auth()->user(), Account::class);
        
        $accounts = $query->latest()->get();

        return Inertia::render('Accounting/Accounts/Index', [
            'accounts' => $accounts,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:income,expense,asset,liability',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',
        ]);

        $validated['company_id'] = auth()->user()->company_id;
        
        Account::create($validated);

        return redirect()->back()->with('success', 'Account created successfully');
    }

    public function update(Request $request, Account $account)
    {
        if ($account->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:income,expense,asset,liability',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',
        ]);

        $account->update($validated);

        return redirect()->back()->with('success', 'Account updated successfully');
    }

    public function destroy(Account $account)
    {
        if ($account->company_id !== auth()->user()->company_id) {
            abort(403);
        }
        
        // Ensure no transactions exist before deleting
        if ($account->transactions()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete account with existing transactions.');
        }
        
        $account->delete();

        return redirect()->back()->with('success', 'Account deleted successfully');
    }
}
