@component('mail::message')
{{ $user->name }},

Vous avez demandé la réinitialisation de votre mot de passe. Cliquez sur le bouton ci-dessous pour définir un nouveau mot de passe.

@component('mail::button', ['url' => url('reset/' . $user->remember_token)])
Réinitialiser mon mot de passe
@endcomponent

Si vous n'avez pas fait cette demande, ignorez cet email.

Merci,
{{ config('app.name') }}
@endcomponent
