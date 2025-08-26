@props([
    'title' => '',
    'subtitle' => '',
    'icon' => null,
    'breadcrumbs' => [],
])

<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
      <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                  @if ($icon)
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
            <nav class="flex items-center text-sm">
                  <ol class="flex items-center space-x-2">
                        @foreach ($breadcrumbs as $breadcrumb)
                              <li class="flex items-center">
                                    <a href="{{ $breadcrumb['url'] ?? '#' }}"
                                          class="hover:text-violet-600 dark:hover:text-violet-400 transition-colors flex items-center">
                                          @if (!empty($breadcrumb['icon']))
                                                <i
                                                      class="{{ $breadcrumb['icon'] }} text-violet-600 dark:text-violet-400 mr-1"></i>
                                          @endif
                                          {{ $breadcrumb['label'] }}
                                    </a>

                                    @if (!$loop->last)
                                          <span class="mx-2 text-gray-400">
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
