@extends('layouts.app')
@section('content')
<div class="m-5">
    <!-- Breadcrumb Start -->
    <div
        class="mb-6 mt-3 flex flex-col gap-3 sm:flex-row items-center justify-between"
    >
        <h2 class="uppercase font-bold text-black dark:text-bodydark">
            Liste des programmations
        </h2>
        <nav>
            <ol class="flex items-center gap-2">
                <li>
                    <span class="font-medium text-violet-600"><i class="fa-solid fa-flask-vial"></i></span>
                </li>
                <li>
                    /<a class="font-medium hover:text-violet-600 transition duration-300"
                        href="{{ url('admin/dashboard') }}"> Dashboard</a>
                </li>
                <li>
                    /<a class="font-medium hover:text-violet-600 transition duration-300"
                        href="{{ url('admin/examinations/schedule/add') }}"> Créer une programmation</a>
                </li>
            </ol>
        </nav>
    </div>
    @include('message')

    <div
        class="rounded-lg border border-stroke bg-white px-5 pb-2.5 pt-6 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5 xl:pb-1"
    >
        <form action="" method="get">
            <div class="mb-4.5 grid grid-cols-2 xl:grid-cols-4 gap-3 items-center">
                <div class="w-full">
                    <div
                        x-data="{ isOptionSelected: false }"
                        class="relative z-20 bg-gray-100 dark:bg-form-input"
                    >
                        <select id="exam_id" name="exam_id"
                                class="relative z-20 w-full appearance-none rounded-lg border border-stroke bg-gray-100 px-5 py-2.5 outline-none transition focus:border-violet-600 active:border-violet-600 dark:border-form-strokedark dark:bg-form-input dark:focus:border-violet-600"
                                :class="isOptionSelected && 'text-black dark:text-white'"
                                @change="isOptionSelected = true"
                        >
                            <option selected disabled value="" class="text-body">Choisissez une évaluation</option>
                            @foreach($getExams as $exams)
                            <option value="{{ $exams -> id }}" class="text-body" {{ (Request::get('exam_id') == $exams->id) ? 'selected' : '' }}>{{ $exams -> name }}</option>
                            @endforeach
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
                <div class="w-full">
                    <div
                        x-data="{ isOptionSelected: false }"
                        class="relative z-20 bg-gray-100 dark:bg-form-input"
                    >
                        <select id="class_id" name="class_id"
                                class="relative z-20 w-full appearance-none rounded-lg border border-stroke bg-gray-100 px-5 py-2.5 outline-none transition focus:border-violet-600 active:border-violet-600 dark:border-form-strokedark dark:bg-form-input dark:focus:border-violet-600"
                                :class="isOptionSelected && 'text-black dark:text-white'"
                                @change="isOptionSelected = true"
                        >
                            <option disabled selected value="" class="text-body">Choisissez une classe</option>
                            @foreach($getClass as $class)
                            <option value="{{ $class -> id }}" class="text-body" {{ Request::get('classs_id') == $class->id ? 'selected' : '' }}>{{ $class -> name }}</option>
                            @endforeach
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
                <div class="w-full">
                    <button
                        class="flex w-full justify-between items-center rounded-lg bg-violet-600 px-3 py-2.5 font-medium text-gray hover:bg-opacity-90"
                    >
                        Rechercher
                        <span class="inline-flex items-center text-sm text-gray-900">
                                    <i class="fa-solid fa-search text-white"></i>
                                </span>
                    </button>
                </div>
                <div class="w-full">
                    <a href="{{ url('admin/examinations/schedule/list') }}"
                       class="flex w-full justify-center rounded-lg bg-gray-500 px-3 py-2.5 font-medium text-gray hover:bg-opacity-90"
                    >
                        Réïnitialisez
                    </a>
                </div>
            </div>
        </form>

    </div>
</div>
@endsection



