<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;


class OnlineUser
{
    /**
     * Handle an incoming request.
     *
     * Met à jour le statut "en ligne" via le cache uniquement.
     * Le last_login est écrit une seule fois lors de la connexion (AuthController).
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            // Cache uniquement — pas de SELECT ni d'UPDATE en base sur chaque requête
            Cache::put('OnlineUser.' . Auth::id(), true, now()->addMinutes(2));
        }
        return $next($request);
    }
}
