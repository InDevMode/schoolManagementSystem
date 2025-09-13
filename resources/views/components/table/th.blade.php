@props(['align' => 'left'])

<th {{ $attributes->merge([
    'class' => "px-6 py-3 text-{$align} text-xs font-medium text-gray-100 dark:text-gray-300 bg-violet-600 dark:bg-gray-700 uppercase tracking-wider"
]) }}>
    {{ $slot }}
</th>
