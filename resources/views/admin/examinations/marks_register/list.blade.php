@extends('layouts.app')
@section('content')
      <div class="container mx-auto px-4 py-5">
            @include('message')
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-5 gap-4">
                  <div>
                        <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                              <i class="fa-solid  fa-square-poll-horizontal text-primary-600"></i>
                              Liste du registres des notes des apprenants
                        </h1>
                        <p class="text-gray-600 dark:text-gray-300 mt-1">Gérez la liste du registres des notes des
                              apprenants de votre
                              plateforme</p>
                  </div>

                  <nav class="flex items-center text-sm">
                        <ol class="flex items-center space-x-2">
                              <li class="flex items-center">
                                    <a href="{{ url('admin/dashboard') }}"
                                          class="text-primary-600 hover:text-violet-600 transition-colors">
                                          <i class="fas fa-home mr-1"></i>
                                          Tableau de bord
                                    </a>
                                    <span class="mx-2 text-gray-400">
                                          <iconify-icon icon="mdi:chevron-right" class="text-gray-400" width="16"
                                                height="16"></iconify-icon>
                                    </span>
                              </li>
                              <li class="flex items-center">
                                    <span class="text-violet-500">
                                          Registres
                                    </span>
                              </li>
                        </ol>
                  </nav>
            </div>

            <div class="text-red-600 dark:text-red-400 text-sm font-semibold my-3">Veuillez choisir l'évaluation et la classe
                  dont vous souhaiterez
                  voir le registre des notes </div>

            <!-- Filter Section -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-5">
                  <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-filter text-primary-600"></i>
                        Filtres de recherche
                  </h2>

                  <form action="" method="get" id="searchForm">
                        {{ csrf_field() }}
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

                              <div class="w-full">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                          Evaluation <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                          <select id="exam_id" name="exam_id" required
                                                class="custom-select w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500 transition-all duration-200">
                                                <option selected disabled value="">Veuillez choisir
                                                      une évaluation pour voir le registre des notes
                                                      @foreach ($getExams as $exams)
                                                <option value="{{ $exams->id }}" class="text-body"
                                                      {{ Request::get('exam_id') == $exams->id ? 'selected' : '' }}>
                                                      {{ $exams->name }}</option>
                                                @endforeach
                                          </select>
                                          <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                                <iconify-icon icon="mdi:chevron-down" class="text-gray-400" width="20"
                                                      height="20"></iconify-icon>
                                          </div>
                                    </div>
                              </div>

                              <div class="w-full">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                          Classe <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                          <select id="class_id" name="class_id" required
                                                class="custom-select w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500 transition-all duration-200">
                                                <option selected disabled value="">Veuillez choisir
                                                      une classe pour afficher le registre des notes
                                                      @foreach ($getClass as $class)
                                                <option value="{{ $class->id }}" class="text-body"
                                                      {{ Request::get('class_id') == $class->id ? 'selected' : '' }}>
                                                      {{ $class->name }}</option>
                                                @endforeach
                                          </select>
                                          <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                                <iconify-icon icon="mdi:chevron-down" class="text-gray-400" width="20"
                                                      height="20"></iconify-icon>
                                          </div>
                                    </div>
                              </div>

                              <!-- Action Buttons -->
                              <div class="flex items-end gap-2 w-full">
                                    <button type="submit"
                                          class="w-full bg-violet-600 hover:bg-violet-700 text-white font-medium rounded-lg px-4 py-2.5 flex items-center justify-center gap-2 transition-colors">
                                          <i class="fas fa-search"></i>
                                          Rechercher
                                    </button>
                                    <a href="{{ url('admin/examinations/marks_register/list') }}"
                                          class="w-full bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700 text-gray-800 dark:text-white font-medium rounded-lg px-4 py-2.5 flex items-center justify-center gap-2 transition-colors">
                                          <i class="fas fa-sync-alt"></i>
                                          Réinitialiser
                                    </a>
                              </div>
                        </div>
                  </form>
            </div>

            @if (!empty($getSubject) && $getSubject->count() > 0)
                  <div class="relative overflow rounded-lg z-10">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                              <thead class="rounded-lg bg-violet-600 dark:bg-gray-700">
                                    <tr>
                                          <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                                Apprenants</th>
                                          @foreach ($getSubject as $subject)
                                                <th scope="col"
                                                      class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                                      {{ $subject->subject_name }}
                                                      <span
                                                            class="block bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 rounded text-sm py-1 px-2 my-1 w-fit">
                                                            ({{ $subject->subject_type == 'practical' ? 'Pratique' : 'Théorique' }}
                                                            => {{ $subject->passing_marks }} / {{ $subject->full_marks }})
                                                      </span>
                                                </th>
                                          @endforeach
                                          <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                                Actions</th>
                                    </tr>
                              </thead>
                              <tbody class="z-20 bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @if (!empty($getStudent) && $getStudent->count() > 0)
                                          @foreach ($getStudent as $student)
                                                <form name="post" class="SubmitForm">
                                                      <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                      <input type="hidden" name="student_id" value="{{ $student->id }}">
                                                      <input type="hidden" name="exam_id"
                                                            value="{{ Request::get('exam_id') }}">
                                                      <input type="hidden" name="class_id"
                                                            value="{{ Request::get('class_id') }}">
                                                      {{ csrf_field() }}
                                                      <tr
                                                            class="hover:bg-violet-100 dark:hover:bg-gray-700 transition-colors w-full">
                                                            <td class="px-6 py-3">{{ $student->name }}
                                                                  {{ $student->last_name }}</td>
                                                            @php
                                                                  $i = 1;
                                                                  $totalStudentMark = 0;
                                                                  $totalFullMarks = 0;
                                                                  $totalPassingMarks = 0;
                                                            @endphp
                                                            @foreach ($getSubject as $index => $subject)
                                                                  @php

                                                                        $totalMark = 0;
                                                                        $totalFullMarks =
                                                                            $totalFullMarks + $subject->full_marks;
                                                                        $totalPassingMarks =
                                                                            $totalPassingMarks +
                                                                            $subject->passing_marks;

                                                                        $getMark = \App\Models\ScheduleModel::getMarks(
                                                                            $student->id,
                                                                            Request::get('exam_id'),
                                                                            Request::get('class_id'),
                                                                            $subject->subject_id,
                                                                        );
                                                                        if (!empty($getMark)) {
                                                                            $totalMark =
                                                                                $getMark->class_work +
                                                                                $getMark->home_work +
                                                                                $getMark->exam_work +
                                                                                $getMark->test_work;
                                                                        }
                                                                        $totalStudentMark =
                                                                            $totalStudentMark + $totalMark;
                                                                        $percentage =
                                                                            ($totalStudentMark * 100) / $totalFullMarks;
                                                                        $getGrade = \App\Models\MarksGradeModel::getGrade(
                                                                            $percentage,
                                                                        );
                                                                  @endphp
                                                                  <td class="px-6 py-3">
                                                                        <div>
                                                                              <label
                                                                                    class="mb-3 block text-sm font-medium text-black dark:text-white">
                                                                                    Travail de classe <span
                                                                                          class="text-meta-1">*</span>
                                                                                    <input type="hidden"
                                                                                          name="marks[{{ $index }}][passing_marks]"
                                                                                          value="{{ $subject->passing_marks }}">
                                                                                    <input type="hidden"
                                                                                          name="marks[{{ $index }}][full_marks]"
                                                                                          value="{{ $subject->full_marks }}">
                                                                                    <input type="hidden"
                                                                                          name="marks[{{ $index }}][id]"
                                                                                          value="{{ $subject->id }}">
                                                                                    <input type="hidden"
                                                                                          name="marks[{{ $index }}][subject_id]"
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
                                                                                    class="mb-3 block text-sm font-medium text-black dark:text-white">
                                                                                    Travail de maison <span
                                                                                          class="text-meta-1">*</span>
                                                                                    <input type="text"
                                                                                          id="marks[{{ $index }}][home_work]"
                                                                                          name="marks[{{ $index }}][home_work]"
                                                                                          value="{{ $getMark ? $getMark->home_work : '' }}"
                                                                                          placeholder="Entrez une note de classe"
                                                                                          class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600">
                                                                        </div>
                                                                        <div>
                                                                              <label
                                                                                    class="mb-3 block text-sm font-medium text-black dark:text-white">
                                                                                    Travail d'examens <span
                                                                                          class="text-meta-1">*</span>
                                                                                    <input type="text"
                                                                                          id="marks[{{ $index }}][exam_work]"
                                                                                          name="marks[{{ $index }}][exam_work]"
                                                                                          value="{{ $getMark ? $getMark->exam_work : '' }}"
                                                                                          placeholder="Entrez une note de classe"
                                                                                          class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600">
                                                                        </div>
                                                                        <div>
                                                                              <label
                                                                                    class="mb-3 block text-sm font-medium text-black dark:text-white">
                                                                                    Travaux d'essai <span
                                                                                          class="text-meta-1">*</span>
                                                                                    <input type="text"
                                                                                          id="marks[{{ $index }}][test_work]"
                                                                                          name="marks[{{ $index }}][test_work]"
                                                                                          value="{{ $getMark ? $getMark->test_work : '' }}"
                                                                                          placeholder="Entrez une note de classe"
                                                                                          class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-white dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600">
                                                                        </div>
                                                                        @if (!empty($getMark))
                                                                              <div>
                                                                                    <label
                                                                                          class="mb-3 block text-sm font-medium text-black dark:text-white">
                                                                                          Résultats
                                                                                          <div
                                                                                                class="mb-3 w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition disabled:cursor-default disabled:bg-white dark:border-form-strokedark dark:bg-form-input dark:text-white">
                                                                                                <p
                                                                                                      class="flex justify-between">
                                                                                                      <span>Note totale
                                                                                                            =></span><span>{{ $totalMark }}</span>
                                                                                                </p>
                                                                                                <p
                                                                                                      class="flex justify-between">
                                                                                                      <span>Note de passage
                                                                                                            =></span><span>{{ $subject->passing_marks }}</span>
                                                                                                </p>
                                                                                                @if (!empty($getGrade))
                                                                                                      <p>Grade =>
                                                                                                            {{ $getGrade }}
                                                                                                @endif
                                                                                                <p
                                                                                                      class="flex justify-between">
                                                                                                      <span>Décision
                                                                                                            =></span><span
                                                                                                            class="{{ $totalMark >= $subject->passing_marks ? 'font-bold text-emerald-500' : 'text-red-500 font-bold' }}">{{ $totalMark >= $subject->passing_marks ? 'Admis' : 'Refusé' }}</span>
                                                                                                </p>
                                                                                          </div>
                                                                              </div>
                                                                        @endif
                                                                        <button type="submit"
                                                                              data-student="{{ $student->id }}"
                                                                              data-exam="{{ Request::get('exam_id') }}"
                                                                              data-class="{{ Request::get('class_id') }}"
                                                                              data-subject="{{ $subject->id }}"
                                                                              class="saveSingleSubject w-full flex justify-center items-center py-3 px-4 bg-gradient-to-r from-violet-600 to-violet-500 hover:from-violet-700 hover:to-violet-600 text-white font-medium rounded-lg shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-opacity-50 transition-all duration-300">
                                                                              <iconify-icon
                                                                                    icon="mdi:content-save-check-outline"
                                                                                    class="mr-2" width="20"
                                                                                    height="20"></iconify-icon>
                                                                              Sauvegarder
                                                                        </button>
                                                                  </td>
                                                                  @php
                                                                        $i = 1;
                                                                  @endphp
                                                            @endforeach
                                                            <td class="px-6 py-3">
                                                                  <button type="submit" id="addMarksRegister"
                                                                        data-action="saveAll"
                                                                        class="w-full flex justify-center items-center py-3 px-4 my-3 bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-700 hover:to-emerald-600 text-white font-medium rounded-lg shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-opacity-50 transition-all duration-300">
                                                                        <iconify-icon icon="mdi:content-save-check-outline"
                                                                              class="mr-2" width="20"
                                                                              height="20"></iconify-icon>Ajouter
                                                                  </button>
                                                                  <a target="_blank" href="{{ url('admin/examinations/marks_register/print?exam_id=' . Request::get('exam_id') .  '&student_id=' . $student->id) }}"
                                                                        data-action="saveAll"
                                                                        class="w-full flex justify-center items-center py-3 px-4 my-3 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white font-medium rounded-lg shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition-all duration-300">
                                                                        <iconify-icon icon="mdi:content-save-check-outline"
                                                                              class="mr-2" width="20"
                                                                              height="20"></iconify-icon>Imprimer
                                                                  </a>
                                                                  @php
                                                                        $percentage =
                                                                            ($totalStudentMark * 100) / $totalFullMarks;
                                                                        $getGrade = \App\Models\MarksGradeModel::getGrade(
                                                                            $percentage,
                                                                        );
                                                                  @endphp
                                                                  <div
                                                                        class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition disabled:cursor-default disabled:bg-white dark:border-form-strokedark dark:bg-form-input dark:text-white">
                                                                        <p>Note totale de l'apprenant =>
                                                                              {{ $totalStudentMark }}</p>
                                                                        <p>Note totale => {{ $totalFullMarks }}</p>
                                                                        <p>Note de passage => {{ $totalPassingMarks }}</p>
                                                                        <p>Pourcentage => {{ round($percentage, 2) }} %</p>
                                                                        @if (!empty($getGrade))
                                                                              <p>Grade => {{ $getGrade }}
                                                                        @endif
                                                                        <p
                                                                              class="{{ $totalStudentMark >= $totalPassingMarks ? 'text-emerald-400 font-bold' : 'text-red-500 font-bold' }}">
                                                                              Décision =>
                                                                              {{ $totalStudentMark >= $totalPassingMarks ? 'Admis' : 'Refusé' }}
                                                                        </p>
                                                                  </div>
                                                            </td>
                                                      </tr>
                                                </form>
                                          @endforeach
                                    @else
                                          <tr>
                                                <td colspan="{{ count($getSubject) + 2 }}"
                                                      class="px-6 py-3 text-center dark:text-gray-400 font-medium">Aucun
                                                      résultat disponible
                                                </td>
                                          </tr>
                                    @endif
                              </tbody>
                        </table>
                  </div>
            @else
                  <p class="text-center dark:text-gray-400 font-medium py-3 text-sm">Aucun résultat disponible</p>
            @endif
      </div>
