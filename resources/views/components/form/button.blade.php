@props([
    'type' => 'submit',
    'icon' => null,
    'color' => 'indigo',
])

@php
      $bgClass = "from-$color-600";
      $toClass = "to-$color-400";
      $textColor = 'text-white';
      $hoverColor = 'hover:shadow-2xl';
@endphp

<button type="{{ $type }}"
      {{ $attributes->merge([
          'class' => "w-full h-11 flex justify-center items-center space-x-2 py-3 px-4 bg-gradient-to-r $bgClass $toClass $textColor font-medium rounded-lg shadow-md $hoverColor
                                       transition-all duration-300",
      ]) }}>
      @if ($icon)
            <iconify-icon icon="{{ $icon }}" class="mr-2" width="20" height="20"></iconify-icon>
      @endif
      {{ $slot }}
</button>
