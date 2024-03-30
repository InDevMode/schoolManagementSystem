<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>SchoolManagementSystem</title>

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

<body class="max-w-sm sm:max-w-xl lg:max-w-7xl mx-auto flex justify-center items-center min-h-screen text-slate-800 px-2.5 sm:px-0 bg-white">
    <div class="lg:p-8">
        {{-- <h1 class="hidden lg:block text-slate text-3xl font-bold text-center my-5 uppercase">
            School Management System
        </h1> --}}
        <div class="flex flex-col lg:flex-row w-full h-1/2 lg:px-44">
            <div class="w-full hidden lg:block">
                <img class="object-cover rounded-l-lg h-96 w-full " src="https://img.freepik.com/psd-gratuit/jeune-femme-discutant-smartphone-tout-etant-assis-chaise-fond-isole-illustration-3d-personnages-dessins-animes_1150-63078.jpg?t=st=1711807749~exp=1711811349~hmac=15d45b38dd560c2a9482586d1b953f681e8eb81abf47108c162334f564b68118&w=1380"
                    alt="">
            </div>
            <form class="w-full bg-gray-100 border-2 lg:border-t lg:border-b lg:border-e border-gray-200 lg:rounded-r-lg rounded-md p-3 lg:px-5 lg:h-96">
                <h2 class="md:text-2xl lg:text-3xl mb-5 font-bold uppercase text-center bg-white text-slate-800 rounded-t-md py-2">Connectez-vous</h2>
                <div class="flex mb-5">
                    <span
                        class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border border-e-0 border-gray-300 rounded-s-md dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600">
                        <i class="fa-solid fa-envelope"></i>
                    </span>
                    <input type="email" id="email"
                        class="rounded-none rounded-e-lg bg-white border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block flex-1 min-w-0 w-full text-sm p-2  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500"
                        placeholder="email..." required>
                </div>
                <div class="flex mb-5">
                    <span
                        class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border border-e-0 border-gray-300 rounded-s-md dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <input type="password" id="password"
                        class="rounded-none rounded-e-lg bg-white border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block flex-1 min-w-0 w-full text-sm p-2  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500"
                        placeholder="mot de passe..." required>
                </div>
                <div
                    class="flex flex-col sm:flex-row sm:items-center justify-between mb-5 transition-all duration-300 ease-out">
                    <span class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="remember_token" type="checkbox" value=""
                                class="w-4 h-4 border border-gray-300 rounded bg-white focus:ring-3 focus:ring-violet-300 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-violet-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:outline-none checked:bg-violet-600"
                                required />
                        </div>
                        <label for="remember_token" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Se
                            souvenir
                            de moi</label>
                    </span>
                    <button type="submit"
                        class="text-white bg-violet-600 hover:bg-violet-800 focus:ring-4 focus:outline-none focus:ring-violet-300 font-medium rounded-md text-sm px-5 py-2.5 text-center dark:bg-violet-600 dark:hover:bg-violet-700 dark:focus:ring-violet-800 transition-all duration-500 ease-out w-full sm:w-fit mt-3 hover:scale-105">Connexion</button>
                </div>
                <button type="submit"
                    class="flex items-center justify-center gap-x-3 text-gray-600 border-2 border-gray-400 hover:text-white hover:border-violet-600 hover:bg-violet-600 focus:ring-4 focus:outline-none focus:ring-violet-300 font-medium rounded-md text-sm px-5 py-2.5 text-center dark:bg-violet-500 dark:hover:bg-violet-600 dark:focus:ring-violet-500 w-full mb-3 transition-all ease-in-out duration-500 hover:scale-105">
                    <i class="fa-brands fa-google-plus-g"></i>
                    Continuez
                    avec Google</button>
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center font-medium mt-5">
                    <span class="text-sm"><a href="#" class="hover:underline transition-all ease-in duration-300">Mot
                            de passe oublié</a></span>
                    <span class="text-sm"><a href="#"
                            class="hover:underline text-violet-600 transition-all ease-in duration-500">Créer un
                            compte</a></span>
                </div>
            </form>
        </div>
    </div>
</body>

<!--Script setup flowbite-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
<script src="https://kit.fontawesome.com/79fa04224e.js" crossorigin="anonymous"></script>
</html>
