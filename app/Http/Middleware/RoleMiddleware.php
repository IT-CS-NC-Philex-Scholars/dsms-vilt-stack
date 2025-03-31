<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Check if the user has the required role.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string $role The role required to access the route.
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Check if user is authenticated and has the required role
        // Assumes your User model has a 'role' attribute/column
        if (!Auth::check() || !$request->user()->hasRole($role)) {
            // You might want to redirect to a specific route or show a custom error view
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
