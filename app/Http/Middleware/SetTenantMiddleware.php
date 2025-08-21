<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetTenantMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Set tenant based on logged in user
        if (Auth::check() && Auth::user()->tenant_id) {
            // Set tenant in session if not already set
            if (!session()->has('tenant_id') || session('tenant_id') !== Auth::user()->tenant_id) {
                session(['tenant_id' => Auth::user()->tenant_id]);
            }
        }

        return $next($request);
    }
}
