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
    <div class="pb-3 text-red-500 dark:text-red-400 font-semibold text-sm">Choisissez une évaluation et une classe pour
        voir le
        registre des notes
    </div>
    <div
        class="rounded-lg border border-stroke bg-white px-5 pb-2.5 pt-6 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5"
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
                            <option value="{{ $exams -> exam_id }}" class="text-body" {{ ( Request::get(
                            'exam_id') == $exams->exam_id) ? 'selected' : '' }}>{{ $exams -> exam_name }}</option>
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
                            <option value="{{ $class -> class_id }}" class="text-body" {{ ( Request::get(
                            'class_id') == $class->class_id) ? 'selected' : '' }}>{{ $class -> class_name }}</option>
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

        @if(!empty($getSubject) && $getSubject->count() > 0)
        <div class="relative overflow rounded-lg z-10">
            <table class="w-full text-[12px] text-left rtl:text-right text-white dark:text-white">
                <thead class="rounded-sm bg-violet-600 uppercase text-white dark:bg-meta-4">
                <tr>
                    <th scope="col" class="px-6 py-3">Apprenants</th>
                    @foreach($getSubject as $subject)
                    <th scope="col" class="px-6 py-3">
                        {{ $subject->subject_name }}
                        <span
                            class="block bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 rounded text-sm py-1 px-2 my-1 w-fit">
                             ({{ $subject->subject_type == 'practical' ? 'Pratique' : 'Théorique' }} => {{ $subject->passing_marks }} / {{ $subject->full_marks }})
                        </span>
                    </th>
                    @endforeach
                    <th scope="col" class="px-6 py-3">Actions</th>
                </tr>
                </thead>
                <tbody>
                @if(!empty($getStudent) && $getStudent->count() > 0)
                @foreach($getStudent as $student)
                <form name="post" class="SubmitForm">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="student_id" value="{{ $student->id }}">
                    <input type="hidden" name="exam_id" value="{{ Request::get('exam_id') }}">
                    <input type="hidden" name="class_id" value="{{ Request::get('class_id') }}">
                    {{ csrf_field() }}
                    <tr class="hover:bg-violet-100 dark:hover:bg-gray-700 transition duration-300 border-b dark:border-gray-600 hover:border-violet-400 dark:text-gray-200 text-gray-500">
                        <td class="px-6 py-3">{{ $student->name }} {{ $student->last_name }}</td>
                        @php
                        $i = 1;
                        $totalStudentMark =0;
                        $totalFullMarks = 0;
                        $totalPassingMarks = 0;
                        @endphp
                        @foreach($getSubject as $index => $subject)
                        @php

                        $totalMark = 0;
                        $totalFullMarks = $totalFullMarks + $subject->full_marks;
                        $totalPassingMarks = $totalPassingMarks + $subject->passing_marks;

                        $getMark = \App\Models\ScheduleModel::getMarks($student->id, Request::get('exam_id'),
                        Request::get('class_id'), $subject->subject_id);
                        if(!empty($getMark)) {
                        $totalMark = $getMark->class_work + $getMark->home_work + $getMark->exam_work +
                        $getMark->test_work;
                        }
                        $totalStudentMark = $totalStudentMark + $totalMark;
                        $percentage = ($totalStudentMark * 100) / $totalFullMarks;
                        $getGrade = \App\Models\MarksGradeModel::getGrade($percentage);
                        @endphp
                        <td class="px-6 py-3">
                            <div>
                                <label
                                    class="mb-3 block text-sm font-medium text-black dark:text-white"
                                >
                                    Travail de classe <span class="text-meta-1">*</span>
                                    <input type="hidden" name="marks[{{ $index }}][passing_marks]"
                                    value="{{ $subject->passing_marks }}">
                                    <input type="hidden" name="marks[{{ $index }}][full_marks]"
                                           value="{{ $subject->full_marks }}">
                                    <input type="hidden" name="marks[{{ $index }}][id]"
                                           value="{{ $subject->id }}">
                                    <input type="hidden" name="marks[{{ $index }}][subject_id]"
                                           value="{{ $subject->subject_id }}">
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
                                           class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-white dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600">
                            </div>
                            @if(!empty($getMark))
                            <div>
                                <label
                                    class="mb-3 block text-sm font-medium text-black dark:text-white"
                                >
                                    Résultats
                                    <div
                                        class="mb-3 w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition disabled:cursor-default disabled:bg-white dark:border-form-strokedark dark:bg-form-input dark:text-white">
                                        <p class="flex justify-between"><span>Note totale =></span><span>{{ $totalMark }}</span>
                                        </p>
                                        <p class="flex justify-between">
                                            <span>Note de passage =></span><span>{{ $subject->passing_marks}}</span></p>
                                        <p class="flex justify-between">
                                            <span>Décision =></span><span>{{ ($totalMark >= $subject->passing_marks ) ? 'Admis' : 'Refusé'}}</span>
                                        </p>
                                        @if(!empty($getGrade))
                                        <p>Note => {{ $getGrade }}
                                            @endif
                                        <p class="flex justify-between">
                                            <span>Décision =></span><span class="{{ $totalMark >= $subject->passing_marks
                                            ? 'font-bold text-emerald-500'
                                            : 'text-red-500 font-bold' }}">{{ ($totalMark >= $subject->passing_marks ) ? 'Admis' : 'Refusé'}}</span>
                                        </p>
                                    </div>
                            </div>
                            @endif
                            <button type="submit"
                                    data-student="{{ $student->id }}"
                                    data-exam="{{ Request::get('exam_id') }}"
                                    data-class="{{ Request::get('class_id') }}"
                                    data-subject="{{ $subject->id }}"
                                    class="saveSingleSubject flex w-fit justify-center rounded-lg bg-violet-600 p-3 font-medium text-gray hover:bg-opacity-90">
                                Sauvegarder
                            </button>
                        </td>
                        @php
                        $i = 1;
                        @endphp
                        @endforeach
                        <td class="px-6 py-3">
                            <button type="submit" id="addMarksRegister" data-action="saveAll"
                                    class="flex w-full justify-center rounded-lg bg-emerald-400 p-3 font-medium text-gray hover:bg-opacity-90 mb-3">
                                Ajouter
                            </button>
                            @php
                            $percentage = ($totalStudentMark * 100) / $totalFullMarks;
                            $getGrade = \App\Models\MarksGradeModel::getGrade($percentage);
                            @endphp
                            <div
                                class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition disabled:cursor-default disabled:bg-white dark:border-form-strokedark dark:bg-form-input dark:text-white">
                                <p>Note totale de l'apprenant => {{ $totalStudentMark }}</p>
                                <p>Note totale => {{ $totalFullMarks }}</p>
                                <p>Note de passage => {{ $totalPassingMarks }}</p>
                                <p>Pourcentage => {{ round($percentage, 2) }} %</p>
                                @if(!empty($getGrade))
                                <p>Note => {{ $getGrade }}
                                @endif
                                <p class="{{ $totalStudentMark >= $totalPassingMarks ? 'text-emerald-400 font-bold' : 'text-red-500 font-bold' }}">
                                    Décision => {{ $totalStudentMark >= $totalPassingMarks ? 'Admis' : 'Refusé' }}
                                </p>
                            </div>
                        </td>
                    </tr>
                </form>
                @endforeach
                @else
                <tr>
                    <td colspan="{{ count($getSubject) + 2 }}"
                        class="px-6 py-3 text-center dark:text-gray-400 font-medium">Aucun résultat disponible
                    </td>
                </tr>
                @endif
                </tbody>
            </table>
        </div>
        @else
        <p class="text-center dark:text-gray-400 font-medium py-3">Aucun résultat disponible</p>
        @endif
    </div>
