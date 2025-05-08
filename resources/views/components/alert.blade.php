@props(['type' => 'info', 'message' => '', 'id' => ''])

@php
$colors = [
'primary' => ['text' => 'text-blue-800', 'border' => 'border-blue-300', 'bg' => 'bg-blue-50', 'darkText' => 'dark:text-blue-400', 'darkBg' => 'dark:bg-gray-800', 'darkBorder' => 'dark:border-blue-800'],
'error' => ['text' => 'text-red-800', 'border' => 'border-red-300', 'bg' => 'bg-red-50', 'darkText' => 'dark:text-red-400', 'darkBg' => 'dark:bg-gray-800', 'darkBorder' => 'dark:border-red-800'],
'success' => ['text' => 'text-green-800', 'border' => 'border-green-300', 'bg' => 'bg-green-50', 'darkText' => 'dark:text-green-400', 'darkBg' => 'dark:bg-gray-800', 'darkBorder' => 'dark:border-green-800'],
'secondary' => ['text' => 'text-yellow-800', 'border' => 'border-yellow-300', 'bg' => 'bg-yellow-50', 'darkText' => 'dark:text-yellow-300', 'darkBg' => 'dark:bg-gray-800', 'darkBorder' => 'dark:border-yellow-800'],
'light' => ['text' => 'text-gray-800', 'border' => 'border-gray-300', 'bg' => 'bg-gray-50', 'darkText' => 'dark:text-gray-300', 'darkBg' => 'dark:bg-gray-800', 'darkBorder' => 'dark:border-gray-600'],
];

$color = $colors[$type] ?? $colors['primary'];
@endphp

<div id="alert-{{ $id }}"
     class="flex items-center p-4 mb-4 {{ $color['text'] }} border-t-4 {{ $color['border'] }} {{ $color['bg'] }} {{ $color['darkText'] }} {{ $color['darkBg'] }} {{ $color['darkBorder'] }}"
     role="alert">
    <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" fill="currentColor" viewBox="0 0 24 24">
        <path fill-rule="evenodd"
              d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm{{ $type === 'error' ? '1-4a1 1 0 1 0-2 0v5a1 1 0 1 0 2 0V8Zm-1 7a1 1 0 1 0 0 2h.01a1 1 0 1 0 0-2H12Z' : '13.707-1.293a1 1 0 0 0-1.414-1.414L11 12.586l-1.793-1.793a1 1 0 0 0-1.414 1.414l2.5 2.5a1 1 0 0 0 1.414 0l4-4Z' }}"
              clip-rule="evenodd"/>
    </svg>

    <div class="ms-3 text-sm font-medium">
        {{ $message }}
    </div>

    <button type="button"
            class="ms-auto -mx-1.5 -my-1.5 {{ $color['bg'] }} text-inherit rounded-lg focus:ring-2 p-1.5 hover:opacity-80 inline-flex items-center justify-center h-8 w-8"
            data-dismiss-target="#alert-{{ $id }}" aria-label="Close">
        <span class="sr-only">Dismiss</span>
        <svg class="w-3 h-3" aria-hidden="true" fill="none" viewBox="0 0 14 14">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
        </svg>
    </button>
</div>
