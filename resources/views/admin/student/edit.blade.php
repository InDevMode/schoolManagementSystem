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
                        Modifier cet apprenant
                    </h2>
                    <nav>
                        <ol class="flex items-center gap-2">
                            <li>
                                <span class="font-medium text-emerald-400"><i
                                        class="fa-solid fa-user-graduate"></i></span>
                            </li>
                            <li>
                                /<a class="font-medium hover:text-emerald-400 transition duration-300"
                                    href="{{ url('admin/student/list') }}"> Liste des apprenants</a>
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
                                    <div>
                                        <label
                                            class="mb-3 block text-sm font-medium text-black dark:text-white"
                                        >
                                            Photo de profile
                                        </label>
                                        <input
                                            type="file" id="profile_picture" name="profile_picture"
                                            class="w-full cursor-pointer rounded-lg border-[1.5px] border-stroke bg-gray-100 font-normal outline-none transition file:mr-5 file:border-collapse file:cursor-pointer file:border-0 file:border-r file:border-solid file:border-stroke file:bg-whiter file:px-5 file:py-3 file:hover:bg-emerald-400 file:hover:bg-opacity-10 focus:border-emerald-400 active:border-emerald-400 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:file:border-form-strokedark dark:file:bg-white/30 dark:file:text-white dark:focus:border-emerald-400"
                                        />
                                        <img
                                            src="{{ $profile_picture_url }}"
                                            alt="profile cover"
                                            class="w-32 mt-3 rounded-full object-cover object-center"
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
                                        <input id="name" name="name" value="{{ old('name', $getStudent->name) }}"
                                               required
                                               type="text"
                                               placeholder="Entrez un nom"
                                               class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition focus:border-emerald-400 active:border-emerald-400 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-emerald-400"
                                        />
                                    </div>

                                    <div class="w-full xl:w-1/2">
                                        <label
                                            class="mb-3 block text-sm font-medium text-black dark:text-white"
                                        >
                                            Prénom <span class="text-meta-1">*</span>
                                        </label>
                                        <input id="last_name" name="last_name"
                                               value="{{ old('last_name', $getStudent->last_name) }}" required
                                               type="text"
                                               placeholder="Entrez un prénom"
                                               class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition focus:border-emerald-400 active:border-emerald-400 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-emerald-400"
                                        />
                                    </div>
                                </div>
                                <div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
                                    <div class="w-full xl:w-1/2">
                                        <label
                                            class="mb-3 block text-sm font-medium text-black dark:text-white"
                                        >
                                            Numéro d'Admission <span class="text-meta-1">*</span>
                                        </label>
                                        <input id="admission_number" name="admission_number"
                                               value="{{ old('admission_number', $getStudent->admission_number) }}"
                                               required
                                               type="text"
                                               placeholder="Entrez un numéro d'admission"
                                               class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition focus:border-emerald-400 active:border-emerald-400 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-emerald-400"
                                        />
                                    </div>
                                    <div class="w-full xl:w-1/2">
                                        <label
                                            class="mb-3 block text-sm font-medium text-black dark:text-white"
                                        >
                                            Numéro de rôle <span class="text-meta-1">*</span>
                                        </label>
                                        <input id="roll_number" name="roll_number"
                                               value="{{ old('roll_number', $getStudent->roll_number) }}"
                                               required
                                               type="text"
                                               placeholder="Entrez un numéro de rôle"
                                               class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition focus:border-emerald-400 active:border-emerald-400 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-emerald-400"
                                        />
                                    </div>
                                </div>
                                <div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
                                    <div class="w-full xl:w-1/2">
                                        <label
                                            class="mb-3 block text-sm font-medium text-black dark:text-white"
                                        >
                                            Genre <span class="text-meta-1">*</span>
                                        </label>
                                        <div
                                            x-data="{ isOptionSelected: false }"
                                            class="relative z-20 bg-transparent dark:bg-form-input"
                                        >
                                            <select id="gender" name="gender" required
                                                    class="relative z-20 w-full appearance-none rounded-lg border border-stroke bg-gray-100 px-5 py-2.5 outline-none transition focus:border-emerald-400 active:border-emerald-400 dark:border-form-strokedark dark:bg-form-input dark:focus:border-emerald-400"
                                                    :class="isOptionSelected && 'text-black dark:text-white'"
                                                    @change="isOptionSelected = true"
                                            >
                                                <option selected disabled class="text-body">
                                                    Choisissez un genre pour cet apprenant
                                                </option>
                                                <option class="text-body" value="male" {{ (old(
                                                'gender', $getStudent->gender) == 'male') ? 'selected' : ''
                                                }}>Masculin</option>
                                                <option class="text-body" value="female" {{ (old(
                                                'gender', $getStudent->gender) == 'female') ? 'selected' : ''
                                                }}>Féminin</option>
                                                <option class="text-body" value="other" {{ (old(
                                                'gender', $getStudent->gender) == 'other') ? 'selected' : ''
                                                }}>Autre</option>
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
                                    <div class="w-full xl:w-1/2">
                                        <label
                                            class="mb-3 block text-sm font-medium text-black dark:text-white"
                                        >
                                            Date de naissance <span class="text-meta-1">*</span>
                                        </label>
                                        <div class="relative">
                                            <input id="date_of_birth" name="date_of_birth"
                                                   value="{{ old('date_of_birth', $getStudent->date_of_birth) }}"
                                                   required
                                                   data-class="flatpickr-right"
                                                   placeholder="Entrez une date de naissance"
                                                   class="form-datepicker w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition focus:border-emerald-400 active:border-emerald-400 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-emerald-400"
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
                                </div>
                                <div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
                                    <div class="w-full xl:w-1/2">
                                        <label
                                            class="mb-3 block text-sm font-medium text-black dark:text-white"
                                        >
                                            Téléphone <span class="text-meta-1">*</span>
                                        </label>
                                        <input id="mobile_number" name="mobile_number"
                                               value="{{ old('mobile_number', $getStudent->mobile_number) }}"
                                               required
                                               type="text"
                                               placeholder="Entrez un numéro de téléphone"
                                               class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition focus:border-emerald-400 active:border-emerald-400 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-emerald-400"
                                        />
                                    </div>
                                    <div class="w-full xl:w-1/2">
                                        <label
                                            class="mb-3 block text-sm font-medium text-black dark:text-white"
                                        >
                                            Date d'Admission <span class="text-meta-1">*</span>
                                        </label>
                                        <div class="relative">
                                            <input id="admission_date" name="admission_date"
                                                   value="{{ old('admission_date', $getStudent->admission_date) }}"
                                                   required
                                                   data-class="flatpickr-right"
                                                   placeholder="Entrez une date d'adhésion"
                                                   class="form-datepicker w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition focus:border-emerald-400 active:border-emerald-400 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-emerald-400"
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
                                </div>
                                <div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
                                    <div class="w-full xl:w-1/2">
                                        <label
                                            class="mb-3 block text-sm font-medium text-black dark:text-white"
                                        >
                                            Caste <span class="text-meta-1">*</span>
                                        </label>
                                        <input id="caste" name="caste"
                                               value="{{ old('caste', $getStudent->caste) }}"
                                               required
                                               type="text"
                                               placeholder="Entrez un caste"
                                               class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition focus:border-emerald-400 active:border-emerald-400 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-emerald-400"
                                        />
                                    </div>
                                    <div class="w-full xl:w-1/2">
                                        <label
                                            class="mb-3 block text-sm font-medium text-black dark:text-white"
                                        >
                                            Religion <span class="text-meta-1">*</span>
                                        </label>
                                        <input id="religion" name="religion"
                                               value="{{ old('religion', $getStudent->religion) }}"
                                               required
                                               type="text"
                                               placeholder="Entrez une religion"
                                               class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition focus:border-emerald-400 active:border-emerald-400 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-emerald-400"
                                        />
                                    </div>
                                </div>
                                <div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
                                    <div class="w-full xl:w-1/2">
                                        <label
                                            class="mb-3 block text-sm font-medium text-black dark:text-white"
                                        >
                                            Classe <span class="text-meta-1">*</span>
                                        </label>
                                        <div
                                            x-data="{ isOptionSelected: false }"
                                            class="relative z-20 bg-gray-100 dark:bg-form-input"
                                        >
                                            <select id="class_id" name="class_id" required
                                                    class="relative z-20 w-full appearance-none rounded-lg border border-stroke bg-gray-100 px-5 py-2.5 outline-none transition focus:border-emerald-400 active:border-emerald-400 dark:border-form-strokedark dark:bg-form-input dark:focus:border-emerald-400"
                                                    :class="isOptionSelected && 'text-black dark:text-white'"
                                                    @change="isOptionSelected = true"
                                            >
                                                <option selected disabled value="" class="text-body">
                                                    Choisissez une classe pour cet apprenant
                                                </option>
                                                @foreach($getClass as $class)
                                                <option class="text-body" value="{{ $class->id }}" {{ old('class_id', $getStudent->class_id) == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
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
                                    <div class="w-full xl:w-1/2">
                                        <label
                                            class="mb-3 block text-sm font-medium text-black dark:text-white"
                                        >
                                            Groupe Sanguin <span class="text-meta-1">*</span>
                                        </label>
                                        <div
                                            x-data="{ isOptionSelected: false }"
                                            class="relative z-20 bg-gray-100 dark:bg-form-input"
                                        >
                                            <select id="blood_group" name="blood_group" required
                                                    class="relative z-20 w-full appearance-none rounded-lg border border-stroke bg-gray-100 px-5 py-2.5 outline-none transition focus:border-emerald-400 active:border-emerald-400 dark:border-form-strokedark dark:bg-form-input dark:focus:border-emerald-400"
                                                    :class="isOptionSelected && 'text-black dark:text-white'"
                                                    @change="isOptionSelected = true"
                                            >
                                                <option selected disabled value="" class="text-body">
                                                    Choisissez un groupe sanguin pour cet apprenant
                                                </option>
                                                <option class="text-body" value="a+" {{ (old('blood_group', $getStudent->blood_group) == 'a+') ? 'selected' : '' }}>A+</option>
                                                <option class="text-body" value="a-" {{ (old('blood_group', $getStudent->blood_group) == 'a-') ? 'selected' : '' }}>A-</option>
                                                <option class="text-body" value="b+" {{ (old('blood_group', $getStudent->blood_group) == 'b+') ? 'selected' : '' }}>B+</option>
                                                <option class="text-body" value="b-" {{ (old('blood_group', $getStudent->blood_group) == 'b-') ? 'selected' : '' }}>B-</option>
                                                <option class="text-body" value="ab+" {{ (old('blood_group', $getStudent->blood_group) == 'ab+') ? 'selected' : '' }}>AB+</option>
                                                <option class="text-body" value="ab-" {{ (old('blood_group', $getStudent->blood_group) == 'ab-') ? 'selected' : '' }}>AB-</option>
                                                <option class="text-body" value="o+" {{ (old('blood_group', $getStudent->blood_group) == 'o+') ? 'selected' : '' }}>O+</option>
                                                <option class="text-body" value="o-" {{ (old('blood_group', $getStudent->blood_group) == 'o') ? 'selected' : '' }}>O-</option>
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
                                </div>
                                <div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
                                    <div class="w-full xl:w-1/2">
                                        <label
                                            class="mb-3 block text-sm font-medium text-black dark:text-white"
                                        >
                                            Taille <span class="text-meta-1">*</span>
                                        </label>
                                        <input id="height" name="height"
                                               value="{{ old('height', $getStudent->height) }}"
                                               required
                                               type="text"
                                               placeholder="Entrez une taille"
                                               class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition focus:border-emerald-400 active:border-emerald-400 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-emerald-400"
                                        />
                                    </div>
                                    <div class="w-full xl:w-1/2">
                                        <label
                                            class="mb-3 block text-sm font-medium text-black dark:text-white"
                                        >
                                            Poids <span class="text-meta-1">*</span>
                                        </label>
                                        <input id="weight" name="weight"
                                               value="{{ old('weight', $getStudent->weight) }}"
                                               required
                                               type="text"
                                               placeholder="Entrez un poids"
                                               class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition focus:border-emerald-400 active:border-emerald-400 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-emerald-400"
                                        />
                                    </div>
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
                                                Choisissez un statut pour cet apprenant
                                            </option>
                                            <option class="text-body" value="1" {{ (old(
                                            'status', $getStudent->status) == '1') ? 'selected' : '' }}>Actif</option>
                                            <option class="text-body" value="0" {{ (old(
                                            'status', $getStudent->status) == '0') ? 'selected' : '' }}>Inactif</option>
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
                                <div class="border-t border-gray-200 pb-4.5"></div>
                                <div class="mb-4.5">
                                    <label
                                        class="mb-3 block text-sm font-medium text-black dark:text-white"
                                    >
                                        Email <span class="text-meta-1">*</span>
                                    </label>
                                    <input
                                        type="email" id="email" name="email"
                                        value="{{ old('email', $getStudent->email) }}" required
                                        placeholder="Entrez votre un adresse email"
                                        class="w-full rounded border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition focus:border-emerald-400 active:border-emerald-400 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-emerald-400"
                                    />
                                </div>
                                <div class="mb-4.5">
                                    <label
                                        class="mb-3 block text-sm font-medium text-black dark:text-white"
                                    >
                                        Mot de passe
                                    </label>
                                    <input
                                        type="password" id="password" name="password" value="{{ old('password') }}"
                                        placeholder="Entrez votre un mot de passe"
                                        class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition focus:border-emerald-400 active:border-emerald-400 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-emerald-400"
                                    />
                                </div>
                                <button type="submit"
                                        class="flex w-full justify-center rounded-lg bg-emerald-400 p-3 font-medium text-white hover:bg-opacity-90"
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


