@extends('layouts.app')
@section('content')
<div class="p-4 sm:ml-64">
    <div class="p-4 rounded-lg mt-14">
        <div class="space-x-2 font-semibold">
            <span class="text-emerald-500 text-[25px]"><i class="fa-solid fa-user-secret"></i></span>
            <span>/</span>
            <span class="hover:underline hover:text-emerald-500 transition-all duration-300"><a
                        href="{{ url('admin/admin/list') }}">Liste des administrateurs</a></span>
            <span>/</span>
            <span>Administrateur</span>
        </div>
        <div class="p-4 flex items-center justify-center">
            <div class="w-full max-w-screen-md bg-white shadow-lg mt-24 rounded-md">
                @include('message')
                <form action="" method="post" class="p-5">
                    {{ csrf_field() }}
                    <h2 class="font-bold uppercase text-center text-white rounded-t-md bg-emerald-500 py-3 mb-10">
                        Modifier cet administrateur</h2>
                        <div class="flex mb-5">
                            <input type="text" id="name" name="name" value="{{ $getAdmin -> name }}"
                                   class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                                   placeholder="nom..." required>
                        </div>
                        <div class="flex mb-5">
                            <input type="text" id="last_name" name="last_name" value="{{ $getAdmin -> last_name }}"
                                   class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                                   placeholder="prénom..." required>
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
                        <p class="mb-5 text-red-600 text-[12px]">Est-ce que vous voulez changer le mot de passe ? Si oui
                            veuillez ajouter le nouveau</p>
                    <div class="mb-3">
                        <select id="status" name="status"
                                class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                                required>
                            <option disabled>Définissez un status pour cet administrateur</option>
                            <option {{ (old('status', $getAdmin -> status) == '1') ? 'selected' : '' }} value="1">Actif</option>
                            <option {{ (old('status', $getAdmin -> status) == '0') ? 'selected' : '' }} value="0">Inactif</option>
                        </select>
                    </div>
                    <button type="submit"
                            class="text-white bg-emerald-500 hover:bg-emerald-600 focus:ring-4 focus:outline-none focus:ring-emerald-300 font-medium rounded text-sm px-5 py-2.5 text-center transition-all duration-500 ease-out w-full">
                        Modifier
                    </button>
            </div>
            </form>
        </div>
    </div>
</div>

@endsection

