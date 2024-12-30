@extends('layouts.app')
@section('content')
<div class="p-4 mt-40 sm:ml-64 flex items-center justify-center">
    <div class="p-5 w-full max-w-screen-md">
        @include('message')
        <form action="" method="post" class="">
            {{ csrf_field() }}
            <h2 class="lg:text-3xl sm:text-2xl text-xl font-bold uppercase text-center text-gray-700 rounded-t-md mb-10">
                Modifier un administrateur</h2>
            <div class="flex mb-5">
                        <span
                            class="inline-flex items-center px-3 text-sm text-gray-900 bg-white border border-e-0 border-gray-300 rounded-s-md dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600">
                            <i class="fa-solid fa-user text-violet-600"></i>
                        </span>
                <input type="text" id="name" name="name" value="{{ $getAdmin -> name }}"
                       class="rounded-none rounded-e-md bg-white border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm p-2  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500"
                       placeholder="nom..." required>
            </div>
            <div class="flex mb-5">
                        <span
                            class="inline-flex items-center px-3 text-sm text-gray-900 bg-white border border-e-0 border-gray-300 rounded-s-md dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600">
                            <i class="fa-solid fa-envelope text-violet-600"></i>
                        </span>
                <input type="email" id="email" name="email" value="{{ $getAdmin -> email }}"
                       class="rounded-none rounded-e-md bg-white border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm p-2  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500"
                       placeholder="email..." required>
            </div>
            <div class="flex mb-1">
                        <span
                            class="inline-flex items-center px-3 text-sm text-gray-900 bg-white border border-e-0 border-gray-300 rounded-s-md dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600">
                            <i class="fa-solid fa-lock text-violet-600"></i>
                        </span>
                <input type="password" id="password" name="password"
                       class="rounded-none rounded-e-md bg-white border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm p-2  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500"
                       placeholder="mot de passe...">
            </div>
            <p class="mb-5 text-red-600 text-[10px]">Est-ce que vous voulez changer le mot de passe ? Si oui veuillez ajouter le nouveau</p>
            <button type="submit"
                    class="text-white bg-green-500 hover:bg-green-600 focus:ring-4 focus:outline-none focus:ring-emerald-300 font-medium rounded-md text-sm px-5 py-2.5 text-center dark:bg-violet-600 dark:hover:bg-emerald-700 dark:focus:ring-emerald-800 transition-all duration-500 ease-out w-full hover:scale-105">
                Modifier
            </button>
    </div>
    </form>
</div>
</div>

@endsection

