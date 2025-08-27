@if ($paginator->hasPages())
      <nav role="navigation" aria-label="Pagination Navigation" class="w-full p-4 rounded-xl">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between w-full gap-y-4 sm:gap-y-0 gap-x-2">

                  {{-- Info page actuelle --}}
                  <div>
                        <p class="text-sm font-medium text-white leading-5 flex items-center justify-between bg-violet-500 px-4 py-2.5 gap-x-2 rounded-lg w-full">
                              Page <span class="font-medium text-white block ms-2">{{ $paginator->currentPage() }}</span>
                              <iconify-icon icon="mdi:chevron-right" class="text-white" width="24"
                                    height="24"></iconify-icon>
                              <span class="font-medium text-white block">{{ $paginator->lastPage() }}</span>
                        </p>
                  </div>

                  {{-- Liens de navigation --}}
                  <div>
                        <div class="flex items-center gap-x-2">

                              {{-- Précédent --}}
                              @if ($paginator->onFirstPage())
                                    <span aria-disabled="true" aria-label="Précédent"
                                          class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg bg-violet-500 text-white hover:text-white transition-all duration-200 shadow-sm focus:outline-none focus:ring-2 focus:ring-violet-500 cursor-not-allowed">
                                          <iconify-icon icon="mdi:chevron-left" class="text-white" width="24"
                                                height="24"></iconify-icon> Précédent
                                    </span>
                              @else
                                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                                          class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg bg-violet-500 text-white hover:bg-violet-600 hover:text-white transition-all duration-200 shadow-sm focus:outline-none focus:ring-2 focus:ring-violet-500">
                                          <iconify-icon icon="mdi:chevron-left" class="text-white" width="24"
                                                height="24"></iconify-icon> Précédent
                                    </a>
                              @endif

                              {{-- Numéros de pages --}}
                              @foreach ($elements as $element)
                                    @if (is_string($element))
                                          <span aria-disabled="true"
                                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-gray-800 border border-gray-700 rounded-lg cursor-default">
                                                {{ $element }}
                                          </span>
                                    @endif

                                    @if (is_array($element))
                                          @foreach ($element as $page => $url)
                                                @if ($page == $paginator->currentPage())
                                                      <span aria-current="page"
                                                            class="inline-flex items-center px-4 py-2 text-sm font-bold text-white bg-violet-600 rounded-lg shadow-md ring-2 ring-violet-500">
                                                            {{ $page }}
                                                      </span>
                                                @else
                                                      <a href="{{ $url }}"
                                                            class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg bg-violet-500 text-white hover:bg-violet-600 hover:text-white transition-all duration-200 shadow-sm focus:outline-none focus:ring-2 focus:ring-violet-500">
                                                            {{ $page }}
                                                      </a>
                                                @endif
                                          @endforeach
                                    @endif
                              @endforeach

                              {{-- Suivant --}}
                              @if ($paginator->hasMorePages())
                                    <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                                          class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg bg-violet-500 text-white hover:bg-violet-600 hover:text-white transition-all duration-200 shadow-sm focus:outline-none focus:ring-2 focus:ring-violet-500">
                                          Suivant <iconify-icon icon="mdi:chevron-right" class="text-white"
                                                width="24" height="24"></iconify-icon>
                                    </a>
                              @else
                                    <span aria-disabled="true" aria-label="Suivant"
                                          class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg bg-violet-500 text-white hover:text-white transition-all duration-200 shadow-sm focus:outline-none focus:ring-2 focus:ring-violet-500 cursor-not-allowed">
                                          Suivant <iconify-icon icon="mdi:chevron-right" class="text-white"
                                                width="24" height="24"></iconify-icon>
                                    </span>
                              @endif

                        </div>
                  </div>
            </div>
      </nav>
@endif
