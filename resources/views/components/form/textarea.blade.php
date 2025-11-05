@props([
    'name',
    'label',
    'id' => null,
    'value' => '',
    'required' => false,
    'rows' => 3, // Permet de définir le nombre de lignes
    'placeholder' => '',
])

@php
      $name = $id;
      $textareaClasses = Arr::toCssClasses([
          'w-full h-11 rounded-lg border bg-transparent py-4 px-6 font-satoshi text-black outline-none transition',
          'focus:border-primary dark:focus:border-primary',
          'border-stroke dark:border-form-strokedark',
          'border-danger' => $errors->has($name),
      ]);
@endphp

<div>
      <label for="{{ $id }}" class="mb-2.5 block font-satoshi font-medium text-black dark:text-white">
            {{ $label }} {!! $required ? '<span class="text-danger">*</span>' : '' !!}
      </label>

      <div class="relative">
            <textarea id="{{ $id }}" name="{{ $name }}" rows="{{ $rows }}" placeholder="{{ $placeholder }}"
                  {{ $attributes->merge(['class' => $textareaClasses]) }}>{{ old($name, $value) }}</textarea> {{-- Pour un textarea, la valeur se met entre les balises --}}
      </div>

      @error($name)
            <span class="text-sm text-danger">{{ $message }}</span>
      @enderror
</div>
