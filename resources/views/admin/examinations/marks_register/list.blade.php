@extends('layouts.app')
@section('content')
      <div class="container mx-auto px-4 py-5">
            @include('message')
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-5 gap-4">
                  <div>
                        <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                              <i class="fa-solid  fa-square-poll-horizontal text-primary-600"></i>
                              Liste du registre des notes des apprenants
                        </h1>
                        <p class="text-gray-600 dark:text-gray-300 mt-1">Gérez la liste du registre des notes des apprenants de
                              votre plateforme</p>
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
                  dont vous souhaiterez voir le registre des notes</div>

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
                                                <option selected disabled value="">Veuillez choisir une évaluation
                                                </option>
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
                                                <option selected disabled value="">Veuillez choisir une classe</option>
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
                  <div class="relative overflow-x-auto no-scrollbar rounded-lg shadow border border-gray-200 dark:border-gray-700"
                        style="max-height: 70vh;">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                              <thead class="rounded-lg bg-indigo-500 dark:bg-gray-700 sticky top-0 z-30">
                                    <tr>
                                          <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider sticky left-0 bg-indigo-500 dark:bg-gray-700 z-40 min-w-[180px]">
                                                Apprenants</th>
                                          @foreach ($getSubject as $subject)
                                                <th scope="col"
                                                      class="px-4 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider whitespace-nowrap min-w-[320px]">
                                                      <div class="flex flex-col">
                                                            <span>{{ $subject->subject_name }}</span>
                                                            <span class="text-xs font-normal mt-1 rounded py-1 inline-block">
                                                                  {{ $subject->subject_type == 'practical' ? 'Pratique' : 'Théorique' }}
                                                                  ({{ $subject->passing_marks }} /
                                                                  {{ $subject->full_marks }})
                                                            </span>
                                                      </div>
                                                </th>
                                          @endforeach
                                          <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider right-0 bg-indigo-500 dark:bg-gray-700 z-40 sticky min-w-[200px]">
                                                Actions</th>
                                    </tr>
                              </thead>
                              <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
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
                                                            class="hover:bg-indigo-50 dark:hover:bg-gray-700 transition-colors w-full">
                                                            <td
                                                                  class="px-6 py-4 sticky left-0 bg-white dark:bg-gray-800 z-30 whitespace-nowrap font-medium">
                                                                  {{ $student->name }} {{ $student->last_name }}
                                                            </td>
                                                            @php
                                                                  $i = 1;
                                                                  $totalStudentMark = 0;
                                                                  $totalFullMarks = 0;
                                                                  $totalPassingMarks = 0;
                                                            @endphp
                                                            @foreach ($getSubject as $index => $subject)
                                                                  @php
                                                                        $getMark = \App\Models\ScheduleModel::getMarks(
                                                                            $student->id,
                                                                            Request::get('exam_id'),
                                                                            Request::get('class_id'),
                                                                            $subject->subject_id,
                                                                        );

                                                                        // Éviter la division par zero
                                                                        $totalMark = 0;
                                                                        $percentage = 0;
                                                                        $getGrade = '';

                                                                        if (!empty($getMark)) {
                                                                            $totalMark =
                                                                                $getMark->class_work +
                                                                                $getMark->home_work +
                                                                                $getMark->exam_work +
                                                                                $getMark->test_work +
                                                                                $getMark->quiz_1 +
                                                                                $getMark->quiz_2 +
                                                                                $getMark->quiz_3 +
                                                                                $getMark->quiz_4 +
                                                                                $getMark->quiz_5 +
                                                                                $getMark->assignment_1 +
                                                                                $getMark->assignment_2 +
                                                                                $getMark->assignment_3;

                                                                            // Vérifier que full_marks n'est pas zero avant de diviser
                                                                            if ($subject->full_marks > 0) {
                                                                                $percentage = round(
                                                                                    ($totalMark * 100) /
                                                                                        $subject->full_marks,
                                                                                    2,
                                                                                );
                                                                                $getGrade = \App\Models\MarksGradeModel::getGrade(
                                                                                    $percentage,
                                                                                );
                                                                            }
                                                                        }
                                                                  @endphp

                                                                  <td
                                                                        class="px-4 py-3 whitespace-nowrap border-r border-gray-200 dark:border-gray-600">
                                                                        <!-- Champs cachés avec la bonne structure -->
                                                                        <input type="hidden"
                                                                              name="marks[{{ $index }}][passing_marks]"
                                                                              value="{{ $subject->passing_marks }}">
                                                                        <input type="hidden"
                                                                              name="marks[{{ $index }}][full_marks]"
                                                                              value="{{ $subject->full_marks }}">
                                                                        <input type="hidden"
                                                                              name="marks[{{ $index }}][subject_id]"
                                                                              value="{{ $subject->subject_id }}">

                                                                        <!-- Quiz fields -->
                                                                        <div class="mb-3">
                                                                              <label
                                                                                    class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                                                    Interrogations
                                                                              </label>
                                                                              <div class="grid grid-cols-3 gap-2">
                                                                                    @for ($q = 1; $q <= 5; $q++)
                                                                                          <div>
                                                                                                <input type="number"
                                                                                                      step="0.01"
                                                                                                      min="0"
                                                                                                      max="{{ $subject->full_marks > 0 ? $subject->full_marks : 100 }}"
                                                                                                      name="marks[{{ $index }}][quiz_{{ $q }}]"
                                                                                                      value="{{ $getMark ? $getMark->{'quiz_' . $q} : '' }}"
                                                                                                      placeholder="Interro {{ $q }}"
                                                                                                      class="w-full rounded border border-gray-300 bg-gray-50 px-2 py-1 text-xs text-gray-800 outline-none transition focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                                                          </div>
                                                                                    @endfor
                                                                              </div>
                                                                        </div>

                                                                        <!-- Assignment fields -->
                                                                        <div class="mb-3">
                                                                              <label
                                                                                    class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                                                    Devoirs
                                                                              </label>
                                                                              <div class="grid grid-cols-3 gap-2">
                                                                                    @for ($a = 1; $a <= 3; $a++)
                                                                                          <div>
                                                                                                <input type="number"
                                                                                                      step="0.01"
                                                                                                      min="0"
                                                                                                      max="{{ $subject->full_marks > 0 ? $subject->full_marks : 100 }}"
                                                                                                      name="marks[{{ $index }}][assignment_{{ $a }}]"
                                                                                                      value="{{ $getMark ? $getMark->{'assignment_' . $a} : '' }}"
                                                                                                      placeholder="Devoir {{ $a }}"
                                                                                                      class="w-full rounded border border-gray-300 bg-gray-50 px-2 py-1 text-xs text-gray-800 outline-none transition focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                                                          </div>
                                                                                    @endfor
                                                                              </div>
                                                                        </div>

                                                                        <!-- Travaux individuels et collectifs -->
                                                                        <div class="mb-3">
                                                                              <label
                                                                                    class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                                                    Travaux individuels et collectifs
                                                                              </label>
                                                                              <div class="grid grid-cols-2 gap-2">
                                                                                    <input type="hidden"
                                                                                          name="marks[{{ $index }}][id]"
                                                                                          value="{{ $subject->id }}">
                                                                                    <div>
                                                                                          <input type="number"
                                                                                                step="0.01"
                                                                                                min="0"
                                                                                                max="{{ $subject->full_marks > 0 ? $subject->full_marks : 100 }}"
                                                                                                name="marks[{{ $index }}][class_work]"
                                                                                                value="{{ $getMark ? $getMark->class_work : '' }}"
                                                                                                placeholder="Travail de classe"
                                                                                                class="w-full rounded border border-gray-300 bg-gray-50 px-2 py-1 text-xs text-gray-800 outline-none transition focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                                                    </div>
                                                                                    <div>
                                                                                          <input type="number"
                                                                                                step="0.01"
                                                                                                min="0"
                                                                                                max="{{ $subject->full_marks > 0 ? $subject->full_marks : 100 }}"
                                                                                                name="marks[{{ $index }}][home_work]"
                                                                                                value="{{ $getMark ? $getMark->home_work : '' }}"
                                                                                                placeholder="Travail de maison"
                                                                                                class="w-full rounded border border-gray-300 bg-gray-50 px-2 py-1 text-xs text-gray-800 outline-none transition focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                                                    </div>
                                                                                    <div>
                                                                                          <input type="number"
                                                                                                step="0.01"
                                                                                                min="0"
                                                                                                max="{{ $subject->full_marks > 0 ? $subject->full_marks : 100 }}"
                                                                                                name="marks[{{ $index }}][exam_work]"
                                                                                                value="{{ $getMark ? $getMark->exam_work : '' }}"
                                                                                                placeholder="Travail d'examen"
                                                                                                class="w-full rounded border border-gray-300 bg-gray-50 px-2 py-1 text-xs text-gray-800 outline-none transition focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                                                    </div>
                                                                                    <div>
                                                                                          <input type="number"
                                                                                                step="0.01"
                                                                                                min="0"
                                                                                                max="{{ $subject->full_marks > 0 ? $subject->full_marks : 100 }}"
                                                                                                name="marks[{{ $index }}][test_work]"
                                                                                                value="{{ $getMark ? $getMark->test_work : '' }}"
                                                                                                placeholder="Travail de test"
                                                                                                class="w-full rounded border border-gray-300 bg-gray-50 px-2 py-1 text-xs text-gray-800 outline-none transition focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                                                    </div>
                                                                              </div>
                                                                        </div>

                                                                        <!-- Section résultats -->
                                                                        <div class="result-display mt-3">
                                                                              @if (!empty($getMark))
                                                                                    <div
                                                                                          class="p-2 bg-gray-50 dark:bg-gray-700 rounded border border-gray-200 dark:border-gray-600">
                                                                                          <div class="text-xs">
                                                                                                <p
                                                                                                      class="flex justify-between mb-1">
                                                                                                      <span
                                                                                                            class="font-medium">Note
                                                                                                            totale:</span>
                                                                                                      <span>{{ $totalMark }}</span>
                                                                                                </p>
                                                                                                <p
                                                                                                      class="flex justify-between mb-1">
                                                                                                      <span
                                                                                                            class="font-medium">Note
                                                                                                            maximale:</span>
                                                                                                      <span>{{ $subject->full_marks > 0 ? $subject->full_marks : 'Non définie' }}</span>
                                                                                                </p>
                                                                                                <p
                                                                                                      class="flex justify-between mb-1">
                                                                                                      <span
                                                                                                            class="font-medium">Note
                                                                                                            de passage:</span>
                                                                                                      <span>{{ $subject->passing_marks }}</span>
                                                                                                </p>
                                                                                                @if ($subject->full_marks > 0 && !empty($getGrade))
                                                                                                      <p
                                                                                                            class="flex justify-between mb-1">
                                                                                                            <span
                                                                                                                  class="font-medium">Pourcentage:</span>
                                                                                                            <span>{{ $percentage }}%</span>
                                                                                                      </p>
                                                                                                      <p
                                                                                                            class="flex justify-between mb-1">
                                                                                                            <span
                                                                                                                  class="font-medium">Grade:</span>
                                                                                                            <span>{{ $getGrade }}</span>
                                                                                                      </p>
                                                                                                @endif
                                                                                                <p
                                                                                                      class="flex justify-between mt-2 pt-2 border-t border-gray-200 dark:border-gray-600">
                                                                                                      <span
                                                                                                            class="font-medium">Décision:</span>
                                                                                                      <span
                                                                                                            class="{{ $totalMark >= $subject->passing_marks ? 'text-emerald-600 font-bold' : 'text-red-600 font-bold' }}">
                                                                                                            {{ $totalMark >= $subject->passing_marks ? 'Admis' : 'Refusé' }}
                                                                                                      </span>
                                                                                                </p>
                                                                                          </div>
                                                                                    </div>
                                                                              @endif
                                                                        </div>

                                                                        <button type="button"
                                                                              data-student="{{ $student->id }}"
                                                                              data-exam="{{ Request::get('exam_id') }}"
                                                                              data-class="{{ Request::get('class_id') }}"
                                                                              data-subject="{{ $subject->subject_id }}"
                                                                              class="saveSingleSubject w-full flex justify-center items-center py-1.5 px-3 mt-2 bg-violet-600 hover:bg-violet-700 text-white text-xs font-medium rounded shadow-sm transition-colors">
                                                                              <iconify-icon
                                                                                    icon="mdi:content-save-check-outline"
                                                                                    class="mr-1" width="14"
                                                                                    height="14"></iconify-icon>
                                                                              Sauvegarder
                                                                        </button>
                                                                  </td>
                                                            @endforeach
                                                            <td
                                                                  class="px-4 py-3 sticky right-0 bg-white dark:bg-gray-800 z-30 whitespace-nowrap border-l border-gray-200 dark:border-gray-600">
                                                                  <div class="flex flex-col gap-2">

                                                                        <button type="submit" id="addMarksRegister"
                                                                              class="w-full flex justify-center items-center py-2 px-3 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded shadow-sm transition-colors">
                                                                              <iconify-icon icon="mdi:content-save-all"
                                                                                    class="mr-1" width="16"
                                                                                    height="16"></iconify-icon>
                                                                              Sauvegarder tout
                                                                        </button>

                                                                        <a target="_blank"
                                                                              href="{{ url('admin/examinations/marks_register/print?exam_id=' . Request::get('exam_id') . '&student_id=' . $student->id) }}"
                                                                              class="w-full flex justify-center items-center py-2 px-3 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded shadow-sm transition-colors">
                                                                              <iconify-icon icon="mdi:printer"
                                                                                    class="mr-1" width="16"
                                                                                    height="16"></iconify-icon>
                                                                              Imprimer
                                                                        </a>
                                                                  </div>
                                                                  @php
                                                                        // CORRECTION : Vérifier que $totalFullMarks n'est pas zéro avant de diviser
                                                                    $percentage = 0;
                                                                    $getGrade = 'Non défini';

                                                                        if ($totalFullMarks > 0) {
                                                                            $percentage =
                                                                                ($totalStudentMark * 100) /
                                                                                $totalFullMarks;
                                                                            $getGrade = \App\Models\MarksGradeModel::getGrade(
                                                                                $percentage,
                                                                            );
                                                                        }
                                                                  @endphp
                                                                  <div
                                                                        class="mt-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 text-xs">
                                                                        <h4
                                                                              class="font-bold text-center mb-2 text-gray-800 dark:text-white">
                                                                              RÉSULTAT GLOBAL</h4>
                                                                        <div class="space-y-1">
                                                                              <p class="flex justify-between">
                                                                                    <span class="font-medium">Total
                                                                                          apprenant:</span>
                                                                                    <span>{{ $totalStudentMark }}</span>
                                                                              </p>
                                                                              <p class="flex justify-between">
                                                                                    <span class="font-medium">Total
                                                                                          points:</span>
                                                                                    <span>{{ $totalFullMarks }}</span>
                                                                              </p>
                                                                              <p class="flex justify-between">
                                                                                    <span class="font-medium">Points de
                                                                                          passage:</span>
                                                                                    <span>{{ $totalPassingMarks }}</span>
                                                                              </p>
                                                                              <p class="flex justify-between">
                                                                                    <span
                                                                                          class="font-medium">Pourcentage:</span>
                                                                                    <span>{{ round($percentage, 2) }}%</span>
                                                                              </p>
                                                                              @if (!empty($getGrade))
                                                                                    <p class="flex justify-between">
                                                                                          <span
                                                                                                class="font-medium">Grade:</span>
                                                                                          <span>{{ $getGrade }}</span>
                                                                                    </p>
                                                                              @endif
                                                                              <p
                                                                                    class="flex justify-between mt-2 pt-2 border-t border-gray-200 dark:border-gray-600 font-bold">
                                                                                    <span>Décision:</span>
                                                                                    <span
                                                                                          class="{{ $totalStudentMark >= $totalPassingMarks ? 'text-emerald-600' : 'text-red-600' }}">
                                                                                          {{ $totalStudentMark >= $totalPassingMarks ? 'ADMIS' : 'REFUSÉ' }}
                                                                                    </span>
                                                                              </p>
                                                                        </div>
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
                        handleSingleSubject(button);
                  });
            });
      }

      function handleSingleSubject(button) {
            let student_id = button.getAttribute('data-student');
            let exam_id = button.getAttribute('data-exam');
            let class_id = button.getAttribute('data-class');
            let subject_id = button.getAttribute('data-subject');
            let td = button.closest('td');

            let formData = new FormData();

            // Désactiver le bouton pendant l'envoi
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sauvegarde...';

            // Récupérer tous les champs de cette matière spécifique
            td.querySelectorAll('input').forEach(function(input) {
                  if (input.type === 'number' || input.type === 'hidden') {
                        // Vérifier que le champ appartient à cette matière
                        if (input.name.includes(`[${subject_id}]`)) {
                              let value = input.value === '' ? '0' : input.value;
                              formData.append(input.name, value);
                        }
                  }
            });

            // Ajouter les paramètres principaux
            formData.append('student_id', student_id);
            formData.append('exam_id', exam_id);
            formData.append('class_id', class_id);
            formData.append('subject_id', subject_id);
            formData.append('_token', '{{ csrf_token() }}');

            console.log('Envoi des données pour une matière:', Object.fromEntries(formData));

            // CORRECTION : Utiliser la bonne URL
            fetch("{{ url('admin/examinations/marks_register/addSingleSubject') }}", {
                        method: 'POST',
                        body: formData,
                        headers: {
                              'X-Requested-With': 'XMLHttpRequest',
                              'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                  })
                  .then(response => {
                        if (!response.ok) {
                              throw new Error('Erreur réseau: ' + response.status);
                        }
                        return response.json();
                  })
                  .then(data => {
                        console.log('Réponse reçue:', data);
                        displayMessage(data);
                        if (data.success) {
                              updateSubjectDisplay(td, data.data, subject_id);
                        }
                  })
                  .catch(error => {
                        console.error('Erreur:', error);
                        displayMessage({
                              success: false,
                              message: 'Erreur réseau: ' + error.message
                        });
                  })
                  .finally(() => {
                        // Réactiver le bouton
                        button.disabled = false;
                        button.innerHTML =
                              '<iconify-icon icon="mdi:content-save-check-outline" class="mr-1" width="14" height="14"></iconify-icon> Sauvegarder';
                  });
      }

      function handleFormSubmit(form) {
            let formData = new FormData(form);
            let submitButton = form.querySelector('#addMarksRegister');

            // Désactiver le bouton pendant l'envoi
            if (submitButton) {
                  submitButton.disabled = true;
                  submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sauvegarde...';
            }

            console.log('Envoi de toutes les données:', Object.fromEntries(formData));

            fetch("{{ url('admin/examinations/marks_register/add') }}", {
                        method: 'POST',
                        body: formData,
                        headers: {
                              'X-Requested-With': 'XMLHttpRequest',
                              'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                  })
                  .then(response => {
                        if (!response.ok) {
                              throw new Error('Erreur réseau: ' + response.status);
                        }
                        return response.json();
                  })
                  .then(data => {
                        console.log('Réponse reçue pour toutes les matières:', data);
                        displayMessage(data);
                        if (data.success) {
                              // Recharger après 2 secondes pour voir les nouvelles données
                              setTimeout(() => {
                                    window.location.reload();
                              }, 2000);
                        }
                  })
                  .catch(error => {
                        console.error('Error:', error);
                        displayMessage({
                              success: false,
                              message: 'Erreur réseau. Veuillez réessayer.'
                        });
                  })
                  .finally(() => {
                        // Réactiver le bouton
                        if (submitButton) {
                              submitButton.disabled = false;
                              submitButton.innerHTML =
                                    '<iconify-icon icon="mdi:content-save-all" class="mr-1" width="16" height="16"></iconify-icon> Sauvegarder tout';
                        }
                  });
      }

      function updateSubjectDisplay(td, data, subject_id) {
            if (data && data.marks) {
                  let marks = data.marks;

                  // Calculer le total des notes
                  let totalMark = (parseFloat(marks.class_work) || 0) +
                        (parseFloat(marks.home_work) || 0) +
                        (parseFloat(marks.exam_work) || 0) +
                        (parseFloat(marks.test_work) || 0) +
                        (parseFloat(marks.quiz_1) || 0) +
                        (parseFloat(marks.quiz_2) || 0) +
                        (parseFloat(marks.quiz_3) || 0) +
                        (parseFloat(marks.quiz_4) || 0) +
                        (parseFloat(marks.quiz_5) || 0) +
                        (parseFloat(marks.assignment_1) || 0) +
                        (parseFloat(marks.assignment_2) || 0) +
                        (parseFloat(marks.assignment_3) || 0);

                  // Trouver ou créer le div de résultats
                  let resultDiv = td.querySelector('.result-display');
                  if (!resultDiv) {
                        resultDiv = document.createElement('div');
                        resultDiv.className = 'result-display mt-3';
                        let saveButton = td.querySelector('.saveSingleSubject');
                        td.insertBefore(resultDiv, saveButton);
                  }

                  // Construire le HTML des résultats
                  resultDiv.innerHTML = `
            <div class="p-2 bg-gray-50 dark:bg-gray-700 rounded border border-gray-200 dark:border-gray-600">
                <div class="text-xs">
                    <p class="flex justify-between mb-1">
                        <span class="font-medium">Note totale:</span>
                        <span class="font-bold">${totalMark.toFixed(2)}</span>
                    </p>
                    <p class="flex justify-between mb-1">
                        <span class="font-medium">Note maximale:</span>
                        <span>${data.full_marks || marks.full_marks || 'Non définie'}</span>
                    </p>
                    <p class="flex justify-between mb-1">
                        <span class="font-medium">Note de passage:</span>
                        <span>${data.passing_marks || marks.passing_marks}</span>
                    </p>
                    ${data.quiz_average ? `
                    <p class="flex justify-between mb-1">
                        <span class="font-medium">Moyenne Quiz:</span>
                        <span>${parseFloat(data.quiz_average).toFixed(2)}</span>
                    </p>
                    ` : ''}
                    ${data.assignment_average ? `
                    <p class="flex justify-between mb-1">
                        <span class="font-medium">Moyenne Devoirs:</span>
                        <span>${parseFloat(data.assignment_average).toFixed(2)}</span>
                    </p>
                    ` : ''}
                    ${data.percentage ? `
                    <p class="flex justify-between mb-1">
                        <span class="font-medium">Pourcentage:</span>
                        <span>${data.percentage}%</span>
                    </p>
                    ` : ''}
                    ${data.grade ? `
                    <p class="flex justify-between mb-1">
                        <span class="font-medium">Grade:</span>
                        <span class="font-bold">${data.grade}</span>
                    </p>
                    ` : ''}
                    <p class="flex justify-between mt-2 pt-2 border-t border-gray-200 dark:border-gray-600">
                        <span class="font-medium">Décision:</span>
                        <span class="${totalMark >= (data.passing_marks || marks.passing_marks) ? 'text-emerald-600 font-bold' : 'text-red-600 font-bold'}">
                            ${totalMark >= (data.passing_marks || marks.passing_marks) ? '✅ Admis' : '❌ Refusé'}
                        </span>
                    </p>
                </div>
            </div>
        `;
            }
      }

      function displayMessage(response) {
            console.log('Affichage du message:', response);

            // Supprimer les messages existants
            document.querySelectorAll('.alert-message').forEach(msg => {
                  if (msg.parentNode) {
                        msg.parentNode.removeChild(msg);
                  }
            });

            let messageBox = document.createElement('div');
            messageBox.className = 'alert-message';

            // Styles CSS inline pour éviter les conflits
            messageBox.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 16px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 10000;
            color: white;
            font-weight: 500;
            transform: translateX(400px);
            opacity: 0;
            transition: all 0.3s ease;
            max-width: 400px;
        ${response.success ? 'background-color: #10b981;' : 'background-color: #ef4444;'}
    `;

            messageBox.innerHTML = `
        <div class="flex items-center gap-2" style="color: white;">
            <i class="fas ${response.success ? 'fa-check-circle' : 'fa-exclamation-triangle'}"></i>
            <span>${response.message || 'Action effectuée'}</span>
        </div>
    `;

            document.body.appendChild(messageBox);

            // Animation d'entrée
            setTimeout(() => {
                  messageBox.style.transform = 'translateX(0)';
                  messageBox.style.opacity = '1';
            }, 100);

            // Animation de sortie après 4 secondes
            setTimeout(() => {
                  messageBox.style.transform = 'translateX(400px)';
                  messageBox.style.opacity = '0';
                  setTimeout(() => {
                        if (messageBox.parentNode) {
                              messageBox.parentNode.removeChild(messageBox);
                        }
                  }, 300);
            }, 4000);
      }

      // Fonction de test pour vérifier que les messages fonctionnent
      function testMessage() {
            displayMessage({
                  success: true,
                  message: 'Test de message réussi!'
            });
      }
</script>

<style>
      .overflow-x-auto {
            overflow-x: auto;
            width: 100%;
      }

      .sticky {
            position: sticky;
      }

      .sticky.top-0 {
            top: 0;
      }

      .sticky.left-0 {
            left: 0;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
      }

      .sticky.right-0 {
            right: 0;
            box-shadow: -2px 0 5px rgba(0, 0, 0, 0.1);
      }

      .whitespace-nowrap {
            white-space: nowrap;
      }

      /* Style pour la barre de défilement */
      .overflow-x-auto::-webkit-scrollbar {
            height: 8px;
      }

      .overflow-x-auto::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
      }

      .overflow-x-auto::-webkit-scrollbar-thumb {
            background: #c4b5fd;
            border-radius: 4px;
      }

      .overflow-x-auto::-webkit-scrollbar-thumb:hover {
            background: #a78bfa;
      }

      /* Style sombre pour la barre de défilement */
      .dark .overflow-x-auto::-webkit-scrollbar-track {
            background: #374151;
      }

      .dark .overflow-x-auto::-webkit-scrollbar-thumb {
            background: #6b7280;
      }

      .dark .overflow-x-auto::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
      }

      /* Amélioration de la disposition des champs */
      .min-w-\[320px\] {
            min-width: 320px;
      }

      .text-xs input {
            font-size: 0.75rem;
            padding: 0.4rem 0.5rem;
      }

      .alert-message {
            transform: translateX(100%);
            opacity: 0;
      }

      .alert-message {
            transition: transform 0.3s ease, opacity 0.3s ease;
      }

      /* Style pour le spinner */
      .fa-spinner {
            animation: spin 1s linear infinite;
      }

      @keyframes spin {
            0% {
                  transform: rotate(0deg);
            }

            100% {
                  transform: rotate(360deg);
            }
      }

      /* Désactiver le style par défaut des boutons désactivés */
      button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
      }
</style>
