<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Supplier;
use Inertia\Inertia;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $suppliers = Supplier::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('companies', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Suppliers/Index', [
            'suppliers' => $suppliers,
            'filters' => ['search' => $search],
        ]);
    }

    public function create()
    {
        $industries = \App\Models\PharmaceuticalIndustry::orderBy('name')->pluck('name');
        return Inertia::render('Suppliers/Form', [
            'industries' => $industries,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'companies' => 'nullable|array',
            'companies.*' => 'string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20|unique:suppliers,phone,NULL,id,company_id,' . auth()->user()->company_id,
            'address' => 'nullable|string',
            'opening_balance' => 'numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['current_balance'] = $validated['opening_balance'] ?? 0;

        Supplier::create($validated);

        return redirect()->route('suppliers.index')->with('success', 'Supplier created successfully.');
    }

    public function edit(Supplier $supplier)
    {
        $industries = \App\Models\PharmaceuticalIndustry::orderBy('name')->pluck('name');
        return Inertia::render('Suppliers/Form', [
            'supplier' => $supplier,
            'industries' => $industries,
        ]);
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'companies' => 'nullable|array',
            'companies.*' => 'string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20|unique:suppliers,phone,' . $supplier->id . ',id,company_id,' . auth()->user()->company_id,
            'address' => 'nullable|string',
            'opening_balance' => 'numeric|min:0',
            'is_active' => 'boolean',
        ]);

        // Recalculate current balance if opening balance changes
        $diff = ($validated['opening_balance'] ?? 0) - $supplier->opening_balance;
        if ($diff != 0) {
            $validated['current_balance'] = $supplier->current_balance + $diff;
        }

        $supplier->update($validated);

        return redirect()->route('suppliers.index')->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->current_balance > 0) {
            return back()->with('error', 'Cannot delete supplier with an outstanding balance.');
        }
        
        $supplier->delete();

        return redirect()->route('suppliers.index')->with('success', 'Supplier deleted successfully.');
    }
}
