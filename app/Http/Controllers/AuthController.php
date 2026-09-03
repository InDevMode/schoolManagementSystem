<?php

namespace App\Http\Controllers;

use App\Mail\ForgotPasswordMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function login()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->user_type);
        }

        return Inertia::render('Auth/Login');
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();

            if ($user->status != 1) {
                Auth::logout();
                return back()->with('error', "Cet utilisateur n'est pas activé.");
            }

            // Auth::attempt() a déjà hydraté $user — on met à jour directement sans SELECT supplémentaire
            $user->last_login = now();
            $user->save();
            $request->session()->regenerate();

            return $this->redirectByRole($user->user_type);
        }

        return back()->with('error', 'Email ou mot de passe incorrect.');
    }

    public function forgotPassword()
    {
        return Inertia::render('Auth/ForgotPassword');
    }

    public function changePassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::getEmailSingle($request->email);

        if ($user) {
            $user->remember_token = Str::random(30);
            $user->save();
            Mail::to($user->email)->send(new ForgotPasswordMail($user));
            return back()->with('success', 'Vérifiez votre boîte mail pour réinitialiser votre mot de passe.');
        }

        return back()->with('error', 'Aucun compte trouvé avec cet email.');
    }

    public function resetPassword(string $token)
    {
        $user = User::getTokenSingle($token);
        abort_unless($user, 404);

        return Inertia::render('Auth/ResetPassword', ['token' => $token]);
    }

    public function resetAndChangePassword(Request $request, string $token)
    {
        $request->validate([
            'password'     => 'required|string|min:6',
            'confPassword' => 'required|same:password',
        ]);

        $user = User::getTokenSingle($token);
        abort_unless($user, 404);

        $user->password       = Hash::make($request->password);
        $user->remember_token = null; // Invalider le token après usage
        $user->save();

        return redirect('/login')->with('success', 'Mot de passe réinitialisé avec succès. Connectez-vous.');
    }

    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/login');
    }

    /**
     * Affiche la page de changement forcé de mot de passe.
     */
    public function forceChangePasswordForm()
    {
        // Si l'utilisateur n'a pas à changer son mot de passe, on le redirige
        if (!Auth::check() || (int) (Auth::user()->must_change_password ?? 0) !== 1) {
            return $this->redirectByRole(Auth::user()->user_type);
        }

        return Inertia::render('Auth/ForceChangePassword', [
            'user' => [
                'name'      => Auth::user()->name,
                'last_name' => Auth::user()->last_name,
                'email'     => Auth::user()->email,
            ],
        ]);
    }

    /**
     * Traite le changement forcé de mot de passe.
     */
    public function forceChangePasswordUpdate(Request $request)
    {
        $request->validate([
            'password'     => 'required|string|min:6',
            'confPassword' => 'required|same:password',
        ]);

        $user = User::find(Auth::id());
        abort_unless($user, 404);

        $user->password             = Hash::make($request->password);
        $user->must_change_password = 0;
        $user->save();

        return redirect($this->redirectByRole($user->user_type)->getTargetUrl())
            ->with('success', 'Votre mot de passe a été mis à jour avec succès. Bienvenue !');
    }

    private function redirectByRole(int $userType)
    {
        return match (true) {
            $userType === 0          => redirect('/superadmin/dashboard'),
            $userType === 1          => redirect('/admin/dashboard'),
            $userType === 2          => redirect('/teacher/dashboard'),
            $userType === 3          => redirect('/student/dashboard'),
            $userType === 4          => redirect('/parent/dashboard'),
            $userType >= 5           => redirect('/admin/dashboard'), // rôles custom
            default                  => redirect('/login'),
        };
    }
}
