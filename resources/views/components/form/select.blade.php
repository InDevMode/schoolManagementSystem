@props(['id', 'name', 'label' => null, 'options' => [], 'selected' => null, 'required' => false])

<div class="mb-6 w-full">
      @if ($label)
            <label for="{{ $id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  {{ $label }} @if ($required)
                        <span class="text-red-500">*</span>
                  @endif
            </label>
      @endif
      <div class="relative">
            <select id="{{ $id }}" name="{{ $name }}" @if ($required) required @endif
                  {{ $attributes->merge([
                      'class' => 'h-11 w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5
                                            text-sm text-gray-800 placeholder:text-gray-400
                                            focus:border-indigo-300 focus:ring-3 focus:ring-indigo-500/10
                                            dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:placeholder:text-white/30
                                            dark:focus:border-indigo-800transition-all duration-300',
                  ]) }}>
                  <option value="" disabled selected>Veuillez choisir</option>
                  @foreach ($options as $value => $text)
                        <option value="{{ $value }}" {{ old($name, $selected) == $value ? 'selected' : '' }}>
                              {{ $text }}
                        </option>
                  @endforeach
            </select>
            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                  <iconify-icon icon="mdi:chevron-down" class="text-gray-400" width="20"
                        height="20"></iconify-icon>
            </div>
      </div>
</div>
