@props([
    'title' => '',
    'subtitle' => '',
    'icon' => null,
    'breadcrumbs' => [],
])

<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-5 gap-4 pt-3">
      <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                  @if ($icon)
                        {{-- Utilisation de l'icône principale --}}
                        <i class="{{ $icon }} text-violet-600 dark:text-violet-400"></i>
                  @endif
                  {{ $title }}
            </h1>
            @if ($subtitle)
                  <p class="text-gray-600 dark:text-gray-300 mt-1">
                        {{ $subtitle }}
                  </p>
            @endif
      </div>

      @if (!empty($breadcrumbs))
            <nav class="flex items-center text-sm space-x-2">
                  <ol class="flex items-center space-x-2">
                        @foreach ($breadcrumbs as $breadcrumb)
                              <li class="flex items-center space-x-2">
                                    @if ($loop->last)
                                          {{-- Dernier élément, texte simple sans lien --}}
                                          <span class="text-sm font-medium text-violet-600 dark:text-violet-600">
                                                {{ $breadcrumb['label'] }}
                                          </span>
                                    @else
                                          {{-- Utilisation de votre composant x-link --}}
                                          <x-link href="{{ $breadcrumb['url'] ?? '#' }}"
                                                icon="{{ $breadcrumb['icon'] ?? null }}"
                                                hover="hover:text-violet-600 dark:hover:text-white" class="!p-0">
                                                {{ $breadcrumb['label'] }}
                                          </x-link>
                                    @endif

                                    @if (!$loop->last)
                                          {{-- Séparateur (sauf pour le dernier élément) --}}
                                          <span class="text-gray-400">
                                                <iconify-icon icon="mdi:chevron-right"
                                                      class="text-gray-400 dark:text-gray-200" width="16"
                                                      height="16"></iconify-icon>
                                          </span>
                                    @endif
                              </li>
                        @endforeach
                  </ol>
            </nav>
      @endif
</div>
