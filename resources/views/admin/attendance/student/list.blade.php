@extends('layouts.app')
@section('content')
      <div class="container mx-auto px-4 py-5">
            @include('message')
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-5 gap-4">
                  <div>
                        <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                              <i class="fa-solid  fa-user-check text-primary-600"></i>
                              Liste pour définir la présence des apprenants
                        </h1>
                        <p class="text-gray-600 dark:text-gray-300 mt-1">Gérez la liste de présence des
                              apprenants de votre
                              plateforme</p>
                  </div>

                  <nav class="flex items-center text-sm">
                        <ol class="flex items-center space-x-2">
                              <li class="flex items-center">
                                    <a href="{{ url('admin/dashboard') }}"
                                          class="text-primary-600 hover:text-violet-600 dark:hover:text-gray-200 transition-colors">
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
                                          Présence
                                    </span>
                              </li>
                        </ol>
                  </nav>
            </div>

            <div class="text-red-600 dark:text-red-400 text-sm font-semibold my-3">Veuillez choisir une classe et une date de
                  présence pour définir le type de présence pour les apprenants </div>

            <!-- Filter Section -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-5">
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
                                                      une classe pour définir une présence
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

                              <div class="w-full">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                          Date de présence <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="fas fa-calendar-plus text-gray-400"></i>
                                          </div>
                                          <input type="date" id="attendance_date" name="attendance_date" required
                                                value="{{ Request::get('attendance_date') }}"
                                                class="pl-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-violet-600 focus:border-violet-600 p-2.5">
                                    </div>
                              </div>



                              <!-- Action Buttons -->
                              <div class="flex items-end gap-2 w-full">
                                    <button type="submit"
                                          class="w-full bg-violet-600 hover:bg-violet-700 text-white font-medium rounded-lg px-4 py-2.5 flex items-center justify-center gap-2 transition-colors">
                                          <i class="fas fa-search"></i>
                                          Rechercher
                                    </button>
                                    <a href="{{ url('admin/attendance/students/list') }}"
                                          class="w-full bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700 text-gray-800 dark:text-white font-medium rounded-lg px-4 py-2.5 flex items-center justify-center gap-2 transition-colors">
                                          <i class="fas fa-sync-alt"></i>
                                          Réinitialiser
                                    </a>
                              </div>
                        </div>
                  </form>
            </div>

            @if (!empty(Request::get('class_id')) && !empty(Request::get('attendance_date')))
                  <div
                        class="bg-white rounded-lg dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                        <!-- Table -->
                        <div class="relative overflow rounded-lg z-10">
                              <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="rounded-lg bg-violet-600 dark:bg-gray-700">
                                          <tr>
                                                <th scope="col"
                                                      class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                                      Nom</th>
                                                <th scope="col"
                                                      class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                                      Prénoms</th>
                                                <th scope="col"
                                                      class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                                      Statut</th>
                                          </tr>
                                    </thead>
                                    <tbody
                                          class="z-20 bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                          @if (!empty($getStudent) && $getStudent->count() > 0)
                                                @foreach ($getStudent as $student)
                                                      @php
                                                            $getAttendance = \App\Models\User::getAttendance(
                                                                $student->id,
                                                                Request::get('class_id'),
                                                                Request::get('attendance_date'),
                                                            );
                                                            $attendance_type = $getAttendance->attendance_type ?? '';
                                                      @endphp
                                                      <tr
                                                            class="hover:bg-violet-100 dark:hover:bg-gray-700 transition-colors w-full">
                                                            <td
                                                                  class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 w-1/4">
                                                                  {{ $student->name }}
                                                            </td>
                                                            <td
                                                                  class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 w-1/4">
                                                                  {{ $student->last_name }}
                                                            </td>
                                                            <td
                                                                  class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 w-1/2">
                                                                  <div class="flex items-center space-x-4">
                                                                        <label class="inline-flex items-center w-1/4">
                                                                              <input type="radio" id="{{ $student->id }}"
                                                                                    name="attendance-{{ $student->id }}"
                                                                                    value="1"
                                                                                    class="saveAttendance form-radio text-green-500 h-4 w-4"
                                                                                    {{ $attendance_type == 1 ? 'checked' : '' }}>
                                                                              <span class="ml-2">Présent(e)</span>
                                                                        </label>
                                                                        <label class="inline-flex items-center w-1/4">
                                                                              <input type="radio" id="{{ $student->id }}"
                                                                                    name="attendance-{{ $student->id }}"
                                                                                    value="2"
                                                                                    class="saveAttendance form-radio text-red-500 h-4 w-4"
                                                                                    {{ $attendance_type == 2 ? 'checked' : '' }}>
                                                                              <span class="ml-2">Retard</span>
                                                                        </label>
                                                                        <label class="inline-flex items-center w-1/4">
                                                                              <input type="radio" id="{{ $student->id }}"
                                                                                    name="attendance-{{ $student->id }}"
                                                                                    value="3"
                                                                                    class="saveAttendance form-radio text-yellow-500 h-4 w-4"
                                                                                    {{ $attendance_type == 3 ? 'checked' : '' }}>
                                                                              <span class="ml-2">Absent(e)</span>
                                                                        </label>
                                                                        <label class="inline-flex items-center w-1/4">
                                                                              <input type="radio" id="{{ $student->id }}"
                                                                                    name="attendance-{{ $student->id }}"
                                                                                    value="4"
                                                                                    class="saveAttendance form-radio text-blue-500 h-4 w-4"
                                                                                    {{ $attendance_type == 4 ? 'checked' : '' }}>
                                                                              <span class="ml-2">Demi-journée</span>
                                                                        </label>
                                                                  </div>
                                                            </td>
                                                      </tr>
                                                @endforeach
                                          @else
                                                <tr class="text-center text-gray-700 dark:text-bodydark1">
                                                      <td colspan="100%" class="px-6 py-3"> Aucun apprenant n'appartient à
                                                            cette classe.</td>
                                                </tr>
                                          @endif
                                    </tbody>
                              </table>
                        </div>
                  </div>
            @else
                  <p class="text-center dark:text-gray-400 font-medium py-3 text-sm">Aucun résultat disponible</p>
            @endif
      </div>
