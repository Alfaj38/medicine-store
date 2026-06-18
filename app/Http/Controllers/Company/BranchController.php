<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Branch;
use Inertia\Inertia;

class BranchController extends Controller
{
    public function index()
    {
        $companyId = auth()->user()->company_id;
        
        $branches = Branch::where('company_id', $companyId)
            ->withCount('users')
            ->latest()
            ->get();

        return Inertia::render('Company/Branches/Index', [
            'branches' => $branches
        ]);
    }
}
