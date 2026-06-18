@component('mail::message')

# {{ $notice->title }}

Bonjour **{{ $recipient->name }} {{ $recipient->last_name }}**,

Vous avez reçu une nouvelle notification du tableau d'affichage.

---

{!! $notice->message !!}

---

@component('mail::panel')
📅 **Date de publication :** {{ \Carbon\Carbon::parse($notice->publish_date)->translatedFormat('d F Y') }}

🔔 **Date de notification :** {{ \Carbon\Carbon::parse($notice->notice_date)->translatedFormat('d F Y') }}

✍️ **Publié par :** {{ $notice->created_by_name }}
@endcomponent

@component('mail::button', ['url' => url('/'), 'color' => 'primary'])
Accéder à mon espace
@endcomponent

Merci,<br>
{{ config('app.name') }}

@endcomponent
