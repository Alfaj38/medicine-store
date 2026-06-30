<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\SubscriptionService;

class EnsureActiveSubscription
{
    protected SubscriptionService $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only check tenant routes (where the tenant is resolved)
        if (app()->bound('tenant')) {
            $tenant = app('tenant');
            
            // If the request is mutating data (POST, PUT, PATCH, DELETE)
            if (!$request->isMethodSafe()) {
                
                // Allow logout, login, or subscription upgrades
                if ($request->is('logout') || $request->is('login') || $request->is('company/subscription*')) {
                    return $next($request);
                }

                // Check if subscription is active
                if (!$this->subscriptionService->isSubscriptionActive($tenant)) {
                    // For Inertia or AJAX requests, return a specific response or throw a 403
                    if ($request->wantsJson() || $request->header('X-Inertia')) {
                        abort(403, 'Your subscription has expired. Your account is in Read-Only mode. Please upgrade to create or edit records.');
                    }
                    
                    abort(403, 'Your subscription has expired. Your account is in Read-Only mode.');
                }
            }
        }

        return $next($request);
    }
}
