<template>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Rapports des travaux de maison</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ homeworks.total }} soumission(s)</p>
        </div>

        <div class="card overflow-hidden">
            <DataTable
                :columns="columns"
                :rows="homeworks.data"
                row-key="id"
                export-filename="rapports-devoirs"
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
        </div>
    </div>
</template>

<script setup lang="ts">
import { AppBadge, DataTable } from '@/Components/UI';

interface HomeworkReport {
    id: number;
    student_name: string;
    student_last_name: string;
    class_name: string;
    subject_name: string;
    status: string;
    document_file: string | null;
    created_at: string;
}

defineProps<{
    homeworks: {
        data: HomeworkReport[];
        total: number;
        from: number;
        to: number;
        links: { url: string | null; label: string; active: boolean }[];
    };
}>();

const columns = [
    { key: 'student', label: 'Apprenant' },
    { key: 'class_name', label: 'Classe' },
    { key: 'subject_name', label: 'Matière' },
    { key: 'status', label: 'Statut' },
    { key: 'document_file', label: 'Document' },
    { key: 'created_at', label: 'Date' },
];

const formatDate = (d: string) => {
    if (!d) return '—';
    try { return new Date(d).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' }); }
    catch { return d; }
};
</script>
