<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * SuperAdminMiddleware — user_type = 0
 * Accès exclusif aux routes de configuration système (rôles, permissions).
 */
class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->user_type === 0) {
            return $next($request);
        }

        Auth::logout();
        return redirect(url(''));
    }
}
