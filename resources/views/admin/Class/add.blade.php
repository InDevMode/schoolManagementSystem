@extends('layouts.app')
@section('content')
<div class="p-4 mt-40 sm:ml-64 flex items-center justify-center">
    <div class="w-full max-w-screen-md bg-white shadow-lg mt-24 rounded-md">
        @include('message')
        <h2 class="bg-violet-500 font-bold uppercase text-center text-white rounded-t-lg py-3">
            Créer une nouvelle classe</h2>
        <form action="{{ url('admin/class/add') }}" method="post" class="p-5">
            {{ csrf_field() }}
            <div class="flex mb-5">
                <input type="text" id="name" name="name" value="{{ old('name') }}"
                       class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                       placeholder="nom de la classe..." required>
            </div>
            <div class="flex mb-5">
                <select id="status" name="status"
                        class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                        required>
                    <option disabled selected>Définissez un status pour cette classe</option>
                    <option value="1">Activée</option>
                    <option value="0">Désactivée</option>
                </select>
            </div>
            <button type="submit"
                    class="text-white bg-violet-500 hover:bg-violet-600 focus:ring-4 focus:outline-none focus:ring-violet-300 font-medium rounded-md text-sm px-5 py-2.5 text-center transition-all duration-700 ease-out w-full">
                Créer
            </button>
    </div>
    </form>
</div>
</div>
@endsection

