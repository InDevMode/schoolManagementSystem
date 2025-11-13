@props(['admin'])

<div x-data="{ show: false }">
      {{-- Déclencheur --}}
      <div @click="show = true">
            {{ $trigger }}
      </div>

      {{-- Modal --}}
      <template x-if="show">
            <div class="fixed inset-0 bg-black bg-opacity-30 dark:bg-opacity-80 flex items-center justify-center z-50 p-4"
                  x-show="show" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                  x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                  x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="display: none;">
                  <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg max-w-md w-full"
                        @click.outside="show = false" x-show="show" x-trap.inert="show"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" role="dialog"
                        aria-modal="true" aria-labelledby="profile-modal-title">

                        {{-- Header --}}
                        <div
                              class="flex justify-between items-center rounded-t-lg p-4 border-b dark:border-gray-700 bg-blue-500 dark:bg-gray-700">
                              <h3 id="profile-modal-title" class="text-lg font-semibold text-white">
                                    <i class="fa-solid fa-user mr-2"></i>Profil Administrateur
                              </h3>
                              <button @click="show = false"
                                    class="text-white rounded-full p-1 hover:bg-white/20 transition-colors"
                                    aria-label="Fermer le modal">
                                    <iconify-icon icon="mdi:close" width="20" height="20"></iconify-icon>
                                    <span class="sr-only">Fermer</span>
                              </button>
                        </div>

                        {{-- Contenu du profil --}}
                        <div class="p-6 text-gray-800 dark:text-gray-200">
                              <div class="flex flex-col items-center mb-6">
                                    <div
                                          class="w-20 h-20 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center mb-4">
                                          <i class="fa-solid fa-user text-2xl text-gray-500 dark:text-gray-400"></i>
                                    </div>
                                    <h4 class="text-xl font-semibold">{{ $admin->name }} {{ $admin->last_name }}</h4>
                                    <p class="text-gray-600 dark:text-gray-400">{{ $admin->email }}</p>
                              </div>

                              <div class="space-y-4">
                                    <div class="flex justify-between border-b dark:border-gray-700 pb-2">
                                          <span class="font-medium">Statut:</span>
                                          <span class="flex items-center">
                                                <i
                                                      class="fa-solid fa-circle {{ $admin->status == 1 ? 'text-emerald-400' : 'text-red-600' }} mr-2"></i>
                                                {{ $admin->status == 1 ? 'Actif' : 'Inactif' }}
                                          </span>
                                    </div>

                                    <div class="flex justify-between border-b dark:border-gray-700 pb-2">
                                          <span class="font-medium">Date de création:</span>
                                          <span>{{ \Carbon\Carbon::parse($admin->created_at)->locale('fr')->translatedFormat('d M Y H:i:s') }}</span>
                                    </div>

                                    <div class="flex justify-between border-b dark:border-gray-700 pb-2">
                                          <span class="font-medium">Dernière modification:</span>
                                          <span>{{ \Carbon\Carbon::parse($admin->updated_at)->locale('fr')->translatedFormat('d M Y H:i:s') }}</span>
                                    </div>
                              </div>
                        </div>

                        {{-- Footer --}}
                        <div
                              class="flex justify-end p-4 border-t dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 rounded-b-lg">
                              <x-form.button type="button" @click="show = false" label="Fermer" variant="secondary" />
                        </div>
                  </div>
            </div>
      </template>
</div>
