@props([
    'type' => 'submit',
    'icon' => null,
])

<button type="{{ $type }}"
      {{ $attributes->merge([
          'class' => 'w-full flex justify-center items-center py-3 px-4 bg-gradient-to-r
                       from-violet-600 to-violet-500 hover:from-violet-700 hover:to-violet-600
                       text-white font-medium rounded-lg shadow-md hover:shadow-lg
                       focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-opacity-50
                       transition-all duration-300',
      ]) }}>
      @if ($icon)
            <iconify-icon icon="{{ $icon }}" class="mr-2" width="20" height="20"></iconify-icon>
      @endif
      {{ $slot }}
</button>
