<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!empty(Auth::check())) {
            $userType = Auth::user()->user_type;
            // user_type 0 (super_admin), 1 (admin) ET rôles custom (>= 5) uniquement.
            // Les teachers (2), students (3) et parents (4) sont exclus.
            if ($userType === 0 || $userType === 1 || $userType >= 5) {
                return $next($request);
            }
            Auth::logout();
            return redirect(url(''));
        }

        Auth::logout();
        return redirect(url(''));
    }
}
