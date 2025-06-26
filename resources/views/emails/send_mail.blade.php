@component('mail::message')
{{ $user->name }} {{ $user->last_name }},

{!! $messageContent !!}

@component('mail::button', ['url' => url('/')])
Voir plus
@endcomponent

Merci,<br>
{{ config('app.name') }}
@endcomponent
