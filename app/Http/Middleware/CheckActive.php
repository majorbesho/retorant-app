<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && !auth()->user()->is_active) {
            $allowedRoutes = ['auth.waiting', 'logout', 'locale.switch'];

            if (!in_array($request->route()->getName(), $allowedRoutes)) {
                return redirect()->route('auth.waiting');
            }
        }

        return $next($request);
    }
}