</div>
@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        setupFormHandlers();
        setupSingleSubjectHandlers();
    });

    function setupFormHandlers() {
        document.querySelectorAll('.SubmitForm').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                handleFormSubmit(form);
            });
        });
    }

    function setupSingleSubjectHandlers() {
        document.querySelectorAll('.saveSingleSubject').forEach(function (button) {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                let student_id = button.getAttribute('data-student');
                let exam_id = button.getAttribute('data-exam');
                let class_id = button.getAttribute('data-class');
                let td = button.closest('td');

                handleSingleSubjectSubmit(td, student_id, exam_id, class_id);
            });
        });
    }

    function handleFormSubmit(form) {
        let formData = new FormData(form);
        formData.append('_token', '{{ csrf_token() }}');

        let xhr = new XMLHttpRequest();
        xhr.open('POST', "{{ url('teacher/add_marks_register') }}", true);

        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                let response = JSON.parse(xhr.responseText);
                displayMessage(response);
                if (xhr.status === 200 && response.success) {
                    window.location.reload();
                }
            }
        };

        xhr.send(formData);
    }

    function handleSingleSubjectSubmit(td, student_id, exam_id, class_id) {
        let formData = new FormData();
        td.querySelectorAll('input').forEach(function (input) {
            if (input.type === 'text' || input.type === 'hidden') {
                formData.append(input.name, input.value);
            }
        });

        formData.append('student_id', student_id);
        formData.append('exam_id', exam_id);
        formData.append('class_id', class_id);
        formData.append('_token', '{{ csrf_token() }}');

        let xhr = new XMLHttpRequest();
        xhr.open('POST', "{{ url('teacher/add_single_marks_register') }}", true);

        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                let response = JSON.parse(xhr.responseText);
                displayMessage(response);
                if (xhr.status === 200 && response.success) {
                    window.location.reload();
                }
            }
        };
        xhr.send(formData);
    }

    function displayMessage(response) {
        let messageBox = document.createElement('div');
        messageBox.className = 'message-box';
        messageBox.innerHTML = response.message;

        if (response.success) {
            messageBox.classList.add('message-success');
        } else {
            messageBox.classList.add('message-error');
        }

        document.body.appendChild(messageBox);
        setTimeout(function () {
            messageBox.remove();
        }, 5000);
    }
</script>
@endsection


