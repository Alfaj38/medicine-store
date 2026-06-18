<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return Inertia::render('Auth/Login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'login_type' => ['required', 'in:management,company'],
        ]);

        $attemptCredentials = [
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ];

        if (Auth::attempt($attemptCredentials, $request->boolean('remember'))) {
            $user = Auth::user();

            if ($user->user_type !== $credentials['login_type']) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Access denied. You do not have permission to log in to this portal.',
                ])->onlyInput('email');
            }

            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Your account is inactive. Please contact your administrator.',
                ])->onlyInput('email');
            }

            if ($user->user_type === 'company' && $user->company) {
                if ($user->company->registration_status === 'suspended') {
                    Auth::logout();
                    return back()->withErrors([
                        'email' => 'Your company account is suspended. Please contact platform administration.',
                    ])->onlyInput('email');
                }
                if ($user->company->registration_status === 'pending') {
                    Auth::logout();
                    return back()->withErrors([
                        'email' => 'Your company account is pending approval.',
                    ])->onlyInput('email');
                }
            }

            $request->session()->regenerate();

            if ($user->user_type === 'management') {
                return redirect()->intended('/admin/dashboard');
            }

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
