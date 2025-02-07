@extends('layouts.app')
@section('content')
<div class="m-5">
    <main>
        <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
            <div class="mx-auto max-w-242.5">
                <div
                    class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                >
                    <h2 class="uppercase font-bold text-black dark:text-bodydark">
                        Modifier votre mot de passe
                    </h2>
                    @if(Auth::user()->user_type == 1)
                    <nav>
                        <ol class="flex items-center gap-2">
                            <li>
                                <span class="font-medium text-emerald-400"><i
                                        class="fa-solid fa-user-shield"></i></span>
                            </li>
                            <li>
                                / <a class="font-medium hover:text-emerald-400" href="{{ url('admin/dashboard') }}">Dashboard</a>
                            </li>
                        </ol>
                    </nav>
                    @elseif(Auth::user()->user_type == 2)
                    <nav>
                        <ol class="flex items-center gap-2">
                            <li>
                                <span class="font-medium text-emerald-400"><i
                                        class="fa-solid fa-user-shield"></i></span>
                            </li>
                            <li>
                                / <a class="font-medium hover:text-emerald-400" href="{{ url('teacher/dashboard') }}">Dashboard</a>
                            </li>
                        </ol>
                    </nav>
                    @elseif(Auth::user()->user_type == 3)
                    <nav>
                        <ol class="flex items-center gap-2">
                            <li>
                                <span class="font-medium text-emerald-400"><i
                                        class="fa-solid fa-user-shield"></i></span>
                            </li>
                            <li>
                                / <a class="font-medium hover:text-emerald-400" href="{{ url('student/dashboard') }}">Dashboard</a>
                            </li>
                        </ol>
                    </nav>
                    @elseif(Auth::user()->user_type == 4)
                    <nav>
                        <ol class="flex items-center gap-2">
                            <li>
                                <span class="font-medium text-emerald-400"><i
                                        class="fa-solid fa-user-shield"></i></span>
                            </li>
                            <li>
                                / <a class="font-medium hover:text-emerald-400" href="{{ url('parent/dashboard') }}">Dashboard</a>
                            </li>
                        </ol>
                    </nav>
                    @endif
                </div>
                @include('message')
                <div class="flex flex-col gap-9">
                    <!-- Contact Form -->
                    <div
                        class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark"
                    >
                        <form action="" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="p-6.5">
                                <div class="mb-4.5">
                                    <label
                                        class="mb-3 block text-sm font-medium text-black dark:text-white"
                                    >
                                        Mot de passe
                                    </label>
                                    <input
                                        type="password" id="old_password" name="old_password" value="{{ old('old_password') }}"
                                        placeholder="Entrez votre ancien mot de passe"
                                        class="w-full rounded border-[1.5px] border-stroke bg-gray-100 px-5 py-3 font-normal text-black outline-none transition focus:border-emerald-400 active:border-emerald-400 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-emerald-400"
                                    />
                                </div>
                                <div class="mb-4.5">
                                    <label
                                        class="mb-3 block text-sm font-medium text-black dark:text-white"
                                    >
                                        Mot de passe
                                    </label>
                                    <input
                                        type="password" id="new_password" name="new_password" value="{{ old('new_password') }}"
                                        placeholder="Entrez votre nouveau mot de passe"
                                        class="w-full rounded border-[1.5px] border-stroke bg-gray-100 px-5 py-3 font-normal text-black outline-none transition focus:border-emerald-400 active:border-emerald-400 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-emerald-400"
                                    />
                                </div>
                                <button type="submit"
                                        class="flex w-full justify-center rounded bg-emerald-400 p-3 font-medium text-white hover:bg-opacity-90"
                                >
                                    Modifier
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection


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

