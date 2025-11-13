@props([
    'title',
    'confirmUrl' => '#',
    'confirmLabel' => 'Oui, supprimer',
    'confirmVariant' => 'danger', // 'danger', 'primary', 'success', 'secondary'
    'cancelLabel' => 'Annuler',
    'id' => Str::uuid(), // Génère un ID unique pour l'accessibilité (ARIA)
])

<div x-data="{ show: false }">
      {{--
      SLOT 1: DÉCLENCHEUR (TRIGGER)
      C'est l'élément sur lequel l'utilisateur clique pour ouvrir le modal.
    --}}
      <div @click="show = true">
            {{ $trigger }}
      </div>

      {{--
      MODAL ENTIER
      Utilise <template> pour qu'Alpine puisse l'ajouter/le retirer du DOM.
    --}}
      <template x-if="show">
            <div class="fixed inset-0 bg-black bg-opacity-30 dark:bg-opacity-80 flex items-center justify-center z-50 p-4"
                  x-show="show" {{-- Transition pour l'arrière-plan (overlay) --}} x-transition:enter="transition ease-out duration-300"
                  x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                  x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                  x-transition:leave-end="opacity-0" style="display: none;" {{-- Caché par défaut, géré par x-show --}}>
                  <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg max-w-lg w-full"
                        @click.outside="show = false" x-show="show" x-trap.inert="show" {{-- Piège le focus clavier à l'intérieur du modal --}}
                        {{-- Transition pour le panneau du modal --}} x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        {{-- Attributs d'accessibilité --}} role="dialog" aria-modal="true"
                        aria-labelledby="modal-title-{{ $id }}">
                        {{-- HEADER --}}
                        <div
                              class="flex justify-between items-center rounded-t-lg p-4 border-b dark:border-gray-700 bg-violet-500 dark:bg-gray-700">
                              <h3 id="modal-title-{{ $id }}" class="text-lg font-semibold text-white">
                                    {{ $title }}</h3>
                              <button @click="show = false"
                                    class="text-white rounded-full p-1 hover:bg-white/20 transition-colors"
                                    aria-label="Close modal">
                                    <iconify-icon icon="mdi:close" width="20" height="20"></iconify-icon>
                                    <span class="sr-only">Fermer</span>
                              </button>
                        </div>

                        {{--
                  SLOT 2: CORPS (BODY)
                  C'est le contenu principal / le message de confirmation.
                --}}
                        <div class="p-6 text-gray-800 dark:text-gray-200">
                              {{ $slot }}
                        </div>

                        {{--
                  FOOTER (AVEC SLOT 3 OPTIONNEL)
                  Contient les boutons d'action.
                --}}
                        <div
                              class="flex justify-between items-center p-4 border-t dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 rounded-b-lg">

                              @if (isset($footer))
                                    {{--
                          SLOT 3: FOOTER PERSONNALISÉ
                          Si vous définissez un <x-slot:footer>, il remplacera
                          les boutons par défaut.
                        --}}
                                    {{ $footer }}
                              @else
                                    {{--
                          FOOTER PAR DÉFAUT
                          Utilise les props 'cancelLabel', 'confirmLabel', etc.
                          (J'utilise x-form.button que je vous ai suggéré de créer)
                        --}}
                                    <x-form.button type="button" @click="show = false" :label="$cancelLabel"
                                          variant="secondary" />

                                    <a href="{{ $confirmUrl }}">
                                          <x-form.button type="button" :label="$confirmLabel" :variant="$confirmVariant" />
                                    </a>
                              @endif
                        </div>
                  </div>
            </div>
      </template>
</div>
