@props([
    'id',
    'label' => '',
    'class' => '',
    'checked' => false,
    'required' => false,
])

<div class="flex items-center">
    <input
        id="{{ $id }}"
        type="checkbox"
        name="{{ $id }}"
        {{ $checked ? 'checked' : '' }}
        @if($required) required @endif
        {{ $attributes->merge(['class' => $class]) }}
    >
    <span class="ml-2 text-sm">{{ $label }}</span>
</div>
