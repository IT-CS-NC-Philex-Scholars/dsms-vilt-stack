<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

final class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Check if the user has the required role.
     *
     * @param  string  $role  The role required to access the route.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        abort_if(! Auth::check() || ! $request->user()->hasRole($role), 403, 'Unauthorized action.');

        return $next($request);
    }
}
