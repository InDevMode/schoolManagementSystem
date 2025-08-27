@props([
    'total' => 0,
    'label' => '',
    'pagination' => null,
])

<div
      class="w-full flex flex-col md:flex-row md:justify-between md:items-center gap-4">

      {{-- Total --}}
      <div class="text-sm text-gray-600 dark:text-gray-400 md:text-left">
            Total de
            <span class="font-semibold text-gray-800 dark:text-white">{{ $total }}</span>
            {{ $label }}<span>{{ $total > 1 ? 's' : '' }}</span>
      </div>

      {{-- Pagination --}}
      <div class="flex justify-center lg:justify-end w-full sm:w-auto">
            {!! $pagination !!}
      </div>

</div>
