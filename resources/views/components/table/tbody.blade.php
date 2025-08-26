@props(['class' => 'divide-y divide-gray-200 dark:divide-gray-700'])

<tbody {{ $attributes->merge(['class' => $class]) }}>
    {{ $slot }}
</tbody>
