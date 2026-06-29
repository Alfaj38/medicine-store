<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContactMessageController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');

        $messages = ContactMessage::query()
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Company/ContactMessages/Index', [
            'messages' => $messages,
            'filters' => $request->only(['status']),
        ]);
    }

    public function show(ContactMessage $contactMessage)
    {
        if ($contactMessage->status === 'unread') {
            $contactMessage->update(['status' => 'read']);
        }

        return redirect()->back(); // The actual data is loaded in the index view modal, this is just to mark as read if needed via API/POST, or we can just update status when they open it via a patch request.
    }

    public function updateStatus(Request $request, ContactMessage $contactMessage)
    {
        $validated = $request->validate([
            'status' => 'required|in:unread,read,replied',
            'reply_message' => 'nullable|string',
        ]);

        $contactMessage->update($validated);

        return redirect()->back()->with('success', 'Message status updated.');
    }

    public function destroy(ContactMessage $contactMessage)
    {
        $contactMessage->delete();

        return redirect()->back()->with('success', 'Message deleted successfully.');
    }
}