@endsection

<script>
      document.addEventListener('DOMContentLoaded', function() {
            setupFormHandlers();
            setupSingleSubjectHandlers();
      });

      function setupFormHandlers() {
            document.querySelectorAll('.SubmitForm').forEach(function(form) {
                  form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        handleFormSubmit(form);
                  });
            });
      }

      function setupSingleSubjectHandlers() {
            document.querySelectorAll('.saveSingleSubject').forEach(function(button) {
                  button.addEventListener('click', function(e) {
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
            xhr.open('POST', "{{ url('admin/examinations/marks_register/add') }}", true);

            xhr.onreadystatechange = function() {
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
            td.querySelectorAll('input').forEach(function(input) {
                  if (input.type === 'text' || input.type === 'hidden') {
                        formData.append(input.name, input.value);
                  }
            });

            formData.append('student_id', student_id);
            formData.append('exam_id', exam_id);
            formData.append('class_id', class_id);
            formData.append('_token', '{{ csrf_token() }}');

            let xhr = new XMLHttpRequest();
            xhr.open('POST', "{{ url('admin/examinations/marks_register/addSingleSubject') }}", true);

            xhr.onreadystatechange = function() {
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
            setTimeout(function() {
                  messageBox.remove();
            }, 2000);
      }
</script>
