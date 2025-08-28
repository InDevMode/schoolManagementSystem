@props(['id', 'label' => null, 'value' => '', 'required' => false])

<div class="mb-6 w-full">
      @if ($label)
            <label for="{{ $id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  {{ $label }} @if ($required)
                        <span class="text-red-500">*</span>
                  @endif
            </label>
      @endif
      <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <i class="fas fa-calendar-check text-gray-400"></i>
            </div>
            <input type="date" id="{{ $id }}" name="{{ $id }}" value="{{ old($name, $value) }}"
                  @if ($required) required @endif
                  {{ $attributes->merge([
                      'class' => 'pl-10 w-full rounded-lg p-2.5',
                  ]) }}>
      </div>
</div>
