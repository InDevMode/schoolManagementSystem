@extends('layouts.app')
@section('content')
      <div class="m-2">
            @include('message')
            <div class="container mx-auto px-4 py-8 max-w-6xl">
                  <!-- Header Section -->
                  <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
                        <div class="mb-4 md:mb-0">
                              <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center">
                                    <iconify-icon icon="fa-solid:bell" class="text-emerald-600 mr-2" width="28"
                                          height="28"></iconify-icon>
                                    Modifier cette notification
                              </h1>
                              <p class="text-gray-600 dark:text-gray-300 mt-1">Remplissez les détails pour modifier une
                                    notification
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
                                                <a href="{{ url('admin/communicate/noticeboard/list') }}"
                                                      class="ml-1 text-sm font-medium text-gray-700 hover:text-emerald-600 md:ml-2 dark:text-gray-400 dark:hover:text-white">Liste
                                                      des notifications</a>
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
                  <div class="bg-white rounded-lg shadow-md overflow-hidden dark:bg-gray-800 transition-colors duration-300">
                        <div class="p-6 md:p-8">
                              <form action="" method="post" enctype="multipart/form-data">
                                    {{ csrf_field() }}

                                    <div class="w-full mb-5">
                                          <label class="mb-3 block text-sm font-medium text-black dark:text-white">
                                                Titre <span class="text-meta-1">*</span>
                                          </label>
                                          <input id="title" name="title"
                                                value="{{ old('title', $getNoticeBoard->title) }}" required type="text"
                                                placeholder="Entrez un titre"
                                                class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-50 dark:bg-gray-700 px-5 py-2.5 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600" />
                                    </div>

                                    <div class="w-full mb-5">
                                          <label class="mb-3 block text-sm font-medium text-black dark:text-white">
                                                Date d'affichage <span class="text-meta-1">*</span>
                                          </label>
                                          <div class="relative">
                                                <div
                                                      class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                      <i class="fas fa-calendar-check text-gray-400"></i>
                                                </div>
                                                <input type="date" id="notice_date" name="notice_date" required
                                                      value="{{ old('notice_date', $getNoticeBoard->notice_date) }}"
                                                      class="pl-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-600 focus:border-primary-600 p-2.5">
                                          </div>
                                    </div>

                                    <div class="w-full mb-5">
                                          <label class="mb-3 block text-sm font-medium text-black dark:text-white">
                                                Date de publication <span class="text-meta-1">*</span>
                                          </label>
                                          <div class="relative">
                                                <div
                                                      class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                      <i class="fas fa-calendar-check text-gray-400"></i>
                                                </div>
                                                <input type="date" id="publish_date" name="publish_date" required
                                                      value="{{ old('publish_date', $getNoticeBoard->publish_date) }}"
                                                      class="pl-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-600 focus:border-primary-600 p-2.5">
                                          </div>
                                    </div>


                                    @php
                                          $message_to_teacher = $getNoticeBoard->getMessageToSingle(
                                              $getNoticeBoard->id,
                                              2,
                                          );
                                          $message_to_student = $getNoticeBoard->getMessageToSingle(
                                              $getNoticeBoard->id,
                                              3,
                                          );
                                          $message_to_parent = $getNoticeBoard->getMessageToSingle(
                                              $getNoticeBoard->id,
                                              4,
                                          );
                                    @endphp
                                    <div class="w-full mb-5">
                                          <h3 class="mb-4 font-semibold text-gray-900 dark:text-white">Envoyer à : <span
                                                      class="text-meta-1">*</span></h3>
                                          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                                <!-- Teachers -->
                                                <div
                                                      class="relative flex items-start bg-gray-50 rounded-lg p-4 dark:bg-gray-700 transition-colors duration-200 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                      <div class="flex h-5 items-center">
                                                            <input id="message_to_teachers" type="checkbox" value="2"
                                                                  name="message_to[]"
                                                                  {{ !empty($message_to_teacher) ? 'checked' : '' }}
                                                                  value="2" class="checkbox-custom-success">
                                                      </div>
                                                      <div class="ml-3 text-sm">
                                                            <label for="message_to_teachers"
                                                                  class="font-medium text-gray-700 dark:text-gray-300 flex items-center">
                                                                  <iconify-icon icon="mdi:teacher"
                                                                        class="text-emerald-400 mr-2" width="18"
                                                                        height="18"></iconify-icon>
                                                                  Professeurs
                                                            </label>
                                                            <p class="text-gray-500 dark:text-gray-400 text-xs mt-1">
                                                                  Envoyer aux
                                                                  professeurs
                                                                  concernés</p>
                                                      </div>
                                                </div>

                                                <!-- Students -->
                                                <div
                                                      class="relative flex items-start bg-gray-50 rounded-lg p-4 dark:bg-gray-700 transition-colors duration-200 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                      <div class="flex h-5 items-center">
                                                            <input id="message_to_students" type="checkbox" value="3"
                                                                  name="message_to[]"
                                                                  {{ !empty($message_to_student) ? 'checked' : '' }}
                                                                  value="3" class="checkbox-custom-success">
                                                      </div>
                                                      <div class="ml-3 text-sm">
                                                            <label for="message_to_students"
                                                                  class="font-medium text-gray-700 dark:text-gray-300 flex items-center">
                                                                  <iconify-icon icon="mdi:account-school"
                                                                        class="text-emerald-400 mr-2" width="18"
                                                                        height="18"></iconify-icon>
                                                                  Apprenants
                                                            </label>
                                                            <p class="text-gray-500 dark:text-gray-400 text-xs mt-1">
                                                                  Envoyer à tous
                                                                  les
                                                                  apprenants</p>
                                                      </div>
                                                </div>

                                                <!-- Parents -->
                                                <div
                                                      class="relative flex items-start bg-gray-50 rounded-lg p-4 dark:bg-gray-700 transition-colors duration-200 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                      <div class="flex h-5 items-center">
                                                            <input id="message_to_parents" type="checkbox" value="4"
                                                                  name="message_to[]"
                                                                  {{ !empty($message_to_parent) ? 'checked' : '' }}
                                                                  value="4" class="checkbox-custom-success">
                                                      </div>
                                                      <div class="ml-3 text-sm">
                                                            <label for="message_to_parents"
                                                                  class="font-medium text-gray-700 dark:text-gray-300 flex items-center">
                                                                  <iconify-icon icon="mdi:user-group"
                                                                        class="text-emerald-400 mr-2" width="18"
                                                                        height="18"></iconify-icon>
                                                                  Parents
                                                            </label>
                                                            <p class="text-gray-500 dark:text-gray-400 text-xs mt-1">
                                                                  Envoyer aux
                                                                  parents
                                                                  concernés</p>
                                                      </div>
                                                </div>
                                          </div>
                                    </div>

                                    <div class="mb-4.5">
                                          <label class="mb-3 block text-sm font-medium text-black dark:text-white">
                                                Message <span class="text-meta-1">*</span>
                                          </label>
                                          <textarea id="compose-textarea" name="message"
                                                class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-50 dark:bg-gray-700 text-gray-200 placeholder-gray-200 px-5 py-2.5 font-normal outline-none transition focus:border-emerald-400 active:border-emerald-400 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-emerald-400"
                                                required>{{ old('message', $getNoticeBoard->message) }}</textarea>
                                    </div>

                                    <div class="mt-8">
                                          <button type="submit"
                                                class="w-full flex justify-center items-center py-3 px-4 bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-700 hover:to-emerald-600 text-white font-medium rounded-lg shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-opacity-50 transition-all duration-300">
                                                <iconify-icon icon="mdi:content-save-check-outline" class="mr-2"
                                                      width="20" height="20"></iconify-icon>
                                                Créer une nouvelle notifications
                                          </button>
                                    </div>
                              </form>
                        </div>
                  </div>
            </div>
      @endsection

      <script>
            document.addEventListener("DOMContentLoaded", function() {
                  const textarea = document.getElementById("compose-textarea");
                  if (textarea) {
                        // Summernote nécessite jQuery, donc on déclenche via jQuery depuis JS natif
                        window.jQuery(textarea).summernote({
                              placeholder: 'Entrez votre message ici...',
                              height: 200,
                              callbacks: {
                                    onInit: function() {
                                          const editor = document.querySelector(
                                                '.note-editable');
                                          if (editor && document.documentElement.classList
                                                .contains('dark')) {
                                                editor.classList.add('text-white',
                                                      'bg-gray-800',
                                                      'placeholder-gray-200');
                                          }
                                    }
                              }
                        });

                  }
            });
      </script>
