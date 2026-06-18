<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ScopeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$scopes): Response
    {
        if (!auth()->check()) {
            abort(403);
        }

        $userScope = auth()->user()->data_scope;

        if (!empty($scopes) && !in_array($userScope, $scopes)) {
            abort(403, 'Your data scope level does not permit access to this resource.');
        }

        return $next($request);
    }
}
