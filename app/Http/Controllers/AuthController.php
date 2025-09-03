<?php

namespace App\Http\Controllers;

use App\Mail\ForgotPasswordMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login()
    {
        if (!empty(Auth::check())) {

            match (Auth::user()->user_type) {
                1 => redirect('admin/dashboard'),
                2 => redirect('teacher/dashboard'),
                3 => redirect('student/dashboard'),
                4 => redirect('parent/dashboard'),
                default => redirect(url('')),
            };

        }

        return view('auth.login');
    }

    public function authenticate(Request $request): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        $remember = !empty($request->remember);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $remember)) {
            $user = Auth::user();

            if ($user->status != 1) {
                Auth::logout();
                return redirect()->back()->with('error', 'Cet utilisateur n\'est pas activé.');
            }

            // ✅ Mise à jour du last_login ici
            $user = User::find($user->id);
            $user->last_login = now();
            $user->save();

            return match ($user->user_type) {
                1 => redirect('admin/dashboard'),
                2 => redirect('teacher/dashboard'),
                3 => redirect('student/dashboard'),
                4 => redirect('parent/dashboard'),
                default => redirect(url('')),
            };

        }

        return redirect()->back()->with('error', 'Email et mot de passe incorrect.');
    }

    public function forgotPassword(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('auth.forgot');
    }

    public function changePassword(Request $request): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        $user = User::getEmailSingle($request->email);

        if (!empty($user)) {
            $user->remember_token = Str::random(30);
            $user->save();

            Mail::to($user->email)->send(new ForgotPasswordMail($user));
            return redirect()->back()->with('success', 'Veuillez vérifier votre boîte mail et réinitialiser votre mot de passe.');
        } else {
            return redirect()->back()->with('error', 'Email non trouvé dans le système.');
        }
    }

    public function resetPassword($token)
    {
        $user = User::getTokenSingle($token);

        if (!empty($user)) {
            $data['user'] = $user;
            return view('auth.reset', $data);
        } else {
            abort(404);
        }
    }

    public function resetAndChangePassword(Request $request, string $token): \Illuminate\Http\RedirectResponse
    {
        $user = User::getTokenSingle($token);

        return match (true) {
            $request->password !== $request->confPassword =>
            redirect()->back()->with('error', 'Les deux mots de passe ne correspondent pas.'),

            !$user =>
            redirect()->back()->with('error', 'Token invalide ou utilisateur introuvable.'),

            default => tap($user, function ($u) use ($request) {
                    $u->password = Hash::make($request->password);
                    $u->remember_token = Str::random(30);
                    $u->save();
                })->redirect(url(''))->with('success', 'Votre mot de passe a été réinitialisé avec succès.'),
        };
    }

    public function logout(): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        Auth::logout();
        return redirect(url(''));
    }
}
