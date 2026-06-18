<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FeatureFlag;
use Inertia\Inertia;

class FeatureFlagController extends Controller
{
    public function index()
    {
        $featureFlags = FeatureFlag::latest()->paginate(20);

        return Inertia::render('Admin/FeatureFlags/Index', [
            'featureFlags' => $featureFlags
        ]);
    }
}
