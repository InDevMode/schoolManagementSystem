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
    <div style="display:none;" id="alert-border-3" class="flex items-center p-4 mb-4 text-green-800 border-t-4 border-green-300 bg-green-50 dark:text-green-400 dark:bg-gray-800 dark:border-green-800" role="alert">
        <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
        </svg>
        <div id="success-message" class="ms-3 text-sm font-medium">
        </div>
        <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-green-50 text-emerald-600 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-emerald-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-emerald-400 dark:hover:bg-gray-700"  data-dismiss-target="#alert-border-3" aria-label="Close">
            <span class="sr-only">Dismiss</span>
            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
            </svg>
        </button>
    </div>
    <div
        class="rounded-lg border border-stroke bg-white px-5 pb-2.5 pt-6 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5 xl:pb-1"
    >
        <form action="" method="get">
        <div class="pb-3 text-gray-700 dark:text-gray-200">Choisissez une évaluation et une classe pour voir le registre des notes</div>
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
                        {{ $subject->subject_name }}
                        <span
                            class="block bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 rounded text-sm py-1 px-2 my-1 w-fit">
                             ({{ $subject->subject_type == 'practical' ? 'Pratique' : 'Théorique' }} => {{ $subject->passing_marks }} / {{ $subject->full_marks }})
                        </span>
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
                    <form name="post" class="SubmitForm">
                        <input type="hidden" name="student_id" value="{{ $student->id }}">
                        <input type="hidden" name="exam_id" value="{{ Request::get('exam_id') }}">
                        <input type="hidden" name="class_id" value="{{ Request::get('class_id') }}">
                        {{ csrf_field() }}
                        <tr class="hover:bg-violet-100 dark:hover:bg-gray-700 transition duration-300 border-b dark:border-gray-600 hover:border-violet-400 dark:text-gray-200 text-gray-500">
                            <td class="px-6 py-3">
                                {{ $student->name }} {{ $student->last_name }}
                            </td>
                            @php
                            $i = 1;
                            @endphp
                            @foreach($getSubject as $index => $subject)
                            @php
                            $getMark = \App\Models\ScheduleModel::getMarks($student->id, Request::get('exam_id'), Request::get('class_id'), $subject->subject_id);
                            @endphp
                            <td class="px-6 py-3">
                                <div>
                                    <label
                                        class="mb-3 block text-sm font-medium text-black dark:text-white"
                                    >
                                        Travail de classe <span class="text-meta-1">*</span>
                                        <input type="hidden" name="marks[{{ $index }}][subject_id]" value="{{ $subject->subject_id }}">
                                        <input type="text"
                                               id="marks[{{ $index }}][class_work]"
                                               name="marks[{{ $index }}][class_work]"
                                               value="{{ $getMark ? $getMark->class_work : '' }}"
                                               placeholder="Entrez une note de classe"
                                               class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600">
                                </div>
                                <div>
                                    <label
                                        class="mb-3 block text-sm font-medium text-black dark:text-white"
                                    >
                                        Travail de maison <span class="text-meta-1">*</span>
                                        <input type="text"
                                               id="marks[{{ $index }}][home_work]"
                                               name="marks[{{ $index }}][home_work]"
                                               value="{{ $getMark ? $getMark->home_work : '' }}"
                                               placeholder="Entrez une note de classe"
                                               class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600">
                                </div>
                                <div>
                                    <label
                                        class="mb-3 block text-sm font-medium text-black dark:text-white"
                                    >
                                        Travail d'examens <span class="text-meta-1">*</span>
                                        <input type="text"
                                               id="marks[{{ $index }}][exam_work]"
                                               name="marks[{{ $index }}][exam_work]"
                                               value="{{ $getMark ? $getMark->exam_work : '' }}"
                                               placeholder="Entrez une note de classe"
                                               class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600">
                                </div>
                                <div>
                                    <label
                                        class="mb-3 block text-sm font-medium text-black dark:text-white"
                                    >
                                        Travaux d'essai <span class="text-meta-1">*</span>
                                        <input type="text"
                                               id="marks[{{ $index }}][test_work]"
                                               name="marks[{{ $index }}][test_work]"
                                               value="{{ $getMark ? $getMark->test_work : '' }}"
                                               placeholder="Entrez une note de classe"
                                               class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600">
                                </div>
                            </td>
                            @php
                            $i = 1;
                            @endphp
                            @endforeach
                            <td class="px-6 py-3">
                                <button type="submit"
                                        class="flex w-full justify-center rounded-lg bg-violet-600 p-3 font-medium text-gray hover:bg-opacity-90"
                                >
                                    Ajouter
                                </button>
                            </td>
                        </tr>
                    </form>
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

@section('script')
<script type="text/javascript">
    document.querySelectorAll('.SubmitForm').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(form);
            let xhr = new XMLHttpRequest();
            xhr.open('POST', "{{ url('admin/examinations/marks_register/add') }}", true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    let response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        document.getElementById('alert-border-3').innerText = 'Ces registres de notes pour ces évaluations ont été ajoutées avec succès.';
                        document.getElementById('alert-border-3').style.display = 'block';
                    } else {
                        document.getElementById('alert-border-3').innerText = 'Une erreur s\'est produite. Veuillez réessayer.';
                        document.getElementById('alert-border-3').style.display = 'block';
                        document.getElementById('alert-border-3').style.color = 'red';
                    }
                }
            };
            xhr.send(formData);
        });
    });

</script>
@endsection
