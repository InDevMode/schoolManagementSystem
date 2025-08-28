@props([
    'href' => '#',
    'icon' => null,
    'color' => 'flex items-center gap-2 px-4 py-2 text-sm transition duration-300 text-gray-500 hover:underline dark:text-gray-200',
])

<a href="{{ $href }}"
      {{ $attributes->merge(['class' => "$color"]) }}>
      @if ($icon)
            <i class="{{ $icon }} w-4 h-4"></i>
      @endif
      <span>{{ $slot }}</span>
</a>
