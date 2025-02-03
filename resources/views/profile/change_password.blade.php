@extends('layouts.app')
@section('content')
<div class="p-4 sm:ml-64">
    <div class="p-4 rounded-lg mt-14">
        <div class="space-x-2 font-semibold">
            <span class="text-emerald-500 text-[25px]"><i class="fa-solid fa-user-secret"></i></span>
            <span><i class="fa-solid fa-chevron-right"></i></span>
            @if(Auth::user()->user_type == 1)
            <span class="hover:underline hover:text-emerald-600 transition-all duration-300"><a
                    href="{{ url('admin/dashboard') }}">Dashboard</a></span>
            @elseif(Auth::user()->user_type == 2)
            <span class="hover:underline hover:text-emerald-600 transition-all duration-300"><a
                    href="{{ url('teacher/dashboard') }}">Dashboard</a></span>
            @elseif(Auth::user()->user_type == 3)
            <span class="hover:underline hover:text-emerald-600 transition-all duration-300"><a
                    href="{{ url('student/dashboard') }}">Dashboard</a></span>
            @elseif(Auth::user()->user_type == 4)
            <span class="hover:underline hover:text-emerald-600 transition-all duration-300"><a
                    href="{{ url('parent/dashboard') }}">Dashboard</a></span>
            @endif
            <span><i class="fa-solid fa-chevron-right"></i></span>
            <span>Profile</span>
        </div>
        <div class="p-4 flex items-center justify-center">
            <div class="w-full max-w-screen-md bg-white shadow-lg mt-24 rounded-md">
                @include('message')
                <h2 class="bg-emerald-500 font-bold uppercase text-center text-white rounded-t-lg py-3">
                    Modifier votre mot de passe</h2>
                <form action="" method="post" class="p-5">
                    {{ csrf_field() }}
                    <div class="flex mb-5">
                        <input type="password" id="old_password" name="old_password" value=""
                               class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                               placeholder="ancien mode de passe..." required>
                    </div>
                    <div class="flex mb-5">
                        <input type="password" id="new_password" name="new_password" value=""
                               class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                               placeholder="nouveau mode de passe..." required>
                    </div>
                    <button type="submit"
                            class="text-white bg-emerald-500 hover:bg-emerald-600 focus:ring-4 focus:outline-none focus:ring-emerald-300 font-medium rounded text-sm px-5 py-2.5 text-center transition-all duration-700 ease-out w-full">
                        Mettre à jour
                    </button>
            </div>
            </form>
        </div>
    </div>
</div>
@endsection

