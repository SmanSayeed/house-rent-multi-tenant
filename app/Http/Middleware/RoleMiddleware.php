<?php

namespace App\Http\Middleware;

use App\Helpers\ResponseHelper;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            if ($request->expectsJson()) {
                return ResponseHelper::unauthorized('Authentication required');
            }
            return redirect()->route('login');
        }

        // Check if user has the required role
        $user = auth()->user();
        $hasRole = false;

        switch ($role) {
            case 'admin':
                $hasRole = $user->isAdmin();
                $loginRoute = 'admin.login';
                break;
            case 'house_owner':
                $hasRole = $user->isHouseOwner();
                $loginRoute = 'house-owner.login';
                break;
            case 'tenant':
                $hasRole = $user->isTenant();
                $loginRoute = 'tenant.login';
                break;
            default:
                $hasRole = false;
                $loginRoute = 'login';
        }

        if (!$hasRole) {
            if ($request->expectsJson()) {
                return ResponseHelper::forbidden("{$role} access required");
            }
            abort(403, "{$role} access required");
        }

        return $next($request);
    }
}
