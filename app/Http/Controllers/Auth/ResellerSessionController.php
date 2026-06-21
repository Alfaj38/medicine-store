<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ResellerSessionController extends Controller
{
    public function create()
    {
        return Inertia::render('Reseller/Login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('reseller')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $reseller = Auth::guard('reseller')->user();

            if ($reseller->status === 'pending') {
                return redirect()->route('reseller.pending');
            }

            if ($reseller->status !== 'approved') {
                Auth::guard('reseller')->logout();
                return back()->withErrors([
                    'email' => 'Your account is suspended or rejected.',
                ])->onlyInput('email');
            }

            return redirect()->intended('/reseller/dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function destroy(Request $request)
    {
        Auth::guard('reseller')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
