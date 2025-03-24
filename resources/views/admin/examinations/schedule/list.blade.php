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
                    <a href="{{ url('admin/examinations/schedule/list') }}"
                       class="flex w-full justify-center rounded-lg bg-gray-500 px-3 py-2.5 font-medium text-gray hover:bg-opacity-90"
                    >
                        Réïnitialisez
                    </a>
                </div>
            </div>
        </form>

        @if(!empty($getExamSchedule))
        <form action="{{ url('admin/examinations/schedule/add') }}" method="post">
            {{ csrf_field() }}
            <input type="hidden" name="exam_id" value="{{ Request::get('exam_id') }}">
            <input type="hidden" name="class_id" value="{{ Request::get('class_id') }}">
            <div class="relative overflow rounded-lg z-10">
                <table class="w-full text-[12px] text-left rtl:text-right text-white dark:text-white">
                    <thead
                        class="rounded-sm bg-violet-600 uppercase text-white dark:bg-meta-4"
                    >
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            Matière
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Date
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Heure de début
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Heure de fin
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Numéro de salle
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Note totale
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Note de passage
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @php
                    $i = 1;
                    @endphp
                    @foreach($getExamSchedule as $index => $examSchedule)
                    <tr class="hover:bg-violet-100 dark:hover:bg-gray-700 transition duration-300 border-b dark:border-gray-600 hover:border-violet-400 dark:text-gray-200 text-gray-500">
                        <td class="px-6 py-3 w-72">
                            {{ $examSchedule['subject_name'] }}
                            <input type="hidden" name="schedule[{{ $index }}][subject_id]" value="{{ $examSchedule['subject_id'] }}">
                        </td>
                        <td class="px-6 py-3 w-72">
                            <div class="w-full">
                                <div class="relative">
                                    <input id="schedule[{{ $i }}][exam_date]" name="schedule[{{ $index }}][exam_date]" value="{{ old('schedule.' . $index . '.exam_date', $examSchedule['exam_date']) }}"
                                           class="form-datepicker w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal outline-none transition focus:border-violet-600 active:border-violet-600 dark:border-form-strokedark dark:bg-form-input dark:focus:border-violet-600"
                                           placeholder="YYYY / MM / DD"
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
                        </td>
                        <td class="px-6 py-3 font-semibold">
                            <input type="time" id="schedule[{{ $index }}][start_time]"
                                   name="schedule[{{ $index }}][start_time]"
                                   value="{{ old('schedule.' . $index . '.start_time', $examSchedule['start_time']) }}"
                                   class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600">
                        </td>
                        <td class="px-6 py-3 font-semibold">
                            <input type="time" id="schedule[{{ $index }}][end_time]"
                                   name="schedule[{{ $index }}][end_time]"
                                   value="{{ old('schedule.' . $index . '.end_time', $examSchedule['end_time']) }}"
                                   class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600">
                        </td>
                        <td class="px-6 py-3 w-60">
                            <input type="text" id="schedule[{{ $index }}][room_number]"
                                   name="schedule[{{ $index }}][room_number]"
                                   value="{{ old('schedule.' . $index . '.room_number', $examSchedule['room_number']) }}"
                                   placeholder="numéro de salle"
                                   class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600">
                        </td>
                        <td class="px-6 py-3 w-60">
                            <input type="text" id="schedule[{{ $index }}][full_marks]"
                                   name="schedule[{{ $index }}][full_marks]"
                                   value="{{ old('schedule.' . $index . '.full_marks', $examSchedule['full_marks']) }}"
                                   placeholder="totale des notes"
                                   class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600">
                        </td>
                        <td class="px-6 py-3 w-60">
                            <input type="text" id="schedule[{{ $index }}][passing_marks]"
                                   name="schedule[{{ $index }}][passing_marks]"
                                   value="{{ old('schedule.' . $index . '.passing_marks', $examSchedule['passing_marks']) }}"
                                   placeholder="note de passage"
                                   class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600">
                        </td>
                    </tr>
                    @php
                    $i++
                    @endphp
                    @endforeach
                    @if(empty($getExamSchedule))
                    <tr class="text-center text-gray-700 dark:text-bodydark1">
                        <td colspan="7" class="py-3"> Aucune évaluation programmée.</td>
                    </tr>
                    @endif
                    </tbody>
                </table>
                <div class="flex my-3">
                    <button type="submit"
                            class="text-white bg-violet-600 hover:bg-violet-600 focus:ring-4 focus:outline-none focus:ring-violet-300 font-medium rounded-md text-sm px-5 py-2.5 text-center transition-all duration-700 ease-out w-fit">
                        Ajouter
                    </button>
                </div>
            </div>
        </form>
        @else
        <div class="flex justify-center py-3">
            Cette assignation n'est pas active
        </div>
        @endif
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



