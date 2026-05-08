<template>
    <div class="space-y-6">
        <div class="flex items-center gap-3">
            <a href="/teacher/practicalworks/homework/list" class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Soumissions des apprenants</h1>
        </div>

        <div v-if="homeworks.data.length" class="card overflow-hidden">
            <AppTable :columns="columns" :rows="homeworks.data" :pagination="homeworks" row-key="id">
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
            </AppTable>
        </div>

        <div v-else class="card p-8 text-center text-gray-500 dark:text-gray-400">
            Aucune soumission pour ce travail.
        </div>
    </div>
</template>

<script setup lang="ts">
import { AppTable, AppBadge } from '@/Components/UI';

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
</script>
