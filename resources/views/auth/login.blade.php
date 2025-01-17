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

        .backImage {
            background-image: url("public/images/back1.jpg");
            background-size: cover;
            background-repeat: no-repeat;
        }
    </style>
</head>

<body class="max-w-[640px] sm:max-w-[1640px] mx-auto h-screen backImage flex">
<div id="animated-container"
     class="lg:w-[800px] md:w-[600px] w-full rounded-lg flex items-center justify-center p-8 transform -translate-x-full transition-transform duration-1000 ease-out">
    <div>
        <div class="flex flex-col items-center gap-2">
            <form action="{{ url('login') }}" method="post" class="bg-white border border-gray-200 shadow-2xl rounded-lg p-8">
                @include('message')
                {{ csrf_field() }}
                <h2 class="lg:text-3xl sm:text-2xl font-bold uppercase text-center text-gray-700 mb-3">
                    Connectez-vous</h2>
                <div class="flex mb-5">
                        <span
                            class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-100 border border-e-0 border-gray-300 rounded-s-md">
                            <i class="fa-solid fa-envelope text-violet-600"></i>
                        </span>
                    <input type="email" id="email" name="email"
                           class="rounded-none rounded-e-md bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm p-2"
                           placeholder="email..." required>
                </div>
                <div class="flex mb-5">
                        <span
                            class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-100 border border-e-0 border-gray-300 rounded-s-md">
                            <i class="fa-solid fa-lock text-violet-600"></i>
                        </span>
                    <input type="password" id="password" name="password"
                           class="rounded-none rounded-e-md bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm p-2"
                           placeholder="mot de passe..." required>
                </div>
                <div
                    class="flex flex-col sm:flex-row sm:items-center justify-between transition-all duration-300 ease-out mb-3">
                        <span class="flex items-start mb-3 sm:mb-0">
                            <div class="flex items-center h-5">
                                <input id="remember_token" name="remember" type="checkbox"
                                       class="w-4 h-4 border border-gray-300 rounded bg-gray-100 focus:ring-3 focus:ring-violet-300 focus:outline-none checked:bg-violet-600"
                                />
                            </div>
                            <label for="remember_token"
                                   class="ms-2 text-sm font-medium text-gray-700 dark:text-gray-300">Se
                                souvenir
                                de moi
                            </label>
                        </span>
                    <button type="submit"
                            class="text-white bg-violet-600 hover:bg-violet-800 focus:ring-4 focus:outline-none focus:ring-violet-300 font-medium rounded-md text-sm px-5 py-2.5 text-center transition-all duration-500 ease-out w-full sm:w-fit hover:scale-105">
                        Connexion
                    </button>
                </div>
                <div class="flex justify-between gap-8 items-center mb-3 text-sm">
                    <hr class="border border-gray-400 w-1/2">
                    <span class="text-violet-500 font-bold">Or</span>
                    <hr class="border border-gray-400 w-1/2">
                </div>
                <button type="submit"
                        class="flex items-center justify-center gap-x-3 text-violet-500 border border-gray-400 bg-gray-100 group focus:ring-4 focus:outline-none focus:ring-violet-300 font-medium rounded-md text-sm px-5 py-2.5 text-center w-full mb-3 transition-all ease-in-out duration-700 hover:-translate-y-2 hover:translate-x-2 hover:bg-violet-600 hover:border-violet-600">
                    <i class="fa-brands fa-facebook text-violet-500 transition-colors duration-300 group-hover:text-white"></i>
                    <span class="transition-colors duration-300 group-hover:text-white">
                        Continuez avec Facebook
                    </span>
                </button>
                <button type="submit"
                        class="flex items-center justify-center gap-x-3 text-pink-500 border border-gray-400 bg-gray-100 group focus:ring-4 focus:outline-none focus:ring-pink-300 font-medium rounded-md text-sm px-5 py-2.5 text-center w-full mb-3 transition-all ease-in-out duration-700 hover:-translate-y-2 hover:translate-x-2 hover:bg-pink-600 hover:border-pink-600">
                    <i class="fa-brands fa-google-plus-g text-pink-500 transition-colors duration-300 group-hover:text-white"></i>
                    <span class="transition-colors duration-300 group-hover:text-white">
                        Continuez avec Google
                    </span>
                </button>
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center font-medium mt-5">
                        <span class="block text-sm text-gray-700">
                            <a href="{{ url('forgot-password') }}"
                               class="hover:underline transition-all ease-in duration-300">Mot
                                de passe oublié
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
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const animatedContainer = document.getElementById('animated-container');
        if (animatedContainer) {
            setTimeout(() => {
                animatedContainer.classList.remove('-translate-x-full');
                animatedContainer.classList.add('translate-x-0');
            }, 1000);
        }
    });

</script>

</html>
