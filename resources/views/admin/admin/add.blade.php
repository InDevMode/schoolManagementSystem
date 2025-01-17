@extends('layouts.app')
@section('content')
<div class="p-4 sm:ml-64">
    <div class="p-4 rounded-lg mt-14">
        <div class="space-x-2 font-semibold">
            <span class="text-violet-500 text-[25px]"><i class="fa-solid fa-user-secret"></i></span>
            <span>/</span>
            <span class="hover:underline hover:text-violet-500 transition-all duration-300"><a
                    href="{{ url('admin/admin/list') }}">Listes des administrateurs</a></span>
            <span>/</span>
            <span>Administrateur</span>
        </div>
        <div class="p-4 flex items-center justify-center">
            <div class="w-full max-w-screen-md bg-white shadow-lg mt-24 rounded-md">
                @include('message')
                <form action="{{ url('admin/admin/add') }}" method="post" class="p-5">
                    {{ csrf_field() }}
                    <h2 class="font-bold uppercase text-center text-white rounded-t-md bg-violet-500 py-3 mb-10">
                        Créer un administrateur</h2>
                    <div class="flex mb-5">
                        <input type="text" id="name" name="name" value="{{ old('name') }}"
                               class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                               placeholder="nom..." required>
                    </div>
                    <div class="flex mb-5">
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                               class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                               placeholder="email..." required>
                    </div>
                    <div class="flex mb-5">
                        <input type="password" id="password" name="password"
                               class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                               placeholder="mot de passe..." required>
                    </div>
                    <button type="submit"
                            class="text-white bg-violet-500 hover:bg-violet-600 focus:ring-4 focus:outline-none focus:ring-violet-300 font-medium rounded text-sm px-5 py-2.5 text-center transition-all duration-500 ease-out w-full">
                        Créer
                    </button>
            </div>
            </form>
        </div>
    </div>
</div>
@endsection

