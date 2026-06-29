<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Prescription;

class PrescriptionController extends Controller
{
    public function index(Request $request)
    {
        $scope = app('data_scope');
        
        $query = Prescription::query();
        $query = $scope->apply($query, auth()->user(), Prescription::class)->with('media');

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        } else if (!$request->has('status')) {
            $query->whereIn('status', ['pending', 'processing']);
        }

        $prescriptions = $query->latest()->paginate(20)->withQueryString()->through(function($p) {
            return [
                'id' => $p->id,
                'patient_name' => $p->patient_name,
                'patient_phone' => $p->patient_phone,
                'patient_address' => $p->patient_address,
                'notes' => $p->notes,
                'status' => $p->status,
                'created_at' => $p->created_at->diffForHumans(),
                'image_url' => $p->getFirstMediaUrl('prescription') ?: null,
            ];
        });

        return Inertia::render('Prescriptions/Index', [
            'prescriptions' => $prescriptions,
            'filters' => $request->only(['status']),
        ]);
    }

    public function update(Request $request, $id)
    {
        $scope = app('data_scope');
        $query = Prescription::query();
        $query = $scope->apply($query, auth()->user(), Prescription::class);
        $prescription = $query->findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,processing,completed,rejected',
        ]);

        $prescription->update(['status' => $validated['status']]);

        return redirect()->back()->with('success', 'Prescription status updated!');
    }
}
