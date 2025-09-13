@props([
    'type' => 'submit',
    'icon' => null,
    'bg' => 'from-indigo-600',
    'to' => 'to-indigo-400',
    'border' => 'border-transparent',
    'text' => 'text-gray-700 dark:text-gray-200',
    'hover' => 'hover:shadow-2xl'
])

<button type="{{ $type }}"
      {{ $attributes->merge([
          'class' => "w-full h-11 flex justify-center items-center space-x-2 py-3 px-4 bg-gradient-to-r $bg $to
                                $border $text font-medium rounded-lg shadow-md $hover
                                 transition-all duration-300 transition-colors",
      ]) }}>
      @if ($icon)
            <iconify-icon icon="{{ $icon }}" class="mr-2" width="20" height="20"></iconify-icon>
      @endif
      {{ $slot }}
</button>
