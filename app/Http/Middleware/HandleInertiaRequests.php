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
            $permissions = [];
            
            if ($request->user()->roles && $request->user()->roles->count() > 0) {
                $roleId = $request->user()->roles->first()->id;
                $permissions = \App\Models\PagePermission::where('role_id', $roleId)
                    ->get()
                    ->keyBy('page')
                    ->toArray();
            }

            $authData = [
                'user' => collect($request->user())->except(['password', 'remember_token']),
                'scope' => $request->user()->data_scope,
                'permissions' => $permissions,
                'branches' => $request->user()->getAllowedBranchIds(),
            ];
        }

        // Alert counts shared only for management users (used in admin sidebar/header)
        $pendingBranches = 0;
        $newLeads        = 0;
        if ($request->user() && $request->user()->isManagement()) {
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
