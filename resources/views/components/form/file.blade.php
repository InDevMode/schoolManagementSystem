@props([
    'name',
    'label',
    'id' => null,
    'required' => false,
    'leftIcon' => 'mdi:file-upload-outline', // Icône par défaut
])

@php
      $name = $id;
@endphp

{{--
    On utilise Alpine.js pour gérer l'affichage du nom du fichier.
    x-data="{ fileName: '' }" initialise une variable locale 'fileName'.
    @change="fileName = $event.target.files[0].name" met à jour cette variable
    dès qu'un fichier est sélectionné.
--}}
<div x-data="{ fileName: '' }">
      <label for="{{ $id }}" class="mb-2.5 block font-satoshi font-medium text-black dark:text-white">
            {{ $label }} {!! $required ? '<span class="text-danger">*</span>' : '' !!}
      </label>

      <div class="relative">
            {{-- Le champ de fichier réel, mais caché --}}
            <input type="file" id="{{ $id }}" name="{{ $name }}" class="sr-only" {{-- C'est la classe qui le cache visuellement --}}
                  @change="fileName = $event.target.files.length > 0 ? $event.target.files[0].name : ''" />

            {{-- Notre faux champ stylisé qui sert de bouton --}}
            <label for="{{ $id }}"
                  class="flex h-11 cursor-pointer items-center justify-between rounded-lg border border-stroke bg-gray px-6 font-satoshi text-body outline-none transition dark:border-form-strokedark dark:bg-form-input dark:text-bodydark">
                  <div class="flex items-center gap-3">
                        @if ($leftIcon)
                              <span>
                                    <iconify-icon icon="{{ $leftIcon }}"
                                          class="text-bodydark text-xl"></iconify-icon>
                              </span>
                        @endif
                        {{-- Affiche le nom du fichier si sélectionné, sinon un texte par défaut --}}
                        <span x-text="fileName || 'Aucun fichier sélectionné'"></span>
                  </div>

                  <span class="rounded-md bg-primary px-4 py-1.5 text-sm font-medium text-white">
                        Choisir
                  </span>
            </label>
      </div>

      @error($name)
            <span class="text-sm text-danger">{{ $message }}</span>
      @enderror
</div>
