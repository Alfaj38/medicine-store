<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class MarketingController extends Controller
{
    public function index()
    {
        $reseller = Auth::guard('reseller')->user();
        return Inertia::render('Reseller/Marketing/Index');
    }

    public function downloadBrandKit()
    {
        // For development, we return a generated dummy text file
        // In production, this would be: return Storage::download('marketing/brand-kit.zip');
        return response()->streamDownload(function () {
            echo "SaaSMedi Official Brand Kit\n\nLogo Guidelines:\n- Primary Color: Emerald Green (#00b67a)\n- Font: Inter\n";
        }, 'SaaSMedi_Brand_Kit.txt');
    }

    public function downloadSocialPack()
    {
        return response()->streamDownload(function () {
            echo "SaaSMedi Social Media Pack\n\nContains banners and images for Facebook, Twitter, and LinkedIn.\n";
        }, 'SaaSMedi_Social_Pack.txt');
    }

    public function viewEmailTemplates()
    {
        return response()->streamDownload(function () {
            echo "Email Subject: Upgrade Your Pharmacy Management Today\n\nHi [Name],\nAre you tired of manually tracking inventory?\nSaaSMedi can help you...\n";
        }, 'SaaSMedi_Email_Templates.txt');
    }
}
