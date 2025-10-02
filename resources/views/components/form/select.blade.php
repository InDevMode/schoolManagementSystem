@props(['id', 'label' => null, 'options' => [], 'selected' => null, 'icon' => null, 'required' => false])

<div class="mb-6 w-full">
      @if ($label)
            <label for="{{ $id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  {{ $label }} @if ($required)
                        <span class="text-red-500">*</span>
                  @endif
            </label>
      @endif
      <div class="relative">
            @if ($icon)
                  <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        @if (Str::startsWith($icon, ['fa', 'fas', 'far', 'fal', 'fad']))
                              {{-- Use <i> for Font Awesome icons --}}
                              <i class="{{ $icon }} text-gray-400 dark:text-gray-500"></i>
                        @else
                              {{-- Use <iconify-icon> for all other icon sets --}}
                              <iconify-icon icon="{{ $icon }}" width="20" height="20"
                                    class="text-gray-400 dark:text-gray-500"></iconify-icon>
                        @endif
                  </span>
            @endif
            <select id="{{ $id }}" name="{{ $id }}"
                  @if ($required) required @endif
                  {{ $attributes->merge([
                      'class' =>
                          'h-11 w-full appearance-none rounded-lg border border-gray-300 bg-gray-50
                                      px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400
                                      focus:border-indigo-300 focus:ring-3 focus:ring-indigo-500/10
                                      dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:placeholder:text-white/30
                                      dark:focus:border-indigo-800 transition-all duration-300 ' . ($icon ? 'pl-10' : ''),
                  ]) }}>
                  <option value="" disabled selected>Veuillez choisir une option</option>
                  @foreach ($options as $value => $text)
                        <option value="{{ $value }}" {{ old($id, $selected) == $value ? 'selected' : '' }}>
                              {{ $text }}
                        </option>
                  @endforeach
            </select>
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                  <iconify-icon icon="mdi:chevron-down" class="text-gray-700 dark:text-gray-500" width="20"
                        height="20"></iconify-icon>
            </div>
      </div>
</div>
