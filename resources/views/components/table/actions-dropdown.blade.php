<div class="relative inline-block text-left" x-data="{
    id: $id('dropdown'),
    open: false,
    dropUp: false,
    checkPosition() {
        let r = $el.getBoundingClientRect(); // bouton
        let table = document.getElementById('myTable').getBoundingClientRect();

        let spaceBottom = table.bottom - r.bottom; // espace entre le bouton et le bas du tableau
        let spaceTop = r.top - table.top; // espace entre le haut du tableau et le bouton

        // si espace < 150px en bas ET assez d’espace en haut
        this.dropUp = spaceBottom < 150 && spaceTop > 150;
    }
}" x-effect="open = (openDropdown === id)">


      <!-- Bouton -->
      <button type="button"
            class="group flex items-center justify-center gap-x-1.5 rounded-lg shadow-lg bg-white dark:bg-gray-700 border dark:border-gray-600 px-3 py-2 text-sm font-semibold text-gray-700 hover:text-violet-600 dark:text-gray-200 hover:bg-gray-100"
            @click.stop="
            if (openDropdown === id) {
                openDropdown = null
            } else {
                checkPosition();
                openDropdown = id;
            }
        ">
            Actions
            <span class="-mr-1 text-gray-400 group-hover:text-violet-600">
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
