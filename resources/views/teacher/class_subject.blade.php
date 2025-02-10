@extends('layouts.app')
@section('content')
<div class="m-5">
    <!-- Breadcrumb Start -->
    <div
        class="mb-6 mt-3 flex flex-col gap-3 sm:flex-row items-center justify-between"
    >
        <h2 class="uppercase font-bold text-black dark:text-bodydark">
            Liste de mes classes et matières
        </h2>
        <nav>
            <ol class="flex items-center gap-2">
                <li>
                    <span class="font-medium text-violet-600"><i class="fa-solid fa-landmark"></i></span>
                </li>
                <li>
                    <a class="font-medium hover:text-violet-600 transition duration-300"
                       href="{{ url('teacher/dashboard') }}">/ Dashboard</a>
                </li>
            </ol>
        </nav>
    </div>
    @include('message')
    <div class="">
        <div class="mt-4">
            {{ $getClassSubjectTeacher->links('vendor.pagination.tailwind') }}
        </div>
    </div>
    <div
        class="rounded-sm border border-stroke bg-white px-5 pb-2.5 pt-6 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5 xl:pb-1"
    >
        <form action="" method="get">
            <div class="mb-4.5 grid grid-cols-2 xl:grid-cols-4 gap-3 items-center">
                <div class="w-full">
                    <input
                        type="text" id="class_name" name="class_name" value="{{ Request::get('class_name') }}"
                        placeholder="classe..."
                        class="w-full rounded border-[1.5px] border-stroke bg-transparent px-5 py-3 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600"
                    />
                </div>
                <div class="w-full">
                    <input
                        type="text" id="subject_name" name="subject_name" value="{{ Request::get('subject_name') }}"
                        placeholder="matière..."
                        class="w-full rounded border-[1.5px] border-stroke bg-transparent px-5 py-3 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600"
                    />
                </div>
                <div class="w-full">
                    <div
                        x-data="{ isOptionSelected: false }"
                        class="relative z-20 bg-transparent dark:bg-form-input"
                    >
                        <select id="subject_type" name="subject_type"
                                class="relative z-20 w-full appearance-none rounded border border-stroke bg-transparent px-5 py-3 outline-none transition focus:border-primary active:border-primary dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary"
                                :class="isOptionSelected && 'text-black dark:text-white'"
                                @change="isOptionSelected = true"
                        >
                            <option value="" class="text-body">
                                Type...
                            </option>
                            <option value="practical" class="text-body" {{ Request::get(
                            'subject_type') == 'practical' ? 'selected' : '' }}>Pratique</option>
                            <option value="theoretical" class="text-body" {{ Request::get(
                            'subject_type') == 'theoretical' ? 'selected' : '' }}>Théorique</option>
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
                        class="flex w-full justify-between items-center rounded bg-violet-600 p-3 font-medium text-gray hover:bg-opacity-90"
                    >
                        Rechercher
                        <span class="inline-flex items-center text-sm text-gray-900">
                                    <i class="fa-solid fa-search text-white"></i>
                                </span>
                    </button>
                </div>
                <div class="w-full">
                    <a href="{{ url('teacher/class_subject') }}"
                       class="flex w-full justify-center rounded bg-bodydark2 p-3 font-medium text-gray hover:bg-opacity-90"
                    >
                        Réïnitialisez
                    </a>
                </div>
            </div>
        </form>

        <div class="relative overflow rounded-lg z-10">
            <table class="w-full text-sm text-left rtl:text-right text-white dark:text-white">
                <thead
                    class="rounded-sm bg-violet-600 uppercase text-white dark:bg-meta-4"
                >
                <tr>
                    <th scope="col" class="px-6 py-3">
                        Classe
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Matière
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Heures
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Type
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Actions
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach($getClassSubjectTeacher as $index => $classSubjectTeacher)
                <tr class="hover:bg-violet-100 dark:hover:bg-gray-700 transition duration-300 border-b dark:border-gray-600 hover:border-violet-400 dark:text-gray-200 text-gray-500">
                    <td class="px-6 py-3">
                        {{ $classSubjectTeacher -> class_name }}
                    </td>
                    <td class="px-6 py-3">
                        {{ $classSubjectTeacher -> subject_name }}
                    </td>
                    <td class="px-6 py-3 font-semibold">
                        @php
                        $timetable = \App\Models\ClassTeacherModel::getMyClassTimetable($classSubjectTeacher->class_id, $classSubjectTeacher->subject_id);
                        @endphp
                        @if ($timetable)
                        <span class="bg-gray-200 dark:bg-gray-700 flex justify-center border border-gray-500 dark:border-gray-700 py-2 px-3 rounded">{{ \Carbon\Carbon::parse($timetable->start_time)->format('G\h i\m\i\n') }} à {{ \Carbon\Carbon::parse($timetable->end_time)->format('G\h i\m\i\n') }}</span>
                        @else
                        <p>Aucune heure disponible pour cette matière.</p>
                        @endif
                    </td>
                    <td class="px-6 py-3">
                        <div class="flex items-center">
                            <p class="px-6 py-1 rounded-full {{ $classSubjectTeacher->subject_type == 'practical' ? 'text-violet-700 dark:text-gray-200 bg-violet-100 dark:bg-violet-900' : 'text-red-700 dark:text-gray-200 bg-red-100 dark:bg-red-900' }}">
                                {{ $classSubjectTeacher->subject_type == 'practical' ? 'Pratique' : 'Théorique' }}
                            </p>
                        </div>
                    </td>
                    <td class="px-6 py-3">
                        <div class="relative inline-block text-left" x-data="{ open: false }">
                            <div>
                                <button
                                    type="button"
                                    class="group inline-flex w-full justify-center gap-x-1.5 rounded-lg shadow-md bg-white dark:bg-gray-800 border dark:border-gray-600 dark:hover:text-violet-600 px-3 py-2 text-sm font-semibold text-gray-700 hover:text-violet-600 dark:text-gray-200 hover:bg-gray-100"
                                    @click="open = !open"
                                    id="menu-button"
                                    aria-expanded="true"
                                    aria-haspopup="true">
                                    Actions
                                    <svg class="-mr-1 size-5 group-hover:text-violet-600 text-gray-400"
                                         viewBox="0 0 20 20" fill="currentColor"
                                         aria-hidden="true">
                                        <path fill-rule="evenodd"
                                              d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            </div>
                            <div
                                class="absolute right-0 z-50 mt-2 w-56 origin-top-right rounded-md bg-white dark:bg-gray-800 ring-1 shadow-lg ring-black/5 focus:outline-hidden"
                                role="menu"
                                aria-orientation="vertical"
                                aria-labelledby="menu-button"
                                tabindex="{{ $index + 1 }}"
                                x-show="open"
                                @click.away="open = false"
                                x-transition
                            >
                                <div class="py-1">
                                    <a href="{{ url('teacher/class_subject/'. $classSubjectTeacher -> class_id.'/timetable/'. $classSubjectTeacher->subject_id) }}"
                                       class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:text-emerald-400 dark:hover:text-emerald-400"
                                       role="menuitem">Horaire de cours</a>
                                </div>
                            </div>
                        </div>
                    </td>

                </tr>
                @endforeach
                @if($getClassSubjectTeacher->isEmpty())
                <tr class="text-center text-gray-700 dark:text-bodydark1">
                    <td colspan="9" class="px-6 py-3"> Aucun classes et matières assignées.</td>
                </tr>
                @endif
                <tr class="">
                    <td colspan="9"
                        class="px-6 py-3"
                    >
                        <div class="mt-3 mb-3 flex items-center justify-between">
                            <h2 class="text-title-sm uppercase font-bold text-black dark:text-white">
                                Total
                            </h2>
                            <nav>
                                <ol class="flex items-center bg-white shadow-lg border border-gray-200 dark:border-gray-600 w-fit dark:bg-black py-2 px-8 rounded">
                                    <li>
                                        <p class="text-md font-semibold text-gray-700 dark:text-gray-200">
                                            {{ $getClassSubjectTeacher->total() }}</p>
                                    </li>
                                </ol>
                            </nav>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

<script>
    function toggleMenu(event, index) {
        event.stopPropagation();
        document.querySelectorAll('.relative .hidden').forEach(menu => menu.classList.add('hidden'));
        const menu = document.getElementById('dropdown-menu-' + index);
        menu.classList.toggle('hidden');
    }

    document.addEventListener('click', function () {
        document.querySelectorAll('.relative .hidden').forEach(menu => menu.classList.add('hidden'));
    });
</script>


