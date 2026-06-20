<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BankController extends Controller
{
    public function index()
    {
        $banks = Bank::latest()->get();
        return Inertia::render('Admin/Banks/Index', [
            'banks' => $banks
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:banks,name',
            'is_active' => 'boolean'
        ]);

        Bank::create($validated);

        return back()->with('success', 'Bank created successfully.');
    }

    public function update(Request $request, Bank $bank)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:banks,name,' . $bank->id,
            'is_active' => 'boolean'
        ]);

        $bank->update($validated);

        return back()->with('success', 'Bank updated successfully.');
    }

    public function destroy(Bank $bank)
    {
        $bank->delete();
        return back()->with('success', 'Bank deleted successfully.');
    }
}
