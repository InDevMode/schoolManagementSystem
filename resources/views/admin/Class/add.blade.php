@extends('layouts.app')
@section('content')
<div class="p-4 sm:ml-64">
    <div class="p-4 rounded-lg mt-14">
        <div class="space-x-2 font-semibold mt-3">
            <span class="text-violet-500"><i class="fa-solid fa-landmark"></i></span>
            <span><i class="fa-solid fa-chevron-right"></i></span>
            <span class="hover:underline hover:text-violet-500 transition-all duration-300"><a
                    href="{{ url('admin/class/list') }}">Liste des classes</a></span>
            <span><i class="fa-solid fa-chevron-right"></i></span>
            <span>Classe</span>
        </div>
        <div class="p-4 flex items-center justify-center">
            <div class="w-full max-w-screen-md bg-white shadow-xl mt-24 rounded-md">
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
                            <option disabled>Définissez un status pour cette classe</option>
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
</div>
@endsection

