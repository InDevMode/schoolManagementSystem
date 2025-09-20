@props(['bloodGroup' => null])

@php
      $colors = [
          'a+' => 'bg-indigo-100 border-indigo-800 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200',
          'a-' => 'bg-indigo-100 border-indigo-800 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200',
          'b+' => 'bg-green-100 border-green-800 text-green-800 dark:bg-green-900 dark:text-green-200',
          'b-' => 'bg-green-100 border-green-800 text-green-800 dark:bg-green-900 dark:text-green-200',
          'ab+' => 'bg-yellow-100 border-yellow-800 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
          'ab-' => 'bg-yellow-100 border-yellow-800 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
          'o+' => 'bg-purple-100 border-purple-800 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
          'o-' => 'bg-purple-100 border-purple-800 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
      ];
      $class =
          $colors[strtolower($bloodGroup)] ??
          'bg-gray-100 border-gray-800 text-gray-800 dark:bg-gray-700 dark:text-gray-200';
@endphp

@if ($bloodGroup)
      <span
            class="px-2 py-1 border w-16 inline-flex justify-center text-xs leading-5 font-semibold rounded-full {{ $class }}">
            {{ strtoupper($bloodGroup) }}
      </span>
@else
      <span
            class="px-2 py-1 border w-20 inline-flex justify-center text-xs leading-5 font-semibold rounded-full bg-gray-100 border-gray-800 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
            Inconnu
      </span>
@endif
