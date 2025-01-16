@extends('layouts.app')
@section('content')
<div class="p-4 mt-40 sm:ml-64 flex items-center justify-center">
    <div class="w-full max-w-screen-md bg-white shadow-lg mt-24 rounded-md">
        @include('message')
        <form action="" method="post" class="p-5">
            {{ csrf_field() }}
            <h2 class="font-bold uppercase text-center text-white rounded-t-md bg-emerald-500 py-3 mb-10">
                Modifier un administrateur</h2>
            <div class="flex mb-5">
                <input type="text" id="name" name="name" value="{{ $getAdmin -> name }}"
                       class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                       placeholder="nom..." required>
            </div>
            <div class="flex mb-5">
                <input type="email" id="email" name="email" value="{{ $getAdmin -> email }}"
                       class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                       placeholder="email..." required>
            </div>
            <div class="flex mb-1">
                <input type="password" id="password" name="password"
                       class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                       placeholder="mot de passe...">
            </div>
            <p class="mb-5 text-red-600 text-[12px]">Est-ce que vous voulez changer le mot de passe ? Si oui veuillez ajouter le nouveau</p>
            <button type="submit"
                    class="text-white bg-emerald-500 hover:bg-emerald-600 focus:ring-4 focus:outline-none focus:ring-emerald-300 font-medium rounded text-sm px-5 py-2.5 text-center transition-all duration-500 ease-out w-full">
                Modifier
            </button>
    </div>
    </form>
</div>
</div>

@endsection

