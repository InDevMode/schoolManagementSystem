@props([
    'href' => '#',
    'icon' => null,
    'label' => '',
    'textColor' => 'text-gray-700 dark:text-gray-300',
    'hoverTextColor' => 'hover:text-indigo-600',
    'hoverBgColor' => 'hover:bg-indigo-500/25 dark:hover:bg-gray-600',
])

@php
      $isFa = Str::startsWith($icon, ['fa', 'fas', 'far', 'fal', 'fad']);
@endphp

<li>
      <a href="{{ $href }}"
            {{ $attributes->merge([
                'class' => "flex items-center gap-3.5 px-6 py-2.5 text-sm font-medium duration-300 ease-in-out lg:text-base rounded-lg $textColor $hoverTextColor $hoverBgColor",
            ]) }}>

            @if ($icon)
                  <span class="mr-2">
                        @if ($isFa)
                              {{-- Use <i> for Font Awesome icons --}}
                              <i class="{{ $icon }}"></i>
                        @else
                              {{-- Use <iconify-icon> for all other icon sets --}}
                              <iconify-icon icon="{{ $icon }}" width="20" height="20"></iconify-icon>
                        @endif
                  </span>
            @endif

            {{-- Use $slot as the primary content, with a fallback to $label --}}
            {{ $slot }}
      </a>
</li>
