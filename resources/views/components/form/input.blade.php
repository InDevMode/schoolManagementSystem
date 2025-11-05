@props([
    'name',
    'label',
    'id' => null, // L'id est maintenant optionnel
    'value' => '',
    'type' => 'text',
    'leftIcon' => null,
    'required' => false,
])

@php
      // Si l'id n'est pas fourni, il prend automatiquement la valeur de 'name'.
      // Cela simplifie l'appel du composant !
$name = $id;
$isPassword = $type === 'password';
$inputClasses = Arr::toCssClasses([
    'w-full h-11 rounded-lg border bg-transparent py-4 font-satoshi text-black outline-none transition', // Ajout de la police
    'focus:border-primary dark:focus:border-primary',
    'border-stroke dark:border-form-strokedark',
    'border-danger' => $errors->has($name),
    'pl-12' => $leftIcon,
    'pr-12' => $isPassword,
    'px-6' => !$leftIcon && !$isPassword,
      ]);
@endphp

<div x-data="{ show: {{ $isPassword ? 'false' : 'true' }} }">
      {{-- On utilise $id pour le 'for' du label --}}
      <label for="{{ $id }}" class="mb-2.5 block font-satoshi font-medium text-black dark:text-white">
            {{ $label }} {!! $required ? '<span class="text-danger">*</span>' : '' !!}
      </label>

      <div class="relative">
            @if ($leftIcon)
                  <span class="absolute left-4 top-0 flex h-full items-center">
                        <iconify-icon icon="{{ $leftIcon }}" class="text-bodydark" width="20" height="20"></iconify-icon>
                  </span>
            @endif

            <input id="{{ $id }}" {{-- L'id est défini ici --}} name="{{ $name }}" {{-- Le name est indispensable et est bien séparé --}}
                  @if ($isPassword) x-bind:type="show ? 'text' : 'password'"
            @else
                type="{{ $type }}" @endif
                  {{-- On utilise bien $name pour old(), ce qui est correct --}} value="{{ old($name, $value) }}"
                  {{ $attributes->merge(['class' => $inputClasses]) }} />

            @if ($isPassword)
                  <span class="absolute right-4 top-0 flex h-full items-center cursor-pointer" @click="show = !show">
                        <iconify-icon x-show="!show" icon="mdi:eye-off-outline" class="text-bodydark" width="20" height="20"></iconify-icon>
                        <iconify-icon x-show="show" icon="mdi:eye-outline" class="text-bodydark" width="20" height="20"></iconify-icon>
                  </span>
            @endif
      </div>

      {{-- La validation d'erreur se base aussi sur le 'name' --}}
      @error($name)
            <span class="text-sm text-danger">{{ $message }}</span>
      @enderror
</div>
