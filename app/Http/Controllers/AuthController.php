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
            if (Auth::user()->user_type == 1) {
                return redirect('admin/dashboard');
            } else if (Auth::user()->user_type == 2) {
                return redirect('teacher/dashboard');
            } else if (Auth::user()->user_type == 3) {
                return redirect('student/dashboard');
            } else if (Auth::user()->user_type == 4) {
                return redirect('parent/dashboard');
            }
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

            if ($user->user_type == 1) {
                return redirect('admin/dashboard');
            } elseif ($user->user_type == 2) {
                return redirect('teacher/dashboard');
            } elseif ($user->user_type == 3) {
                return redirect('student/dashboard');
            } elseif ($user->user_type == 4) {
                return redirect('parent/dashboard');
            }

            return redirect(url(''));
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

    public function resetAndChangePassword(Request $request, $token): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        if ($request->password == $request->confPassword) {

            $user = User::getTokenSingle($token);
            if (!$user) {
                return redirect()->back()->with('error', 'Token invalide ou utilisateur introuvable.');
            }
            $user->password = Hash::make($request->password);
            $user->remember_token = Str::random(30);
            $user->save();

            return redirect(url(''))->with('success', 'Votre mot de passe a été réinitialisé avec succès.');
        } else {
            return redirect()->back()->with('error', 'Les deux mot de passes ne correspondent pas.');
        }
    }

    public function logout(): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        Auth::logout();
        return redirect(url(''));
    }
}
