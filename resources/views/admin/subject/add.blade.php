@extends('layouts.app')
@section('content')
<div class="p-4 mt-72 sm:ml-64 flex items-center justify-center">
    <div class="p-8 w-full max-w-screen-md shadow-xl rounded bg-gray-100 border">
        @include('message')
        <form action="{{ url('admin/subject/add') }}" method="post" class="">
            {{ csrf_field() }}
            <h2 class="lg:text-3xl sm:text-2xl text-xl font-bold uppercase text-center text-gray-700 rounded mb-10">
                Créer une nouvelle matière</h2>
            <div class="flex mb-5">
                <input type="text" id="name" name="name" value="{{ old('name') }}"
                       class="rounded bg-white border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500"
                       placeholder="nom de la matière..." required>
            </div>
            <div class="flex mb-5">
                <select id="type" name="type" class="rounded bg-white border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500" required>
                    <option selected>Définissez un type pour cette matière</option>
                    <option value="theoretical">Théorique</option>
                    <option value="practical">Pratique</option>
                </select>
            </div>
            <div class="flex mb-5">
                <select id="status" name="status" class="rounded bg-white border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500" required>
                    <option selected>Définissez un status pour cette classe</option>
                    <option value="1">Activée</option>
                    <option value="0">Désactivée</option>
                </select>
            </div>
            <button type="submit"
                    class="text-white bg-violet-600 hover:bg-violet-800 focus:ring-4 focus:outline-none focus:ring-violet-300 font-medium rounded-md text-sm px-5 py-2.5 text-center dark:bg-violet-600 dark:hover:bg-violet-700 dark:focus:ring-violet-800 transition-all duration-500 ease-out w-full hover:scale-105">
                Créer cette matière
            </button>
    </div>
    </form>
</div>
</div>
@endsection

