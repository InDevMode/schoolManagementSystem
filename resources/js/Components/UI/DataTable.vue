<template>
    <div class="space-y-0">

        <!-- Toolbar : pagination HAUT + recherche + actions -->
        <div class="flex flex-col sm:flex-row sm:items-center gap-3 px-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-t-xl border-b-0">

            <!-- Pagination haut -->
            <div class="flex items-center gap-1 order-2 sm:order-1 flex-wrap">
                <button :disabled="currentPage<=1" class="px-2 py-1 rounded-lg text-xs font-medium transition-colors disabled:opacity-40 disabled:cursor-not-allowed hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300" @click="currentPage=1">«</button>
                <button :disabled="currentPage<=1" class="px-2 py-1 rounded-lg text-xs font-medium transition-colors disabled:opacity-40 disabled:cursor-not-allowed hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300" @click="currentPage--">‹</button>
                <template v-for="p in visiblePages" :key="p">
                    <span v-if="p==='...'" class="px-2 text-gray-400 text-xs">…</span>
                    <button v-else :class="['px-2.5 py-1 rounded-lg text-xs font-medium transition-colors', p===currentPage ? 'bg-primary-600 text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300']" @click="currentPage=p">{{ p }}</button>
                </template>
                <button :disabled="currentPage>=totalPages" class="px-2 py-1 rounded-lg text-xs font-medium transition-colors disabled:opacity-40 disabled:cursor-not-allowed hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300" @click="currentPage++">›</button>
                <button :disabled="currentPage>=totalPages" class="px-2 py-1 rounded-lg text-xs font-medium transition-colors disabled:opacity-40 disabled:cursor-not-allowed hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300" @click="currentPage=totalPages">»</button>
                <span class="text-xs text-gray-400 dark:text-gray-500 ml-1 whitespace-nowrap">{{ rangeFrom }}-{{ rangeTo }} / {{ filteredRows.length }}</span>
            </div>

            <!-- Recherche intégrée -->
            <div class="relative flex-1 max-w-xs order-1 sm:order-2">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input v-model="search" type="text" placeholder="Rechercher..." class="w-full pl-8 pr-4 py-1.5 text-xs rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500"/>
            </div>

            <div class="flex items-center gap-2 order-3 ml-auto flex-wrap">
                <!-- Actions sélection multiple -->
                <Transition enter-active-class="animate-fade-in" leave-active-class="transition duration-100 ease-in" leave-to-class="opacity-0">
                    <div v-if="selected.length>0" class="flex items-center gap-2">
                        <span class="text-xs font-medium text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20 px-2 py-1 rounded-full whitespace-nowrap">{{ selected.length }} sél.</span>
                        <button v-if="showResetPassword" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium rounded-lg bg-warning-50 dark:bg-warning-900/20 text-warning-700 dark:text-warning-300 hover:bg-warning-100 transition-colors" @click="confirmResetPasswords">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                            MDP
                        </button>
                        <button class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium rounded-lg bg-danger-50 dark:bg-danger-900/20 text-danger-700 dark:text-danger-300 hover:bg-danger-100 transition-colors" @click="confirmMultiDelete">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Supprimer
                        </button>
                        <button class="text-xs text-gray-400 hover:text-gray-600 transition-colors" @click="selected=[]">✕</button>
                    </div>
                </Transition>

                <!-- Lignes par page -->
                <select v-model="perPage" class="text-xs rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option v-for="n in [10,25,50,100]" :key="n" :value="n">{{ n }}/page</option>
                </select>

                <!-- Export -->
                <div v-if="exportable" class="relative">
                    <button class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors" @click="showExportMenu=!showExportMenu">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Export
                    </button>
                    <Transition enter-active-class="animate-slide-down" leave-active-class="transition duration-100 ease-in" leave-to-class="opacity-0 scale-95">
                        <div v-if="showExportMenu" class="absolute right-0 top-full mt-1 w-36 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-xl py-1 z-50">
                            <button class="w-full flex items-center gap-2 px-3 py-2 text-xs text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors" @click="exportData('xlsx')">
                                <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6z"/></svg>
                                Excel (.xlsx)
                            </button>
                            <button class="w-full flex items-center gap-2 px-3 py-2 text-xs text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors" @click="exportData('csv')">
                                <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6z"/></svg>
                                CSV (.csv)
                            </button>
                        </div>
                    </Transition>
                </div>

                <slot name="toolbar"/>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-b-xl">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-primary-600 dark:bg-primary-800">
                        <th v-if="selectable" class="w-10 px-3 py-3 sticky left-0 bg-primary-600 dark:bg-primary-800 z-10">
                            <input
                                type="checkbox"
                                :checked="allSelected"
                                :indeterminate="someSelected"
                                class="w-4 h-4 rounded border-white/50 cursor-pointer"
                                style="accent-color: white;"
                                @change="toggleAll"
                            />
                        </th>
                        <th class="w-10 px-3 py-3 text-left text-xs font-semibold text-white/80 uppercase tracking-wider">#</th>
                        <th
                            v-for="col in columns"
                            :key="col.key"
                            :class="['px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider whitespace-nowrap', col.sortable!==false ? 'cursor-pointer select-none hover:bg-primary-700 dark:hover:bg-primary-700 transition-colors' : '']"
                            @click="col.sortable!==false && toggleSort(col.key)"
                        >
                            <div class="flex items-center gap-1">
                                {{ col.label }}
                                <span v-if="col.sortable!==false" class="flex flex-col opacity-70">
                                    <svg :class="['w-2.5 h-2.5', sortKey===col.key&&sortDir==='asc'?'opacity-100':'opacity-40']" fill="currentColor" viewBox="0 0 24 24"><path d="M7 14l5-5 5 5z"/></svg>
                                    <svg :class="['w-2.5 h-2.5 -mt-1', sortKey===col.key&&sortDir==='desc'?'opacity-100':'opacity-40']" fill="currentColor" viewBox="0 0 24 24"><path d="M7 10l5 5 5-5z"/></svg>
                                </span>
                            </div>
                        </th>
                        <th v-if="$slots.actions" class="px-4 py-3 text-right text-xs font-semibold text-white uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                    <template v-if="loading">
                        <tr v-for="i in perPage" :key="i">
                            <td :colspan="totalCols" class="px-4 py-3">
                                <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"/>
                            </td>
                        </tr>
                    </template>
                    <template v-else-if="!paginatedRows.length">
                        <tr>
                            <td :colspan="totalCols" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center gap-2 text-gray-400 dark:text-gray-500">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                    <span class="text-sm font-medium">{{ search ? 'Aucun résultat pour "'+search+'"' : 'Aucune donnée' }}</span>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <template v-else>
                        <tr
                            v-for="(row,idx) in paginatedRows"
                            :key="rowKey ? row[rowKey] : idx"
                            :class="['transition-colors duration-100', selected.includes(rowId(row)) ? 'bg-primary-50 dark:bg-primary-900/10' : 'hover:bg-gray-50 dark:hover:bg-gray-700/50']"
                        >
                            <td v-if="selectable" class="px-3 py-2.5 sticky left-0 bg-inherit z-10">
                                <input
                                    type="checkbox"
                                    :checked="selected.includes(rowId(row))"
                                    class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 cursor-pointer"
                                    style="accent-color: #7c3aed;"
                                    @change="toggleRow(row)"
                                />
                            </td>
                            <td class="px-3 py-2.5 text-xs text-gray-400 dark:text-gray-500 font-mono">{{ (currentPage-1)*perPage+idx+1 }}</td>
                            <td v-for="col in columns" :key="col.key" :class="['px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300', col.cellClass]">
                                <slot :name="'cell-'+col.key" :row="row" :value="row[col.key]">{{ row[col.key] ?? '—' }}</slot>
                            </td>
                            <td v-if="$slots.actions" class="px-4 py-2.5 text-right">
                                <slot name="actions" :row="row" :index="idx"/>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Confirm Dialog -->
        <ConfirmDialog
            v-model="confirm.show"
            :title="confirm.title"
            :message="confirm.message"
            :confirm-label="confirm.confirmLabel"
            :variant="confirm.variant"
            @confirm="confirm.onConfirm(); confirm.show=false"
            @cancel="confirm.show=false"
        />
    </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import * as XLSX from 'xlsx';
