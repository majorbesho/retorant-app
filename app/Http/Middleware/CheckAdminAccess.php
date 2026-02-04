<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user) {
            abort(403, 'User not authenticated.');
        }

        // Allow if user is super admin
        if ($user->is_super_admin) {
            return $next($request);
        }

        // Allow if user has super_admin or restaurant_owner role
        if ($user->hasRole(['super_admin', 'restaurant_owner'])) {
            return $next($request);
        }

        abort(403, 'User does not have the right roles.');
    }
}
