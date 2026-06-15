<template>
    <div class="space-y-6">
        <div class="flex items-center gap-3">
            <Link href="/teacher/practicalworks/homework/list" class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            </Link>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Soumissions des apprenants</h1>
        </div>

        <div v-if="homeworks.data.length" class="card overflow-hidden">
            <DataTable
                :columns="columns"
                :rows="homeworks.data"
                row-key="id"
                export-filename="soumissions"
            >
                <template #cell-student="{ row }">
                    <span class="font-medium text-gray-900 dark:text-white">{{ row.student_last_name }} {{ row.student_name }}</span>
                </template>
                <template #cell-status="{ row }">
                    <AppBadge variant="success" dot>{{ statusLabel(row.status as string) }}</AppBadge>
                </template>
                <template #cell-document_file="{ row }">
                    <div v-if="row.document_file" class="flex items-center gap-1.5">
                        <!-- Voir : ouvre dans un nouvel onglet -->
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
                        <!-- Télécharger : force le download -->
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
                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ formatDate(row.created_at as string) }}</span>
                </template>
            </DataTable>
        </div>

        <div v-else class="card p-8 text-center text-gray-500 dark:text-gray-400">
            Aucune soumission pour ce travail.
        </div>
    </div>
</template>

<script setup lang="ts">
import { AppBadge, DataTable } from '@/Components/UI';
import { Link } from '@inertiajs/vue3';

interface HomeworkSubmission {
    id: number;
    student_name: string;
    student_last_name: string;
    status: string;
    document_file: string | null;
    description: string;
    created_at: string;
}

defineProps<{
    homeworks: {
        data: HomeworkSubmission[];
        total: number;
        from: number;
        to: number;
        links: { url: string | null; label: string; active: boolean }[];
    };
    workId: number;
}>();

const columns = [
    { key: 'student', label: 'Apprenant' },
    { key: 'status', label: 'Statut' },
    { key: 'description', label: 'Description' },
    { key: 'document_file', label: 'Document' },
    { key: 'created_at', label: 'Date' },
];

const statusLabels: Record<string, string> = {
    submitted: 'Soumis',
    pending:   'En attente',
    graded:    'Noté',
    late:      'En retard',
};

const statusLabel = (status: string): string =>
    statusLabels[status] ?? status;

const formatDate = (dateStr: string): string => {
    if (!dateStr) return '—';
    const d = new Date(dateStr);
    if (isNaN(d.getTime())) return dateStr;
    return d.toLocaleDateString('fr-FR', {
        day:   '2-digit',
        month: '2-digit',
        year:  'numeric',
        hour:  '2-digit',
        minute:'2-digit',
    });
};
</script>
