@props(['title', 'confirmUrl' => '#'])

<div x-data="{ showConfirm: false }">
      <div @click="showConfirm = true">
            {{ $trigger }}
      </div>

      <template x-if="showConfirm">
            <div class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50 p-4"
                  @click.outside="showConfirm = false">
                  <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg max-w-lg w-full">
                        <div class="flex justify-between items-center p-4 border-b bg-violet-500 dark:bg-gray-700">
                              <h3 class="text-lg font-semibold text-white">{{ $title }}</h3>
                              <button @click="showConfirm = false" class="text-white">✕</button>
                        </div>

                        <div class="p-4 text-gray-800 dark:text-gray-200 max-w-lg">
                                    {{ $slot }}
                        </div>

                        <div class="flex justify-between items-center p-4 border-t">
                              <button @click="showConfirm = false"
                                    class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded text-gray-800 dark:text-gray-200">
                                    Annuler
                              </button>
                              @if ($confirmUrl)
                                    <a href="{{ $confirmUrl }}"
                                          class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                                          Oui, supprimer
                                    </a>
                              @endif
                        </div>
                  </div>
            </div>
      </template>
</div>
