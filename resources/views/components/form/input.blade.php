@props([
    'id',
    'type' => 'text',
    'label' => null,
    'placeholder' => '',
    'value' => '',
    'icon' => null,
    'required' => false,
])

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
                        <iconify-icon icon="{{ $icon }}" width="20" height="20"></iconify-icon>
                  </span>
            @endif

            <input type="{{ $type }}" id="{{ $id }}" name="{{ $id }}"
                  value="{{ old($id, $value) }}" placeholder="{{ $placeholder }}"
                  @if ($required) required @endif
                  {{ $attributes->merge([
                      'class' =>
                          'h-11 w-full rounded-lg border border-gray-300 bg-gray-50
                                                 px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400
                                                 
                                                 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:placeholder:text-white/50
                                                 dark:focus:border-indigo-800 transition-all duration-300 ' . ($icon ? 'pl-10' : ''),
                  ]) }}>
      </div>
</div>
