@extends('layouts.app')
@section('content')
      <div class="container mx-auto px-4 py-5">
            @include('message')
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-5 gap-4">
                  <div>
                        <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                              <i class="fa-solid fa-user-check text-primary-600"></i>
                              Liste des rapports de présence
                        </h1>
                        <p class="text-gray-600 dark:text-gray-300 mt-1">Gérez la liste des rapports de présence de votre
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
                                    <a href="{{ url('admin/attendance/report') }}"
                                          class="text-primary-600 hover:text-violet-600 transition-colors">
                                          <i class="fas fa-plus-circle mr-1"></i>
                                          Rapports
                                    </a>
                              </li>
                        </ol>
                  </nav>
            </div>

            <!-- Filter Section -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-5">
                  <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-filter text-primary-600"></i>
                        Filtres de recherche
                  </h2>

                  <form>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                              <!--  student_name Input -->
                              <div>
                                    <label for="student_name"
                                          class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nom de
                                          l'apprenant</label>
                                    <div class="relative">
                                          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="fas  fa-user-graduate text-gray-400"></i>
                                          </div>
                                          <input type="text" id="student_name" name="student_name"
                                                value="{{ Request::get('student_name') }}"
                                                placeholder="Entrez un nom d'apprenant..."
                                                class="pl-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-600 focus:border-primary-600 p-2.5">
                                    </div>
                              </div>

                              <div>
                                    <label for="student_last_name"
                                          class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Prénom de
                                          l'apprenant</label>
                                    <div class="relative">
                                          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="fas  fa-user-graduate text-gray-400"></i>
                                          </div>
                                          <input type="text" id="student_last_name" name="student_last_name"
                                                value="{{ Request::get('student_last_name') }}"
                                                placeholder="Entrez un nom d'apprenant..."
                                                class="pl-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-600 focus:border-primary-600 p-2.5">
                                    </div>
                              </div>

                              <div class="w-full">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                          Classe
                                    </label>
                                    <div class="relative">
                                          <select id="class_id" name="class_id"
                                                class="custom-select w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500 transition-all duration-200">
                                                <option selected disabled value="">Veuillez choisir
                                                      une classe</option>
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

                              <div>
                                    <label for="start_attendance_date"
                                          class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Début de la
                                          date de présence</label>
                                    <div class="relative">
                                          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="fas fa-calendar-plus text-gray-400"></i>
                                          </div>
                                          <input type="date" id="start_attendance_date" name="start_attendance_date"
                                                value="{{ Request::get('start_attendance_date') }}"
                                                placeholder="Entrez un début de date de présence..."
                                                class="pl-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-600 focus:border-primary-600 p-2.5">
                                    </div>
                              </div>

                              <div>
                                    <label for="end_attendance_date"
                                          class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fin de la
                                          date de présence</label>
                                    <div class="relative">
                                          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="fas fa-calendar-plus text-gray-400"></i>
                                          </div>
                                          <input type="date" id="end_attendance_date" name="end_attendance_date"
                                                value="{{ Request::get('end_attendance_date') }}"
                                                placeholder="Entrez un une date de fin de présence..."
                                                class="pl-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-600 focus:border-primary-600 p-2.5">
                                    </div>
                              </div>

                              <div class="w-full">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                          Statut
                                    </label>
                                    <div class="relative">
                                          <select id="attendance_type" name="attendance_type"
                                                class="custom-select w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500 transition-all duration-200">
                                                <option selected disabled value="">Veuillez choisir
                                                      un statut de présence</option>
                                                <option value="1" class="text-body"
                                                      {{ Request::get('attendance_type') == 1 ? 'selected' : '' }}>
                                                      Présent(e)</option>
                                                <option value="2" class="text-body"
                                                      {{ Request::get('attendance_type') == 2 ? 'selected' : '' }}>Retard
                                                </option>
                                                <option value="3" class="text-body"
                                                      {{ Request::get('attendance_type') == 3 ? 'selected' : '' }}>
                                                      Absent(e)</option>
                                                <option value="4" class="text-body"
                                                      {{ Request::get('attendance_type') == 4 ? 'selected' : '' }}>
                                                      Demi-journée</option>
                                          </select>
                                          <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                                <iconify-icon icon="mdi:chevron-down" class="text-gray-400" width="20"
                                                      height="20"></iconify-icon>
                                          </div>
                                    </div>
                              </div>


                              <!-- Date Created Input -->
                              <div>
                                    <label for="created_at"
                                          class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date
                                          de création</label>
                                    <div class="relative">
                                          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="fas fa-calendar-plus text-gray-400"></i>
                                          </div>
                                          <input type="date" id="created_at" name="created_at"
                                                value="{{ Request::get('created_at') }}"
                                                class="pl-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-600 focus:border-primary-600 p-2.5">
                                    </div>
                              </div>

                              <!-- Date Updated Input -->
                              <div>
                                    <label for="updated_at"
                                          class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date
                                          de modification</label>
                                    <div class="relative">
                                          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="fas fa-calendar-check text-gray-400"></i>
                                          </div>
                                          <input type="date" id="updated_at" name="updated_at"
                                                value="{{ Request::get('updated_at') }}"
                                                class="pl-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-600 focus:border-primary-600 p-2.5">
                                    </div>
                              </div>

                              <!-- Action Buttons -->
                              <div class="flex items-end gap-2">
                                    <button type="submit"
                                          class="w-full bg-violet-600 hover:bg-violet-700 text-white font-medium rounded-lg px-4 py-2.5 flex items-center justify-center gap-2 transition-colors">
                                          <i class="fas fa-search"></i>
                                          Rechercher
                                    </button>
                                    <a href="{{ url('admin/attendance/report') }}"
                                          class="w-full bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700 text-gray-800 dark:text-white font-medium rounded-lg px-4 py-2.5 flex items-center justify-center gap-2 transition-colors">
                                          <i class="fas fa-sync-alt"></i>
                                          Réinitialiser
                                    </a>
                              </div>
                        </div>
                  </form>
            </div>

            <div class="my-5">
                  {{ $getStudentAttendance->links('vendor.pagination.tailwind') }}
            </div>

            <!-- Results Section -->
            <div
                  class="bg-white rounded-lg dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                  <!-- Table -->
                  <div class="relative overflow rounded-lg z-10">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                              <thead class="rounded-lg bg-violet-600 dark:bg-gray-700">
                                    <tr>
                                          <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                                Nom
                                          </th>
                                          <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                                Prénoms
                                          </th>
                                          <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                                Classe
                                          </th>
                                          <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                                Statut
                                          </th>
                                          <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                                Date de présence
                                          </th>
                                          <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                                Créé par
                                          </th>
                                          <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                                Date de création
                                          </th>
                                    </tr>
                              </thead>
                              <tbody class="z-20 bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @if (!empty($getStudentAttendance) && $getStudentAttendance->count() > 0)
                                          @foreach ($getStudentAttendance as $studentAttendance)
                                                <tr
                                                      class="hover:bg-violet-100 dark:hover:bg-gray-700 transition-colors w-full">
                                                      <td scope="row"
                                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                            {{ $studentAttendance->student_name }}
                                                      </td>
                                                      <td
                                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                            {{ $studentAttendance->student_last_name }}
                                                      </td>
                                                      <td class="px-6 py-4">
                                                            {{ $studentAttendance->class_name }}
                                                      </td>
                                                      <td
                                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                            <span
                                                                  class="
                              {{ $studentAttendance->attendance_type == 1
                                  ? 'bg-emerald-200 text-emerald-800 rounded-full px-2 py-1 border border-emerald-800 w-32 block flex justify-center'
                                  : ($studentAttendance->attendance_type == 2
                                      ? 'bg-yellow-200 text-yellow-800 rounded-full px-2 py-1 border border-yellow-800 w-32 block flex justify-center'
                                      : ($studentAttendance->attendance_type == 3
                                          ? 'bg-red-200 text-red-800 rounded-full px-2 py-1 border border-red-800 w-32 block flex justify-center'
                                          : ($studentAttendance->attendance_type == 4
                                              ? 'bg-violet-200 text-violet-800 rounded-full px-2 py-1 border border-violet-800 w-32 block flex justify-center'
                                              : ''))) }}">
                                                                  {{ $studentAttendance->attendance_type == 1
                                                                      ? 'Présent(e)'
                                                                      : ($studentAttendance->attendance_type == 2
                                                                          ? 'Retard'
                                                                          : ($studentAttendance->attendance_type == 3
                                                                              ? 'Absent(e)'
                                                                              : ($studentAttendance->attendance_type == 4
                                                                                  ? 'Demi-journée'
                                                                                  : 'Non défini'))) }}
                                                            </span>
                                                      </td>
                                                      <td
                                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                            {{ \Carbon\Carbon::parse($studentAttendance->attendance_date)->locale('fr')->translatedFormat('d M Y') }}
                                                      </td>
                                                      <td
                                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                            {{ $studentAttendance->created_by_name }}
                                                      </td>
                                                      <td
                                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                            {{ \Carbon\Carbon::parse($studentAttendance->created_at)->locale('fr')->translatedFormat('d M Y H:i:s') }}
                                                      </td>
                                                </tr>
                                          @endforeach
                                    @else
                                          <tr class="text-center text-gray-700 dark:text-bodydark1">
                                                <td colspan="100%" class="px-6 py-3"> Aucun résultat disponible</td>
                                          </tr>
                                    @endif

                              </tbody>
                        </table>
                  </div>

                  <!-- Table Footer -->
                  <div
                        class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row justify-between items-center gap-4">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                              Total de <span class="font-medium">{{ $getStudentAttendance->total() }}</span> présence<span
                                    class="">{{ $getStudentAttendance->total() > 1 ? 's' : '' }}</span> affichée<span
                                    class="">{{ $getStudentAttendance->total() > 1 ? 's' : '' }}</span>
                        </div>

                        <!-- Pagination -->
                        <nav class="flex items-center gap-5">
                              {{ $getStudentAttendance->links('vendor.pagination.tailwind') }}
                        </nav>
                  </div>
            </div>
      </div>
@endsection

<script></script>
