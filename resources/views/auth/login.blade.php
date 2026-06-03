<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      @vite(['resources/css/app.css', 'resources/js/app.js'])
      @php
            $getSettingFaviconAndLogo = \App\Models\SettingModel::getSingle(1);
            $logo_url = !empty($getSettingFaviconAndLogo->logo)
                ? \App\Models\SettingModel::getFaviconLogo($getSettingFaviconAndLogo->logo)
                : asset('upload/logo.png');
      @endphp
      <link rel="shortcut icon" href="{{ $logo_url }}" />
      <title>{{ !empty($header_title) ? $header_title : 'Connexion' }} - School</title>

      <!-- Fonts -->
      <link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
      <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,300;0,14..32,400;0,14..32,500;0,14..32,600;0,14..32,700;0,14..32,800;1,14..32,400&display=swap" rel="stylesheet">

</head>

<body x-cloak x-data="{ page: 'SchoolManagmentSystem', 'loaded': true, 'darkMode': false, 'stickyMenu': false, 'sidebarToggle': false, 'scrollTop': false }" x-init="darkMode = JSON.parse(localStorage.getItem('darkMode'));
$watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)))" :class="{ 'dark bg-gray-900': darkMode === true }">
      <!-- ===== Preloader Start ===== -->
      @include('layouts.preloader')
      <!-- ===== Preloader End ===== -->
      <div class="relative p-6 bg-white z-1 dark:bg-gray-900 sm:p-0">
            <div class="relative flex flex-col justify-center w-full h-screen dark:bg-gray-900 sm:p-0 lg:flex-row">
                  <!-- Form -->
                  <div class="flex flex-col flex-1 w-full lg:w-1/2">
                        <div class="w-full max-w-md pt-10 mx-auto">
                              <nav>
                                    <!-- Dark Mode Toggler -->
                                    <label :class="darkMode ? 'bg-indigo-500' : 'bg-stroke'"
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
                        <div class="flex flex-col justify-center flex-1 w-full max-w-md mx-auto">
                              <div>
                                    <div class="mb-5 sm:mb-8">
                                          <h1
                                                class="mb-2 font-semibold text-gray-800 text-title-sm dark:text-white/90 sm:text-title-md">
                                                Connexion
                                          </h1>
                                          <p class="text-sm text-gray-500 dark:text-gray-300">
                                                Entrez votre email et mot de passe pour continuer.
                                          </p>
                                    </div>

                                    <div class="mb-5">
                                          <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 sm:gap-5">
                                                <x-form.button type="button" bg="from-gray-300 dark:from-gray-700"
                                                      to="to-gray-700 dark:to-gray-600"
                                                      hover="hover:from-gray-700 hover:to-gray-300 dark:from-gray-800"
                                                      text="text-white">
                                                      @include('components.svg.google') Google
                                                </x-form.button>

                                                <x-form.button type="button" bg="from-gray-300 dark:from-gray-700"
                                                      to="to-gray-700 dark:to-gray-600"
                                                      hover="hover:from-gray-700 hover:to-gray-300 dark:from-gray-800"
                                                      text="text-white">
                                                      @include('components.svg.facebook') Facebook
                                                </x-form.button>
                                          </div>

                                          <div class="relative py-3 sm:py-5">
                                                <div class="absolute inset-0 flex items-center">
                                                      <div class="w-full border-t border-gray-200 dark:border-gray-700">
                                                      </div>
                                                </div>
                                                <div class="relative flex justify-center text-sm">
                                                      <span
                                                            class="p-2 text-gray-400 bg-white dark:bg-gray-900 sm:px-5 sm:py-2">Ou</span>
                                                </div>
                                          </div>

                                          <form action="{{ url('login') }}" method="POST">
                                                {{ csrf_field() }}
                                                <div class="space-y-5">

                                                      <div>
                                                            <x-form.input type="email" id="email"
                                                                  icon="mdi:email-send" placeholder="Entrez un email"
                                                                  label="Email" required></x-form.input>
                                                            <!-- Password -->
                                                            <x-form.password label="Mot de Passe" id="password"
                                                                  icon="mdi:lock" placeholder="Entrez un mot de passe"
                                                                  required></x-form.password>
                                                      </div>

                                                      <!-- Checkbox -->
                                                      <div class="flex items-center justify-between text-gray-700 dark:text-gray-300">
                                                            <x-form.checked id="remember" class="checkbox-custom"
                                                                  label="Rester connecté(e)" />

                                                            <x-link href="{{ url('forgot_password') }}">
                                                                  Mot de passe oublé ?
                                                            </x-link>
                                                      </div>
                                                      <!-- Button -->
                                                      <x-form.button text="text-white"
                                                            icon="mdi:check-circle">Connectez-vous</x-form.button>
                                                </div>
                                          </form>

                                    </div>
                                    @include('message')

                              </div>
                        </div>
                  </div>

                  <div
                        class="relative items-center hidden w-full h-full bg-gradient-to-r
                       from-indigo-800 to-indigo-400 dark:from-gray-700 dark:to-gray-900 lg:grid lg:w-1/2">
                        <div class="flex items-center justify-center z-1">
                              <!-- ===== Section 2 ===== -->
                              <div class="flex flex-col items-center max-w-sm">
                                    <img src="{{ asset('public/images/connexion.png') }}" alt="Connexion" />
                                    <p class="text-center text-white dark:text-white text-2xl font-medium uppercase">
                                          School Management System
                                    </p>
                              </div>
                        </div>
                  </div>

            </div>
      </div>
</body>


<!--Script setup flowbite-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
<script src="https://kit.fontawesome.com/79fa04224e.js" crossorigin="anonymous"></script>
<script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

</html>
