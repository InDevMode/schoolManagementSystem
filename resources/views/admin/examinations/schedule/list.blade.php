@extends('layouts.app')
@section('content')
      <div class="container mx-auto px-4 py-5">
            @include('message')
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-5 gap-4">
                  <div>
                        <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                              <i class="fa-solid fa-clock text-primary-600"></i>
                              Liste des programmations de cours
                        </h1>
                        <p class="text-gray-600 dark:text-gray-300 mt-1">Gérez la liste des programmations de cours de votre
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
                                          Programmations
                                    </span>
                              </li>
                        </ol>
                  </nav>
            </div>

            <div class="text-red-600 dark:text-red-400 text-sm font-semibold my-3">Veuillez choisir l'évaluation et la classe
                  dont vous souhaiterez
                  définir les programmations
            </div>

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
                                                      une évaluation pour ajouter une programmation
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
                                                      une classe pour afficher les programmations
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
                                    <a href="{{ url('admin/examinations/schedule/list') }}"
                                          class="w-full bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700 text-gray-800 dark:text-white font-medium rounded-lg px-4 py-2.5 flex items-center justify-center gap-2 transition-colors">
                                          <i class="fas fa-sync-alt"></i>
                                          Réinitialiser
                                    </a>
                              </div>
                        </div>
                  </form>
            </div>

            @if (!empty($getExamSchedule))
                  <form action="{{ url('admin/examinations/schedule/add') }}" method="post">
                        {{ csrf_field() }}
                        <input type="hidden" name="exam_id" value="{{ Request::get('exam_id') }}">
                        <input type="hidden" name="class_id" value="{{ Request::get('class_id') }}">
                        <div class="relative overflow rounded-lg z-10">
                              <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="rounded-lg bg-violet-600 dark:bg-gray-700">
                                          <tr>
                                                <th scope="col"
                                                      class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                                      Matière
                                                </th>
                                                <th scope="col"
                                                      class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                                      Date
                                                </th>
                                                <th scope="col"
                                                      class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                                      Heure de début
                                                </th>
                                                <th scope="col"
                                                      class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                                      Heure de fin
                                                </th>
                                                <th scope="col"
                                                      class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                                      Numéro de salle
                                                </th>
                                                <th scope="col"
                                                      class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                                      Note totale
                                                </th>
                                                <th scope="col"
                                                      class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                                      Note de passage
                                                </th>
                                          </tr>
                                    </thead>
                                    <tbody
                                          class="z-20 bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                          @php
                                                $i = 1;
                                          @endphp
                                          @foreach ($getExamSchedule as $index => $examSchedule)
                                                <tr
                                                      class="hover:bg-violet-100 dark:hover:bg-gray-700 transition-colors w-full">
                                                      <td
                                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                            {{ $examSchedule['subject_name'] }}
                                                            <input type="hidden"
                                                                  name="schedule[{{ $index }}][subject_id]"
                                                                  value="{{ $examSchedule['subject_id'] }}">
                                                      </td>
                                                      <td
                                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                            <div class="w-full">

                                                                  <div class="relative">
                                                                        <div
                                                                              class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                                              <i
                                                                                    class="fas fa-calendar-check text-gray-400"></i>
                                                                        </div>
                                                                        <input type="date" id="schedule[{{ $i }}][exam_date]"
                                                                             name="schedule[{{ $index }}][exam_date]"
                                                                               value="{{ old('schedule.' . $index . '.exam_date', $examSchedule['exam_date']) }}"
                                                                              class="pl-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-600 focus:border-primary-600 p-2.5">
                                                                  </div>
                                                            </div>
                                                      </td>
                                                      <td
                                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                            <input type="time"
                                                                  id="schedule[{{ $index }}][start_time]"
                                                                  name="schedule[{{ $index }}][start_time]"
                                                                  value="{{ old('schedule.' . $index . '.start_time', $examSchedule['start_time']) }}"
                                                                  class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-50 px-5 py-2.5 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600">
                                                      </td>
                                                      <td class="px-6 py-3 font-semibold">
                                                            <input type="time"
                                                                  id="schedule[{{ $index }}][end_time]"
                                                                  name="schedule[{{ $index }}][end_time]"
                                                                  value="{{ old('schedule.' . $index . '.end_time', $examSchedule['end_time']) }}"
                                                                  class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-50 px-5 py-2.5 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600">
                                                      </td>
                                                      <td
                                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                            <input type="text"
                                                                  id="schedule[{{ $index }}][room_number]"
                                                                  name="schedule[{{ $index }}][room_number]"
                                                                  value="{{ old('schedule.' . $index . '.room_number', $examSchedule['room_number']) }}"
                                                                  placeholder="numéro de salle"
                                                                  class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-50 px-5 py-2.5 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600">
                                                      </td>
                                                      <td
                                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                            <input type="text"
                                                                  id="schedule[{{ $index }}][full_marks]"
                                                                  name="schedule[{{ $index }}][full_marks]"
                                                                  value="{{ old('schedule.' . $index . '.full_marks', $examSchedule['full_marks']) }}"
                                                                  placeholder="totale des notes"
                                                                  class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-50 px-5 py-2.5 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600">
                                                      </td>
                                                      <td
                                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                            <input type="text"
                                                                  id="schedule[{{ $index }}][passing_marks]"
                                                                  name="schedule[{{ $index }}][passing_marks]"
                                                                  value="{{ old('schedule.' . $index . '.passing_marks', $examSchedule['passing_marks']) }}"
                                                                  placeholder="note de passage"
                                                                  class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-50 px-5 py-2.5 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600">
                                                      </td>
                                                </tr>
                                                @php
                                                      $i++;
                                                @endphp
                                          @endforeach
                                          @if (empty($getExamSchedule))
                                                <tr class="text-center text-gray-700 dark:text-bodydark1">
                                                      <td colspan="100%" class="py-3"> Aucune évaluation programmée.</td>
                                                </tr>
                                          @endif
                                    </tbody>
                              </table>
                              <div class="w-full my-3">
                                    <button type="submit"
                                          class="w-full flex justify-center items-center py-3 px-4 bg-gradient-to-r from-violet-600 to-violet-500 hover:from-violet-700 hover:to-violet-600 text-white font-medium rounded-lg shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-opacity-50 transition-all duration-300">
                                          <iconify-icon icon="mdi:content-save-check-outline" class="mr-2" width="20"
                                                height="20"></iconify-icon>
                                          Ajouter une programmation
                                    </button>
                              </div>
                        </div>
                  </form>
            @else
                  <div class="flex justify-center py-3 text-gray-700 dark:text-bodydark1 text-sm">
                        Cette assignation n'est pas active pour le moment
                  </div>
            @endif
      </div>
      </div>
@endsection

<script>

</script>
