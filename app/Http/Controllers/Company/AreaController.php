<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Area;
use Inertia\Inertia;

class AreaController extends Controller
{
    public function index()
    {
        $companyId = auth()->user()->company_id;
        
        $areas = Area::where('company_id', $companyId)
            ->withCount('branches')
            ->get();

        return Inertia::render('Company/Areas/Index', [
            'areas' => $areas
        ]);
    }
}
