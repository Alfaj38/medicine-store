<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ResellerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('reseller')->check()) {
            return redirect()->route('reseller.login');
        }

        $reseller = Auth::guard('reseller')->user();

        if ($reseller->status !== 'approved') {
            if ($request->routeIs('reseller.pending')) {
                return $next($request);
            }
            return redirect()->route('reseller.pending');
        }

        return $next($request);
    }
}
