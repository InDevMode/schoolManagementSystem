<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
      <meta charset="UTF-8" />
      <meta http-equiv="X-UA-Compatible" content="IE=edge" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      @vite(['resources/css/app.css', 'resources/js/app.js'])
      <title>{{ !empty($header_title) ? $header_title : '' }} - SMS</title>

      @php
            $getSettingFaviconAndLogo = \App\Models\SettingModel::getSingle(1);
            $favicon_url = !empty($getSettingFaviconAndLogo->favicon)
                ? \App\Models\SettingModel::getFaviconLogo($getSettingFaviconAndLogo->favicon)
                : asset('upload/favicon.png');
            $logo_url = !empty($getSettingFaviconAndLogo->logo)
                ? \App\Models\SettingModel::getFaviconLogo($getSettingFaviconAndLogo->logo)
                : asset('upload/logo.png');
      @endphp

      <link rel="shortcut icon" href="{{ $favicon_url }}" />
      <link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
      <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">

      @yield('style')
      <style>
            [x-cloak] {
                  display: none !important;
            }
      </style>

</head>

<body x-cloak x-data="{ page: 'SchoolManagmentSystem', 'loaded': true, 'darkMode': false, 'stickyMenu': false, 'sidebarToggle': false, 'scrollTop': false }" x-init="darkMode = JSON.parse(localStorage.getItem('darkMode'));
$watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)))" :class="{ 'dark bg-gray-900': darkMode === true }">
      <!-- ===== Preloader Start ===== -->
      @include('layouts.preloader')
      <!-- ===== Page Wrapper Start ===== -->
      <div class="flex h-screen overflow-hidden">
            @include('layouts.sidebar')
            <!-- ===== Content Area Start ===== -->
            <div class="relative flex flex-1 flex-col overflow-y-auto overflow-x-hidden">
                  @include('layouts.header')
                  <!-- ===== Main Content Start ===== -->
                  @yield('content')
            </div>
      </div>

</body>
<script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
<script src="https://kit.fontawesome.com/79fa04224e.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script src="{{ asset('public/js/components/chart-01.js') }}"></script>
<script src="{{ asset('public/js/components/chart-02.js') }}"></script>
<script src="{{ asset('public/js/components/chart-03.js') }}"></script>
<script src="{{ asset('public/js/components/chart-04.js') }}"></script>
<script src="{{ asset('public/js/components/map-01.js') }}"></script>
<script src="{{ asset('public/js/us-aea-en.js') }}"></script>
<!-- jQuery (obligatoire) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Summernote CSS & JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.js"></script>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- Inclure Flatpickr et la localisation française -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/fr.js"></script>
<script src="https://cdn.kkiapay.me/k.js"></script>

@yield('script')

</html>
