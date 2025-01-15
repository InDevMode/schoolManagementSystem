@extends('layouts.app')
@section('content')
<div class="p-4 mt-72 sm:ml-64 flex items-center justify-center">
    <div class="p-8 w-full max-w-screen-md shadow-xl rounded bg-gray-100 border">
        @include('message')
        <form action="{{ url('admin/assign_subject/add') }}" method="post" class="">
            {{ csrf_field() }}
            <h2 class="lg:text-3xl sm:text-2xl text-xl font-bold uppercase text-center text-gray-700 rounded mb-10">
                Assignez une nouvelle matière</h2>
            <div class="flex mb-5">
                <select id="class_id" name="class_id"
                        class="rounded bg-white border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500"
                        required>
                    <option selected>Choisissez la classe que vous voudriez assignée</option>
                    @foreach($getClass as $class)
                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex mb-5">
                <select multiple id="subject_id" name="subject_id[]"
                        class="rounded bg-white border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500"
                        required>
                    <option selected>Choisissez la matière que vous voudriez assignée</option>
                    @foreach($getSubject as $subject)
                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex mb-5">
                <select id="status" name="status"
                        class="rounded bg-white border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500"
                        required>
                    <option selected>Définissez un status pour cette assignation</option>
                    <option value="1">Activée</option>
                    <option value="0">Désactivée</option>
                </select>
            </div>
            <button type="submit"
                    class="text-white bg-violet-600 hover:bg-violet-800 focus:ring-4 focus:outline-none focus:ring-violet-300 font-medium rounded-md text-sm px-5 py-2.5 text-center dark:bg-violet-600 dark:hover:bg-violet-700 dark:focus:ring-violet-800 transition-all duration-500 ease-out w-full hover:scale-105">
                Assignez
            </button>
    </div>
    </form>
</div>
</div>
@endsection

