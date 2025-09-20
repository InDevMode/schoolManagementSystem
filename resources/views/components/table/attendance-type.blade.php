@props(['type' => null])

@php
      $labels = [
          1 => 'Présent(e)',
          2 => 'Retard',
          3 => 'Absent(e)',
          4 => 'Demi-journée',
      ];

      $colors = [
          1 => 'bg-emerald-100 border-emerald-800 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200',
          2 => 'bg-yellow-100 border-yellow-800 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
          3 => 'bg-red-100 border-red-800 text-red-800 dark:bg-red-900 dark:text-red-200',
          4 => 'bg-blue-100 border-blue-800 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
      ];

      $label = $labels[$type] ?? 'Inconnu';
      $class = $colors[$type] ?? 'bg-gray-100 border-gray-800 text-gray-800 dark:bg-gray-700 dark:text-gray-200';
@endphp

<span
      class="px-2 py-1 border inline-flex justify-center text-xs leading-5 font-semibold rounded-full {{ $class }}">
      {{ $label }}
</span>
