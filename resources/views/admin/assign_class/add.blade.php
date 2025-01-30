@extends('layouts.app')
@section('content')
<div class="p-4 sm:ml-64">
    <div class="p-4 rounded-lg mt-14">
        <div class="space-x-2 font-semibold">
            <span class="text-violet-500 text-[25px]"><i class="fa-solid fa-arrows-rotate"></i></span>
            <span>/</span>
            <span class="hover:underline hover:text-violet-600 transition-all duration-300"><a
                    href="{{ url('admin/assign_class/list') }}">Liste des assignations</a></span>
            <span>/</span>
            <span>Assignation</span>
        </div>
        <div class="p-4 flex items-center justify-center">
            <div class="w-full max-w-screen-md bg-white shadow-lg mt-24 rounded-md">
                @include('message')
                <h2 class="bg-violet-500 font-bold uppercase text-center text-white rounded-t-lg py-3">
                    Assignez une ou des classe(s) à un professeur</h2>
                <form action="{{ url('admin/assign_class/add') }}" method="post" class="p-5">
                    {{ csrf_field() }}
                    <div class="flex mb-5">
                        <select id="class_id" name="class_id"
                                class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                                required>
                            <option disabled>Choisissez la classe que vous voudriez assignée</option>
                            @foreach($getClass as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex mb-5">
                        <select multiple id="teacher_id" name="teacher_id[]"
                                class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                                required>
                            <option disabled>Choisissez la matière que vous voudriez assignée</option>
                            @foreach($getTeacher as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->name }} {{ $teacher->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex mb-5">
                        <select id="status" name="status"
                                class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                                required>
                            <option disabled>Définissez un status pour cette assignation</option>
                            <option value="1">Activée</option>
                            <option value="0">Désactivée</option>
                        </select>
                    </div>
                    <button type="submit"
                            class="text-white bg-violet-500 hover:bg-violet-600 focus:ring-4 focus:outline-none focus:ring-violet-300 font-medium rounded-md text-sm px-5 py-2.5 text-center transition-all duration-700 ease-out w-full">
                        Assignez
                    </button>
            </div>
            </form>
        </div>
    </div>
</div>
@endsection

