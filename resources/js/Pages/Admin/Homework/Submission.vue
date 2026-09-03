<template>
    <div class="space-y-6">
        <PageHeader :title="`Soumissions (${homeworks.data.length})`" subtitle="Liste des soumissions des apprenants" color="indigo">
            <template #icon>
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </template>
            <template #actions>
                <Link href="/admin/practicalworks/reports"
                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-150
                           text-white bg-gray-500 hover:bg-gray-600 active:bg-gray-700
                           shadow-sm shadow-gray-200 dark:shadow-gray-900/40">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Retour
                </Link>
            </template>
        </PageHeader>

        <DataTable
            :columns="columns"
            :rows="tableRows"
            row-key="id"
            export-filename="soumissions"
            :selectable="false"
        >
            <template #cell-student="{ row }">
                <span class="font-medium text-gray-900 dark:text-white">{{ row.student_last_name }} {{ row.student_name }}</span>
            </template>
            <template #cell-status="{ row }">
                <AppBadge :variant="statusVariant(row.status as string)" dot>{{ statusLabel(row.status as string) }}</AppBadge>
            </template>
            <template #cell-document_file="{ row }">
                    <div v-if="row.document_file" class="flex items-center gap-1.5">
                        <a
                            :href="`/upload/homeworks/${row.document_file}`"
                            target="_blank"
                            rel="noopener noreferrer"
                            title="Voir le document"
                            class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-xs font-medium transition-all duration-150
                                   text-white bg-violet-500 hover:bg-violet-600 active:bg-violet-700
                                   shadow-sm shadow-violet-200 dark:shadow-violet-900/40"
                        >
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Voir
                        </a>
                        <a
                            :href="`/upload/homeworks/${row.document_file}`"
                            :download="row.document_file"
                            title="Télécharger le document"
                            class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-xs font-medium transition-all duration-150
                                   text-white bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700
                                   shadow-sm shadow-emerald-200 dark:shadow-emerald-900/40"
                        >
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Télécharger
                        </a>
                    </div>
                    <span v-else class="text-gray-400 text-xs">—</span>
                </template>
            <template #cell-created_at="{ row }">
                <span class="text-xs text-gray-500">{{ formatDate(row.created_at as string) }}</span>
            </template>
        </DataTable>

        <div v-if="!homeworks.data.length" class="card p-8 text-center text-gray-500 dark:text-gray-400">
            Aucune soumission pour ce travail.
        </div>
    </div>
</template>

<script setup lang="ts">
import { fmtDate } from '@/Utils/dateFormat';
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { PageHeader, DataTable, AppBadge } from '@/Components/UI';
import { stripHtml } from '@/Utils/html';

interface HomeworkSubmission {
    [key: string]: unknown;
    id: number;
    student_name: string;
    student_last_name: string;
    status: string;
    document_file: string | null;
    description: string;
    created_at: string;
}

const props = defineProps<{
    homeworks: { data: HomeworkSubmission[]; total: number; from: number; to: number; links: any[] };
    workId: number;
}>();

const tableRows = computed(() => props.homeworks.data.map(h => ({
    ...h,
    description: stripHtml(h.description, 80),
})));

const columns = [
    { key: 'student',       label: 'Apprenant' },
    { key: 'status',        label: 'Statut', exportFormat: (v: unknown) => ({ hold: 'En attente', submitted: 'Soumis', done: 'Fait', processed: 'Traité', resolved: 'Résolu' }[v as string] ?? String(v ?? '—')) },
    { key: 'description',   label: 'Description' },
    { key: 'document_file', label: 'Document' },
    { key: 'created_at',    label: 'Date' },
];

const formatDate = fmtDate;

const statusVariant = (s: string) => ({ hold: 'warning', submitted: 'success', done: 'primary', processed: 'secondary', resolved: 'success' }[s] ?? 'warning') as any;
const statusLabel   = (s: string) => ({ hold: 'En attente', submitted: 'Soumis', done: 'Fait', processed: 'Traité', resolved: 'Résolu' }[s] ?? s);
</script>
