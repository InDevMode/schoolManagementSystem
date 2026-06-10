@extends('layouts.app')
@section('content')
      <div class="container mx-auto px-4 py-5">
            @include('message')
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-5 gap-4">
                  <div>
                        <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                              <i class="fa-solid fa-landmark text-primary-600"></i>
                              Liste des classes assignées à un professeur
                        </h1>
                        <p class="text-gray-600 dark:text-gray-300 mt-1">Gérez les assignations des classes à des professeurs
                              de votre plateforme</p>
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
                                    <a href="{{ url('admin/assign_class/add') }}"
                                          class="text-primary-600 hover:text-violet-600 transition-colors">
                                          <i class="fas fa-plus-circle mr-1"></i>
                                          Créer une assignation
                                    </a>
                              </li>
                        </ol>
                  </nav>
            </div>

            <!-- Filter Section -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-5">
                  <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-filter text-primary-600"></i>
                        Filtres de recherche
                  </h2>

                  <form>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

                              <!-- Classe Name Input -->
                              <div>
                                    <label for="class_name"
                                          class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nom de la
                                          classe</label>
                                    <div class="relative">
                                          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="fas fa-landmark text-gray-400"></i>
                                          </div>
                                          <input type="text" id="class_name" name="class_name"
                                                value="{{ Request::get('class_name') }}"
                                                placeholder="Entrez un nom de classe..."
                                                class="pl-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-600 focus:border-primary-600 p-2.5">
                                    </div>
                              </div>

                              <!-- Teacher Name Input -->
                              <div>
                                    <label for="teacher_name"
                                          class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nom du
                                          professeur</label>
                                    <div class="relative">
                                          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="fas fa-chalkboard-teacher text-gray-400"></i>
                                          </div>
                                          <input type="text" id="teacher_name" name="teacher_name"
                                                value="{{ Request::get('teacher_name') }}"
                                                placeholder="Entrez un nom de professeur..."
                                                class="pl-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-600 focus:border-primary-600 p-2.5">
                                    </div>
                              </div>

                              <!-- Status Select -->
                              <div>
                                    <label for="status"
                                          class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Statut</label>
                                    <div class="relative">
                                          <select id="status" name="status"
                                                class="appearance-none w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-600 focus:border-primary-600 p-2.5 pr-8">
                                                <option disabled selected>Tous les statuts</option>
                                                <option value="1" {{ Request::get('status') === '1' ? 'selected' : '' }}>
                                                      Actif</option>
                                                <option value="0" {{ Request::get('status') === '0' ? 'selected' : '' }}>
                                                      Inactif</option>
                                          </select>
                                          <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                                <i class="fas fa-chevron-down text-gray-400"></i>
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
                                    <a href="{{ url('admin/assign_class/list') }}"
                                          class="w-full bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700 text-gray-800 dark:text-white font-medium rounded-lg px-4 py-2.5 flex items-center justify-center gap-2 transition-colors">
                                          <i class="fas fa-sync-alt"></i>
                                          Réinitialiser
                                    </a>
                              </div>
                        </div>
                  </form>
            </div>

            <div class="my-5">
                  {{ $getClassTeacher->links('vendor.pagination.tailwind') }}
            </div>

            <!-- Results Section -->
            <div
                  class="bg-white rounded-lg dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                  <!-- Table -->
                  <div class="relative overflow rounded-lg z-10">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                              <thead class="rounded-lg bg-violet-600 dark:bg-gray-700">
                                    <tr>
                                          <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                                Classe
                                          </th>
                                          <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                                Professeur
                                          </th>
                                          <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                                Status
                                          </th>
                                          <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                                Crée par
                                          </th>
                                          <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                                Date de Création
                                          </th>
                                             <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                                Date de Modification
                                          </th>
                                          <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                                Actions
                                          </th>
                                    </tr>
                              </thead>
                              <tbody class="z-20 bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach ($getClassTeacher as $index => $classTeacher)
                                          <tr class="hover:bg-violet-100 dark:hover:bg-gray-700 transition-colors">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                      {{ $classTeacher->class_name }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                      {{ $classTeacher->teacher_name }}
                                                      {{ $classTeacher->teacher_last_name }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                      <span
                                                            class="px-2 py-1 border w-24 inline-flex justify-center text-xs leading-5 font-semibold rounded-full {{ $classTeacher->status == 1 ? 'bg-green-100 border-green-800 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 border-red-800 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                                            {{ $classTeacher->status == 1 ? 'Actif' : 'Inactif' }}
                                                      </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                      {{ $classTeacher->created_by_name }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                      {{ \Carbon\Carbon::parse($classTeacher->created_at)->locale('fr')->translatedFormat('d M Y H:i:s') }}
                                                </td>
                                                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                      {{ \Carbon\Carbon::parse($classTeacher->updated_at)->locale('fr')->translatedFormat('d M Y H:i:s') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                      <div class="relative inline-block text-left" x-data="{ open: false }">
                                                            <div>
                                                                  <button type="button"
                                                                        class="group inline-flex w-full justify-center gap-x-1.5 rounded-lg shadow-md bg-white dark:bg-gray-800 border dark:border-gray-600 dark:hover:text-violet-600 px-3 py-2 text-sm font-semibold text-gray-700 hover:text-violet-600 dark:text-gray-200 hover:bg-gray-100"
                                                                        @click="open = !open" id="menu-button"
                                                                        aria-expanded="true" aria-haspopup="true">
                                                                        Actions
                                                                        <svg class="-mr-1 size-5 group-hover:text-violet-600 text-gray-400"
                                                                              viewBox="0 0 20 20" fill="currentColor"
                                                                              aria-hidden="true">
                                                                              <path fill-rule="evenodd"
                                                                                    d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z"
                                                                                    clip-rule="evenodd" />
                                                                        </svg>
                                                                  </button>
                                                            </div>
                                                            <div class="absolute right-0 z-50 mt-2 w-72 origin-top-right rounded-lg bg-white dark:bg-gray-800 ring-1 shadow-lg ring-black/5 focus:outline-hidden"
                                                                  role="menu" aria-orientation="vertical"
                                                                  aria-labelledby="menu-button"
                                                                  tabindex="{{ $index + 1 }}" x-show="open"
                                                                  @click.away="open = false" x-transition>
                                                                  <div class="py-1">
                                                                        <a href="{{ url('admin/assign_class/edit_single', $classTeacher->id) }}"
                                                                              class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:text-emerald-400 dark:hover:text-emerald-400"
                                                                              role="menuitem"><i
                                                                                    class="fas fa-edit mr-2"></i>Modifier une
                                                                              assignation</a>
                                                                        <a href="{{ url('admin/assign_class/edit', $classTeacher->id) }}"
                                                                              class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:text-emerald-400 dark:hover:text-emerald-400"
                                                                              role="menuitem"><i
                                                                                    class="fas fa-edit mr-2"></i>Modifier
                                                                              plusieurs assignations</a>
                                                                        <div x-data="{ showConfirm: false }">
                                                                              <!-- Bouton initial -->
                                                                              <button type="button"
                                                                                    @click="showConfirm = true"
                                                                                    class="block w-full px-4 py-2 text-left text-sm text-gray-700 dark:text-gray-200 hover:text-red-400 dark:hover:text-red-400">
                                                                                    <i class="fas fa-trash mr-2"></i>
                                                                                    Supprimer une assignation
                                                                              </button>

                                                                              <!-- Modal de confirmation -->
                                                                              <template x-if="showConfirm">
                                                                                    <div
                                                                                          class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
                                                                                          <div
                                                                                                class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border-b dark:border-gray-400 w-[30%] h-auto">
                                                                                                <div
                                                                                                      class="flex items-center justify-between p-4 border-b dakr:border-gray-600 border-gray-200 rounded-t bg-violet-500 dark:bg-gray-700">
                                                                                                      <h3
                                                                                                            class="text-lg font-semibold text-white dark:text-white">
                                                                                                            Supprimer une
                                                                                                            assignation
                                                                                                      </h3>
                                                                                                      <button type="button"
                                                                                                            @click="showConfirm = false"
                                                                                                            class="text-white hover:text-gray-900 dark:hover:text-white rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center">
                                                                                                            <iconify-icon
                                                                                                                  icon="mdi:close"
                                                                                                                  width="20"
                                                                                                                  height="20"></iconify-icon>
                                                                                                      </button>
                                                                                                </div>

                                                                                                <!-- Message -->
                                                                                                <div class="p-4">
                                                                                                      <div
                                                                                                            class="text-center text-lg text-gray-800 dark:text-gray-200">
                                                                                                            <p> Êtes-vous sûr
                                                                                                                  de vouloir
                                                                                                                  supprimer
                                                                                                                  cette assignation:
                                                                                                            </p>
                                                                                                            <p
                                                                                                                  class="font-bold">
                                                                                                                  {{ $classTeacher->class_name }} et  {{ $classTeacher->teacher_name }}
                                                                                                                  ?
                                                                                                            </p>
                                                                                                      </div>
                                                                                                </div>

                                                                                                <!-- Pied du modal -->
                                                                                                <div
                                                                                                      class="flex justify-between px-4 py-3 rounded-b">
                                                                                                      <button
                                                                                                            @click="showConfirm = false"
                                                                                                            class="block px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-sm rounded hover:bg-gray-300 dark:hover:bg-gray-600">
                                                                                                            Annuler
                                                                                                      </button>
                                                                                                      <a href="{{ url('admin/assign_class/delete', $classTeacher->id) }}"
                                                                                                            class="block px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm">
                                                                                                            Oui supprimer
                                                                                                      </a>
                                                                                                </div>
                                                                                          </div>
                                                                                    </div>
                                                                              </template>
                                                                        </div>
                                                                  </div>
                                                            </div>
                                                      </div>
                                                </td>
                                          </tr>
                                    @endforeach
                                    @if ($getClassTeacher->isEmpty())
                                          <tr class="text-center text-gray-700 dark:text-bodydark1">
                                                <td colspan="100%" class="px-6 py-3"> Aucune assignation de classe trouvée.
                                                </td>
                                          </tr>
                                    @endif
                              </tbody>
                        </table>

                        <!-- Table Footer -->
                        <div
                              class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row justify-between items-center gap-4">
                              <div class="text-sm text-gray-500 dark:text-gray-400">
                                    Total de <span class="font-medium">{{ $getClassTeacher->total() }}</span> classe<span
                                          class="">{{ $getClassTeacher->total() > 1 ? 's' : '' }} assignée<span
                                                class="">{{ $getClassTeacher->total() > 1 ? 's' : '' }}</span>
                                          affiché<span class="">{{ $getClassTeacher->total() > 1 ? 's' : '' }}</span>
                              </div>

                              <!-- Pagination -->
                              <nav class="flex items-center gap-5">
                                    {{ $getClassTeacher->links('vendor.pagination.tailwind') }}
                              </nav>
                        </div>
                  </div>
            </div>
      @endsection

      <script></script>
