<?php

namespace App\Http\Controllers\Admin\Crm;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LeadController extends Controller
{
    public function index()
    {
        $leads = Lead::latest()->get();

        // Group leads by status for the Kanban board
        $kanbanBoard = [
            'new' => $leads->where('status', 'new')->values(),
            'contacted' => $leads->where('status', 'contacted')->values(),
            'demo_scheduled' => $leads->where('status', 'demo_scheduled')->values(),
            'in_trial' => $leads->where('status', 'in_trial')->values(),
            'converted' => $leads->where('status', 'converted')->values(),
            'lost' => $leads->where('status', 'lost')->values(),
        ];

        return Inertia::render('Admin/Crm/Leads/Index', [
            'groupedLeads' => $kanbanBoard
        ]);
    }

    public function updateStatus(Request $request, Lead $lead)
    {
        $request->validate([
            'status' => 'required|in:new,contacted,demo_scheduled,in_trial,converted,lost'
        ]);

        $lead->update([
            'status' => $request->status
        ]);

        return back()->with('success', 'Lead status updated.');
    }

    public function storeNote(Request $request, Lead $lead)
    {
        $request->validate([
            'notes' => 'required|string'
        ]);

        $lead->update([
            'notes' => $request->notes
        ]);

        return back()->with('success', 'Notes saved.');
    }
}
