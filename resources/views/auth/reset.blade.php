<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>Changez le mot de passe</title>

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

<body class="max-w-[460px] sm:max-w-7xl mx-auto flex justify-center items-center h-screen backImage">
<div class="lg:bg-gray-100 bg-white rounded-lg shadow-2xl">
    <h1 class="text-center uppercase bg-violet-500 lg:text-3xl sm:text-2xl text-white font-bold px-3 py-2 lg:py-3 rounded-t-lg lg:mb-3">School
        Management
        System
    </h1>
    <div class="flex flex-col lg:flex-row items-center gap-2">
        <div class="w-full rounded-l-none rounded-md p-8">
            @include('message')
            <form action="" method="post" class="lg:bg-white lg:border border-gray-200 lg:shadow rounded-lg sm:py-10 sm:px-5"">
                {{ csrf_field() }}
                <h2 class="lg:text-2xl sm:text-2xl text-sm font-bold uppercase text-center text-gray-700 rounded-t-md sm:mb-5">
                    Changez votre mot de passe</h2>
                <div class="flex mb-5">
                        <span
                            class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-100 border border-e-0 border-gray-300 rounded-s-md">
                            <i class="fa-solid fa-lock text-violet-600"></i>
                        </span>
                    <input type="password" id="password" name="password"
                           class="rounded-none rounded-e-md bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm p-2"
                           placeholder="nouveau mot de passe..." required>
                </div>
                <div class="flex mb-5">
                        <span
                            class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-100 border border-e-0 border-gray-300 rounded-s-md">
                            <i class="fa-solid fa-lock text-violet-600"></i>
                        </span>
                    <input type="password" id="password" name="confPassword"
                           class="rounded-none rounded-e-md bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm p-2"
                           placeholder="Confirmez votre mot de passe..." required>
                </div>
                <button type="submit"
                        class="text-white bg-violet-500 hover:bg-violet-600 focus:ring-4 focus:outline-none focus:ring-violet-300 font-medium rounded-md text-sm px-5 py-2.5 mt-5 text-center transition-all duration-500 ease-out w-full hover:scale-105">
                    Réinitialisez
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
