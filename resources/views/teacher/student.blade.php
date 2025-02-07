@extends('layouts.app')
@section('content')
<div class="m-5">
    <!-- Breadcrumb Start -->
    <div
        class="mb-6 mt-3 flex flex-col gap-3 sm:flex-row items-center justify-between"
    >
        <h2 class="uppercase font-bold text-black dark:text-bodydark">
            Liste de mes apprenants
        </h2>
        <nav>
            <ol class="flex items-center gap-2">
                <li>
                    <span class="font-medium text-violet-600"><i class="fa-solid fa-house-chimney"></i></span>
                </li>
                <li>
                    <a class="font-medium hover:text-violet-600 transition duration-300"
                       href="{{ url('teacher/dashboard') }}">/ Dashboard</a>
                </li>
                <li>
                    <a class="font-medium hover:text-violet-600 transition duration-300"
                       href="{{ url('teacher/my_student') }}">/ Apprenants</a>
                </li>
            </ol>
        </nav>
    </div>
    @include('message')
    <div class="">
        <div class="mt-4">
            {{ $getTeacherStudent->links('vendor.pagination.tailwind') }}
        </div>
    </div>
    <div
        class="rounded-sm border border-stroke bg-white px-5 pb-2.5 pt-6 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5 xl:pb-1"
    >
        <form action="" method="get">
            <div class="mb-4.5 grid grid-cols-2 xl:grid-cols-4 gap-3 items-center">
                <div class="w-full xl:w-1/8">
                    <input
                        type="text" id="admission_number" name="admission_number"
                        value="{{ Request::get('admission_number') }}"
                        placeholder="numéro d'admission..."
                        class="w-full rounded border-[1.5px] border-stroke bg-transparent px-5 py-3 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600"
                    />
                </div>
                <div class="w-full xl:w-1/8">
                    <input
                        type="text" id="name" name="name" value="{{ Request::get('name') }}"
                        placeholder="nom..."
                        class="w-full rounded border-[1.5px] border-stroke bg-transparent px-5 py-3 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600"
                    />
                </div>
                <div class="w-full xl:w-1/8">
                    <input
                        type="text" id="last_name" name="last_name" value="{{ Request::get('last_name') }}"
                        placeholder="prénom..."
                        class="w-full rounded border-[1.5px] border-stroke bg-transparent px-5 py-3 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600"
                    />
                </div>
                <div class="w-full xl:w-1/8">
                    <input
                        type="email" id="email" name="email" value="{{ Request::get('email') }}"
                        placeholder="email..."
                        class="w-full rounded border-[1.5px] border-stroke bg-transparent px-5 py-3 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600"
                    />
                </div>
                <div class="w-full xl:w-1/8">
                    <div
                        x-data="{ isOptionSelected: false }"
                        class="relative z-20 bg-transparent dark:bg-form-input"
                    >
                        <select id="status" name="status"
                                class="relative z-20 w-full appearance-none rounded border border-stroke bg-transparent px-5 py-3 outline-none transition focus:border-primary active:border-primary dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary"
                                :class="isOptionSelected && 'text-black dark:text-white'"
                                @change="isOptionSelected = true"
                        >
                            <option value="" class="text-body">
                                Statut...
                            </option>
                            <option value="0" class="text-body" {{ Request::get(
                            'status') == '0' ? 'selected' : '' }}>Inactif</option>
                            <option value="1" class="text-body" {{ Request::get(
                            'status') == '1' ? 'selected' : '' }}>Actif</option>
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
                <div class="w-full xl:w-1/8">
                    <div
                        x-data="{ isOptionSelected: false }"
                        class="relative z-20 bg-transparent dark:bg-form-input"
                    >
                        <select id="gender" name="gender"
                                class="relative z-20 w-full appearance-none rounded border border-stroke bg-transparent px-5 py-3 outline-none transition focus:border-primary active:border-primary dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary"
                                :class="isOptionSelected && 'text-black dark:text-white'"
                                @change="isOptionSelected = true"
                        >
                            <option value="" class="text-body">
                                Statut...
                            </option>
                            <option value="male" class="text-body" {{ Request::get(
                            'gender') == 'male' ? 'selected' : '' }}>Masculin</option>
                            <option value="female" class="text-body" {{ Request::get(
                            'gender') == 'female' ? 'selected' : '' }}>Féminin</option>
                            <option value="other" class="text-body" {{ Request::get(
                            'gender') == 'other' ? 'selected' : '' }}>Autre</option>
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
                <div class="w-full xl:w-1/8">
                    <div class="relative">
                        <input id="date_of_birth" name="date_of_birth" value="{{ Request::get('date_of_birth') }}"
                               class="form-datepicker w-full rounded border-[1.5px] border-stroke bg-transparent px-5 py-3 font-normal outline-none transition focus:border-violet-600 active:border-violet-600 dark:border-form-strokedark dark:bg-form-input dark:focus:border-violet-600"
                               placeholder="date de naissance..."
                               data-class="flatpickr-right"
                        />

                        <div
                            class="pointer-events-none absolute inset-0 left-auto right-5 flex items-center"
                        >
                            <svg
                                width="18"
                                height="18"
                                viewBox="0 0 18 18"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M15.7504 2.9812H14.2879V2.36245C14.2879 2.02495 14.0066 1.71558 13.641 1.71558C13.2754 1.71558 12.9941 1.99683 12.9941 2.36245V2.9812H4.97852V2.36245C4.97852 2.02495 4.69727 1.71558 4.33164 1.71558C3.96602 1.71558 3.68477 1.99683 3.68477 2.36245V2.9812H2.25039C1.29414 2.9812 0.478516 3.7687 0.478516 4.75308V14.5406C0.478516 15.4968 1.26602 16.3125 2.25039 16.3125H15.7504C16.7066 16.3125 17.5223 15.525 17.5223 14.5406V4.72495C17.5223 3.7687 16.7066 2.9812 15.7504 2.9812ZM1.77227 8.21245H4.16289V10.9968H1.77227V8.21245ZM5.42852 8.21245H8.38164V10.9968H5.42852V8.21245ZM8.38164 12.2625V15.0187H5.42852V12.2625H8.38164V12.2625ZM9.64727 12.2625H12.6004V15.0187H9.64727V12.2625ZM9.64727 10.9968V8.21245H12.6004V10.9968H9.64727ZM13.8379 8.21245H16.2285V10.9968H13.8379V8.21245ZM2.25039 4.24683H3.71289V4.83745C3.71289 5.17495 3.99414 5.48433 4.35977 5.48433C4.72539 5.48433 5.00664 5.20308 5.00664 4.83745V4.24683H13.0504V4.83745C13.0504 5.17495 13.3316 5.48433 13.6973 5.48433C14.0629 5.48433 14.3441 5.20308 14.3441 4.83745V4.24683H15.7504C16.0316 4.24683 16.2566 4.47183 16.2566 4.75308V6.94683H1.77227V4.75308C1.77227 4.47183 1.96914 4.24683 2.25039 4.24683ZM1.77227 14.5125V12.2343H4.16289V14.9906H2.25039C1.96914 15.0187 1.77227 14.7937 1.77227 14.5125ZM15.7504 15.0187H13.8379V12.2625H16.2285V14.5406C16.2566 14.7937 16.0316 15.0187 15.7504 15.0187Z"
                                    fill="#64748B"
                                />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="w-full xl:w-1/8">
                    <div class="relative">
                        <input id="created_at" name="created_at" value="{{ Request::get('created_at') }}"
                               class="form-datepicker w-full rounded border-[1.5px] border-stroke bg-transparent px-5 py-3 font-normal outline-none transition focus:border-violet-600 active:border-violet-600 dark:border-form-strokedark dark:bg-form-input dark:focus:border-violet-600"
                               placeholder="creation..."
                               data-class="flatpickr-right"
                        />

                        <div
                            class="pointer-events-none absolute inset-0 left-auto right-5 flex items-center"
                        >
                            <svg
                                width="18"
                                height="18"
                                viewBox="0 0 18 18"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M15.7504 2.9812H14.2879V2.36245C14.2879 2.02495 14.0066 1.71558 13.641 1.71558C13.2754 1.71558 12.9941 1.99683 12.9941 2.36245V2.9812H4.97852V2.36245C4.97852 2.02495 4.69727 1.71558 4.33164 1.71558C3.96602 1.71558 3.68477 1.99683 3.68477 2.36245V2.9812H2.25039C1.29414 2.9812 0.478516 3.7687 0.478516 4.75308V14.5406C0.478516 15.4968 1.26602 16.3125 2.25039 16.3125H15.7504C16.7066 16.3125 17.5223 15.525 17.5223 14.5406V4.72495C17.5223 3.7687 16.7066 2.9812 15.7504 2.9812ZM1.77227 8.21245H4.16289V10.9968H1.77227V8.21245ZM5.42852 8.21245H8.38164V10.9968H5.42852V8.21245ZM8.38164 12.2625V15.0187H5.42852V12.2625H8.38164V12.2625ZM9.64727 12.2625H12.6004V15.0187H9.64727V12.2625ZM9.64727 10.9968V8.21245H12.6004V10.9968H9.64727ZM13.8379 8.21245H16.2285V10.9968H13.8379V8.21245ZM2.25039 4.24683H3.71289V4.83745C3.71289 5.17495 3.99414 5.48433 4.35977 5.48433C4.72539 5.48433 5.00664 5.20308 5.00664 4.83745V4.24683H13.0504V4.83745C13.0504 5.17495 13.3316 5.48433 13.6973 5.48433C14.0629 5.48433 14.3441 5.20308 14.3441 4.83745V4.24683H15.7504C16.0316 4.24683 16.2566 4.47183 16.2566 4.75308V6.94683H1.77227V4.75308C1.77227 4.47183 1.96914 4.24683 2.25039 4.24683ZM1.77227 14.5125V12.2343H4.16289V14.9906H2.25039C1.96914 15.0187 1.77227 14.7937 1.77227 14.5125ZM15.7504 15.0187H13.8379V12.2625H16.2285V14.5406C16.2566 14.7937 16.0316 15.0187 15.7504 15.0187Z"
                                    fill="#64748B"
                                />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="w-full xl:w-1/8">
                    <div class="relative">
                        <input id="updated_at" name="updated_at" value="{{ Request::get('updated_at') }}"
                               class="form-datepicker w-full rounded border-[1.5px] border-stroke bg-transparent px-5 py-3 font-normal outline-none transition focus:border-violet-600 active:border-violet-600 dark:border-form-strokedark dark:bg-form-input dark:focus:border-violet-600"
                               placeholder="modification..."
                               data-class="flatpickr-right"
                        />
                        <div
                            class="pointer-events-none absolute inset-0 left-auto right-5 flex items-center"
                        >
                            <svg
                                width="18"
                                height="18"
                                viewBox="0 0 18 18"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M15.7504 2.9812H14.2879V2.36245C14.2879 2.02495 14.0066 1.71558 13.641 1.71558C13.2754 1.71558 12.9941 1.99683 12.9941 2.36245V2.9812H4.97852V2.36245C4.97852 2.02495 4.69727 1.71558 4.33164 1.71558C3.96602 1.71558 3.68477 1.99683 3.68477 2.36245V2.9812H2.25039C1.29414 2.9812 0.478516 3.7687 0.478516 4.75308V14.5406C0.478516 15.4968 1.26602 16.3125 2.25039 16.3125H15.7504C16.7066 16.3125 17.5223 15.525 17.5223 14.5406V4.72495C17.5223 3.7687 16.7066 2.9812 15.7504 2.9812ZM1.77227 8.21245H4.16289V10.9968H1.77227V8.21245ZM5.42852 8.21245H8.38164V10.9968H5.42852V8.21245ZM8.38164 12.2625V15.0187H5.42852V12.2625H8.38164V12.2625ZM9.64727 12.2625H12.6004V15.0187H9.64727V12.2625ZM9.64727 10.9968V8.21245H12.6004V10.9968H9.64727ZM13.8379 8.21245H16.2285V10.9968H13.8379V8.21245ZM2.25039 4.24683H3.71289V4.83745C3.71289 5.17495 3.99414 5.48433 4.35977 5.48433C4.72539 5.48433 5.00664 5.20308 5.00664 4.83745V4.24683H13.0504V4.83745C13.0504 5.17495 13.3316 5.48433 13.6973 5.48433C14.0629 5.48433 14.3441 5.20308 14.3441 4.83745V4.24683H15.7504C16.0316 4.24683 16.2566 4.47183 16.2566 4.75308V6.94683H1.77227V4.75308C1.77227 4.47183 1.96914 4.24683 2.25039 4.24683ZM1.77227 14.5125V12.2343H4.16289V14.9906H2.25039C1.96914 15.0187 1.77227 14.7937 1.77227 14.5125ZM15.7504 15.0187H13.8379V12.2625H16.2285V14.5406C16.2566 14.7937 16.0316 15.0187 15.7504 15.0187Z"
                                    fill="#64748B"
                                />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="w-full xl:w-1/8">
                    <button
                        class="flex w-full justify-between items-center rounded bg-violet-600 p-3 font-medium text-gray hover:bg-opacity-90"
                    >
                        Rechercher
                        <span class="inline-flex items-center text-sm text-gray-900">
                                    <i class="fa-solid fa-search text-white"></i>
                                </span>
                    </button>
                </div>
                <div class="w-full xl:w-1/8">
                    <a href="{{ url('teacher/my_student') }}"
                       class="flex w-full justify-center rounded bg-bodydark2 p-3 font-medium text-gray hover:bg-opacity-90"
                    >
                        Réïnitialisez
                    </a>
                </div>
            </div>
        </form>

        <div class="flex flex-col">
            <div
                class="grid grid-cols-2 sm:grid-cols-4 3xl:grid-cols-8 rounded-sm bg-gray-200 dark:bg-meta-4"
            >
                <div class="hidden 3xl:block p-2.5 3xl:p-5">
                    <h5 class="text-sm font-medium uppercase xsm:text-base">N°</h5>
                </div>
                <div class="p-2.5 3xl:p-5">
                    <h5 class="text-sm font-medium uppercase xsm:text-base">Nom & Prénoms</h5>
                </div>
                <div class="hidden 3xl:block p-2.5 3xl:p-5">
                    <h5 class="text-sm font-medium uppercase xsm:text-base">Email</h5>
                </div>
                <div class="hidden sm:block p-2.5 3xl:p-5">
                    <h5 class="text-sm font-medium uppercase xsm:text-base">Status</h5>
                </div>
                <div class="p-2.5 3xl:p-5">
                    <h5 class="text-sm font-medium uppercase xsm:text-base">Classe</h5>
                </div>
                <div class="hidden sm:block p-2.5 3xl:p-5">
                    <h5 class="text-sm font-medium uppercase xsm:text-base">Genre</h5>
                </div>
                <div class="hidden 3xl:block p-2.5 3xl:p-5">
                    <h5 class="text-sm font-medium uppercase xsm:text-base">Date de Naissance</h5>
                </div>
                <div class="hidden 3xl:block p-2.5 3xl:p-5">
                    <h5 class="text-sm font-medium uppercase xsm:text-base">Date de Création</h5>
                </div>
            </div>
            @foreach($getTeacherStudent as $index => $teacherStudent)
            <div
                class="grid grid-cols-2 sm:grid-cols-4 3xl:grid-cols-8 hover:bg-gray-2 dark:hover:bg-gray-700 transition duration-300">
                <div class="hidden p-2.5 3xl:p-5 3xl:flex items-center">
                    <p class="font-medium text-sm text-black dark:text-white">
                        {{ $teacherStudent->admission_number }}
                    </p>
                </div>
                <div class="flex items-center p-2.5 3xl:p-5">
                    <p class="font-medium text-black dark:text-white">
                        {{ $teacherStudent->name }} {{ $teacherStudent->last_name }}
                    </p>
                </div>
                <div class="hidden p-2.5 3xl:p-5 3xl:flex items-center">
                    <p class="font-medium text-sm me-2 px-2.5 py-0.5 rounded bg-gray-2 dark:bg-gray-700">
                        {{ $teacherStudent -> email }}</p>
                </div>
                <div class="hidden sm:flex items-center p-2.5 3xl:p-5">
                    <p class="font-medium text-sm text-black dark:text-white flex items-center">
                    <div
                        class="h-2.5 w-2.5 rounded-full {{ $teacherStudent->status == 1 ? 'bg-emerald-500' : 'bg-red-500' }} me-2"></div>
                    {{ $teacherStudent->status == 1 ? 'Actif' : 'Inactif' }}
                    </p>
                </div>
                <div class="flex p-2.5 3xl:p-5">
                    <p class="font-medium text-sm text-black dark:text-white">
                        {{ $teacherStudent->class_name }}
                    </p>
                </div>
                <div class="hidden sm:flex items-center p-2.5 3xl:p-5">
                    <p class="font-medium text-sm px-4 py-1 rounded-full dark:text-white
                {{ $teacherStudent->gender == 'male' ? 'text-violet-700 bg-violet-100 dark:bg-violet-900' : ($teacherStudent->gender == 'female' ? 'text-red-700 bg-red-100 dark:bg-red-900' : 'text-pink-700 bg-pink-100 dark:bg-pink-900') }}">
                        {{ $teacherStudent->gender == 'male' ? 'Masculin' : ($teacherStudent->gender == 'female' ? 'Féminin' : 'Autre')
                        }}
                    </p>
                </div>
                <div class="hidden 3xl:flex p-2.5 3xl:p-5">
                    <p class="font-medium text-sm text-meta-5">
                        {{ \Carbon\Carbon::parse($teacherStudent->date_of_birth)->locale('fr')->translatedFormat('d M Y')
                        }}
                    </p>
                </div>
                <div class="hidden 3xl:flex p-2.5  3xl:p-5">
                    <p class="font-medium text-sm text-meta-5">
                        {{ \Carbon\Carbon::parse($teacherStudent->created_at)->locale('fr')->translatedFormat('d M Y H:i:s')
                        }}
                    </p>
                </div>
            </div>
            @endforeach

            @if($getTeacherStudent->isEmpty())
            <div class="flex justify-center">
                <div class="py-3"> Aucun apprenant assigné.</div>
            </div>
            @endif
            <div
                class="mb-6 mt-3 border-t border-gray-200 pt-2 flex gap-3 items-center justify-between"
            >
                <h2 class="text-title-sm uppercase font-bold text-black dark:text-white">
                    Total
                </h2>
                <nav>
                    <ol class="flex items-center gap-2 bg-bodydark1 w-fit dark:bg-black p-2 px-8 rounded">
                        <li>
                            <p class="text-md font-medium">{{ $getTeacherStudent->total() }}</p>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
@endsection


