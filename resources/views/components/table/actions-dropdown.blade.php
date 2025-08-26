<div class="relative inline-block text-left" x-data="{
    id: $id('dropdown'),
    open: false,
    dropUp: false,
    checkPosition() {
        // calcule la position du bouton par rapport à la fenêtre
        let r = $el.getBoundingClientRect();
        let spaceBottom = window.innerHeight - r.bottom;
        let spaceTop = r.top;

        // si pas assez d’espace en bas (<200px), on ouvre vers le haut
        this.dropUp = spaceBottom < 100 && spaceTop > 100;
    }
}" x-effect="open = (openDropdown === id)">

      <!-- Bouton -->
      <button type="button"
            class="group flex items-center w-full justify-center gap-x-1.5 rounded-lg shadow-md bg-white dark:bg-gray-800 border dark:border-gray-600 px-3 py-2 text-sm font-semibold text-gray-700 hover:text-violet-600 dark:text-gray-200 hover:bg-gray-100"
            @click.stop="
            if (openDropdown === id) {
                openDropdown = null
            } else {
                checkPosition();
                openDropdown = id;
            }
        ">
            Actions
            <span class="block -mr-1 size-5 group-hover:text-violet-600 text-gray-400">
                  <i class="fas fa-chevron-down"></i>
            </span>
      </button>

      <!-- Dropdown -->

      <div x-show="open" @click.outside="open = false" x-transition
            :class="dropUp ? 'bottom-full mb-2 origin-bottom-right' : 'mt-2 origin-top-right'"
            class="absolute right-0 z-50 w-56 rounded-md bg-white dark:bg-gray-800 ring-1 shadow-lg ring-black/5"
            x-cloak>
            <div class="py-1">
                  {{ $slot }}
            </div>
      </div>
