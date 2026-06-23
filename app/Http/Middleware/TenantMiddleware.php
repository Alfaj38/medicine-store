<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Company;

class TenantMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->company) {
            app()->instance('tenant', auth()->user()->company);
        } else {
            $host = $request->getHost();
            
            // Extract subdomain (e.g. "squarepharma" from "squarepharma.saasmedi.com" or "squarepharma.localhost")
            $parts = explode('.', $host);
            
            if (count($parts) >= 2 && $parts[0] !== 'www') {
                $slug = $parts[0];
                
                // Check if tenant exists
                $company = Company::where('slug', $slug)->first();
                
                if ($company) {
                    app()->instance('tenant', $company);
                }
            }
        }

        return $next($request);
    }
}
