@props(['id', 'name', 'type' => 'text', 'label' => null, 'placeholder' => '', 'value' => '', 'required' => false])

<div class="mb-6 w-full">
      @if ($label)
            <label for="{{ $id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  {{ $label }} @if ($required)
                        <span class="text-red-500">*</span>
                  @endif
            </label>
      @endif
      <div class="relative">
            <input type="{{ $type }}" id="{{ $id }}" name="{{ $name }}"
                  value="{{ old($name, $value) }}" placeholder="{{ $placeholder }}"
                  @if ($required) required @endif
                  {{ $attributes->merge([
                      'class' => 'form-input w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50
                                          focus:ring-2 focus:ring-violet-500 focus:border-violet-500
                                          dark:bg-gray-700 dark:border-gray-600 dark:text-white
                                          dark:focus:ring-violet-500 dark:focus:border-violet-500 transition-all duration-200',
                  ]) }}>
      </div>
</div>
