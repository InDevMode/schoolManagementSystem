@props([
    'name',
    'label',
    'id' => null,
    'value' => '',
    'leftIcon' => 'mdi:calendar-outline', // Icône par défaut
    'required' => false,
])

@php
      $name = $id;
      // Les styles d'un input date sont un peu différents pour la lisibilité
$inputClasses = Arr::toCssClasses([
    'relative w-full h-11 rounded-lg border bg-transparent py-4 font-satoshi text-black outline-none transition',
    'focus:border-primary dark:focus:border-primary',
    'border-stroke dark:border-form-strokedark',
    'border-danger' => $errors->has($name),
    'pl-12 pr-6', // Padding spécifique pour la date
      ]);
@endphp

<div>
      <label for="{{ $id }}" class="mb-2.5 block font-satoshi font-medium text-black dark:text-white">
            {{ $label }} {!! $required ? '<span class="text-danger">*</span>' : '' !!}
      </label>

      <div class="relative">
            @if ($leftIcon)
                  <span class="absolute left-4 top-0 flex h-full items-center">
                        <iconify-icon icon="{{ $leftIcon }}" class="text-bodydark" width="20" height="20"></iconify-icon>
                  </span>
            @endif

            <input type="date" id="{{ $id }}" name="{{ $name }}" value="{{ old($name, $value) }}"
                  {{ $attributes->merge(['class' => $inputClasses]) }} />
      </div>

      @error($name)
            <span class="text-sm text-danger">{{ $message }}</span>
      @enderror
</div>
