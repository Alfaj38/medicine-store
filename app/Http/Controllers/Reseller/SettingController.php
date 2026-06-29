<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    public function index()
    {
        $reseller = Auth::guard('reseller')->user();
        
        return Inertia::render('Reseller/Settings/Index', [
            'reseller' => $reseller
        ]);
    }

    public function update(Request $request)
    {
        $reseller = Auth::guard('reseller')->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'bank_info' => 'nullable|array',
        ]);

        $reseller->update($validated);

        return back()->with('success', 'Settings updated successfully.');
    }
}
