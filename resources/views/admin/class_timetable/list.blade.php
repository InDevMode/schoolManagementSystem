@extends('layouts.app')
@section('content')
      <div class="container mx-auto px-4 py-5">
            @include('message')
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-5 gap-4">
                  <div>
                        <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                              <i class="fa-solid fa-clock text-primary-600"></i>
                              Liste des horaires de cours
                        </h1>
                        <p class="text-gray-600 dark:text-gray-300 mt-1">Gérez la liste des horaires de cours de votre
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
                                    <span class="text-violet-600">
                                          Horaire de cours
                                    </span>
                              </li>
                        </ol>
                  </nav>
            </div>

            <div class="text-red-600 dark:text-red-400 text-sm font-semibold my-3">Veuillez chossir la classe et la matière
                  dont vous souhaiterez
                  définir les horaires
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
                                          Classe <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                          <select id="class_id" name="class_id" required
                                                class="custom-select w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500 transition-all duration-200">
                                                <option selected disabled value="">Veuillez choisir
                                                      une classe pour afficher les matières associées
                                                      @foreach ($getClass as $class)
                                                <option {{ Request::get('class_id') == $class->id ? 'selected' : '' }}
                                                      value="{{ $class->id }}">{{ $class->name }}</option>
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
                                          Matière <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                          <select id="subject_id" name="subject_id" required
                                                class="custom-select w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500 transition-all duration-200">
                                                <option selected disabled value="">Veuillez choisir
                                                      une matière pour afficher les horaires de cours
                                                      @if (!empty($getSubject))
                                                            @foreach ($getSubject as $subject)
                                                <option
                                                      {{ Request::get('subject_id') == $subject->subject_id ? 'selected' : '' }}
                                                      value="{{ $subject->subject_id }}">
                                                      {{ $subject->subject_name }}</option>
                                                @endforeach
                                                @endif
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
                                    <a href="{{ url('admin/class_timetable/list') }}"
                                          class="w-full bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700 text-gray-800 dark:text-white font-medium rounded-lg px-4 py-2.5 flex items-center justify-center gap-2 transition-colors">
                                          <i class="fas fa-sync-alt"></i>
                                          Réinitialiser
                                    </a>
                              </div>
                        </div>
                  </form>
            </div>


            @if (!empty(Request::get('class_id') && !empty(Request::get('subject_id'))))
                  <form action="{{ url('admin/class_timetable/add') }}" method="post"
                        class="">
                        {{ csrf_field() }}
                        <input type="hidden" name="subject_id" value="{{ Request::get('subject_id') }}">
                        <input type="hidden" name="class_id" value="{{ Request::get('class_id') }}">

                        <!-- Table -->
                        <div class="relative overflow rounded-lg z-10">
                              <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="rounded-lg bg-violet-600 dark:bg-gray-700">
                                          <tr>
                                                <th scope="col"
                                                      class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                                      Jour
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
                                                      Salle
                                                </th>
                                          </tr>
                                    </thead>
                                    <tbody
                                          class="z-20 bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                          @php
                                                $i = 1;
                                          @endphp
                                          @foreach ($week as $index => $weekData)
                                                <tr class="hover:bg-violet-100 dark:hover:bg-gray-700 transition-colors w-full">
                                                      <td
                                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 w-[25%]">
                                                            <input type="text"
                                                                  id="timetable[{{ $index }}][week_id]"
                                                                    name="timetable[{{ $index }}][week_id]" disabled
                                                                  value="{{ old('timetable.' . $index . '.week_name', $weekData['week_name']) }}"
                                                                  class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600">
                                                      </td>
                                                      <td
                                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 w-[25%]">
                                                            <input type="time"
                                                                  id="timetable[{{ $index }}][start_time]"
                                                                  name="timetable[{{ $index }}][start_time]"
                                                                  value="{{ old('timetable.' . $index . '.start_time', $weekData['start_time']) }}"
                                                                  class="w- rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600">
                                                      </td>
                                                      <td
                                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400" w-[25%]>
                                                            <input type="time"
                                                                  id="timetable[{{ $index }}][end_time]"
                                                                  name="timetable[{{ $index }}][end_time]"
                                                                  value="{{ old('timetable.' . $index . '.end_time', $weekData['end_time']) }}"
                                                                  class="w- rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600">
                                                      </td>
                                                      <td
                                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 w-[25%]">
                                                            <input type="text"
                                                                  id="timetable[{{ $index }}][room_number]"
                                                                  name="timetable[{{ $index }}][room_number]"
                                                                  value="{{ old('timetable.' . $index . '.room_number', $weekData['room_number']) }}"
                                                                  class="w- rounded-lg border-[1.5px] border-stroke bg-gray-100 px-5 py-2.5 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600">
                                                      </td>
                                                </tr>
                                                @php
                                                      $i++;
                                                @endphp
                                          @endforeach
                                          @if ($getWeek->isEmpty())
                                                <tr>
                                                      <td colspan="100%"
                                                            class="text-center text-gray-700 dark:text-bodydark1">
                                                            Aucun horaire de cours trouvé.
                                                      </td>
                                                </tr>
                                          @endif
                                    </tbody>
                              </table>
                              <div class="w-full my-3">
                                    <button type="submit"
                                          class="w-full flex justify-center items-center py-3 px-4 bg-gradient-to-r from-violet-600 to-violet-500 hover:from-violet-700 hover:to-violet-600 text-white font-medium rounded-lg shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-opacity-50 transition-all duration-300">
                                          <iconify-icon icon="mdi:content-save-check-outline" class="mr-2" width="20"
                                                height="20"></iconify-icon>
                                          Ajouter un horaire
                                    </button>
                              </div>
                        </div>

                  </form>
            @endif
      </div>
@endsection

<script>
      document.addEventListener("DOMContentLoaded", function() {
            const classSelect = document.getElementById("class_id");
            const subjectSelect = document.getElementById("subject_id");

            if (!classSelect || !subjectSelect) return;

            classSelect.addEventListener("change", function() {
                  const class_id = this.value;
                  console.log("class_id:", class_id);

                  fetch("{{ url('admin/class_timetable/subject') }}", {
                              method: "POST",
                              headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                              },
                              body: JSON.stringify({
                                    class_id: class_id
                              })
                        })
                        .then(response => {
                              if (!response.ok) {
                                    throw new Error("Erreur serveur : " + response
                                          .statusText);
                              }
                              return response.json();
                        })
                        .then(data => {
                              subjectSelect.innerHTML = "";

                              let defaultOption = document.createElement("option");
                              defaultOption.disabled = true;
                              defaultOption.selected = true;
                              defaultOption.textContent = "Choisissez une matière...";
                              subjectSelect.appendChild(defaultOption);

                              if (data.subjects && data.subjects.length > 0) {
                                    data.subjects.forEach(subject => {
                                          let option = document.createElement(
                                                "option");
                                          option.value = subject.id;
                                          option.textContent = subject.name;
                                          subjectSelect.appendChild(option);
                                    });
                              } else {
                                    let noDataOption = document.createElement("option");
                                    noDataOption.disabled = true;
                                    noDataOption.textContent =
                                          "Aucune matière disponible pour cette classe";
                                    subjectSelect.appendChild(noDataOption);
                              }
                        })
                        .catch(error => {
                              console.error("Erreur javascript :", error);
                        });
            });
      });
</script>
