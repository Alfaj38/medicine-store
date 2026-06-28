<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $authData = null;

        if ($request->user()) {
            $user = $request->user();
            $permissions = [];
            
            if ($user instanceof \App\Models\User) {
                $user->loadMissing(['roles', 'company.subscription.plan']);
                
                // Phase 10: Permission Optimization - Cache DB checks for 1 hour
                $cacheKey = "user_{$user->id}_auth_data";
                $cachedAuth = \Illuminate\Support\Facades\Cache::remember($cacheKey, now()->addHour(), function () use ($user) {
                    $permissions = [];
                    if (method_exists($user, 'getAllPermissions')) {
                        $permissions = $user->getAllPermissions()->pluck('name')->toArray();
                    }

                    return [
                        'permissions' => $permissions,
                        'branches' => $user->getAllowedBranchIds(),
                        'scope' => $user->data_scope,
                    ];
                });

                $authData = [
                    'user' => collect($user)->except(['password', 'remember_token']),
                    'scope' => $cachedAuth['scope'],
                    'permissions' => $cachedAuth['permissions'],
                    'branches' => $cachedAuth['branches'],
                ];
            } else {
                // For Resellers or other models
                $authData = [
                    'user' => collect($user)->except(['password', 'remember_token']),
                    'scope' => 'all',
                    'permissions' => [],
                    'branches' => [],
                ];
            }
        }

        // Alert counts shared only for management users (used in admin sidebar/header)
        $pendingBranches = 0;
        $newLeads        = 0;
        if ($request->user() && $request->user() instanceof \App\Models\User && $request->user()->isManagement()) {
            $pendingBranches = \App\Models\Branch::where('approval_status', 'pending')->count();
            $newLeads        = \App\Models\DemoRequest::where('status', 'pending')->count();
        }

        return [
            ...parent::share($request),
            'auth'            => $authData,
            'pendingBranches' => $pendingBranches,
            'newLeads'        => $newLeads,
            'flash' => [
                'success'     => fn () => $request->session()->get('success'),
                'error'       => fn () => $request->session()->get('error'),
                'newCustomer' => fn () => $request->session()->get('newCustomer'),
            ],
        ];
    }
}
