<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPagePermission
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $page): Response
    {
        if (!auth()->check()) {
            abort(403);
        }

        $user = auth()->user();

        if ($user->isCompanyOwner() || $user->isSuperAdmin()) {
            return $next($request);
        }

        $roles = $user->roles()->pluck('id');
        
        $permission = \App\Models\PagePermission::whereIn('role_id', $roles)
            ->where('page', $page)
            ->first();

        if (!$permission) {
            abort(403, "You do not have permission to access the {$page} module.");
        }

        $method = $request->method();
        
        if ($method === 'GET' && !$permission->can_view) {
            abort(403, 'View permission denied.');
        } elseif ($method === 'POST' && !$permission->can_create) {
            abort(403, 'Create permission denied.');
        } elseif (in_array($method, ['PUT', 'PATCH']) && !$permission->can_edit) {
            abort(403, 'Edit permission denied.');
        } elseif ($method === 'DELETE' && !$permission->can_delete) {
            abort(403, 'Delete permission denied.');
        }

        return $next($request);
    }
}
