@extends('layouts.app')
@section('content')
      <div class="m-2">
            @include('message')
            <div class="container mx-auto px-4 py-8 max-w-6xl">
                  <!-- Header Section -->
                  <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
                        <div class="mb-4 md:mb-0">
                              <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center">
                                    <iconify-icon icon="fa-solid:bell" class="text-violet-600 mr-2" width="28"
                                          height="28"></iconify-icon>
                                    Créer un nouveau mail
                              </h1>
                              <p class="text-gray-600 dark:text-gray-300 mt-1">Remplissez les détails pour créer un nouveau
                                    mail</p>
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
                                    <li aria-current="page">
                                          <div class="flex items-center">
                                                <iconify-icon icon="mdi:chevron-right" class="text-gray-400" width="16"
                                                      height="16"></iconify-icon>
                                                <span
                                                      class="ml-1 text-sm font-medium text-violet-600 md:ml-2 dark:text-violet-400">Mails</span>
                                          </div>
                                    </li>
                              </ol>
                        </nav>
                  </div>

                  <!-- Main Form Section -->
                  <div class="bg-white rounded-xl shadow-md overflow-hidden dark:bg-gray-800 transition-colors duration-300">
                        <div class="p-6 md:p-8">
                              <form action="{{ url('admin/communicate/send_mail') }}" method="post"
                                    enctype="multipart/form-data">
                                    {{ csrf_field() }}

                                    <div class="w-full mb-5">
                                          <label class="mb-3 block text-sm font-medium text-black dark:text-white">
                                                Objet <span class="text-meta-1">*</span>
                                          </label>
                                          <input id="subject" name="subject" value="{{ old('subject') }}" required
                                                type="text" placeholder="Entrez un l'objet du mail"
                                                class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-50 dark:bg-gray-700 px-5 py-2.5 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600" />
                                    </div>

                                    <!-- ✅ Composant de sélection avancée d'utilisateurs -->
                                    <div class="">
                                          <!-- Select caché utilisé pour soumettre les valeurs au backend -->
                                          <select class="hidden" x-cloak id="user_id" name="user_id[]" required multiple>
                                                @foreach ($getUsers as $user)
                                                      <option value="{{ $user->id }}"
                                                            data-name="{{ $user->name }} {{ $user->last_name }}"
                                                            data-label="{{ $user->suffix }}">
                                                            {{ $user->full_name }}
                                                      </option>
                                                @endforeach
                                          </select>

                                          <!-- Composant Alpine.js -->
                                          <div x-data="dropdown()" x-init="loadOptions()"
                                                class="flex flex-col items-center">
                                                <!-- Valeurs sélectionnées -->
                                                <input type="hidden" :value="selectedValues()" />

                                                <!-- Zone de sélection visible -->
                                                <div class="relative z-30 inline-block w-full">
                                                      <div class="relative flex flex-col items-center">
                                                            <div @click="open" class="w-full">
                                                                  <div
                                                                        class="mb-2 flex rounded-lg border border-stroke bg-gray-50 dark:bg-gray-700 py-2 pl-3 pr-3 outline-none transition focus:border-violet-600 active:border-violet-600 dark:border-form-strokedark dark:bg-form-input">
                                                                        <div class="flex flex-auto flex-wrap space-x-1">
                                                                              <!-- Badges des users sélectionnés -->
                                                                              <template x-for="(option,index) in selected"
                                                                                    :key="index">
                                                                                    <div
                                                                                          class="my-1 flex items-center justify-center rounded border-[.5px] border-stroke bg-gray-200 dark:bg-gray-700 px-2.5 py-1.5 text-sm font-medium dark:border-strokedark dark:bg-white/30">
                                                                                          <div class="max-w-full flex-initial"
                                                                                                x-text="options[option].text">
                                                                                          </div>
                                                                                          <div
                                                                                                class="flex flex-auto flex-row-reverse">
                                                                                                <div @click.stop="remove(index, option)"
                                                                                                      class="cursor-pointer pl-2 hover:text-danger">
                                                                                                      <iconify-icon
                                                                                                            icon="mdi:close"></iconify-icon>
                                                                                                </div>
                                                                                          </div>
                                                                                    </div>
                                                                              </template>

                                                                              <!-- Champ vide / placeholder -->
                                                                              <div x-show="selected.length == 0"
                                                                                    class="flex-1">
                                                                                    <input placeholder="Choisissez un ou plusieurs utilisateurs"
                                                                                            class="custom-select dark:placeholder-white h-full w-full appearance-none bg-gray-50 dark:bg-gray-700 p-1 px-2 outline-none"
                                                                                          :value="selectedValues()"
                                                                                          readonly />
                                                                              </div>
                                                                        </div>

                                                                        <!-- Chevron -->
                                                                        <div class="flex w-8 items-center py-1 pl-1 pr-1">
                                                                              <button type="button" @click="open"
                                                                                    class="h-6 w-6 cursor-pointer outline-none focus:outline-none"
                                                                                    :class="isOpen() ?
                                                                                        'rotate-180' :
                                                                                        ''">
                                                                                    <iconify-icon icon="mdi:chevron-down"
                                                                                          width="24"
                                                                                          height="24"></iconify-icon>
                                                                              </button>
                                                                        </div>
                                                                  </div>
                                                            </div>

                                                            <!-- Dropdown de sélection -->
                                                            <div class="w-full px-4">
                                                                  <div x-show.transition.origin.top="isOpen()"
                                                                        class="max-h-[800px] absolute top-full left-0 z-40 w-full overflow-y-auto no-scrollbar rounded bg-white shadow dark:bg-form-input"
                                                                        @click.outside="close">

                                                                        <!-- 🔍 Zone de recherche -->
                                                                        <div class="p-2 dark:border-form-strokedark">
                                                                              <input type="text" x-model="searchTerm"
                                                                                    placeholder="Rechercher un utilisateur..."
                                                                                    class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-50 dark:bg-gray-700 px-5 py-2.5 font-normal text-black outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-violet-600" />
                                                                        </div>

                                                                        <!-- 🔘 Bouton Tout sélectionner / désélectionner -->
                                                                        <div
                                                                              class="flex justify-end px-2 py-1 dark:border-form-strokedark">
                                                                              <button type="button"
                                                                                    class="text-md font-medium text-violet-600 hover:underline "
                                                                                    @click="toggleAll()"
                                                                                    x-text="areAllSelected() ? 'Tout désélectionner' : 'Tout sélectionner'">
                                                                              </button>
                                                                        </div>

                                                                        <!-- ✅ Liste filtrée des utilisateurs -->
                                                                        <div class="flex w-full flex-col">
                                                                              <template
                                                                                    x-for="(option, index) in filteredOptions()"
                                                                                    :key="index">
                                                                                    <div class="w-full cursor-pointer rounded-t border-b border-stroke hover:bg-violet-600/5 dark:border-form-strokedark"
                                                                                          @click="select(option.index, $event)">
                                                                                          <div :class="option
                                                                                              .selected ?
                                                                                              'border-violet-600' :
                                                                                              ''"
                                                                                                class="relative flex w-full items-center border-l-2 border-gray-50 dark:bg-gray-700 p-2 pl-2">
                                                                                                <div
                                                                                                      class="flex w-full items-center">
                                                                                                      <div class="mx-2 leading-6"
                                                                                                            x-text="option.text">
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

                                    <!-- Recipient Selection -->
                                    <div class="mb-8">
                                          <h3 class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                                Envoyer à : <span class="text-red-500">*</span>
                                          </h3>
                                          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                                <!-- Teachers -->
                                                <div
                                                      class="relative flex items-start bg-gray-50 rounded-lg p-4 dark:bg-gray-700 transition-colors duration-200 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                      <div class="flex h-5 items-center">
                                                            <input id="message_to_teachers" name="message_to[]"
                                                                  type="checkbox" value="2" class="checkbox-custom">
                                                      </div>
                                                      <div class="ml-3 text-sm">
                                                            <label for="message_to_teachers"
                                                                  class="font-medium text-gray-700 dark:text-gray-300 flex items-center">
                                                                  <iconify-icon icon="mdi:teacher"
                                                                        class="text-violet-600 mr-2" width="18"
                                                                        height="18"></iconify-icon>
                                                                  Professeurs
                                                            </label>
                                                            <p class="text-gray-500 dark:text-gray-400 text-xs mt-1">
                                                                  Envoyer aux
                                                                  enseignants
                                                                  concernés</p>
                                                      </div>
                                                </div>

                                                <!-- Students -->
                                                <div
                                                      class="relative flex items-start bg-gray-50 rounded-lg p-4 dark:bg-gray-700 transition-colors duration-200 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                      <div class="flex h-5 items-center">
                                                            <input id="message_to_students" name="message_to[]"
                                                                  type="checkbox" value="3" class="checkbox-custom">
                                                      </div>
                                                      <div class="ml-3 text-sm">
                                                            <label for="message_to_students"
                                                                  class="font-medium text-gray-700 dark:text-gray-300 flex items-center">
                                                                  <iconify-icon icon="mdi:account-school"
                                                                        class="text-violet-600 mr-2" width="18"
                                                                        height="18"></iconify-icon>
                                                                  Apprenants
                                                            </label>
                                                            <p class="text-gray-500 dark:text-gray-400 text-xs mt-1">
                                                                  Envoyer à tous
                                                                  les
                                                                  étudiants</p>
                                                      </div>
                                                </div>

                                                <!-- Parents -->
                                                <div
                                                      class="relative flex items-start bg-gray-50 rounded-lg p-4 dark:bg-gray-700 transition-colors duration-200 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                      <div class="flex h-5 items-center">
                                                            <input id="message_to_parents" name="message_to[]"
                                                                  type="checkbox" value="4" class="checkbox-custom">
                                                      </div>
                                                      <div class="ml-3 text-sm">
                                                            <label for="message_to_parents"
                                                                  class="font-medium text-gray-700 dark:text-gray-300 flex items-center">
                                                                  <iconify-icon icon="mdi:account-child"
                                                                        class="text-violet-600 mr-2" width="18"
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
                                                class="w-full rounded-lg border-[1.5px] border-stroke bg-gray-50 dark:bg-gray-700 px-5 py-2.5 font-normal text-gray-800 dark:text-white placeholder-gray-400 outline-none transition focus:border-violet-600 active:border-violet-600 disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-violet-600"
                                                required>{{ old('message') }}</textarea>
                                    </div>

                                    <div class="mt-8">
                                          <button type="submit"
                                                class="w-full flex justify-center items-center py-3 px-4 bg-gradient-to-r from-violet-600 to-violet-500 hover:from-violet-700 hover:to-violet-600 text-white font-medium rounded-lg shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-opacity-50 transition-all duration-300">
                                                <iconify-icon icon="mdi:content-save-check-outline" class="mr-2"
                                                      width="20" height="20"></iconify-icon>
                                                Envoyez le mail
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
                  if (textarea && window.jQuery) {
                        window.jQuery(textarea).summernote({
                              placeholder: 'Entrez votre message ici...',
                              height: 200
                        });
                  }
            });


            function dropdown() {
                  return {
                        options: [],
                        selected: [],
                        show: false,
                        searchTerm: "",

                        open() {
                              this.show = true;
                        },
                        close() {
                              this.show = false;
                              this.searchTerm = "";
                        },
                        isOpen() {
                              return this.show === true;
                        },

                        // Charger les users depuis le select caché
                        loadOptions() {
                              const selectOptions = document.getElementById("user_id").options;
                              for (let i = 0; i < selectOptions.length; i++) {
                                    this.options.push({
                                          index: i,
                                          value: selectOptions[i].value,
                                          text: selectOptions[i].innerText,
                                          selected: selectOptions[i].hasAttribute("selected"),
                                    });
                                    if (selectOptions[i].hasAttribute("selected")) {
                                          this.selected.push(i);
                                    }
                              }
                              this.updateSelectElement();
                        },

                        // Sélectionner / désélectionner un user
                        select(index, event) {
                              if (!this.options[index].selected) {
                                    this.options[index].selected = true;
                                    this.selected.push(index);
                              } else {
                                    this.options[index].selected = false;
                                    this.selected = this.selected.filter(i => i !== index);
                              }
                              this.updateSelectElement();
                        },

                        // Supprimer une sélection depuis les badges
                        remove(index, option) {
                              this.options[option].selected = false;
                              this.selected.splice(index, 1);
                              this.updateSelectElement();
                        },

                        // Obtenir les valeurs sélectionnées
                        selectedValues() {
                              return this.selected.map(i => this.options[i].value);
                        },

                        // Mettre à jour le <select> caché
                        updateSelectElement() {
                              const select = document.getElementById("user_id");
                              select.innerHTML = "";
                              this.selectedValues().forEach(value => {
                                    const option = document.createElement("option");
                                    option.value = value;
                                    option.selected = true;
                                    select.appendChild(option);
                              });
                        },

                        // Filtrer les utilisateurs selon la recherche
                        filteredOptions() {
                              if (!this.searchTerm) return this.options;
                              return this.options.filter(option =>
                                    option.text.toLowerCase().includes(this.searchTerm.toLowerCase())
                              );
                        },

                        // Vérifie si tout est sélectionné
                        areAllSelected() {
                              return this.options.length > 0 && this.selected.length === this.options.length;
                        },

                        // Tout sélectionner ou tout désélectionner
                        toggleAll() {
                              if (this.areAllSelected()) {
                                    this.options.forEach(opt => opt.selected = false);
                                    this.selected = [];
                              } else {
                                    this.selected = [];
                                    this.options.forEach((opt, index) => {
                                          opt.selected = true;
                                          this.selected.push(index);
                                    });
                              }
                              this.updateSelectElement();
                        }
                  };
            }
      </script>
