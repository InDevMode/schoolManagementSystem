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
                <span>Horaire de Cours</span>
            </div>
        </div>

        <form action="" method="get"
              class="my-5 shadow p-3 bg-white rounded border border-gray-300" id="searchForm">
            {{ csrf_field() }}
            <div class="grid grid-cols-3 gap-x-2">
                <div class="flex mb-5">
                    <select id="class_id" name="class_id"
                            class="class_id rounded-full bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                            required>
                        <option disabled selected>Choisissez la classe pour laquelle vous souhaitez définir un horaire
                        </option>
                        @foreach($getClass as $class)
                        <option {{ Request::get('class_id') == $class->id ? 'selected' : '' }} value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex mb-5">
                    <select id="subject_id" name="subject_id"
                            class="subject_id rounded-full bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                            required>
                        <option disabled selected>Choisissez la matière pour laquelle vous souhaitez définir un
                            horaire
                        </option>
                        @if(!empty($getSubject))
                            @foreach($getSubject as $subject)
                            <option {{ Request::get('subject_id') == $getSubject->$subject_id ? 'selected' : '' }} value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <!-- Boutons -->

                <div class="w-full">
                    <a href="{{ url('admin/class_timetable/add') }}"
                       class="block text-gray-800 bg-gray-300 hover:bg-gray-400 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-full text-sm px-5 py-2.5 text-center transition-all duration-500 ease-out w-full hover:scale-105">
                        Réinitialiser les filtres
                    </a>
                </div>
            </div>
        </form>

    </div>
</div>
@endsection




