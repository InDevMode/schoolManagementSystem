<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ParentMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect(url('login'));
        }

        $user = Auth::user();

        if ((int) $user->user_type === 4 && (int) $user->status === 1) {
            return $next($request);
        }

        Auth::logout();
        return redirect(url('login'))->with('error', 'Accès refusé ou compte désactivé.');
    }
}
