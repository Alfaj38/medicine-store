<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Settings/Index');
    }

    public function email()
    {
        $settings = Setting::whereIn('key', [
            'mail_host', 'mail_port', 'mail_username', 'mail_password', 'mail_encryption', 'mail_from_address', 'mail_from_name'
        ])->pluck('value', 'key');

        return Inertia::render('Admin/Settings/Email', [
            'settings' => $settings
        ]);
    }

    public function sms()
    {
        $settings = Setting::whereIn('key', [
            'sms_provider', 'sms_api_key', 'sms_sender_id'
        ])->pluck('value', 'key');

        return Inertia::render('Admin/Settings/Sms', [
            'settings' => $settings
        ]);
    }

    public function paymentGateway()
    {
        $settings = Setting::whereIn('key', [
            'stripe_key', 'stripe_secret', 'stripe_webhook_secret', 'sslcommerz_store_id', 'sslcommerz_store_password', 'sslcommerz_sandbox'
        ])->pluck('value', 'key');

        return Inertia::render('Admin/Settings/PaymentGateway', [
            'settings' => $settings
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->except(['_token']);

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return back()->with('success', 'Settings updated successfully.');
    }
}
