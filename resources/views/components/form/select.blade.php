@props([
    'name',
    'label',
    'options', // Tableau des options (ex: ['1' => 'Actif', '0' => 'Inactif'])
    'id' => null,
    'value' => '', // La valeur sélectionnée (pour les formulaires de modification)
    'leftIcon' => null,
    'required' => false,
    'placeholder' => 'Sélectionnez une option', // Texte pour l'option vide
])

@php
      $name = $id;
      $selectClasses = Arr::toCssClasses([
          'relative z-20 h-11 w-full appearance-none rounded-lg border bg-transparent py-4 font-satoshi text-black outline-none transition',
          'focus:border-primary dark:focus:border-primary',
          'border-stroke dark:border-form-strokedark',
          'border-danger' => $errors->has($name),
          'pl-12 pr-6' => $leftIcon,
          'px-6' => !$leftIcon,
      ]);
@endphp

<div>
      <label for="{{ $id }}" class="mb-2.5 block font-satoshi font-medium text-black dark:text-white">
            {{ $label }} {!! $required ? '<span class="text-danger">*</span>' : '' !!}
      </label>

      <div class="relative">
            @if ($leftIcon)
                  <span class="absolute left-4 top-0 flex h-full items-center z-10">
                        <iconify-icon icon="{{ $leftIcon }}" class="text-bodydark" width="20" height="20"></iconify-icon>
                  </span>
            @endif

            <select id="{{ $id }}" name="{{ $name }}"
                  {{ $attributes->merge(['class' => $selectClasses]) }}>
                  {{-- Option vide par défaut --}}
                  <option value="" disabled selected>{{ $placeholder }}</option>

                  {{-- Boucle sur les options fournies --}}
                  @foreach ($options as $optionValue => $optionLabel)
                        <option value="{{ $optionValue }}" {{ old($name, $value) == $optionValue ? 'selected' : '' }}>
                              {{ $optionLabel }}
                        </option>
                  @endforeach
            </select>

            {{-- Flèche du select --}}
            <span class="absolute left-4 top-0 z-10 flex h-full items-center">
                  <iconify-icon icon="mdi:chevron-down" class="text-bodydark text-xl"></iconify-icon>
            </span>
      </div>

      @error($name)
            <span class="text-sm text-danger">{{ $message }}</span>
      @enderror
</div>
