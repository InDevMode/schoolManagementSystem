@props([
    'title' => null,
    'icon' => null,
])

<div
      {{ $attributes->merge(['class' => 'bg-white rounded-lg shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700 transition-colors duration-300']) }}>

      {{-- Titre de la Card (optionnel) --}}
      @if ($title)
            <div class="p-4 md:p-6 border-b border-gray-200 dark:border-gray-700">
                  <h2 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                        @if ($icon)
                              <i class="{{ $icon }} text-violet-600"></i>
                        @endif
                        {{ $title }}
                  </h2>
            </div>
      @endif

      {{-- Contenu de la Card --}}
      <div class="p-6 md:p-8">
            {{ $slot }}
      </div>

      {{-- Footer de la Card (optionnel) --}}
      @if (isset($footer))
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                  {{ $footer }}
            </div>
      @endif
</div>
