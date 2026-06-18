<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Inertia\Inertia;

/**
 * CheckPermission — vérifie qu'un utilisateur a la permission Spatie requise.
 *
 * Usage dans les routes :
 *   Route::get('admin/student/list', ...)->middleware('check_perm:view.users.students');
 *
 * Le super_admin (user_type=0) passe toujours.
 */
class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        // Super admin : accès total
        if ($user->user_type === 0) {
            return $next($request);
        }

        // Vérification Spatie
        if ($user->can($permission)) {
            return $next($request);
        }

        // Refus — Inertia ou JSON
        if ($request->wantsJson() || $request->header('X-Inertia')) {
            return Inertia::render('Errors/403', [
                'message' => "Veuillez contacter votre administrateur.",
            ])->toResponse($request)->setStatusCode(403);
        }

        abort(403, "Accès refusé. Veuillez contacter votre administrateur.");
    }
}
