@extends('layouts.app')
@section('content')
      <div class="m-2">
            @include('message')
            <div class="container mx-auto px-4 py-8 max-w-6xl">
                  <!-- Header Section -->
                  <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
                        <div class="mb-4 md:mb-0">
                              <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center">
                                    <iconify-icon icon="fa-solid:landmark" class="text-emerald-600 mr-2" width="28"
                                          height="28"></iconify-icon>
                                    Modifer cette assignation
                              </h1>
                              <p class="text-gray-600 dark:text-gray-300 mt-1">Remplissez les détails pour modifier les
                                    informations d'une assignation
                              </p>
                        </div>

                        <nav class="flex" aria-label="Breadcrumb">
                              <ol class="inline-flex items-center space-x-1 md:space-x-3">
                                    <li class="inline-flex items-center">
                                          <a href="{{ url('admin/dashboard') }}"
                                                class="inline-flex items-center text-sm font-medium text-gray-700 dark:text-gray-400 dark:hover:text-white">
                                                <iconify-icon icon="mdi:home" class="mr-2" width="16"
                                                      height="16"></iconify-icon>
                                                Tableau de bord
                                          </a>
                                    </li>
                                    <li>
                                          <div class="flex items-center">
                                                <iconify-icon icon="mdi:chevron-right" class="text-gray-400" width="16"
                                                      height="16"></iconify-icon>
                                                <a href="{{ url('admin/assign_subject/list') }}"
                                                      class="ml-1 text-sm font-medium text-gray-700 md:ml-2 dark:text-gray-400 dark:hover:text-white">Liste
                                                      des matières assignées</a>
                                          </div>
                                    </li>
                                    <li aria-current="page">
                                          <div class="flex items-center">
                                                <iconify-icon icon="mdi:chevron-right" class="text-gray-400" width="16"
                                                      height="16"></iconify-icon>
                                                <span
                                                      class="ml-1 text-sm font-medium text-emerald-600 md:ml-2 dark:text-emerald-600">Modifier</span>
                                          </div>
                                    </li>
                              </ol>
                        </nav>
                  </div>

                  <div class="bg-white rounded-lg shadow-md overflow-hidden dark:bg-gray-800 transition-colors duration-300">
                        <div class="p-6 md:p-8">
                              <form action="" method="post" enctype="multipart/form-data">
                                    {{ csrf_field() }}

                                    <div class="mb-4.5">
                                          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Classe <span class="text-red-500">*</span>
                                          </label>
                                          <div class="relative">
                                                <select id="class_id" name="class_id" required
                                                      class="custom-select w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-emerald-500 dark:focus:border-emerald-500 transition-all duration-200">
                                                      <option selected disabled value="">Veuillez choisir
                                                            une classe pour cette assignation
                                                            @foreach ($getClass as $class)
                                                      <option {{ $getClassSubject->class_id == $class->id ? 'selected' : '' }}
                                                            value="{{ $class->id }}">{{ $class->name }}
                                                      </option>
                                                      @endforeach
                                                </select>
                                                <div
                                                      class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                                      <iconify-icon icon="mdi:chevron-down" class="text-gray-400"
                                                            width="20" height="20"></iconify-icon>
                                                </div>
                                          </div>
                                    </div>

                                    <div class="mb-4.5">
                                          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Matière <span class="text-red-500">*</span>
                                          </label>
                                          <div class="relative">
                                                <select id="subject_id" name="subject_id" required
                                                      class="custom-select w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-emerald-500 dark:focus:border-emerald-500 transition-all duration-200">
                                                      <option selected disabled value="">Veuillez choisir
                                                            une matirère pour cette assignation
                                                            @foreach ($getSubject as $subject)
                                                      <option
                                                            {{ $getClassSubject->subject_id == $subject->id ? 'selected' : '' }}
                                                            value="{{ $subject->id }}">{{ $subject->name }}
                                                            {{ $subject->last_name }}
                                                            @endforeach
                                                </select>
                                                <div
                                                      class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                                      <iconify-icon icon="mdi:chevron-down" class="text-gray-400"
                                                            width="20" height="20"></iconify-icon>
                                                </div>
                                          </div>
                                    </div>

                                    <div class="mb-4.5">
                                          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Coefficient <span class="text-red-500">*</span>
                                          </label>
                                          <div class="relative">
                                                <input type="number" id="coefficient" name="coefficient"
                                                      value="{{ old('coefficient', $getClassSubject->coefficient) }}" required
                                                      class="form-input w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-emerald-500 dark:focus:border-emerald-500 transition-all duration-200"
                                                      placeholder="Entrez le coefficient">
                                          </div>
                                    </div>

                                    <div class="mb-4.5">
                                          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Statut <span class="text-red-500">*</span>
                                          </label>
                                          <div class="relative">
                                                <select id="status" name="status" required
                                                      class="custom-select w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-emerald-500 dark:focus:border-emerald-500 transition-all duration-200">
                                                      <option selected disabled value="">Veuillez choisir un
                                                            status pour cette assignation
                                                      <option value="1"
                                                            {{ old('status', $getClassSubject->status) == '1' ? 'selected' : '' }}>
                                                            Active
                                                      </option>
                                                      <option value="0"
                                                            {{ old('status', $getClassSubject->status) == '0' ? 'selected' : '' }}>
                                                            Inactive
                                                      </option>
                                                </select>
                                                <div
                                                      class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                                      <iconify-icon icon="mdi:chevron-down" class="text-gray-400"
                                                            width="20" height="20"></iconify-icon>
                                                </div>
                                          </div>
                                    </div>

                                    <div class="mt-8">
                                          <button type="submit"
                                                class="w-full flex justify-center items-center py-3 px-4 bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-700 hover:to-emerald-600 text-white font-medium rounded-lg shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-opacity-50 transition-all duration-300">
                                                <iconify-icon icon="mdi:content-save-check-outline" class="mr-2"
                                                      width="20" height="20"></iconify-icon>
                                                Modifier cette assignation
                                          </button>
                                    </div>
                              </form>
                        </div>
                  </div>
            </div>
      </div>
@endsection
