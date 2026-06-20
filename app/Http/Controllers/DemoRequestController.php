<?php

namespace App\Http\Controllers;

use App\Models\DemoRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DemoRequestController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'email'              => 'required|email|max:255',
            'phone'              => 'required|string|max:30',
            'company_name'       => 'required|string|max:255',
            'branch_count'       => 'required|string',
            'current_software'   => 'nullable|array',
            'pain_points'        => 'nullable|array',
            'preferred_language' => 'nullable|string',
            'preferred_date'     => 'required|date|after:today',
            'preferred_time'     => 'required|string',
            'questions'          => 'nullable|string|max:1000',
            'referral_source'    => 'nullable|string',
        ]);

        $validated['lead_score'] = DemoRequest::calculateScore($validated);

        DemoRequest::create($validated);

        return redirect()->route('success', ['type' => 'demo', 'name' => $validated['name']]);
    }
}
