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
                        Créer un administrateur
                    </h2>
                    <nav>
                        <ol class="flex items-center gap-2">
                            <li>
                                <span class="font-medium"><i class="fa-solid fa-user-shield"></i></span>
                            </li>
                            <li>
                                <a class="font-medium hover:text-violet-600 transition duration-300"
                                   href="{{ url('admin/admin/list') }}">/ Administrateurs</a>
                            </li>
                        </ol>
                    </nav>
                </div>
                @include('message')
                <div class="flex flex-col gap-9">
                    <!-- Contact Form -->
                    <div
                        class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark"
                    >
                        <form action="{{ url('admin/admin/add') }}" method="post">
                            {{ csrf_field() }}
                            <div class="p-6.5">
                                <div class="mb-4.5">
                                    <div>
                                        <label
                                            class="mb-3 block text-sm font-medium text-black dark:text-white"
                                        >
                                            Photo de profile
                                        </label>
                                        <input
                                            type="file" id="profile_picture" name="profile_picture"
                                            class="w-full cursor-pointer rounded-lg border-[1.5px] border-stroke bg-gray-100 font-normal outline-none transition file:mr-5 file:border-collapse file:cursor-pointer file:border-0 file:border-r file:border-solid file:border-stroke file:bg-whiter file:px-5 file:py-3 file:hover:bg-violet-600 file:hover:bg-opacity-10 focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:file:border-form-strokedark dark:file:bg-white/30 dark:file:text-white dark:focus:border-violet-600"
                                        />
                                    </div>
                                </div>
                                <div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
                                    <div class="w-full xl:w-1/2">
                                        <label
                                            class="mb-3 block text-sm font-medium text-black dark:text-white"
                                        >
                                            Nom <span class="text-meta-1">*</span>
                                        </label>
                                        <input id="name" name="name" value="{{ old('name') }}" required
                                               type="text"
                                               placeholder="Entrez un nom"
                                               class="w-full rounded border-[1.5px] border-stroke bg-gray-100 px-5 py-3 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600"
                                        />
                                    </div>

                                    <div class="w-full xl:w-1/2">
                                        <label
                                            class="mb-3 block text-sm font-medium text-black dark:text-white"
                                        >
                                            Prénom <span class="text-meta-1">*</span>
                                        </label>
                                        <input id="last_name" name="last_name" value="{{ old('last_name') }}" required
                                               type="text"
                                               placeholder="Entrez un prénom"
                                               class="w-full rounded border-[1.5px] border-stroke bg-gray-100 px-5 py-3 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600"
                                        />
                                    </div>
                                </div>

                                <div class="mb-4.5">
                                    <label
                                        class="mb-3 block text-sm font-medium text-black dark:text-white"
                                    >
                                        Email <span class="text-meta-1">*</span>
                                    </label>
                                    <input
                                        type="email" id="email" name="email" value="{{ old('email') }}" required
                                        placeholder="Entrez votre un adresse email"
                                        class="w-full rounded border-[1.5px] border-stroke bg-gray-100 px-5 py-3 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600"
                                    />
                                </div>
                                <div class="mb-4.5">
                                    <label
                                        class="mb-3 block text-sm font-medium text-black dark:text-white"
                                    >
                                        Mot de passe <span class="text-meta-1">*</span>
                                    </label>
                                    <input
                                        type="password" id="password" name="password" value="{{ old('password') }}"
                                        required
                                        placeholder="Entrez votre un mot de passe"
                                        class="w-full rounded border-[1.5px] border-stroke bg-gray-100 px-5 py-3 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600"
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
                                                class="relative z-20 w-full appearance-none rounded border border-stroke bg-gray-100 px-5 py-3 outline-none transition focus:border-violet-600 active:border-violet-600 dark:border-form-strokedark dark:bg-form-input dark:focus:border-violet-600"
                                                :class="isOptionSelected && 'text-black dark:text-white'"
                                                @change="isOptionSelected = true"
                                        >
                                            <option selected disabled value="" class="text-body">
                                                Choisissez un statut pour cet administrateur
                                            </option>
                                            <option class="text-body" value="1" {{ (old(
                                            'status') == '1') ? 'selected' : '' }}>Actif</option>
                                            <option class="text-body" value="0" {{ (old(
                                            'status') == '0') ? 'selected' : '' }}>Inactif</option>
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
                                        class="flex w-full justify-center rounded bg-violet-600 p-3 font-medium text-gray hover:bg-opacity-90"
                                >
                                    Créer
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