import ConfirmDialog from './ConfirmDialog.vue';
import { useToast } from '@/Composables/useToast';

interface Column {
    key: string;
    label: string;
    sortable?: boolean;
    cellClass?: string;
    searchable?: boolean;
    exportFormat?: (v: unknown) => string;
}

interface Props {
    columns: Column[];
    rows: Record<string, unknown>[];
    rowKey?: string;
    loading?: boolean;
    selectable?: boolean;
    exportable?: boolean;
    exportFilename?: string;
    showResetPassword?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    loading: false,
    selectable: true,
    exportable: true,
    exportFilename: 'export',
    showResetPassword: false,
});

const emit = defineEmits<{
    delete: [ids: (string | number)[]];
    'reset-password': [ids: (string | number)[]];
    'selection-change': [ids: (string | number)[]];
}>();

const toast = useToast();

const search         = ref('');
const sortKey        = ref('');
const sortDir        = ref<'asc' | 'desc'>('asc');
const currentPage    = ref(1);
const perPage        = ref(10);
const selected       = ref<(string | number)[]>([]);
const showExportMenu = ref(false);

const confirm = ref({
    show: false, title: '', message: '',
    confirmLabel: 'Confirmer',
    variant: 'danger' as 'danger' | 'warning' | 'info',
    onConfirm: () => {},
});

