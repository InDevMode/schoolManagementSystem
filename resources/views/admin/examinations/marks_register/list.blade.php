@extends('layouts.app')
@section('content')
<div class="m-5">
    <!-- Breadcrumb Start -->
    <div
        class="mb-6 mt-3 flex flex-col gap-3 sm:flex-row items-center justify-between"
    >
        <h2 class="uppercase font-bold text-black dark:text-bodydark">
            Liste des registres de notes
        </h2>
        <nav>
            <ol class="flex items-center gap-2">
                <li>
                    <span class="font-medium text-violet-600"><i class="fa-solid fa-registered"></i></span>
                </li>
                <li>
                    /<a class="font-medium hover:text-violet-600 transition duration-300"
                        href="{{ url('admin/dashboard') }}"> Dashboard</a>
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
                        <select id="exam_id" name="exam_id" required
                                class="relative z-20 w-full appearance-none rounded-lg border border-stroke bg-gray-100 px-5 py-2.5 outline-none transition focus:border-violet-600 active:border-violet-600 dark:border-form-strokedark dark:bg-form-input dark:focus:border-violet-600"
                                :class="isOptionSelected && 'text-black dark:text-white'"
                                @change="isOptionSelected = true"
                        >
                            <option selected disabled value="" class="text-body">Choisissez une évaluation</option>
                            @foreach($getExams as $exams)
                            <option value="{{ $exams -> id }}" class="text-body" {{ (Request::get(
                            'exam_id') == $exams->id) ? 'selected' : '' }}>{{ $exams -> name }}</option>
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
                        <select id="class_id" name="class_id" required
                                class="relative z-20 w-full appearance-none rounded-lg border border-stroke bg-gray-100 px-5 py-2.5 outline-none transition focus:border-violet-600 active:border-violet-600 dark:border-form-strokedark dark:bg-form-input dark:focus:border-violet-600"
                                :class="isOptionSelected && 'text-black dark:text-white'"
                                @change="isOptionSelected = true"
                        >
                            <option disabled selected value="" class="text-body">Choisissez une classe</option>
                            @foreach($getClass as $class)
                            <option value="{{ $class -> id }}" class="text-body" {{ Request::get(
                            'class_id') == $class->id ? 'selected' : '' }}>{{ $class -> name }}</option>
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
                    <a href="{{ url('admin/examinations/marks_register/list') }}"
                       class="flex w-full justify-center rounded-lg bg-gray-500 px-3 py-2.5 font-medium text-gray hover:bg-opacity-90"
                    >
                        Réïnitialisez
                    </a>
                </div>
            </div>
        </form>

        @if(!empty($getSubject) && !empty($getSubject->count()))
        <div class="relative overflow rounded-lg z-10">
            <table class="w-full text-sm text-left rtl:text-right text-white dark:text-white">
                <thead
                    class="rounded-sm bg-violet-600 uppercase text-white dark:bg-meta-4"
                >
                <tr>
                    <th scope="col" class="px-6 py-3">
                        Apprenants
                    </th>
                    @foreach($getSubject as $subject)
                    <th scope="col" class="px-6 py-3">
                        {{ $subject->subject_name }} <span
                            class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 rounded text-sm  py-1 px-2"> {{ $subject->subject_type == 'practical' ? 'Pratique' : 'Théorique' }}</span>
                        ({{ $subject->passing_marks }} / {{ $subject->full_marks }})
                    </th>
                    @endforeach
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Actions
                    </th>
                </tr>
                </thead>
                <tbody>
                @if(!empty($getStudent) && !empty($getStudent->count()))
                @foreach($getStudent as $student)
                <tr class="hover:bg-violet-100 dark:hover:bg-gray-700 transition duration-300 border-b dark:border-gray-600 hover:border-violet-400 dark:text-gray-200 text-gray-500">
                    <td class="px-6 py-3">
                        {{ $student->name }} {{ $student->last_name }}
                    </td>
                    @foreach($getSubject as $subject)
                    <td class="px-6 py-3">
                        <div>
                            <label
                                class="mb-3 block text-sm font-medium text-black dark:text-white"
                            >
                                Travail de classe <span class="text-meta-1">*</span>
                                <input type="text"
                                       id=""
                                       name=""
                                       value=""
                                       placeholder="Entrez une note de classe"
                                       class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600">
                        </div>
                        <div>
                            <label
                                class="mb-3 block text-sm font-medium text-black dark:text-white"
                            >
                                Travail de maison <span class="text-meta-1">*</span>
                                <input type="text"
                                       id=""
                                       name=""
                                       value=""
                                       placeholder="Entrez une note de classe"
                                       class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600">
                        </div>
                        <div>
                            <label
                                class="mb-3 block text-sm font-medium text-black dark:text-white"
                            >
                                Travail d'examens <span class="text-meta-1">*</span>
                                <input type="text"
                                       id=""
                                       name=""
                                       value=""
                                       placeholder="Entrez une note de classe"
                                       class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600">
                        </div>
                        <div>
                            <label
                                class="mb-3 block text-sm font-medium text-black dark:text-white"
                            >
                                Travaux d'essai <span class="text-meta-1">*</span>
                                <input type="text"
                                       id=""
                                       name=""
                                       value=""
                                       placeholder="Entrez une note de classe"
                                       class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600">
                        </div>
                    </td>
                    @endforeach
                    <td class="px-6 py-3">
                        <button type="submit"
                                class="flex w-full justify-center rounded-lg bg-violet-600 p-3 font-medium text-gray hover:bg-opacity-90"
                        >
                            Ajouter
                        </button>
                    </td>
                </tr>
                @endforeach
                @if(empty($getSubject))
                <tr class="text-center text-gray-700 dark:text-bodydark1">
                    <td colspan="7" class="py-3"> Aucune évaluation programmée.</td>
                </tr>
                @endif
                @endif
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection
