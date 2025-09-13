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
                                    <a href="{{ $breadcrumb['url'] ?? '#' }}"
                                          class="hover:text-violet-600 dark:hover:text-violet-400 font-medium transition-colors flex items-center">
                                          @if (!empty($breadcrumb['icon']))
                                                <i
                                                      class="{{ $breadcrumb['icon'] }} text-violet-600 dark:text-violet-400"></i>
                                          @endif
                                          {{ $breadcrumb['label'] }}
                                    </a>

                                    @if (!$loop->last)
                                          <span class="text-gray-400">
                                                <iconify-icon icon="mdi:chevron-right"
                                                      class="text-gray-400 dark:text-gray-200" width="24"
                                                      height="24"></iconify-icon>
                                          </span>
                                    @endif
                              </li>
                        @endforeach
                  </ol>
            </nav>
      @endif
</div>
