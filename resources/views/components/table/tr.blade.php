@props(['class' => 'bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-indigo-50 dark:hover:bg-gray-600'])

<tr {{ $attributes->merge(['class' => $class]) }}>
    {{ $slot }}
</tr>
