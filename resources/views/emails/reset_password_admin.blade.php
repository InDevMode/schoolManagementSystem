@component('mail::message')
# Réinitialisation de votre mot de passe

Bonjour **{{ $user->last_name }} {{ $user->name }}**,

Un administrateur a réinitialisé votre mot de passe sur **{{ config('app.name') }}**.

Voici vos nouveaux identifiants de connexion :

@component('mail::panel')
**Email :** {{ $user->email }}

**Mot de passe :** `{{ $plainPassword }}`
@endcomponent

@component('mail::button', ['url' => url('/login'), 'color' => 'primary'])
Se connecter
@endcomponent

> **Important :** Pour des raisons de sécurité, nous vous recommandons de changer ce mot de passe dès votre prochaine connexion via votre profil.

Si vous n'attendiez pas cette modification, veuillez contacter votre administrateur immédiatement.

Cordialement,
**L'équipe {{ config('app.name') }}**
@endcomponent
