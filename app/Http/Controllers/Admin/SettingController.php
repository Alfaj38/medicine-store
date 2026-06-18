<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingController extends Controller
{
    public function index()
    {
        // Settings could be loaded from a settings table or config
        $settings = [];

        return Inertia::render('Admin/Settings/Index', [
            'settings' => $settings
        ]);
    }
}
