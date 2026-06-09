<template>
    <div class="space-y-6">
        <!-- En-tête -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <AppButton variant="ghost" size="sm" href="/admin/practicalworks/homework/list">
                    <template #icon>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </template>
                    Retour
                </AppButton>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Corbeille — Travaux de maison</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ works.total }} travail(aux) supprimé(s)</p>
                </div>
            </div>
        </div>

        <!-- Alerte info -->
        <div class="flex items-start gap-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-4">
            <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-sm text-amber-700 dark:text-amber-300">
                Les travaux dans la corbeille ne sont plus visibles par les apprenants. Vous pouvez les restaurer à tout moment.
            </p>
        </div>

        <!-- Table -->
        <DataTable
            :columns="columns"
            :rows="tableRows"
            row-key="id"
            export-filename="corbeille_devoirs"
            :selectable="false"
        >
            <template #cell-deleted_at="{ row }">
                <span class="text-xs text-gray-500">{{ formatDate(row.deleted_at as string) }}</span>
            </template>
            <template #cell-description="{ row }">
                <span class="line-clamp-1 text-sm text-gray-500">{{ row.description }}</span>
            </template>
            <template #actions="{ row }">
                <div class="flex items-center justify-end gap-1">
                    <button
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium text-success-700 bg-success-50 hover:bg-success-100 dark:text-success-400 dark:bg-success-900/20 dark:hover:bg-success-900/40 transition-colors"
                        title="Restaurer ce travail"
                        @click="restore(row.id as number)"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                        </svg>
                        Restaurer
                    </button>
                </div>
            </template>
        </DataTable>

        <div v-if="!works.data.length" class="card p-12 text-center">
            <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            <p class="text-gray-500 dark:text-gray-400 font-medium">La corbeille est vide</p>
            <p class="text-sm text-gray-400 mt-1">Les travaux supprimés apparaîtront ici.</p>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { AppButton, DataTable } from '@/Components/UI';
import { stripHtml } from '@/Utils/html';
import { useToast } from '@/Composables/useToast';

interface TrashedWork {
    [key: string]: unknown;
    id: number;
    class_name: string;
    subject_name: string;
    work_date: string;
    submission_date: string;
    description: string;
    deleted_at: string | null;
    created_by_name: string;
}

const props = defineProps<{
    works: { data: TrashedWork[]; total: number; from: number; to: number; links: any[] };
}>();

const toast = useToast();

const tableRows = computed(() =>
    props.works.data.map(w => ({ ...w, description: stripHtml(w.description as string, 60) }))
);

const columns = [
    { key: 'class_name',      label: 'Classe' },
    { key: 'subject_name',    label: 'Matière' },
    { key: 'work_date',       label: 'Date' },
    { key: 'submission_date', label: 'Remise' },
    { key: 'description',     label: 'Description' },
    { key: 'deleted_at',      label: 'Supprimé le' },
];

const formatDate = (d: string) => {
    if (!d) return '—';
    try { return new Date(d).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' }); }
    catch { return d; }
};

const restore = (id: number) => {
    router.get(`/admin/practicalworks/homework/restore/${id}`, {}, {
        onSuccess: () => toast.success('Travail restauré avec succès.'),
        onError:   () => toast.error('Erreur lors de la restauration.'),
    });
};
</script>
