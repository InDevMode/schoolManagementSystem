@props([
    'href' => '#',
    'icon' => null,
    'label' => '',
    'textColor' => 'text-gray-700 dark:text-gray-300',
    'hoverTextColor' => 'hover:text-indigo-600',
    'hoverBgColor' => 'hover:bg-indigo-500/25 dark:hover:bg-gray-600',
])

@php
      $isFa = $icon && str_starts_with($icon, 'fa');
      $isMdi = $icon && str_starts_with($icon, 'mdi');
@endphp

<li>
      <a href="{{ $href }}"
            {{ $attributes->merge([
                'class' => "flex items-center gap-3.5 px-6 py-2.5 text-sm font-medium duration-300 ease-in-out lg:text-base rounded-lg $textColor $hoverTextColor $hoverBgColor",
            ]) }}>

            @if ($icon)
                  <span>
                        @if ($isFa)
                              <i class="{{ $icon }}"></i>
                        @elseif ($isMdi)
                              <iconify-icon icon="{{ $icon }}" width="20" height="20"></iconify-icon>
                        @else
                              {{-- Fallback Iconify si non FA/MDI --}}
                              <iconify-icon icon="{{ $icon }}" width="20" height="20"></iconify-icon>
                        @endif
                  </span>
            @endif

            {{ $label ?: $slot }}
      </a>
</li>
