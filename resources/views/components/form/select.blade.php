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
                      'class' => 'custom-select w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50
                                           focus:ring-2 focus:ring-violet-500 focus:border-violet-500
                                           dark:bg-gray-700 dark:border-gray-600 dark:text-white
                                           dark:focus:ring-violet-500 dark:focus:border-violet-500 transition-all duration-200',
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
