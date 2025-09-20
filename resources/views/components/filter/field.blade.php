@props([
    'id',
    'label' => null,
    'type' => 'text',
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'icon' => null,
    'options' => [], // Pour les champs de type 'select'
    'selected' => null, // Pour les champs de type 'select'
])

<div class="mb-6 w-full">
      @if ($label)
            <label for="{{ $id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  {{ $label }}
                  @if ($required)
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
                              {{-- Font Awesome icons --}}
                              <i class="{{ $icon }}"></i>
                        @else
                              {{-- Iconify icons --}}
                              <iconify-icon icon="{{ $icon }}" width="20" height="20"></iconify-icon>
                        @endif
                  </span>
            @endif

            {{-- Logique pour le type de champ --}}
            @if ($type === 'select')
                  <select id="{{ $id }}" name="{{ $id }}"
                        @if ($required) required @endif
                        {{ $attributes->merge([
                            'class' =>
                                'h-11 w-full appearance-none rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-indigo-300 focus:ring-3 focus:ring-indigo-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-indigo-800 transition-all duration-300 ' .
                                ($icon ? 'pl-10' : ''),
                        ]) }}>
                        <option value="" disabled selected>Veuillez choisir une option</option>
                        @foreach ($options as $optionValue => $optionText)
                              <option value="{{ $optionValue }}"
                                    {{ old($id, $selected) == $optionValue ? 'selected' : '' }}>
                                    {{ $optionText }}
                              </option>
                        @endforeach
                  </select>
                  <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <iconify-icon icon="mdi:chevron-down" class="text-gray-700 dark:text-gray-500" width="20"
                              height="20"></iconify-icon>
                  </div>
            @else
                  {{-- Composant pour les types 'text', 'date', 'email', 'password', etc. --}}
                  <input @if ($type === 'date') x-data x-ref="dateInput" @endif type="{{ $type }}"
                        id="{{ $id }}" name="{{ $id }}" value="{{ old($id, $value) }}"
                        placeholder="{{ $placeholder }}" @if ($required) required @endif
                        {{ $attributes->merge([
                            'class' =>
                                'h-11 w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-indigo-300 focus:ring-3 focus:ring-indigo-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:placeholder:text-white/50 dark:focus:border-indigo-800 transition-all duration-300 ' .
                                ($icon ? 'pl-10' : '') .
                                ($type === 'date' ? ' custom-input-date' : ''),
                        ]) }}>

                  {{-- Icône de droite cliquable pour le type 'date' --}}
                  @if ($type === 'date')
                        <div
                              class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer text-gray-400 dark:text-gray-500">
                              <span x-on:click="$refs.dateInput.showPicker()">
                                    <iconify-icon icon="mdi:calendar-edit" width="20" height="20"></iconify-icon>
                              </span>
                        </div>
                  @endif
            @endif
      </div>
</div>
