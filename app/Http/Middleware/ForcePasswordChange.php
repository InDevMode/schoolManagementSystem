<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForcePasswordChange
{
    /**
     * Routes exemptées du contrôle (changement forcé + déconnexion).
     */
    private const ALLOWED_ROUTES = [
        'force-change-password',
        'force-change-password.update',
        'logout',
    ];

    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && (int) ($user->must_change_password ?? 0) === 1) {
            // Autoriser uniquement les routes ci-dessus + assets statiques
            if (!in_array($request->route()?->getName(), self::ALLOWED_ROUTES, true)) {
                return redirect()->route('force-change-password');
            }
        }

        return $next($request);
    }
}
