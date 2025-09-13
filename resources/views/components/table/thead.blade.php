@props(['class' => 'text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400'])

<thead {{ $attributes->merge(['class' => $class]) }}>
    <tr>
        {{ $slot }}
    </tr>
</thead>
