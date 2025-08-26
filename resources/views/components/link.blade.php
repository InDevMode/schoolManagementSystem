@props([
    'href' => '#',
    'icon' => null,
    'color' => 'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700',
])

<a href="{{ $href }}"
      {{ $attributes->merge(['class' => "flex items-center gap-2 px-4 py-2 text-sm rounded-md transition $color"]) }}>
      @if ($icon)
            <i class="{{ $icon }} w-4 h-4"></i>
      @endif
      <span>{{ $slot }}</span>
</a>
