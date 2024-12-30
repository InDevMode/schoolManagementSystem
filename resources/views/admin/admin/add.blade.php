@extends('layouts.app')
@section('content')
<div class="p-4 mt-40 sm:ml-64 flex items-center justify-center">
    <div class="p-5 w-full max-w-screen-md">
        @include('message')
        <form action="{{ url('admin/admin/add') }}" method="post" class="">
            {{ csrf_field() }}
            <h2 class="lg:text-3xl sm:text-2xl text-xl font-bold uppercase text-center text-gray-700 rounded-t-md mb-10">
                Créer un administrateur</h2>
            <div class="flex mb-5">
                        <span
                            class="inline-flex items-center px-3 text-sm text-gray-900 bg-white border border-e-0 border-gray-300 rounded-s-md dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600">
                            <i class="fa-solid fa-user text-violet-600"></i>
                        </span>
                <input type="text" id="name" name="name"
                       class="rounded-none rounded-e-md bg-white border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm p-2  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500"
                       placeholder="nom..." required>
            </div>
            <div class="flex mb-5">
                        <span
                            class="inline-flex items-center px-3 text-sm text-gray-900 bg-white border border-e-0 border-gray-300 rounded-s-md dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600">
                            <i class="fa-solid fa-envelope text-violet-600"></i>
                        </span>
                <input type="email" id="email" name="email"
                       class="rounded-none rounded-e-md bg-white border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm p-2  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500"
                       placeholder="email..." required>
            </div>
            <div class="flex mb-5">
                        <span
                            class="inline-flex items-center px-3 text-sm text-gray-900 bg-white border border-e-0 border-gray-300 rounded-s-md dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600">
                            <i class="fa-solid fa-lock text-violet-600"></i>
                        </span>
                <input type="password" id="password" name="password"
                       class="rounded-none rounded-e-md bg-white border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm p-2  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500"
                       placeholder="mot de passe..." required>
            </div>
            <button type="submit"
                    class="text-white bg-violet-600 hover:bg-violet-800 focus:ring-4 focus:outline-none focus:ring-violet-300 font-medium rounded-md text-sm px-5 py-2.5 text-center dark:bg-violet-600 dark:hover:bg-violet-700 dark:focus:ring-violet-800 transition-all duration-500 ease-out w-full hover:scale-105">
                Créer
            </button>
    </div>
    </form>
</div>
</div>
@endsection

