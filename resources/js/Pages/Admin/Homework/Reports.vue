<template>
    <div class="space-y-6">
        <!-- En-tête -->
        <PageHeader title="Rapports — Travaux de maison" :subtitle="`${homeworks.total} soumission(s)`" color="indigo">
            <template #icon>
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </template>
            <template #actions>
                <Link href="/admin/practicalworks/homework/list"
                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-150
                           text-white bg-gray-500 hover:bg-gray-600 active:bg-gray-700
                           shadow-sm shadow-gray-200 dark:shadow-gray-900/40">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Liste des travaux
                </Link>
            </template>
        </PageHeader>

        <!-- Info rôle -->
        <div v-if="!isSuperAdmin" class="flex items-start gap-3 bg-violet-50 dark:bg-violet-900/20 border border-violet-200 dark:border-violet-800 rounded-xl p-4">
            <svg class="w-5 h-5 text-violet-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-sm text-violet-700 dark:text-violet-300">
                Vous voyez toutes les soumissions. Vous pouvez consulter les travaux créés par d'autres administrateurs, mais seul leur créateur peut les modifier.
            </p>
        </div>

        <DataTable
            :columns="columns"
            :rows="tableRows"
            row-key="id"
            export-filename="rapports-devoirs"
            :selectable="false"
        >
            <template #cell-student="{ row }">
                <span class="font-medium text-gray-900 dark:text-white">
                    {{ row.student_last_name }} {{ row.student_name }}
                </span>
            </template>
            <template #cell-status="{ row }">
                <AppBadge
                    :variant="statusVariant(row.homework_status as string)"
                    dot
                >
                    {{ statusLabel(row.homework_status as string) }}
                </AppBadge>
            </template>
            <template #cell-document_file="{ row }">
                <a
                    v-if="row.document_file"
                    :href="`/upload/homeworks/${row.document_file}`"
                    target="_blank"
                    class="inline-flex items-center gap-1 text-xs text-primary-600 hover:text-primary-700 hover:underline font-medium"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Télécharger
                </a>
                <span v-else class="text-gray-400 text-xs">—</span>
            </template>
            <template #cell-created_at="{ row }">
                <span class="text-xs text-gray-500">{{ formatDate(row.created_at as string) }}</span>
            </template>
            <template #cell-actions="{ row }">
                <a
                    :href="`/admin/practicalworks/homework/submission/${row.work_id}`"
                    title="Voir toutes les soumissions de ce travail"
                    class="inline-flex items-center justify-center w-7 h-7 rounded-xl transition-all duration-150
                           text-white bg-indigo-500 hover:bg-indigo-600 active:bg-indigo-700
                           shadow-sm shadow-indigo-200 dark:shadow-indigo-900/40"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </a>
            </template>
        </DataTable>
    </div>
</template>

<script setup lang="ts">
import { fmtDate } from '@/utils/dateFormat';
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { PageHeader, AppBadge, AppButton, DataTable } from '@/Components/UI';
import { stripHtml } from '@/Utils/html';

interface HomeworkReport {
    [key: string]: unknown;
    id: number;
    work_id: number;
    student_name: string;
    student_last_name: string;
    class_name: string;
    subject_name: string;
    homework_status: string;
    document_file: string | null;
    created_at: string;
    created_by: number;
    homework_description: string;
}

const props = defineProps<{
    homeworks: {
        data: HomeworkReport[];
        total: number;
        from: number;
        to: number;
        links: { url: string | null; label: string; active: boolean }[];
    };
    creatorId: number;
    isSuperAdmin: boolean;
}>();

const tableRows = computed(() =>
    props.homeworks.data.map(h => ({
        ...h,
        homework_description: stripHtml(h.homework_description, 60),
    }))
);

const columns = [
    { key: 'student',              label: 'Apprenant' },
    { key: 'class_name',           label: 'Classe' },
    { key: 'subject_name',         label: 'Matière' },
    { key: 'status',               label: 'Statut', exportFormat: (v: unknown) => ({ hold: 'En attente', submitted: 'Soumis', done: 'Fait', processed: 'Traité', resolved: 'Résolu' }[v as string] ?? String(v ?? '—')) },
    { key: 'homework_description', label: 'Commentaire' },
    { key: 'document_file',        label: 'Document' },
    { key: 'created_at',           label: 'Soumis le' },
    { key: 'actions',              label: '' },
];

const statusVariant = (s: string) => {
    switch (s) {
        case 'submitted':  return 'success';
        case 'done':       return 'primary';
        case 'processed':  return 'secondary';
        case 'resolved':   return 'success';
        default:           return 'warning'; // hold
    }
};

const statusLabel = (s: string) => {
    const map: Record<string, string> = {
        hold:      'En attente',
        submitted: 'Soumis',
        done:      'Fait',
        processed: 'Traité',
        resolved:  'Résolu',
    };
    return map[s] ?? s;
};

const formatDate = fmtDate;
</script>
