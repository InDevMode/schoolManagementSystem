@extends('layouts.app')
@section('content')
<div class="p-4 mt-52 ms-28 flex items-center justify-center">
    <div class="w-full max-w-screen-md shadow-xl rounded bg-gray-100 border">
        @include('message')
        <h2 class="bg-violet-500 font-bold uppercase text-center text-white rounded-t-lg py-3">
            Assignez une ou des matière(s) à une classe</h2>
        <form action="{{ url('admin/assign_subject/add') }}" method="post" class="p-5">
            {{ csrf_field() }}
            <div class="flex mb-5">
                <select id="class_id" name="class_id"
                        class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                        required>
                    <option disabled selected>Choisissez la classe que vous voudriez assignée</option>
                    @foreach($getClass as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex mb-5">
                <select multiple id="subject_id" name="subject_id[]"
                        class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                        required>
                    <option disabled selected>Choisissez la matière que vous voudriez assignée</option>
                    @foreach($getSubject as $subject)
                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex mb-5">
                <select id="status" name="status"
                        class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                        required>
                    <option disabled selected>Définissez un status pour cette assignation</option>
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
@endsection

