<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReadOnlyScopeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->data_scope === 'readonly_platform') {
            if (!in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'])) {
                abort(403, 'Your account is in read-only mode.');
            }
        }

        return $next($request);
    }
}
