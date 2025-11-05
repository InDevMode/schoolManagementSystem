@props([
    'label',
    'type' => 'submit', // 'submit' ou 'button'
    'leftIcon' => null,
    'rightIcon' => null,
])

@php
      $buttonClasses = Arr::toCssClasses([
          'flex h-11 items-center justify-center gap-2.5 rounded-lg px-6 text-center font-medium transition',
          'hover:bg-opacity-90',
          // Le style par défaut est un fond solide avec du texte blanc.
          $attributes->get('class') ?: 'bg-primary text-white',
      ]);
@endphp

<button type="{{ $type }}" {{ $attributes->except('class')->merge(['class' => $buttonClasses]) }}>
      @if ($leftIcon)
            <iconify-icon icon="{{ $leftIcon }}" class="text-xl"></iconify-icon>
      @endif
      <span>{{ $label }}</span>
      @if ($rightIcon)
            <iconify-icon icon="{{ $rightIcon }}" class="text-xl"></iconify-icon>
      @endif
</button>
