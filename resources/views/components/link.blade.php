@props([
    'href' => '#',
    'icon' => null,
    'iconColor' => 'text-gray-500',
    'textColor' => 'text-gray-700 dark:text-gray-200',
    'hoverColor' => 'hover:text-indigo-600 dark:hover:text-indigo-400',
])

<a href="{{ $href }}"
      {{ $attributes->merge(['class' => "flex items-center gap-2 px-4 py-2 text-sm transition duration-300 $textColor $hoverColor"]) }}>

      @if ($icon)
            <i class="{{ $icon }} {{ $iconColor }}"></i>
      @endif

      <span>{{ $slot }}</span>
</a>
