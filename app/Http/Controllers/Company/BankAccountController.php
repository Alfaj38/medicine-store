<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CompanyBankAccount;

class BankAccountController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'routing_number' => 'nullable|string|max:255',
            'account_type' => 'nullable|string|max:255',
            'branch_name' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ]);

        $validated['company_id'] = auth()->user()->company_id;

        CompanyBankAccount::create($validated);

        return back()->with('success', 'Bank account added successfully.');
    }

    public function update(Request $request, CompanyBankAccount $bankAccount)
    {
        if ($bankAccount->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $validated = $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'routing_number' => 'nullable|string|max:255',
            'account_type' => 'nullable|string|max:255',
            'branch_name' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ]);

        $bankAccount->update($validated);

        return back()->with('success', 'Bank account updated successfully.');
    }

    public function destroy(CompanyBankAccount $bankAccount)
    {
        if ($bankAccount->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $bankAccount->delete();

        return back()->with('success', 'Bank account deleted successfully.');
    }
}