@endsection

<script type="text/javascript">
      document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.saveAttendance').forEach(function(input) {
                  input.addEventListener('change', function() {
                        const student_id = input.getAttribute('id');
                        const attendance_type = input.value;
                        const attendance_date = document.querySelector(
                              '#attendance_date').value;
                        const class_id = document.querySelector('#class_id').value;
                        const _token = "{{ csrf_token() }}";

                        let formData = new FormData();
                        formData.append('_token', _token);
                        formData.append('student_id', student_id);
                        formData.append('attendance_type', attendance_type);
                        formData.append('attendance_date', attendance_date);
                        formData.append('class_id', class_id);

                        fetch("{{ url('admin/attendance/student/save') }}", {
                                    method: "POST",
                                    headers: {
                                          "Accept": "application/json"
                                    }, // Ajout pour éviter les erreurs de format JSON
                                    body: formData,
                              })
                              .then(response => response.json())
                              .then(data => {
                                    if (data.success !== undefined && data
                                          .message !== undefined) {
                                          displayMessage(data.success, data
                                                .message);
                                    } else {
                                          displayMessage(false,
                                                "Réponse inattendue du serveur."
                                          );
                                    }
                              })
                              .catch(error => {
                                    displayMessage(false,
                                          "Une erreur est survenue lors de l'enregistrement."
                                    );
                                    console.error(
                                          "Erreur lors de la requête :",
                                          error);
                              });
                  });
            });

            function displayMessage(success, message) {
                  let messageBox = document.createElement('div');
                  messageBox.className = 'message-box';
                  messageBox.innerHTML = message;

                  if (success) {
                        messageBox.classList.add('message-success');
                        messageBox.style.backgroundColor = '#BCF0DA'; // Feedback visuel en vert
                        messageBox.style.color = '#03543F';
                  } else {
                        messageBox.classList.add('message-error');
                        messageBox.style.backgroundColor = '#FBD5D5'; // Feedback visuel en rouge
                        messageBox.style.color = '#9B1C1C';
                  }

                  messageBox.style.position = 'fixed';
                  messageBox.style.top = '20px';
                  messageBox.style.right = '20px';
                  messageBox.style.padding = '10px';
                  messageBox.style.borderRadius = '5px';
                  messageBox.style.fontWeight = 'medium';
                  document.body.appendChild(messageBox);

                  setTimeout(function() {
                        messageBox.remove();
                  }, 2000);
            }
      });
</script>
