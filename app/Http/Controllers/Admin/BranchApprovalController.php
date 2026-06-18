<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Branch;
use Inertia\Inertia;

class BranchApprovalController extends Controller
{
    public function index()
    {
        $branches = Branch::where('approval_status', 'pending')
            ->with('company')
            ->latest()
            ->paginate(15);

        return Inertia::render('Admin/BranchApprovals/Index', [
            'branches' => $branches
        ]);
    }

    public function approve(Branch $branch)
    {
        $branch->update([
            'approval_status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Branch approved successfully.');
    }

    public function reject(Branch $branch)
    {
        $branch->update([
            'approval_status' => 'rejected',
        ]);

        return back()->with('success', 'Branch rejected.');
    }
}
