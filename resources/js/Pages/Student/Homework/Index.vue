<template>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Mes travaux de maison</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ works.total }} travail(aux)</p>
        </div>

        <DataTable
            :columns="columns"
            :rows="tableRows"
            row-key="id"
            export-filename="mes_devoirs"
            :selectable="false"
        >
            <template #cell-description="{ row }">
                <span class="line-clamp-2 text-sm text-gray-600 dark:text-gray-400">{{ stripHtml(row.description as string, 100) }}</span>
            </template>
            <template #cell-homework_status="{ row }">
                <AppBadge v-if="row.homework_status" :variant="row.homework_status === 'submitted' ? 'success' : 'warning'" dot>
                    {{ row.homework_status === 'submitted' ? 'Soumis' : 'En attente' }}
                </AppBadge>
                <AppBadge v-else variant="danger" dot>Non soumis</AppBadge>
            </template>
            <template #actions="{ row }">
                <a
                    :href="`/student/my_homework/submission/${row.id}`"
                    class="p-1.5 rounded-lg text-gray-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors inline-flex"
                    title="Soumettre"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                </a>
            </template>
        </DataTable>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { DataTable, AppBadge } from '@/Components/UI';
import { stripHtml } from '@/Utils/html';

interface Work {
    [key: string]: unknown;
    id: number;
    class_name: string;
    subject_name: string;
    work_date: string;
    submission_date: string;
    description: string;
    homework_status?: string;
}

const props = defineProps<{
    works: { data: Work[]; total: number; from: number; to: number; links: any[] };
}>();

const tableRows = computed(() => props.works.data.map(w => ({
    ...w,
    description: stripHtml(w.description, 100),
})));

const columns = [
    { key: 'subject_name',    label: 'Matière' },
    { key: 'work_date',       label: 'Date' },
    { key: 'submission_date', label: 'Remise' },
    { key: 'description',     label: 'Description' },
    { key: 'homework_status', label: 'Statut' },
];
</script>
