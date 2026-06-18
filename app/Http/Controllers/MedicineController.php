<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Medicine;
use App\Models\MedicineCategory;
use App\Models\Unit;
use Inertia\Inertia;

class MedicineController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $medicines = Medicine::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('generic_name', 'like', "%{$search}%")
                      ->orWhere('company_name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Medicines/Index', [
            'medicines' => $medicines,
            'filters' => ['search' => $search],
        ]);
    }

    public function create()
    {
        return Inertia::render('Medicines/Form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'generic_name' => 'nullable|string|max:191',
            'type' => 'nullable|string|max:100',
            'strength' => 'nullable|string|max:100',
            'company_name' => 'nullable|string|max:191',
            'buy_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'vat_percent' => 'nullable|numeric|min:0|max:100',
            'reorder_level' => 'nullable|integer|min:0',
            'barcode' => 'nullable|string|unique:erp.medicines,barcode',
            'is_active' => 'boolean',
        ]);

        Medicine::create($validated);

        return redirect()->route('medicines.index')->with('success', 'Medicine added successfully.');
    }

    public function edit(Medicine $medicine)
    {
        return Inertia::render('Medicines/Form', [
            'medicine' => $medicine,
        ]);
    }

    public function update(Request $request, Medicine $medicine)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'generic_name' => 'nullable|string|max:191',
            'type' => 'nullable|string|max:100',
            'strength' => 'nullable|string|max:100',
            'company_name' => 'nullable|string|max:191',
            'buy_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'vat_percent' => 'nullable|numeric|min:0|max:100',
            'reorder_level' => 'nullable|integer|min:0',
            'barcode' => 'nullable|string|unique:erp.medicines,barcode,' . $medicine->id,
            'is_active' => 'boolean',
        ]);

        $medicine->update($validated);

        return redirect()->route('medicines.index')->with('success', 'Medicine updated successfully.');
    }

    public function destroy(Medicine $medicine)
    {
        $medicine->delete();

        return redirect()->route('medicines.index')->with('success', 'Medicine deleted successfully.');
    }
}
