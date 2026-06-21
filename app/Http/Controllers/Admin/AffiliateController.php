<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Reseller;
use App\Services\PromoCodeService;

class AffiliateController extends Controller
{
    public function applications()
    {
        $applications = Reseller::where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return Inertia::render('Admin/Affiliate/Applications/Index', [
            'applications' => $applications
        ]);
    }

    public function approve(Request $request, $id, PromoCodeService $service)
    {
        $reseller = Reseller::findOrFail($id);
        
        $reseller->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now()
        ]);

        // Auto-generate their first promo code
        $service->generateCode($reseller, 'Primary Code');

        // TODO: Send Approval Email

        return back()->with('success', 'Reseller application approved.');
    }

    public function reject(Request $request, $id)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $reseller = Reseller::findOrFail($id);
        
        $reseller->update([
            'status' => 'rejected',
            'notes' => $validated['reason']
        ]);

        // TODO: Send Rejection Email

        return back()->with('success', 'Reseller application rejected.');
    }

    public function resellers()
    {
        $resellers = Reseller::where('status', '!=', 'pending')
            ->withCount('promoCodes')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return Inertia::render('Admin/Affiliate/Resellers/Index', [
            'resellers' => $resellers
        ]);
    }

    public function show($id)
    {
        $reseller = Reseller::with(['promoCodes', 'commissions.company', 'walletTransactions'])
            ->findOrFail($id);

        return Inertia::render('Admin/Affiliate/Resellers/Show', [
            'reseller' => $reseller
        ]);
    }
}
