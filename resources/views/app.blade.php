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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />

    @routes
    @vite(['resources/css/app.css', 'resources/js/app.ts'])
    @inertiaHead
</head>
<body class="font-inter antialiased bg-gray-50 dark:bg-gray-900">
    @inertia
</body>
</html>
