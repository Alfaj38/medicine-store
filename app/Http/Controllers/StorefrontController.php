<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Inertia\Inertia;

class StorefrontController extends Controller
{
    public function show(Company $company)
    {
        if (!$company->isActive()) {
            abort(404);
        }

        $company->load(['branches']);

        return Inertia::render('Storefront/Show', [
            'company' => $company
        ]);
    }
}
