@extends('layouts.app')
@section('content')
<div class="p-4 mt-72 sm:ml-64 flex items-center justify-center">
    <div class="p-8 w-full max-w-screen-md shadow-xl rounded bg-gray-100 border">
        @include('message')
        <form action="" method="post" class="">
            {{ csrf_field() }}
            <h2 class="lg:text-3xl sm:text-2xl text-xl font-bold uppercase text-center text-gray-700 rounded-t-md mb-10">
                Modifier une matière</h2>
            <div class="flex mb-5">
                <input type="text" id="name" name="name" value="{{ $getSubject->name }}"
                       class="rounded bg-white border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500"
                       placeholder="nom de la matière..." required>
            </div>
            <div class="flex mb-5">
                <select id="type" name="type" class="rounded bg-white border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500" required>
                    <option disabled selected>Définissez un type pour cette matière</option>
                    <option value="theoretical" name="type" {{ $getSubject->type == 'theoretical' ? 'selected' : '' }}>Théorique </option>
                    <option value="practical" name="type" {{ $getSubject->type == 'practical' ? 'selected' : '' }}>Pratique </option>
                </select>
            </div>
            <div class="flex mb-5">
                <select id="status" name="status" class="rounded bg-white border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500" required>
                    <option disabled selected>Définissez un statut pour cette classe</option>
                    <option value="1" name="status" {{ $getSubject->status == 1 ? 'selected' : '' }}>Activée </option>
                    <option value="0" name="status" {{ $getSubject->status == 0 ? 'selected' : '' }}>Désactivée </option>
                </select>
            </div>
            <button type="submit"
                    class="text-white bg-green-500 hover:bg-green-600 focus:ring-4 focus:outline-none focus:ring-emerald-300 font-medium rounded-md text-sm px-5 py-2.5 text-center dark:bg-violet-600 dark:hover:bg-emerald-700 dark:focus:ring-emerald-800 transition-all duration-500 ease-out w-full hover:scale-105">
                Modifier
            </button>
    </div>
    </form>
</div>
</div>
@endsection

