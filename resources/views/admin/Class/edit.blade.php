@extends('layouts.app')
@section('content')
<div class="p-4 mt-52 ms-28 flex items-center justify-center">
    <div class="w-full max-w-screen-md shadow-xl rounded bg-white">
        @include('message')
        <h2 class="bg-emerald-500 font-bold uppercase text-center text-white rounded-t-lg py-3">
            Modifier une classe</h2>
        <form action="" method="post" class="p-5">
            {{ csrf_field() }}
            <div class="flex mb-5">
                <input type="text" id="name" name="name" value="{{ $getClass->name }}"
                       class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                       placeholder="nom de la classe..." required>
            </div>
            <div class="flex mb-5">
                <select id="status" name="status" class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3" required>
                    <option disabled selected>Définissez un statut pour cette classe</option>
                    <option value="1" name="status" {{ $getClass->status == 1 ? 'selected' : '' }}>Activée </option>
                    <option value="0" name="status" {{ $getClass->status == 0 ? 'selected' : '' }}>Désactivée </option>
                </select>
            </div>
            <button type="submit"
                    class="text-white bg-emerald-500 hover:bg-emerald-600 focus:ring-4 focus:outline-none focus:ring-emerald-300 font-medium rounded text-sm px-5 py-2.5 text-center transition-all duration-700 ease-out w-full">
                Modifier
            </button>
    </div>
    </form>
</div>
</div>
@endsection

