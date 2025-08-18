<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>{{ !empty($header_title) ? $header_title : 'Connexion' }} - School</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <!-- Styles -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body class=""
    x-data="{ page: 'signin', 'loaded': true, 'darkMode': true, 'stickyMenu': false, 'sidebarToggle': false, 'scrollTop': false }"
    x-init="
          darkMode = JSON.parse(localStorage.getItem('darkMode'));
          $watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)))"
    :class="{'dark text-bodydark bg-boxdark-2': darkMode === true}">
    <!-- ===== Preloader Start ===== -->
    @include('layouts.preloader')
    <!-- ===== Preloader End ===== -->

    <!-- ===== Page Wrapper Start ===== -->
    <div class="flex min-h-screen overflow-hidden">
        <!-- ===== Content Area Start ===== -->
        <div class="relative flex flex-1 flex-col overflow-y-auto overflow-x-hidden">
            <!-- ===== Main Content Start ===== -->
            <main>
                <div class="mx-auto max-w-screen-2xl px-4 py-36 md:p-18 lg:p-36">
                    <!-- Breadcrumb Start -->
                    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <h2 class="text-title-md2 font-bold text-black dark:text-white uppercase">
                            Connectez-vous
                        </h2>
                        <nav>
                            <!-- Dark Mode Toggler -->
                            <label :class="darkMode ? 'bg-primary' : 'bg-stroke'"
                                class="relative m-0 block h-7.5 w-14 rounded-full">
                                <input type="checkbox" :value="darkMode" @change="darkMode = !darkMode"
                                    class="absolute top-0 z-50 m-0 h-full w-full cursor-pointer opacity-0" />
                                <span :class="darkMode && '!right-1 !translate-x-full'"
                                    class="absolute left-1 top-1/2 flex h-6 w-6 -translate-y-1/2 translate-x-0 items-center justify-center rounded-full bg-white shadow-switcher duration-75 ease-linear">
                                    <span class="dark:hidden">
                                        <i class="fa-solid fa-sun"></i>
                                    </span>
                                    <span class="hidden dark:inline-block">
                                        <i class="fa-solid fa-moon"></i>
                                    </span>
                                </span>
                            </label>
                            <!-- Dark Mode Toggler -->
                        </nav>
                    </div>
                    <!-- Breadcrumb End -->

                    @include('message')
                    <!-- ====== Forms Section Start -->
                    <div
                        class="rounded-lg border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                        <div class="flex flex-wrap items-center">
                            <div class="hidden w-full xl:block xl:w-1/2">
                                <div class="px-26 py-17.5 text-center">
                                    <span class="mt-15 inline-block">
                                        <img src="{{ asset('public/images/connexion.png') }}" alt="illustration" />
                                    </span>
                                </div>
                            </div>
                            <div class="w-full border-stroke dark:border-strokedark xl:w-1/2 xl:border-l-2">
                                <div class="w-full p-4 sm:p-12.5 xl:p-17.5">
                                    <h2
                                        class="mb-9 text-2xl font-bold text-black dark:text-white sm:text-3xl uppercase">
                                        School Management system
                                    </h2>

                                    <form action="{{ url('login') }}" method="post">
                                        {{ csrf_field() }}
                                        <div class="mb-4">
                                            <label
                                                class="mb-2.5 block font-medium text-black dark:text-white">Email</label>
                                            <div class="relative">
                                                <input type="email" name="email" id="email"
                                                    placeholder="Entrer votre email" required
                                                    class="w-full rounded-lg border border-stroke bg-transparent py-4 pl-6 pr-10 outline-none focus:border-violet-500 focus-visible:shadow-none dark:border-form-strokedark dark:bg-form-input dark:focus:border-violet-500" />
                                                <span class="absolute right-4 top-4">
                                                    <span class="text-[24px] text-violet-600"><iconify-icon
                                                            icon="mdi:email-send"></iconify-icon></span>
                                                </span>
                                            </div>
                                        </div>

                                        <div class="mb-6">
                                            <label class="mb-2.5 block font-medium text-black dark:text-white">Mot de
                                                Passe</label>
                                            <div class="relative">
                                                <input type="password" id="password" name="password"
                                                    placeholder="Entrez votre mot de passe" required
                                                    class="w-full rounded-lg border border-stroke bg-transparent py-4 pl-6 pr-10 outline-none focus:border-violet-500 focus-visible:shadow-none dark:border-form-strokedark dark:bg-form-input dark:focus:border-violet-500" />
                                                <span class="absolute right-4 top-4 cursor-pointer"
                                                    onclick="togglePasswordVisibility()">
                                                    <span class="text-[24px] text-violet-600"><iconify-icon
                                                            icon="mdi:lock"
                                                            id="togglePasswordIcon"></iconify-icon></span>
                                                </span>
                                            </div>
                                        </div>

                                        <div class="mb-5">
                                            <button type="submit"
                                                class="w-full cursor-pointer rounded-lg border border-violet-600 bg-violet-600 p-4 font-medium text-white transition hover:bg-opacity-90">Connexion
                                            </button>

                                        </div>

                                        <button
                                            class="w-full rounded-lg border border-stroke bg-gray-100 p-4 font-medium hover:bg-opacity-70 dark:border-strokedark dark:bg-meta-4 dark:hover:bg-opacity-70">
                                            <span class="flex  items-center justify-center gap-3.5 text-violet-600">
                                                <iconify-icon width="24" height="24"
                                                    icon="mdi:facebook-box"></iconify-icon>
                                                Connexion avec Google
                                            </span>

                                        </button>
                                        <div class="mt-6 text-center">
                                            <p class="font-medium">
                                                <a href="{{ url('forgot_password') }}"
                                                    class="hover:text-violet-500 transition duration-300 underline"> Mot
                                                    de passe oublié ?</a>
                                            </p>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- ====== Forms Section End -->
                </div>
            </main>
            <!-- ===== Main Content End ===== -->
        </div>
        <!-- ===== Content Area End ===== -->
    </div>
    <!-- ===== Page Wrapper End ===== -->
</body>


<!--Script setup flowbite-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
<script src="https://kit.fontawesome.com/79fa04224e.js" crossorigin="anonymous"></script>
<script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
<script>
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const icon = document.getElementById('togglePasswordIcon');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.setAttribute('icon', 'mdi:lock-open');
        } else {
            passwordInput.type = 'password';
            icon.setAttribute('icon', 'mdi:lock');
        }
    }
</script>

</html>
