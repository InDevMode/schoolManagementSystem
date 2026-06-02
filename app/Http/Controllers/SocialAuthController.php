<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Redirige l'utilisateur vers le fournisseur OAuth.
     */
    public function redirect(string $provider)
    {
        $this->validateProvider($provider);

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Gère le callback OAuth du fournisseur.
     */
    public function callback(string $provider)
    {
        $this->validateProvider($provider);

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'La connexion via ' . ucfirst($provider) . ' a échoué. Veuillez réessayer.');
        }

        // Cherche ou crée l'utilisateur
        $user = User::where('email', $socialUser->getEmail())->first();

        if (! $user) {
            // Crée un nouveau compte avec un rôle par défaut (étudiant = 3)
            // L'admin pourra changer le rôle depuis le panneau d'administration
            $user = User::create([
                'name'              => $socialUser->getName() ?? $socialUser->getNickname() ?? 'Utilisateur',
                'email'             => $socialUser->getEmail(),
                'password'          => bcrypt(Str::random(24)),
                'user_type'         => 3, // étudiant par défaut
                'status'            => 1,
                'provider'          => $provider,
                'provider_id'       => $socialUser->getId(),
                'remember_token'    => Str::random(30),
            ]);
        } else {
            // Met à jour les infos du fournisseur si l'utilisateur existe déjà
            $user->update([
                'provider'    => $provider,
                'provider_id' => $socialUser->getId(),
            ]);
        }

        if ($user->status != 1) {
            return redirect('/login')->with('error', "Ce compte n'est pas activé. Contactez l'administrateur.");
        }

        Auth::login($user, true);
        $user->update(['last_login' => now()]);
        request()->session()->regenerate();

        return $this->redirectByRole($user->user_type);
    }

    /**
     * Valide que le fournisseur est supporté.
     */
    private function validateProvider(string $provider): void
    {
        abort_unless(in_array($provider, ['google', 'facebook']), 404);
    }

    /**
     * Redirige selon le rôle de l'utilisateur.
     */
    private function redirectByRole(int $userType)
    {
        return match ($userType) {
            1 => redirect('/admin/dashboard'),
            2 => redirect('/teacher/dashboard'),
            3 => redirect('/student/dashboard'),
            4 => redirect('/parent/dashboard'),
            default => redirect('/login'),
        };
    }
}