const rowId = (row: Record<string, unknown>) =>
    props.rowKey ? (row[props.rowKey] as string | number) : JSON.stringify(row);

// Formater une valeur pour l'export — gère status 0/1
const formatExportValue = (col: Column, value: unknown): string => {
    if (col.exportFormat) return col.exportFormat(value);
    // Colonnes de statut : 1 → Actif, 0 → Inactif
    if (col.key === 'status' || col.key.endsWith('_status') || col.key.startsWith('status')) {
        if (value === 1 || value === '1') return 'Actif';
        if (value === 0 || value === '0') return 'Inactif';
    }
    if (value === null || value === undefined) return '';
    if (typeof value === 'boolean') return value ? 'Oui' : 'Non';
    return String(value);
};

// Données filtrées + triées
const filteredRows = computed(() => {
    let data = [...props.rows];
    if (search.value.trim()) {
        const q = search.value.toLowerCase();
        data = data.filter(row =>
            props.columns.some(col => {
                if (col.searchable === false) return false;
                const v = row[col.key];
                return v != null && String(v).toLowerCase().includes(q);
            })
        );
    }
    if (sortKey.value) {
        data.sort((a, b) => {
            const av = a[sortKey.value] ?? '';
            const bv = b[sortKey.value] ?? '';
            const cmp = String(av).localeCompare(String(bv), 'fr', { numeric: true });
            return sortDir.value === 'asc' ? cmp : -cmp;
        });
    }
    return data;
});

const totalPages    = computed(() => Math.max(1, Math.ceil(filteredRows.value.length / perPage.value)));
const paginatedRows = computed(() => {
    const s = (currentPage.value - 1) * perPage.value;
    return filteredRows.value.slice(s, s + perPage.value);
});
const rangeFrom  = computed(() => filteredRows.value.length === 0 ? 0 : (currentPage.value - 1) * perPage.value + 1);
const rangeTo    = computed(() => Math.min(currentPage.value * perPage.value, filteredRows.value.length));
const totalCols  = computed(() => props.columns.length + (props.selectable ? 1 : 0) + (props.$slots.actions ? 1 : 0) + 1);

