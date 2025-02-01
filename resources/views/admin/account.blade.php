@extends('layouts.app')
@section('content')
<div class="p-4 sm:ml-64">
    <div class="p-4 rounded-lg mt-14">
        <div class="space-x-2 font-semibold">
            <span class="text-emerald-500 text-[25px]"><i class="fa-solid fa-user-secret"></i></span>
            <span>/</span>
            <span class="hover:underline hover:text-emerald-500 transition-all duration-300"><a
                    href="{{ url('admin/dashboard') }}">Dashboard</a></span>
            <span>/</span>
            <span>Mon Compte</span>
        </div>
        <div class="p-4 flex items-center justify-center">
            <div class="w-full max-w-screen-md bg-white shadow-lg mt-24 rounded-md">
                @include('message')
                <form action="" method="post" class="p-5">
                    {{ csrf_field() }}
                    <h2 class="font-bold uppercase text-center text-white rounded-t-md bg-emerald-500 py-3 mb-10">
                        Modifier mes informations personnelles</h2>
                    <div class="mb-3">
                        <label class="block mb-2 text-sm font-medium text-gray-900" for="profile_picture">Photo de
                            Profile</label>
                        <input type="file" id="profile_picture" name="profile_picture"
                               class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                               placeholder="Photo de profile...">
                        <img class="h-auto max-w-[100px] rounded-full" src="{{ $profile_picture_url }}" alt="Photo de profile">
                    </div>
                    <div class="flex mb-5">
                        <input type="text" id="name" name="name" value="{{ $getUserData -> name }}"
                               class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                               placeholder="nom..." required>
                    </div>
                    <div class="flex mb-5">
                        <input type="text" id="last_name" name="last_name" value="{{ $getUserData -> last_name }}"
                               class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                               placeholder="prénom..." required>
                    </div>
                    <div class="flex mb-5">
                        <input type="email" id="email" name="email" value="{{ $getUserData -> email }}"
                               class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                               placeholder="email..." required>
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

