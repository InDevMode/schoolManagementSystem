<template>
    <div class="space-y-6">
        <PageHeader title="Journaux de suppression" subtitle="Historique de tous les éléments masqués" color="red">
            <template #icon>
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7"/>
                </svg>
            </template>
        </PageHeader>

        <!-- Filtres -->
        <div class="card p-4">
            <div class="flex flex-row flex-wrap items-center gap-3">
                <div class="flex-1 min-w-[160px]">
                    <AppSelect v-model="filters.table_name" :options="tableOptions" placeholder="Toutes les tables" @change="applyFilters"/>
                </div>
                <div class="flex-1 min-w-[150px]">
                    <AppInput v-model="filters.date_from" type="date" @change="applyFilters"/>
                </div>
                <div class="flex-1 min-w-[150px]">
                    <AppInput v-model="filters.date_to" type="date" @change="applyFilters"/>
                </div>
                <div class="flex-1 min-w-[200px]">
                    <AppInput v-model="filters.search" placeholder="Rechercher dans les raisons..." @input="applyFilters"/>
                </div>
                <button v-if="filters.table_name || filters.date_from || filters.date_to || filters.search"
                    @click="filters = { table_name: '', date_from: '', date_to: '', search: '' }; applyFilters()"
                    class="flex-shrink-0 px-3 py-2 rounded-lg text-xs font-medium
                           text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200
                           bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600
                           transition-colors whitespace-nowrap">
                    Réinitialiser
                </button>
            </div>
        </div>

        <!-- Table -->
        <DataTable :columns="columns" :rows="logs.data" row-key="id" :pagination="logs">
            <template #cell-table_name="{ row }">
                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-mono font-semibold bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                    {{ row.table_name }}
                </span>
            </template>
            <template #cell-deleter="{ row }">
                <p class="text-sm font-medium text-gray-900 dark:text-white">
                    {{ row.deleter_last_name }} {{ row.deleter_name }}
                </p>
            </template>
            <template #cell-deleted_at="{ row }">
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    {{ new Date(row.deleted_at).toLocaleString('fr-FR') }}
                </span>
            </template>
            <template #cell-reason="{ row }">
                <span class="text-sm text-gray-500 dark:text-gray-400 italic">{{ row.reason || '—' }}</span>
            </template>
            <template #actions="{ row }">
                <button class="p-1.5 rounded-lg text-gray-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors"
                    title="Voir les données supprimées"
                    @click="viewRecord(row)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
            </template>
        </DataTable>

        <!-- Modal détail -->
        <AppModal v-model="showDetail" title="Données supprimées" size="xl">
            <div v-if="selectedLog">
                <div class="flex items-center gap-3 mb-4 p-3 rounded-lg bg-danger-50 dark:bg-danger-900/20 border border-danger-100 dark:border-danger-800">
                    <svg class="w-5 h-5 text-danger-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-danger-700 dark:text-danger-400">
                            Enregistrement #{{ selectedLog.record_id }} masqué de <code class="font-mono">{{ selectedLog.table_name }}</code>
                        </p>
                        <p class="text-xs text-danger-600 dark:text-danger-500 mt-0.5">
                            Par {{ selectedLog.deleter_last_name }} {{ selectedLog.deleter_name }}
                            le {{ new Date(selectedLog.deleted_at).toLocaleString('fr-FR') }}
                        </p>
                    </div>
                </div>

                <div class="rounded-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="px-4 py-2 bg-gray-50 dark:bg-gray-800/60 border-b border-gray-100 dark:border-gray-700">
                        <p class="text-xs font-semibold text-gray-500 uppercase">Snapshot des données au moment de la suppression</p>
                    </div>
                    <div class="p-4 max-h-96 overflow-y-auto">
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <template v-for="(val, key) in selectedLog.record_data" :key="key">
                                <div v-if="!['password', 'remember_token'].includes(String(key))"
                                    class="flex flex-col gap-0.5">
                                    <dt class="text-[10px] font-semibold text-gray-400 uppercase font-mono">{{ key }}</dt>
                                    <dd class="text-sm text-gray-700 dark:text-gray-300 break-all">
                                        {{ val ?? '—' }}
                                    </dd>
                                </div>
                            </template>
                        </dl>
                    </div>
                </div>
            </div>
            <template #footer>
                <AppButton variant="ghost" @click="showDetail = false">Fermer</AppButton>
            </template>
        </AppModal>
    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { AppButton, AppInput, AppSelect, AppModal, DataTable } from '@/Components/UI';

const props = defineProps<{
    logs:       { data: any[]; total: number; from: number; to: number; links: any[] };
    tableNames: string[];
}>();

const showDetail  = ref(false);
const selectedLog = ref<any>(null);
const filters     = ref({ table_name: '', date_from: '', date_to: '', search: '' });

const tableOptions = computed(() => [
    { value: '', label: 'Toutes les tables' },
    ...props.tableNames.map(t => ({ value: t, label: t })),
]);

const columns = [
    { key: 'table_name', label: 'Table' },
    { key: 'record_id',  label: 'ID' },
    { key: 'deleter',    label: 'Supprimé par' },
    { key: 'deleted_at', label: 'Date' },
    { key: 'reason',     label: 'Raison' },
];

const viewRecord = (log: any) => {
    selectedLog.value = log;
    showDetail.value  = true;
};

const applyFilters = () => {
    router.get('/superadmin/deletion-logs', {
        table_name: filters.value.table_name || undefined,
        date_from:  filters.value.date_from  || undefined,
        date_to:    filters.value.date_to    || undefined,
        search:     filters.value.search     || undefined,
    }, { preserveState: true });
};
</script>
