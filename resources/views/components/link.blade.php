@props(['label', 'href' => '#', 'leftIcon' => null, 'rightIcon' => null])

@php
      $linkClasses = Arr::toCssClasses([
          'inline-flex items-center gap-2.5 font-medium transition',
          // Le style par défaut : pas de fond, couleur du texte héritée, hover primaire.
          $attributes->get('class') ?: 'text-black hover:text-primary dark:text-white dark:hover:text-primary',
      ]);
@endphp

<a href="{{ $href }}" {{ $attributes->except('class')->merge(['class' => $linkClasses]) }}>
      @if ($leftIcon)
            <iconify-icon icon="{{ $leftIcon }}" class="text-xl" width="20" height="20"></iconify-icon>
      @endif
      <span>{{ $label }}</span>
      @if ($rightIcon)
            <iconify-icon icon="{{ $rightIcon }}" class="text-xl" width="20" height="20"></iconify-icon>
      @endif
</a>
