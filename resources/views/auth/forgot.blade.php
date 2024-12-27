<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>Mot de passe oublié</title>

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

        .backImage {
            background-image: url("public/images/back1.jpg");
            background-size: cover;
            background-repeat: no-repeat;
        }
    </style>
</head>

<body class="max-w-[360px] sm:max-w-7xl mx-auto flex justify-center items-center h-screen backImage">
<div class="bg-white py-2 sm:p-5 border-2 border-gray-300 rounded-md  shadow-2xl ">
    <h1 class="text-center uppercase lg:text-3xl sm:text-2xl text-gray-700 font-bold px-3 py-2 lg:py-0 lg:mb-3">School
        Management
        System
    </h1>
    <div class="flex flex-col lg:flex-row items-center gap-2">
        <div class="hidden lg:block w-full h-full rounded-r-none rounded-md">
            <img src="public/images/login3.png" alt="Image login" class="object-cover w-full h-auto">
        </div>
        <div class="w-full rounded-l-none rounded-md p-5">
            @include('message')
            <form action="{{ url('forgot-password') }}" method="post" class="">
                {{ csrf_field() }}
                <h2 class="lg:text-2xl sm:text-2xl font-bold uppercase text-center text-gray-700 rounded-t-md mb-3">
                    Réinitialisez votre mot de passe</h2>
                <div class="flex mb-5">
                        <span
                            class="inline-flex items-center px-3 text-sm text-gray-900 bg-white border border-e-0 border-gray-300 rounded-s-md dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600">
                            <i class="fa-solid fa-envelope text-violet-600"></i>
                        </span>
                    <input type="email" id="email" name="email"
                           class="rounded-none rounded-e-md bg-white border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm p-2  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500"
                           placeholder="email..." required>
                </div>
                <button type="submit"
                        class="text-white bg-violet-600 hover:bg-violet-800 focus:ring-4 focus:outline-none focus:ring-violet-300 font-medium rounded-md text-sm px-5 py-2.5 text-center dark:bg-violet-600 dark:hover:bg-violet-700 dark:focus:ring-violet-800 transition-all duration-500 ease-out w-full hover:scale-105">
                    Connexion
                </button>
                <div class="flex justify-between gap-8 items-center mt-14 mb-8 text-sm">
                    <hr class="border border-gray-400 w-1/2">
                    <span class="text-violet-500 font-bold">Or</span>
                    <hr class="border border-gray-400 w-1/2">
                </div>

                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center font-medium mt-5">
                        <span class="block text-sm text-gray-700">
                            <a href="{{ url('') }}"
                               class="hover:underline transition-all ease-in duration-300">Connectez-vous</a>
                        </span>
                    <span class="block text-sm">
                            <a href="{{ url('signup') }}"
                               class="hover:underline text-violet-600 transition-all ease-in duration-500">Créer un compte </a>
                        </span>
                </div>
            </form>
        </div>
    </div>
</div>
</body>

<!--Script setup flowbite-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
<script src="https://kit.fontawesome.com/79fa04224e.js" crossorigin="anonymous"></script>

</html>
