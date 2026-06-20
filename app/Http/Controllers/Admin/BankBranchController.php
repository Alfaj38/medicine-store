<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankBranch;
use Illuminate\Http\Request;

class BankBranchController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'bank_id' => 'required|exists:banks,id',
            'name' => 'required|string|max:255',
            'routing_number' => 'required|string|unique:bank_branches,routing_number',
            'is_active' => 'boolean'
        ]);

        BankBranch::create($validated);
        return back()->with('success', 'Branch created successfully.');
    }

    public function update(Request $request, BankBranch $branch)
    {
        $validated = $request->validate([
            'bank_id' => 'required|exists:banks,id',
            'name' => 'required|string|max:255',
            'routing_number' => 'required|string|unique:bank_branches,routing_number,' . $branch->id,
            'is_active' => 'boolean'
        ]);

        $branch->update($validated);
        return back()->with('success', 'Branch updated successfully.');
    }

    public function destroy(BankBranch $branch)
    {
        $branch->delete();
        return back()->with('success', 'Branch deleted successfully.');
    }
}
