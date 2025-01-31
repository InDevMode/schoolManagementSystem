@extends('layouts.app')
@section('content')
<div class="p-4 sm:ml-64">
    <div class="p-4 rounded-lg dark:border-gray-700 mt-14">
        @include('message')
        <div class="flex justify-between pt-2">
            <div class="space-x-2 font-semibold">
                <span class="text-violet-500 text-[25px]"><i class="fa-solid fa-clock"></i></span>
                <span>/</span>
                <span class="hover:underline hover:text-violet-500 transition-all duration-300"><a
                        href="{{ url('admin/dashboard') }}">Dashboard</a></span>
                <span>/</span>
                <span>Liste des Horaires de Cours</span>
            </div>
            <a href="{{ url('admin/class_timetable/add') }}"
               class="uppercase text-white bg-violet-500 hover:bg-violet-600 focus:ring-4 focus:outline-none focus:ring-violet-300 font-bold rounded-full text-sm px-5 py-2.5 text-center transition-all duration-500 ease-out w-full sm:w-fit hover:scale-105">
                Ajouter une nouveau horaire de cours
            </a>
        </div>

        <form action="" method="get"
              class="my-5 shadow p-3 bg-white rounded border border-gray-300" id="searchForm">
            {{ csrf_field() }}

            <div class="grid grid-cols-4 gap-x-2">
                <!-- Nom de la classe -->
                <div>
                    <input type="text" id="class_name" name="class_name" value="{{ Request::get('class_name') }}"
                           class="rounded-full ps-5 bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm p-2"
                           placeholder="Rechercher par le nom de la classe...">
                </div>

                <!-- Nom de la matière -->
                <div>
                    <input type="text" id="subject_name" name="subject_name" value="{{ Request::get('subject_name') }}"
                           class="rounded-full ps-5 bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm p-2"
                           placeholder="Rechercher par le nom de la matière...">
                </div>

                <!-- Boutons -->

                <button type="submit"
                        class="flex justify-between text-white bg-violet-500 hover:bg-violet-600 focus:ring-4 focus:outline-none focus:ring-violet-300 font-medium rounded-full text-sm px-5 py-2.5 text-center transition-all duration-500 ease-out w-full hover:scale-105">
                    Rechercher
                    <span class="inline-flex items-center px-3 text-sm text-gray-900">
                        <i class="fa-solid fa-search text-white"></i>
                    </span>
                </button>
                <a href="{{ url('admin/class_timetable/list') }}"
                   class="text-gray-800 bg-gray-300 hover:bg-gray-400 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-full text-sm px-5 py-2.5 text-center transition-all duration-500 ease-out w-full hover:scale-105">
                    Réinitialiser les filtres
                </a>
            </div>

        </form>

    </div>
</div>
@endsection
