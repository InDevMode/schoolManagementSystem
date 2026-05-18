<template>
    <div class="space-y-6">
        <div class="flex items-center gap-3">
            <a href="/admin/practicalworks/homework/list" class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Soumissions ({{ homeworks.data.length }})</h1>
        </div>

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
                <AppBadge variant="success" dot>{{ row.status }}</AppBadge>
            </template>
            <template #cell-document_file="{ row }">
                <a v-if="row.document_file" :href="`/upload/homeworks/${row.document_file}`" target="_blank" class="text-primary-600 hover:underline text-xs">Voir</a>
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
import { computed } from 'vue';
import { DataTable, AppBadge } from '@/Components/UI';
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
    { key: 'status',        label: 'Statut' },
    { key: 'description',   label: 'Description' },
    { key: 'document_file', label: 'Document' },
    { key: 'created_at',    label: 'Date' },
];

const formatDate = (d: string) => {
    if (!d) return '—';
    try { return new Date(d).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' }); }
    catch { return d; }
};
</script>
