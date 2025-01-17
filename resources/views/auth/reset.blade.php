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

<body class="max-w-[960px] sm:max-w-[1640px] mx-auto h-screen backImage flex">
<div class="lg:w-[800px] md:w-[600px] w-full rounded-lg flex items-center justify-center p-8">
    <div>
        <div class="flex flex-col items-center gap-2">
            <form action="" method="post"
                  class="bg-white border border-gray-200 shadow-2xl rounded-lg p-8">
                @include('message')
                {{ csrf_field() }}
                <h2 class="lg:text-3xl sm:text-2xl font-bold uppercase text-center text-gray-700 mb-3">Changez votre mot
                    de passe</h2>
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
                        class="text-white bg-violet-600 hover:bg-violet-800 focus:ring-4 focus:outline-none focus:ring-violet-300 font-medium rounded-md text-sm px-5 py-2.5 mb-3 text-center transition-all duration-500 ease-out sm:w-full w-fit hover:scale-105">
                    Réintialisez
                </button>
                <div class="flex justify-between gap-8 items-center mb-3 text-sm">
                    <hr class="border border-gray-400 w-1/2">
                    <span class="text-violet-500 font-bold">Or</span>
                    <hr class="border border-gray-400 w-1/2">
                </div>
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center font-medium mt-5">
                        <span class="block text-sm text-gray-700">
                            <a href="{{ url('') }}"
                               class="hover:underline transition-all ease-in duration-300">Connectez-vous
                            </a>
                        </span>
                    <span class="block text-sm">
                            <a href="{{ url('signup') }}"
                               class="hover:underline text-violet-600 transition-all ease-in duration-500">Créer un
                                compte
                            </a>
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
