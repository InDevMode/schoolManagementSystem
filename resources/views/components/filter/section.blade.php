{{-- resources/views/components/filter/section.blade.php --}}
@props(['title' => 'Filtres de recherche multiple'])

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8">
      <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
            <i class="fas fa-filter text-indigo-600"></i>
            {{ $title }}
      </h2>

      <form method="GET">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                  {{ $slot }}
            </div>
      </form>
</div>
