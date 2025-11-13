@props(['href', 'label' => '', 'icon' => null])

{{--
Ce composant est conçu pour être un item <a> à l'intérieur d'un menu dropdown.
Il réplique le style des liens dans votre fichier 'admin/admin/list.blade.php'
--}}

<a href="{{ $href }}"
      {{ $attributes->merge([
          'class' => 'block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200
                          hover:text-violet-600 dark:hover:text-violet-400
                          hover:bg-gray-100 dark:hover:bg-gray-700
                          transition-colors duration-150 flex items-center gap-2',
          'role' => 'menuitem',
      ]) }}>

      {{-- Logique d'icône copiée de vos autres composants --}}
      @if ($icon)
            <span class="flex-shrink-0 w-4 h-4 flex items-center justify-center">
                  @if (Str::startsWith($icon, ['fa', 'fas', 'far', 'fal', 'fad']))
                        {{-- Utilise <i> for Font Awesome icons --}}
                        <i class="{{ $icon }} text-inherit"></i>
                  @else
                        {{-- Utilise <iconify-icon> for all other icon sets --}}
                        <iconify-icon icon="{{ $icon }}" width="16" height="16"
                              class="text-inherit"></iconify-icon>
                  @endif
            </span>
      @endif

      {{-- Le label (supporte le 'label' prop ou le slot par défaut) --}}
      <span class="flex-grow">
            @if ($slot->isNotEmpty())
                  {{ $slot }}
            @else
                  {{ $label }}
            @endif
      </span>
</a>
