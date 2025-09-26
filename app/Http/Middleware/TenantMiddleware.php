<?php

namespace App\Http\Middleware;

use App\Helpers\ResponseHelper;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            if ($request->expectsJson()) {
                return ResponseHelper::unauthorized('Authentication required');
            }
            return redirect()->route('tenant.login');
        }

        // Check if user is tenant
        if (!auth()->user()->isTenant()) {
            if ($request->expectsJson()) {
                return ResponseHelper::forbidden('Tenant access required');
            }
            abort(403, 'Tenant access required');
        }

        return $next($request);
    }
}
