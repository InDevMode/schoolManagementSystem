@extends('layouts.app')
@section('content')
      <div class="m-2">
            @include('message')
            <div class="container mx-auto px-4 py-8 max-w-6xl">
                  <!-- Header Section -->
                  <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
                        <div class="mb-4 md:mb-0">
                              <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center">
                                    <span class="text-emerald-600 mr-2">
                                          <i class="fa-solid fa-square-poll-horizontal text-primary-600" width="28"
                                                height="28"></i>
                                    </span>
                                    Modifier cette note
                              </h1>
                              <p class="text-gray-600 dark:text-gray-300 mt-1">Remplissez les détails pour modifier une
                                    note
                              </p>
                        </div>

                        <nav class="flex" aria-label="Breadcrumb">
                              <ol class="inline-flex items-center space-x-1 md:space-x-3">
                                    <li class="inline-flex items-center">
                                          <a href="{{ url('admin/dashboard') }}"
                                                class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-emerald-600 dark:text-gray-400 dark:hover:text-white">
                                                <iconify-icon icon="mdi:home" class="mr-2" width="16"
                                                      height="16"></iconify-icon>
                                                Tableau de bord
                                          </a>
                                    </li>
                                    <li>
                                          <div class="flex items-center">
                                                <iconify-icon icon="mdi:chevron-right" class="text-gray-400" width="16"
                                                      height="16"></iconify-icon>
                                                <a href="{{ url('admin/examinations/marks_grade/list') }}"
                                                      class="ml-1 text-sm font-medium text-gray-700 hover:text-emerald-600 md:ml-2 dark:text-gray-400 dark:hover:text-white">Liste
                                                      des notes</a>
                                          </div>
                                    </li>
                                    <li aria-current="page">
                                          <div class="flex items-center">
                                                <iconify-icon icon="mdi:chevron-right" class="text-gray-400" width="16"
                                                      height="16"></iconify-icon>
                                                <span
                                                      class="ml-1 text-sm font-medium text-emerald-600 md:ml-2 dark:text-emerald-400">Modifier</span>
                                          </div>
                                    </li>
                              </ol>
                        </nav>
                  </div>

                  <!-- Main Form Section -->
                  <div class="bg-white rounded-xl shadow-md overflow-hidden dark:bg-gray-800 transition-colors duration-300">
                        <div class="p-6 md:p-8">
                              <form action="" method="post" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <div class="p-6.5">
                                          <div class="mb-4.5">
                                                <div>
                                                      <label
                                                            class="mb-3 block text-sm font-medium text-black dark:text-white">
                                                            Nom
                                                      </label>
                                                      <input type="text" id="name" name="name"
                                                            value="{{ old('name', $getMarksGrade->name) }}"
                                                            placeholder="Entrez un nom" required
                                                            class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-50 px-5 py-2.5 font-normal text-black outline-none transition focus:border-emerald-400 active:border-emerald-400 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-emerald-400" />
                                                </div>
                                          </div>
                                          <div class="mb-4.5">
                                                <div>
                                                      <label
                                                            class="mb-3 block text-sm font-medium text-black dark:text-white">
                                                            Pourcentage de
                                                      </label>
                                                      <input type="number" id="percent_from" name="percent_from"
                                                            value="{{ old('percent_from', $getMarksGrade->percent_from) }}"
                                                            placeholder="Entrez un pourcentage" required
                                                            class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-50 px-5 py-2.5 font-normal text-black outline-none transition focus:border-emerald-400 active:border-emerald-400 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-emerald-400" />
                                                </div>
                                          </div>
                                          <div class="mb-4.5">
                                                <div>
                                                      <label
                                                            class="mb-3 block text-sm font-medium text-black dark:text-white">
                                                            Pourcentage à
                                                      </label>
                                                      <input type="number" id="percent_to" name="percent_to"
                                                            value="{{ old('percent_to', $getMarksGrade->percent_to) }}"
                                                            placeholder="Entrez un pourcentage" required
                                                            class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-50 px-5 py-2.5 font-normal text-black outline-none transition focus:border-emerald-400 active:border-emerald-400 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-emerald-400" />
                                                </div>
                                          </div>

                                          <div class="mt-8">
                                                <button type="submit" id="submit-button"
                                                      class="w-full flex justify-center items-center py-3 px-4 bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-700 hover:to-emerald-600 text-white font-medium rounded-lg shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-opacity-50 transition-all duration-300">
                                                      <iconify-icon icon="mdi:content-save-check-outline" class="mr-2"
                                                            width="20" height="20"></iconify-icon>
                                                      Modifier cette note
                                                </button>
                                          </div>
                                    </div>
                              </form>
                        </div>
                  </div>
            </div>
      </div>
      </main>
      </div>
@endsection
<script></script>
