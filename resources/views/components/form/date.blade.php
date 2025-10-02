@props([
    'id',
    'label' => null,
    'value' => '',
    'required' => false,
    'class' => 'custom-input-date', // classe CSS par défaut
    'icon' => null, // pour l'icône de gauche
])

<div x-data="{}" {{-- Initialize a new Alpine.js component scope --}} class="mb-6 w-full">
      @if ($label)
            <label for="{{ $id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  {{ $label }} @if ($required)
                        <span class="text-red-500">*</span>
                  @endif
            </label>
      @endif
      <div class="relative">
            {{-- Icône de gauche (facultatif) --}}
            @if ($icon)
                  <span
                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 dark:text-gray-500">
                        @if (Str::startsWith($icon, ['fa', 'fas', 'far', 'fal', 'fad']))
                              <i class="{{ $icon }}"></i>
                        @else
                              <iconify-icon icon="{{ $icon }}" width="20" height="20"></iconify-icon>
                        @endif
                  </span>
            @endif

            <input x-ref="{{ $id }}" {{-- Reference is now dynamic using the 'id' prop --}} type="date" id="{{ $id }}"
                  name="{{ $id }}" value="{{ old($id, $value) }}"
                  {{ $attributes->merge([
                      'class' =>
                          'h-11 w-full rounded-lg border border-gray-300 bg-gray-50
                                              px-4 py-2.5 text-sm text-gray-800
                                              dark:border-gray-700 dark:bg-gray-800 dark:text-white/90
                                              dark:focus:border-indigo-800 transition-all duration-300 appearance-none' .
                          ($icon ? ' pl-10' : ''),
                  ]) }} />

            {{-- L'icône de droite, maintenant cliquable --}}
            <span x-on:click="$refs.{{ $id }}.showPicker()" {{-- Calls the dynamic reference --}}
                  class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer text-gray-400 dark:text-gray-500">
                  <iconify-icon icon="mdi:calendar-edit" width="20" height="20"></iconify-icon>
            </span>
      </div>
</div>
