<?php

namespace App\Http\Middleware;

use App\Models\School;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * EnsureSchoolActive — bloque l'accès si l'école de l'utilisateur est désactivée.
 *
 * Appliqué globalement via le groupe "web" ou sur les middlewares de rôle.
 * Le super admin (user_type = 0) est toujours autorisé.
 * Les utilisateurs sans school_id (super admin) passent toujours.
 */
class EnsureSchoolActive
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        // Super admin — pas d'école à vérifier
        if ((int) $user->user_type === 0 || ! $user->school_id) {
            return $next($request);
        }

        $school = School::find($user->school_id);

        if (! $school || (int) $school->status !== 1 || (int) $school->is_delete !== 0) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect(url('login'))
                ->with('error', 'Votre école est actuellement désactivée. Contactez l\'administrateur système.');
        }

        return $next($request);
    }
}
