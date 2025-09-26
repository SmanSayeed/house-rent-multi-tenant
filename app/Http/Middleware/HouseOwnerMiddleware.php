<?php

namespace App\Http\Middleware;

use App\Helpers\ResponseHelper;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HouseOwnerMiddleware
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
            return redirect()->route('house-owner.login');
        }

        // Check if user is house owner
        if (!auth()->user()->isHouseOwner()) {
            if ($request->expectsJson()) {
                return ResponseHelper::forbidden('House owner access required');
            }
            abort(403, 'House owner access required');
        }

        return $next($request);
    }
}
