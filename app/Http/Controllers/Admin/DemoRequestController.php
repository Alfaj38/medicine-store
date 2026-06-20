<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DemoRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DemoRequestController extends Controller
{
    /**
     * List all demo requests (paginated).
     */
    public function index(Request $request)
    {
        $queries = $request->only(['search', 'status']);
        $demoRequests = DemoRequest::query()
            ->when($queries['search'] ?? null, function ($q, $search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%");
            })
            ->when($queries['status'] ?? null, function ($q, $status) {
                $q->where('status', $status);
            })
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/DemoRequests/Index', [
            'demoRequests' => $demoRequests,
            'filters' => $queries,
        ]);
    }

    /**
     * Show a single demo request.
     */
    public function show(DemoRequest $demoRequest)
    {
        return Inertia::render('Admin/DemoRequests/Show', [
            'demoRequest' => $demoRequest,
        ]);
    }

    /**
     * Update the status of a demo request.
     */
    public function update(Request $request, DemoRequest $demoRequest)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,completed,no_show',
        ]);
        $demoRequest->update(['status' => $validated['status']]);
        return redirect()->back()->with('success', 'Demo request status updated.');
    }

    /**
     * Delete a demo request.
     */
    public function destroy(DemoRequest $demoRequest)
    {
        $demoRequest->delete();
        return redirect()->route('admin.demo-requests.index')
            ->with('success', 'Demo request removed.');
    }
}
