@extends('layouts.app')
@section('content')
      <div class="m-2">
            @include('message')
            <div class="container mx-auto px-4 py-8 max-w-6xl">
                  <!-- Header Section -->
                  <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
                        <div class="mb-4 md:mb-0">
                              <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center">
                                    <iconify-icon icon="fa-solid:landmark" class="text-violet-600 mr-2" width="28"
                                          height="28"></iconify-icon>
                                    Assignez une classe à un professeur
                              </h1>
                              <p class="text-gray-600 dark:text-gray-300 mt-1">Remplissez les détails pour assignez une classe
                                    à un professeur
                              </p>
                        </div>

                        <nav class="flex" aria-label="Breadcrumb">
                              <ol class="inline-flex items-center space-x-1 md:space-x-3">
                                    <li class="inline-flex items-center">
                                          <a href="{{ url('admin/dashboard') }}"
                                                class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-violet-600 dark:text-gray-400 dark:hover:text-white">
                                                <iconify-icon icon="mdi:home" class="mr-2" width="16"
                                                      height="16"></iconify-icon>
                                                Tableau de bord
                                          </a>
                                    </li>
                                    <li>
                                          <div class="flex items-center">
                                                <iconify-icon icon="mdi:chevron-right" class="text-gray-400" width="16"
                                                      height="16"></iconify-icon>
                                                <a href="{{ url('admin/assign_class/list') }}"
                                                      class="ml-1 text-sm font-medium text-gray-700 hover:text-violet-600 md:ml-2 dark:text-gray-400 dark:hover:text-white">Liste
                                                      des classes assignées</a>
                                          </div>
                                    </li>
                                    <li aria-current="page">
                                          <div class="flex items-center">
                                                <iconify-icon icon="mdi:chevron-right" class="text-gray-400" width="16"
                                                      height="16"></iconify-icon>
                                                <span
                                                      class="ml-1 text-sm font-medium text-violet-600 md:ml-2 dark:text-violet-600">Nouvelle</span>
                                          </div>
                                    </li>
                              </ol>
                        </nav>
                  </div>

                  <!-- Main Form Section -->
                  <div class="bg-white rounded-xl shadow-md overflow-hidden dark:bg-gray-800 transition-colors duration-300">
                        <div class="p-6 md:p-8">
                              <form action="{{ url('admin/assign_class/add') }}" method="post" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <div class="p-6.5">

                                          <div class="mb-4.5">
                                                <label
                                                      class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                      Classe <span class="text-red-500">*</span>
                                                </label>
                                                <div class="relative">
                                                      <select id="class_id" name="class_id" required
                                                            class="custom-select w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500 transition-all duration-200">
                                                            <option selected disabled value="">Veuillez choisir
                                                                  une classe pour assignation
                                                                  @foreach ($getClass as $class)
                                                            <option class="text-body" value="{{ $class->id }}">
                                                                  {{ $class->name }}</option>
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
                                                <label class="mb-3 block text-sm font-medium text-black dark:text-white">
                                                      Professeurs <span class="text-meta-1">*</span>
                                                </label>

                                                <div class="">
                                                      <select class="hidden" x-cloak id="teacher_id" name="teacher_id[]"
                                                            required multiple>
                                                            @foreach ($getTeacher as $teacher)
                                                                  <option value="{{ $teacher->id }}">{{ $teacher->name }}
                                                                        {{ $teacher->last_name }} </option>
                                                            @endforeach
                                                      </select>

                                                      <div x-data="dropdown()" x-init="loadOptions()"
                                                            class="flex flex-col items-center">
                                                            <input type="hidden" :value="selectedValues()" />
                                                            <div class="relative z-30 inline-block w-full">
                                                                  <div class="relative flex flex-col items-center">
                                                                        <div @click="open" class="w-full">
                                                                              <div
                                                                                    class="mb-2 flex rounded-lg border border-stroke bg-gray-50 dark:bg-gray-700 dark:text-white py-2 pl-3 pr-3 outline-none transition focus:border-violet-600 active:border-violet-600 dark:border-form-strokedark dark:bg-form-input">
                                                                                    <div
                                                                                          class="flex flex-auto flex-wrap gap-3">
                                                                                          <template
                                                                                                x-for="(option,index) in selected"
                                                                                                :key="index">
                                                                                                <div
                                                                                                      class="my-1.5 flex items-center justify-center rounded-lg dark:text-white border-[.5px] border-stroke bg-gray px-2.5 py-1.5 text-sm font-medium dark:border-strokedark dark:bg-white/30">
                                                                                                      <div class="max-w-full flex-initial"
                                                                                                            x-model="options[option]"
                                                                                                            x-text="options[option].text">
                                                                                                      </div>
                                                                                                      <div
                                                                                                            class="flex flex-auto flex-row-reverse">
                                                                                                            <div @click="remove(index,option)"
                                                                                                                  class="cursor-pointer pl-2 hover:text-danger">
                                                                                                                  <iconify-icon
                                                                                                                        icon="mdi:close"></iconify-icon>
                                                                                                            </div>
                                                                                                      </div>
                                                                                                </div>
                                                                                          </template>
                                                                                          <div x-show="selected.length == 0"
                                                                                                class="flex-1">
                                                                                                <input placeholder="Choisissez un ou plusieurs professeur(s)"
                                                                                                      class="custom-select dark:placeholder-white h-full w-full appearance-none bg-gray-50 dark:bg-gray-700 p-1 px-2 outline-none"
                                                                                                      :value="selectedValues()" />
                                                                                          </div>
                                                                                    </div>
                                                                                    <div
                                                                                          class="flex w-8 items-center py-1 pl-1 pr-1">
                                                                                          <button type="button"
                                                                                                @click="open"
                                                                                                class="h-6 w-6 cursor-pointer outline-none focus:outline-none"
                                                                                                :class="isOpen() === true ?
                                                                                                    'rotate-180' : ''">
                                                                                                <iconify-icon
                                                                                                      icon="mdi:chevron-down"
                                                                                                      width="24"
                                                                                                      height="24"></iconify-icon>
                                                                                          </button>
                                                                                    </div>
                                                                              </div>
                                                                        </div>
                                                                        <div class="w-full px-4">
                                                                              <div x-show.transition.origin.top="isOpen()"
                                                                                    class="max-h-select absolute top-full left-0 z-40 w-full overflow-y-auto rounded bg-white shadow dark:bg-form-input"
                                                                                    @click.outside="close">
                                                                                    <div class="flex w-full flex-col">
                                                                                          <template
                                                                                                x-for="(option,index) in options"
                                                                                                :key="index">
                                                                                                <div>
                                                                                                      <div class="w-full cursor-pointer rounded-t border-b border-stroke hover:bg-violet-600/5 dark:border-form-strokedark"
                                                                                                            @click="select(index,$event)">
                                                                                                            <div :class="option
                                                                                                                .selected ?
                                                                                                                'border-violet-600' :
                                                                                                                ''"
                                                                                                                  class="relative flex w-full items-center border-l-2 border-gray-50 dark:bg-gray-700 p-2 pl-2">
                                                                                                                  <div
                                                                                                                        class="flex w-full items-center">
                                                                                                                        <div class="mx-2 leading-6"
                                                                                                                              x-model="option"
                                                                                                                              x-text="option.text">
                                                                                                                        </div>
                                                                                                                  </div>
                                                                                                            </div>
                                                                                                      </div>
                                                                                                </div>
                                                                                          </template>
                                                                                    </div>
                                                                              </div>
                                                                        </div>
                                                                  </div>
                                                            </div>
                                                      </div>
                                                </div>
                                          </div>
                                          <div class="mb-6">
                                                <label
                                                      class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                      Statut <span class="text-red-500">*</span>
                                                </label>
                                                <div class="relative">
                                                      <select id="status" name="status" required
                                                            class="custom-select w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-violet-500 dark:focus:border-violet-500 transition-all duration-200">
                                                            <option selected disabled value="">Veuillez choisir un
                                                                  status pour cette assignation
                                                            <option value="1"
                                                                  {{ old('status') == '1' ? 'selected' : '' }}>
                                                                  Active
                                                            </option>
                                                            <option value="0"
                                                                  {{ old('status') == '0' ? 'selected' : '' }}>
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
                                                      class="w-full flex justify-center items-center py-3 px-4 bg-gradient-to-r from-violet-600 to-violet-500 hover:from-violet-700 hover:to-violet-600 text-white font-medium rounded-lg shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-opacity-50 transition-all duration-300">
                                                      <iconify-icon icon="mdi:content-save-check-outline" class="mr-2"
                                                            width="20" height="20"></iconify-icon>
                                                      Créer cette assignation
                                                </button>
                                          </div>
                                    </div>
                              </form>
                        </div>
                  </div>
            </div>
      @endsection


      <script>
            function dropdown() {
                  return {
                        options: [],
                        selected: [],
                        show: false,
                        open() {
                              this.show = true;
                        },
                        close() {
                              this.show = false;
                        },
                        isOpen() {
                              return this.show === true;
                        },
                        select(index, event) {
                              if (!this.options[index].selected) {
                                    this.options[index].selected = true;
                                    this.options[index].element = event.target;
                                    this.selected.push(index);
                              } else {
                                    this.selected.splice(this.selected.lastIndexOf(index), 1);
                                    this.options[index].selected = false;
                              }
                              this.updateSelectElement();
                        },
                        remove(index, option) {
                              this.options[option].selected = false;
                              this.selected.splice(index, 1);
                              this.updateSelectElement();
                        },
                        loadOptions() {
                              const options = document.getElementById("teacher_id").options;
                              for (let i = 0; i < options.length; i++) {
                                    this.options.push({
                                          value: options[i].value,
                                          text: options[i].innerText,
                                          selected: options[i].getAttribute("selected") != null ?
                                                options[i].getAttribute("selected") : false,
                                    });
                              }
                              this.updateSelectElement();
                        },
                        selectedValues() {
                              return this.selected.map((option) => {
                                    return this.options[option].value;
                              });
                        },
                        updateSelectElement() {
                              const select = document.getElementById("teacher_id");
                              select.innerHTML = "";
                              this.selectedValues().forEach(value => {
                                    const option = document.createElement("option");
                                    option.value = value;
                                    option.selected = true;
                                    select.appendChild(option);
                              });
                        }
                  };
            }
      </script>
