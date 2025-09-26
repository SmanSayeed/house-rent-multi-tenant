<?php

namespace App\Http\Middleware;

use App\Helpers\ResponseHelper;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
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
            return redirect()->route('admin.login');
        }

        // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            if ($request->expectsJson()) {
                return ResponseHelper::forbidden('Admin access required');
            }
            abort(403, 'Admin access required');
        }

        return $next($request);
    }
}
