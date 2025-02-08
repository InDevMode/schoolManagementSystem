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
                        Modifier cette classe
                    </h2>
                    <nav>
                        <ol class="flex items-center gap-2">
                            <li>
                                <span class="font-medium text-emerald-400"><i class="fa-solid fa-landmark"></i></span>
                            </li>
                            <li>
                                /<a class="font-medium hover:text-emerald-400 transition duration-300"
                                    href="{{ url('admin/class/list') }}"> Liste des classes</a>
                            </li>
                        </ol>
                    </nav>
                </div>
                @include('message')
                <div class="flex flex-col gap-9">
                    <!-- Contact Form -->
                    <div
                        class="rounded-lg border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark"
                    >
                        <form action="" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="p-6.5">
                                <div class="mb-4.5">
                                    <label
                                        class="mb-3 block text-sm font-medium text-black dark:text-white"
                                    >
                                        Nom <span class="text-meta-1">*</span>
                                    </label>
                                    <input id="name" name="name" value="{{ old('name', $getClass->name) }}" required
                                           type="text"
                                           placeholder="Entrez un nom d'une classe"
                                           class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition focus:border-emerald-400 active:border-emerald-400 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-emerald-400"
                                    />
                                </div>
                                <div class="mb-4.5">
                                    <label
                                        class="mb-3 block text-sm font-medium text-black dark:text-white"
                                    >
                                        Status <span class="text-meta-1">*</span>
                                    </label>
                                    <div
                                        x-data="{ isOptionSelected: false }"
                                        class="relative z-20 bg-gray-100 dark:bg-form-input"
                                    >
                                        <select id="status" name="status" required
                                                class="relative z-20 w-full appearance-none rounded-lg border border-stroke bg-gray-100 px-5 py-2.5 outline-none transition focus:border-emerald-400 active:border-emerald-400 dark:border-form-strokedark dark:bg-form-input dark:focus:border-emerald-400"
                                                :class="isOptionSelected && 'text-black dark:text-white'"
                                                @change="isOptionSelected = true"
                                        >
                                            <option selected disabled value="" class="text-body">
                                                Choisissez un statut pour cette classe
                                            </option>
                                            <option class="text-body" value="1" {{ (old(
                                            'status', $getClass->status) == '1') ? 'selected' : '' }}>Activée</option>
                                            <option class="text-body" value="0" {{ (old(
                                            'status', $getClass->status) == '0') ? 'selected' : '' }}>Désactivée</option>
                                        </select>
                                        <span
                                            class="absolute right-4 top-1/2 z-30 -translate-y-1/2"
                                        >
                                        <svg
                                            class="fill-current"
                                            width="24"
                                            height="24"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            xmlns="http://www.w3.org/2000/svg"
                                        >
                                          <g opacity="0.8">
                                            <path
                                                fill-rule="evenodd"
                                                clip-rule="evenodd"
                                                d="M5.29289 8.29289C5.68342 7.90237 6.31658 7.90237 6.70711 8.29289L12 13.5858L17.2929 8.29289C17.6834 7.90237 18.3166 7.90237 18.7071 8.29289C19.0976 8.68342 19.0976 9.31658 18.7071 9.70711L12.7071 15.7071C12.3166 16.0976 11.6834 16.0976 11.2929 15.7071L5.29289 9.70711C4.90237 9.31658 4.90237 8.68342 5.29289 8.29289Z"
                                                fill=""
                                            ></path>
                                          </g>
                                        </svg>
                                      </span>
                                    </div>
                                </div>
                                <button type="submit"
                                        class="flex w-full justify-center rounded-lg bg-emerald-400 p-3 font-medium text-gray hover:bg-opacity-90"
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
        <div class="space-x-2 font-semibold mt-3">
            <span class="text-emerald-500"><i class="fa-solid fa-landmark"></i></span>
            <span><i class="fa-solid fa-chevron-right"></i></span>
            <span class="hover:underline hover:text-emerald-500 transition-all duration-300"><a
                    href="{{ url('admin/class/list') }}">Liste des classes</a></span>
            <span><i class="fa-solid fa-chevron-right"></i></span>
            <span>Classe</span>
        </div>
        <div class="p-4 flex items-center justify-center">
            <div class="w-full max-w-screen-md bg-white shadow-xl mt-24 rounded-md">
                @include('message')
                <h2 class="bg-emerald-500 font-bold uppercase text-center text-white rounded-t-lg py-3">
                    Modifier cette classe</h2>
                <form action="" method="post" class="p-5">
                    {{ csrf_field() }}
                    <div class="flex mb-5">
                        <input type="text" id="name" name="name" value="{{ $getClass->name }}"
                               class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                               placeholder="nom de la classe..." required>
                    </div>
                    <div class="flex mb-5">
                        <select id="status" name="status"
                                class="rounded bg-gray-100 border border-gray-300 text-gray-900 focus:ring-violet-500 focus:border-violet-500 block w-full text-sm ps-3"
                                required>
                            <option disabled>Définissez un statut pour cette classe</option>
                            <option value="1" name="status" {{ $getClass->status == 1 ? 'selected' : '' }}>Activée
                            </option>
                            <option value="0" name="status" {{ $getClass->status == 0 ? 'selected' : '' }}>Désactivée
                            </option>
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
</div>
@endsection

