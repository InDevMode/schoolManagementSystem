@extends('layouts.app')
@section('content')
      <div class="container mx-auto px-4 py-5">
            @include('message')
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-5 gap-4">
                  <div>
                        <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                              <i class="fa-solid fa-user-graduate text-primary-600"></i>
                              Liste des apprenants
                        </h1>
                        <p class="text-gray-600 dark:text-gray-300 mt-1">Gérez les comptes apprenants de votre plateforme</p>
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
                                    <a href="{{ url('admin/student/add') }}"
                                          class="text-primary-600 hover:text-violet-600 transition-colors">
                                          <i class="fas fa-plus-circle mr-1"></i>
                                          Créer un apprenant
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
                              <!-- Number admission Input -->
                              <div>
                                    <label for="admission_number"
                                          class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Numéro
                                          d'admission</label>
                                    <div class="relative">
                                          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="fas fa-hashtag text-gray-400"></i>
                                          </div>
                                          <input type="text" id="admission_number" name="admission_number"
                                                value="{{ Request::get('admission_number') }}"
                                                placeholder="Entrez un numéro d'admission..."
                                                class="pl-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-600 focus:border-primary-600 p-2.5">
                                    </div>
                              </div>

                              <!-- Name Input -->
                              <div>
                                    <label for="name"
                                          class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nom</label>
                                    <div class="relative">
                                          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="fas fa-user text-gray-400"></i>
                                          </div>
                                          <input type="text" id="name" name="name"
                                                value="{{ Request::get('name') }}" placeholder="Entrez un nom..."
                                                class="pl-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-600 focus:border-primary-600 p-2.5">
                                    </div>
                              </div>

                              <!-- Last Name Input -->
                              <div>
                                    <label for="last_name"
                                          class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Prénoms</label>
                                    <div class="relative">
                                          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="fas fa-user-tag text-gray-400"></i>
                                          </div>
                                          <input type="text" id="last_name" name="last_name"
                                                value="{{ Request::get('last_name') }}" placeholder="Entrez un prénom..."
                                                class="pl-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-600 focus:border-primary-600 p-2.5">
                                    </div>
                              </div>

                              <!-- Email Input -->
                              <div>
                                    <label for="email"
                                          class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                                    <div class="relative">
                                          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="fas fa-envelope text-gray-400"></i>
                                          </div>
                                          <input type="email" id="email" name="email"
                                                value="{{ Request::get('email') }}" placeholder="Entrez un email..."
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

                              <!-- Gender Select -->
                              <div>
                                    <label for="gender"
                                          class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Genre</label>
                                    <div class="relative">
                                          <select id="gender" name="gender"
                                                class="appearance-none w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-600 focus:border-primary-600 p-2.5 pr-8">
                                                <option value="" disabled selected>Tous les genres</option>
                                                <option value="male"
                                                      {{ Request::get('gender') == 'male' ? 'selected' : '' }}>Masculin
                                                </option>
                                                <option value="female"
                                                      {{ Request::get('gender') == 'female' ? 'selected' : '' }}>Féminin
                                                </option>
                                                <option value="other"
                                                      {{ Request::get('gender') == 'other' ? 'selected' : '' }}>Autre
                                                </option>
                                          </select>
                                          <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                                <i class="fas fa-chevron-down text-gray-400"></i>
                                          </div>
                                    </div>
                              </div>

                              <!-- Blood Group Select -->
                              <div>
                                    <label for="blood_group"
                                          class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Genre</label>
                                    <div class="relative">
                                          <select id="blood_group" name="blood_group"
                                                class="appearance-none w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-600 focus:border-primary-600 p-2.5 pr-8">
                                                <option value="" disabled selected>Tous les groupes sanguins</option>
                                                <option value="a+"
                                                      {{ Request::get('blood_group') == 'a+' ? 'selected' : '' }}>A+</option>
                                                <option value="a-"
                                                      {{ Request::get('blood_group') == 'a-' ? 'selected' : '' }}>A-</option>
                                                <option value="b+"
                                                      {{ Request::get('blood_group') == 'b+' ? 'selected' : '' }}>B+</option>
                                                <option value="b-"
                                                      {{ Request::get('blood_group') == 'b-' ? 'selected' : '' }}>B-</option>
                                                <option value="ab+"
                                                      {{ Request::get('blood_group') == 'ab+' ? 'selected' : '' }}>AB+
                                                </option>
                                                <option value="ab-"
                                                      {{ Request::get('blood_group') == 'ab-' ? 'selected' : '' }}>AB-
                                                </option>
                                                <option value="o+"
                                                      {{ Request::get('blood_group') == 'o+' ? 'selected' : '' }}>O+</option>
                                                <option value="o-"
                                                      {{ Request::get('blood_group') == 'o-' ? 'selected' : '' }}>O-</option>
                                                v
                                          </select>
                                          <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                                <i class="fas fa-chevron-down text-gray-400"></i>
                                          </div>
                                    </div>
                              </div>

                              <!-- Mobile Number Input -->
                              <div>
                                    <label for="mobile_number"
                                          class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Téléphone</label>
                                    <div class="relative">
                                          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="fas fa-phone text-gray-400"></i>
                                          </div>
                                          <input type="text" id="mobile_number" name="mobile_number"
                                                value="{{ Request::get('mobile_number') }}"
                                                placeholder="Entrez un téléphone..."
                                                class="pl-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-600 focus:border-primary-600 p-2.5">
                                    </div>
                              </div>

                              <!--Admission date Input -->
                              <div>
                                    <label for="admission_date"
                                          class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date
                                          d'Admission</label>
                                    <div class="relative">
                                          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="fas fa-calendar-plus text-gray-400"></i>
                                          </div>
                                          <input type="date" id="admission_date" name="admission_date"
                                                value="{{ Request::get('admission_date') }}"
                                                class="pl-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-600 focus:border-primary-600 p-2.5">
                                    </div>
                              </div>

                              <!-- Date of birthday Input -->
                              <div>
                                    <label for="date_of_birth"
                                          class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date
                                          de Naissance</label>
                                    <div class="relative">
                                          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="fas fa-calendar-plus text-gray-400"></i>
                                          </div>
                                          <input type="date" id="date_of_birth" name="date_of_birth"
                                                value="{{ Request::get('date_of_birth') }}"
                                                class="pl-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-600 focus:border-primary-600 p-2.5">
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
                                    <a href="{{ url('admin/student/list') }}"
                                          class="w-full bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700 text-gray-800 dark:text-white font-medium rounded-lg px-4 py-2.5 flex items-center justify-center gap-2 transition-colors">
                                          <i class="fas fa-sync-alt"></i>
                                          Réinitialiser
                                    </a>
                              </div>
                        </div>
                  </form>
            </div>

            <div class="my-5">
                  {{ $getStudent->links('vendor.pagination.tailwind') }}
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
                                                Numéro d'admission
                                          </th>
                                          <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                                Nom & Prénoms
                                          </th>
                                          <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                                Parents
                                          </th>
                                          <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                                Email
                                          </th>
                                          <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                                Statut
                                          </th>
                                          <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                                Téléphone
                                          </th>
                                          <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                                Genre
                                          </th>
                                          <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                                Classe
                                          </th>
                                          <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                                Date de Naissance
                                          </th>
                                          <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                                Créé le
                                          </th>
                                          <th scope="col"
                                                class="px-6 py-3 text-right text-xs font-medium text-white dark:text-gray-300 uppercase tracking-wider">
                                                Actions
                                          </th>
                                    </tr>
                              </thead>
                              <tbody class="z-20 bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <!-- Sample Row 1 -->
                                    @foreach ($getStudent as $index => $student)
                                          <tr class="hover:bg-violet-100 dark:hover:bg-gray-700 transition-colors">
                                                <td
                                                      class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                      {{ $student->admission_number }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                      <div class="flex items-center">
                                                            <div class="">
                                                                  <span
                                                                        class="text-sm font-medium text-gray-900 dark:text-white">{{ $student->name }}</span>
                                                                  <span
                                                                        class="text-sm text-gray-500 dark:text-gray-400">{{ $student->last_name }}</span>
                                                            </div>
                                                      </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                      <div class="flex items-center">
                                                            <div class="">
                                                                  <span
                                                                        class="text-sm font-medium text-gray-900 dark:text-white">{{ $student->parent_name }}</span>
                                                                  <span
                                                                        class="text-sm text-gray-500 dark:text-gray-400">{{ $student->parent_last_name }}</span>
                                                            </div>
                                                      </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                      <div class="text-sm text-gray-900 dark:text-white">
                                                            {{ $student->email }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                      <span
                                                            class="px-2 py-1 border w-24 inline-flex justify-center text-xs leading-5 font-semibold rounded-full {{ $student->status == 1 ? 'bg-green-100 border-green-800 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 border-red-800 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                                            {{ $student->status == 1 ? 'Actif' : 'Inactif' }}
                                                      </span>
                                                </td>
                                                <td
                                                      class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                      {{ $student->mobile_number }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                      <span
                                                            class="px-2 py-1 border w-24 inline-flex justify-center text-xs leading-5 font-semibold rounded-full {{ $student->gender == 'male' ? 'bg-violet-100 border-violet-800 text-violet-800 dark:bg-violet-900 dark:text-violet-200' : ($student->gender == 'female' ? 'bg-red-100 border-red-800 text-red-800 dark:bg-red-900 dark:text-red-200' : 'bg-pink-100 border-pink-800 text-pink-800 dark:bg-pink-900 dark:text-pink-200') }}">
                                                            {{ $student->gender == 'male' ? 'Masculin' : ($student->gender == 'female' ? 'Féminin' : 'Autre') }}
                                                      </span>
                                                </td>
                                                <td
                                                      class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                      {{ $student->class_name }}
                                                </td>
                                                <td
                                                      class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                      {{ \Carbon\Carbon::parse($student->date_of_birth)->locale('fr')->translatedFormat('d M Y') }}
                                                </td>
                                                <td
                                                      class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                      {{ \Carbon\Carbon::parse($student->created_at)->locale('fr')->translatedFormat('d M Y H:i:s') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                      <div class="relative inline-block text-left" x-data="{ open: false }">
                                                            <div>
                                                                  <button type="button"
                                                                        class="group inline-flex w-full justify-center gap-x-1.5 rounded-lg shadow-md bg-white dark:bg-gray-800 border dark:border-gray-600 dark:hover:text-violet-600 px-3 py-2 text-sm font-semibold text-gray-700 hover:text-violet-600 dark:text-gray-200 hover:bg-gray-100"
                                                                        @click="open = !open" id="menu-button"
                                                                        aria-expanded="true" aria-haspopup="true">
                                                                        Actions
                                                                        <span
                                                                              class="-mr-1 size-5 group-hover:text-violet-600 text-gray-400"><iconify-icon
                                                                                    icon="mdi:chevron-down" width="22"
                                                                                    height="22"></iconify-icon></span>
                                                                  </button>
                                                            </div>
                                                            <div class="absolute right-0 z-50 mt-2 w-56 origin-top-right rounded-md bg-white dark:bg-gray-800 ring-1 shadow-lg ring-black/5 focus:outline-hidden"
                                                                  role="menu" aria-orientation="vertical"
                                                                  aria-labelledby="menu-button"
                                                                  tabindex="{{ $index + 1 }}" x-show="open"
                                                                  @click.away="open = false" x-transition>
                                                                  <div class="py-1">
                                                                        <a href="{{ url('admin/student/edit', $student->id) }}"
                                                                              class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:text-emerald-400 dark:hover:text-emerald-400"
                                                                              role="menuitem"><i
                                                                                    class="fas fa-edit mr-2"></i>Modifier</a>
                                                                        <a href="{{ url('chat?receiver_id=' . base64_encode($admin->id)) }}"
                                                                              class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:text-indigo-400 dark:hover:text-indigo-400"
                                                                              role="menuitem"><i
                                                                                    class="fas fa-comment mr-2"></i>Message</a>
                                                                        <div x-data="{ showConfirm: false }">
                                                                              <!-- Bouton initial -->
                                                                              <button type="button"
                                                                                    @click="showConfirm = true"
                                                                                    class="block w-full px-4 py-2 text-left text-sm text-gray-700 dark:text-gray-200 hover:text-red-400 dark:hover:text-red-400">
                                                                                    <i class="fas fa-trash mr-2"></i>
                                                                                    Supprimer
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
                                                                                                            Supprimer un
                                                                                                            apprenant
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
                                                                                                                  l'apprenant
                                                                                                                  du nom de
                                                                                                            </p>
                                                                                                            <p
                                                                                                                  class="font-bold">
                                                                                                                  {{ $student->name }}
                                                                                                                  {{ $student->last_name }}
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
                                                                                                      <a href="{{ url('admin/student/delete', $student->id) }}"
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
                                    @if ($getStudent->isEmpty())
                                          <tr class="text-center text-gray-700 dark:text-bodydark1">
                                                <td colspan="10" class="py-3"> Aucun apprenant trouvé.</td>
                                          </tr>
                                    @endif
                              </tbody>
                        </table>
                  </div>

                  <!-- Table Footer -->
                  <div
                        class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row justify-between items-center gap-4">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                              Total de <span class="font-medium">{{ $getStudent->total() }}</span> apprenant<span
                                    class="">{{ $getStudent->total() > 1 ? 's' : '' }}</span> affiché<span
                                    class="">{{ $getStudent->total() > 1 ? 's' : '' }}</span>
                        </div>

                        <!-- Pagination -->
                        <nav class="flex items-center gap-5">
                              {{ $getStudent->links('vendor.pagination.tailwind') }}
                        </nav>
                  </div>
            </div>
      </div>
@endsection

<script></script>
