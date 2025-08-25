<div
      class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row justify-between items-center gap-4">
      <div class="text-sm text-gray-500 dark:text-gray-400">
            Total de <span class="font-medium">{{ $total }}</span> {{ $label }}
      </div>
      <nav class="flex items-center gap-5">
            {{ $pagination }}
      </nav>
</div>
