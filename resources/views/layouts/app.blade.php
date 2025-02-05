<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>{{ !empty($header_title) ? $header_title : '' }} - School</title>
    <link rel="shortcut icon" href="{{ asset('public/images/logo.png') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    @yield('style')
</head>

<body
x-data="{ page: 'SchoolManagmentSystem', 'loaded': true, 'darkMode': true, 'stickyMenu': false, 'sidebarToggle': false,
'scrollTop':
false }"
x-init="
darkMode = JSON.parse(localStorage.getItem('darkMode'));
$watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)))"
:class="{'dark text-bodydark bg-boxdark-2': darkMode === true}"
>
<!-- ===== Preloader Start ===== -->
@include('layouts.partials.preloader')
<!-- ===== Page Wrapper Start ===== -->
<div class="flex h-screen overflow-hidden">
    @include('layouts.partials.sidebar')
    <!-- ===== Content Area Start ===== -->
    <div class="relative flex flex-1 flex-col overflow-y-auto overflow-x-hidden">
        @include('layouts.partials.header')
        <!-- ===== Main Content Start ===== -->
        @yield('content')
    </div>
</div>

</body>

<script src="https://kit.fontawesome.com/79fa04224e.js" crossorigin="anonymous"></script>
<script src="{{ asset('public/js/components/chart-01.js') }}"></script>
<script src="{{ asset('public/js/components/chart-02.js') }}"></script>
<script src="{{ asset('public/js/components/chart-03.js') }}"></script>
<script src="{{ asset('public/js/components/chart-04.js') }}"></script>
<script src="{{ asset('public/js/components/map-01.js') }}"></script>
<script src="{{ asset('public/js/us-aea-en.js') }}"></script>
@yield('script')
<script>
    function toggleDropdown() {
        let dropdown = document.getElementById("dropdown-assign");
        let chevron = document.getElementById("chevron");

        if (dropdown.style.maxHeight && dropdown.style.maxHeight !== "0px") {
            dropdown.style.maxHeight = "0px";
            chevron.style.transform = "rotate(0deg)";
        } else {
            dropdown.style.maxHeight = dropdown.scrollHeight + "px";
            chevron.style.transform = "rotate(90deg)";
        }
    }
</script>

</html>
