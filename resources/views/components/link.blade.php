@props([
    'href' => '#',
    'icon' => null,
    'hover' => 'hover:text-violet-600 dark:hover:text-white',
])

<a href="{{ $href }}"
      {{ $attributes->merge([
          'class' =>
              'inline-flex items-center gap-2 text-sm font-medium transition-colors text-gray-700 dark:text-gray-400 ' .
              $hover,
      ]) }}>
      @if ($icon)
            @if (Str::startsWith($icon, ['fa', 'fas', 'far', 'fal', 'fad']))
                  {{-- Use <i> for Font Awesome icons. Added fas, far, fal, fad for consistency --}}
                  <i class="{{ $icon }} mr-2"></i>
            @else
                  {{-- Use <iconify-icon> for MDI and other icon sets --}}
                  <iconify-icon icon="{{ $icon }}" class="mr-2" width="16" height="16"></iconify-icon>
            @endif
      @endif

      <span>{{ $slot }}</span>
</a>
