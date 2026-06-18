<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SupportTicket;
use Inertia\Inertia;

class SupportTicketController extends Controller
{
    public function index(Request $request)
    {
        $tickets = SupportTicket::with(['company', 'user'])
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate(15);

        return Inertia::render('Admin/SupportTickets/Index', [
            'tickets' => $tickets,
            'filters' => $request->only(['status'])
        ]);
    }

    public function reply(Request $request, SupportTicket $ticket)
    {
        $request->validate([
            'reply' => 'required|string',
            'status' => 'nullable|string'
        ]);

        // Add reply logic here (e.g. create a SupportTicketReply model if exists)
        // For now, we just update the status
        
        $ticket->update([
            'status' => $request->status ?? 'answered'
        ]);

        return back()->with('success', 'Reply sent successfully.');
    }
}
