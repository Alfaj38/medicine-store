<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Company;
use App\Models\Bank;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $company = Company::with(['bankAccounts', 'documents'])->find(auth()->user()->company_id);
        $banks = Bank::with(['branches' => function ($query) {
            $query->select('id', 'bank_id', 'name', 'routing_number')->where('is_active', true);
        }])->where('is_active', true)->orderBy('name')->get(['id', 'name']);
        
        return Inertia::render('Company/Settings/Index', [
            'company' => $company,
            'banks' => $banks
        ]);
    }

    public function update(Request $request)
    {
        $company = Company::find(auth()->user()->company_id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'website' => 'nullable|url|max:255',
            'alternative_phone' => 'nullable|string|max:20',
            'business_type' => 'nullable|string|max:255',
            'year_established' => 'nullable|integer|min:1800|max:' . date('Y'),
            'registration_number' => 'nullable|string|max:255',
            'tax_id' => 'nullable|string|max:255',
            'trade_license_number' => 'nullable|string|max:255',
            'vat_registration_number' => 'nullable|string|max:255',
            'about_company' => 'nullable|string',
        ]);

        if ($request->hasFile('logo')) {
            $request->validate([
                'logo' => 'image|mimes:jpeg,png,jpg,svg|max:2048',
            ]);

            if ($company->logo) {
                Storage::disk('public')->delete($company->logo);
            }

            $path = $request->file('logo')->store('logos', 'public');
            $validated['logo'] = $path;
        }

        $company->update($validated);

        return back()->with('success', 'Company profile updated successfully.');
    }

    public function export()
    {
        $company = Company::with(['branches'])->find(auth()->user()->company_id);
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.company_profile', compact('company'));
        return $pdf->download($company->slug . '-profile.pdf');
    }
}
