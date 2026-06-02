@props(['columns' => [], 'footer' => null])

<div x-data="{
    visibleColumns: @js($columns),
    search: '',
    selectedIds: [],
    openDropdown: null, // dropdown global ouvert
    toggleColumn(col) { this.visibleColumns[col] = !this.visibleColumns[col] },
    toggleAll(el, checked) {
        const root = el.closest('[x-data]');
        const rows = root.querySelectorAll('tbody tr');
        this.selectedIds = checked ? [] : [];

        rows.forEach(tr => {
            const isVisible = tr.offsetParent !== null;
            const cb = tr.querySelector('input.row-check');
            if (!cb) return;
            if (isVisible) {
                cb.checked = checked;
                if (checked) this.selectedIds.push(cb.value);
            }
        });
    }
}"
      class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 w-full">

      <!-- Filtrage + Colonnes -->
      <div
            class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 px-6 py-4 border-b dark:border-gray-700 relative z-20">
            <!-- Filtre -->
            <div class="flex items-center gap-2 w-full sm:w-auto">
                  <input type="text" x-model="search" placeholder="Rechercher ...."
                        class="w-full sm:w-96 px-3 py-2 border rounded-md dark:bg-gray-700 dark:text-gray-200 dark:border-gray-500 outline-none focus:ring-indigo-500 focus:border-indigo-500" />
            </div>

            <!-- Toggle colonnes -->
            <div class="relative" x-data="{ open: false }">
                  <button @click="open = !open"
                        class="px-3 py-2 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-500 rounded-md text-sm font-medium">
                        <i class="fas fa-filter text-indigo-500 me-2"></i>
                        Filtrer les colonnes
                  </button>
                  <div x-show="open" @click.outside="open = false"
                        class="absolute right-0 mt-2 bg-white w-72 dark:bg-gray-800 border dark:border-gray-700 rounded-lg shadow-lg p-2 z-9999">
                        <template x-for="(visible, col) in visibleColumns" :key="col">
                              <label class="flex items-center space-x-2 px-2 py-1">
                                    <input type="checkbox" x-model="visibleColumns[col]" class="check-custom">
                                    <span class="text-sm text-gray-700 dark:text-gray-200" x-text="col"></span>
                              </label>
                        </template>
                  </div>
            </div>
      </div>

      <!-- Table -->
      <div class="relative overflow-x-auto z-10" id="myTable">
            <x-table.table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                  {{ $slot }}
            </x-table.table>
      </div>
      <!-- Footer -->
      <div
            class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row justify-between items-center gap-4">
            {{ $footer ?? '' }}
      </div>
</div>
