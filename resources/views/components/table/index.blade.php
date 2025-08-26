@props(['columns' => [], 'footer' => null])

<div x-data="{
    visibleColumns: @js($columns),
    search: '',
    selectedIds: [],
    openDropdown: null,   // dropdown global ouvert
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
            class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 px-6 py-4 border-b dark:border-gray-700">
            <!-- Filtre -->
            <div class="flex items-center gap-2 w-full sm:w-auto">
                  <input type="text" x-model="search" placeholder="Filtrer les colonnes"
                        class="w-full sm:w-64 px-3 py-2 border rounded-lg dark:bg-gray-700 dark:text-gray-200" />
            </div>

            <!-- Toggle colonnes -->
            <div class="relative" x-data="{ open: false }">
                  <button @click="open = !open" class="px-3 py-2 bg-gray-100 dark:bg-gray-700 rounded-lg text-sm">
                        Colonnes
                  </button>
                  <div x-show="open" @click.outside="open = false"
                        class="absolute right-0 mt-2 bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-lg shadow-lg p-2 z-50">
                        <template x-for="(visible, col) in visibleColumns" :key="col">
                              <label class="flex items-center space-x-2 px-2 py-1">
                                    <input type="checkbox" x-model="visibleColumns[col]">
                                    <span class="text-sm text-gray-700 dark:text-gray-200" x-text="col"></span>
                              </label>
                        </template>
                  </div>
            </div>
      </div>

      <!-- Table -->
      <div class="relative overflow-x-auto z-10">
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
