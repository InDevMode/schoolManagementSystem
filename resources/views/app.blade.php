<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    @php
        try {
            $setting = \App\Models\SettingModel::getSingle(1);
            $favicon = $setting?->getFavicon() ?? asset('upload/favicon.png');
            $appName = $setting?->school_name ?? config('app.name', 'SMS');
        } catch (\Exception $e) {
            $favicon = asset('upload/favicon.png');
            $appName = config('app.name', 'SMS');
        }
    @endphp

    <title inertia>{{ $appName }}</title>
    <link rel="shortcut icon" href="{{ $favicon }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,300;0,14..32,400;0,14..32,500;0,14..32,600;0,14..32,700;0,14..32,800;1,14..32,400&display=swap" rel="stylesheet" />

    @routes
    @vite(['resources/css/app.css', 'resources/js/app.ts'])
    @inertiaHead
    <!-- SDK Kkiapay -->
    <script src="https://cdn.kkiapay.me/k.js" defer></script>
    <!-- SDK FedaPay (optionnel pour widget inline) -->
    <script src="https://cdn.fedapay.com/checkout.js?v=1.1.7" defer></script>
</head>
<body class="font-inter antialiased bg-gray-50 dark:bg-gray-900 app-bg-pattern">
    @inertia
</body>
</html>