const visiblePages = computed(() => {
    const total = totalPages.value;
    const cur   = currentPage.value;
    if (total <= 7) return Array.from({ length: total }, (_, i) => i + 1);
    const pages: (number | string)[] = [1];
    if (cur > 3) pages.push('...');
    for (let i = Math.max(2, cur - 1); i <= Math.min(total - 1, cur + 1); i++) pages.push(i);
    if (cur < total - 2) pages.push('...');
    pages.push(total);
    return pages;
});

const allSelected  = computed(() => paginatedRows.value.length > 0 && paginatedRows.value.every(r => selected.value.includes(rowId(r))));
const someSelected = computed(() => paginatedRows.value.some(r => selected.value.includes(rowId(r))) && !allSelected.value);

const toggleAll = () => {
    if (allSelected.value) {
        const ids = paginatedRows.value.map(rowId);
        selected.value = selected.value.filter(id => !ids.includes(id));
    } else {
        const ids = paginatedRows.value.map(rowId);
        selected.value = [...new Set([...selected.value, ...ids])];
    }
};

const toggleRow = (row: Record<string, unknown>) => {
    const id  = rowId(row);
    const idx = selected.value.indexOf(id);
    if (idx === -1) selected.value.push(id);
    else selected.value.splice(idx, 1);
};

watch(selected, val => emit('selection-change', val));
watch(search,   () => { currentPage.value = 1; });
watch(perPage,  () => { currentPage.value = 1; });

const toggleSort = (key: string) => {
    if (sortKey.value === key) sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc';
    else { sortKey.value = key; sortDir.value = 'asc'; }
};

const openConfirm = (opts: Partial<typeof confirm.value>) =>
    Object.assign(confirm.value, { show: true, ...opts });

const confirmMultiDelete = () => openConfirm({
    title:        'Supprimer la sélection',
    message:      `Voulez-vous vraiment supprimer ${selected.value.length} élément(s) ? Cette action est irréversible.`,
    confirmLabel: 'Supprimer',
    variant:      'danger',
    onConfirm:    () => {
        const ids = [...selected.value];
        emit('delete', ids);
        selected.value = [];
        toast.success(`${ids.length} élément(s) supprimé(s).`);
    },
});

const confirmResetPasswords = () => openConfirm({
    title:        'Réinitialiser les mots de passe',
    message:      `Réinitialiser le mot de passe de ${selected.value.length} utilisateur(s) ?`,
    confirmLabel: 'Réinitialiser',
    variant:      'warning',
    onConfirm:    () => {
        emit('reset-password', [...selected.value]);
        toast.success('Mots de passe réinitialisés.');
    },
});

// Méthode publique — confirmation suppression unique depuis le parent
const confirmDelete = (id: string | number, label = 'cet élément') => openConfirm({
    title:        'Supprimer',
    message:      `Voulez-vous vraiment supprimer ${label} ? Cette action est irréversible.`,
    confirmLabel: 'Supprimer',
    variant:      'danger',
    onConfirm:    () => {
        emit('delete', [id]);
        toast.success('Élément supprimé.');
    },
});

const exportData = (format: 'xlsx' | 'csv') => {
    showExportMenu.value = false;
    const dataToExport = selected.value.length > 0
        ? props.rows.filter(r => selected.value.includes(rowId(r)))
        : filteredRows.value;

    const exportRows = dataToExport.map(row =>
        Object.fromEntries(props.columns.map(col => [col.label, formatExportValue(col, row[col.key])]))
    );

    const ws = XLSX.utils.json_to_sheet(exportRows);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Données');
    const filename = `${props.exportFilename}_${new Date().toISOString().slice(0, 10)}`;
    XLSX.writeFile(wb, `${filename}.${format}`);
    toast.success(`Export ${format.toUpperCase()} téléchargé.`);
};

defineExpose({ confirmDelete, selected, toast });
</script>