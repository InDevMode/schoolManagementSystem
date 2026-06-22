<template>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                Devoirs de {{ student?.last_name }} {{ student?.name }}
            </h1>
        </div>

        <DataTable
            :columns="columns"
            :rows="works.data ?? []"
            row-key="id"
            export-filename="devoirs_apprenant"
            :selectable="false"
        >
            <template #cell-homework_status="{ row }">
                <AppBadge :variant="row.homework_status === 'submitted' ? 'success' : 'warning'" dot>
                    {{ row.homework_status === 'submitted' ? 'Soumis' : 'En attente' }}
                </AppBadge>
            </template>
        </DataTable>
    </div>
</template>

<script setup lang="ts">
import { DataTable, AppBadge } from '@/Components/UI';

defineProps<{ works: any; student: any }>();

const columns = [
    { key: 'subject_name',    label: 'Matière' },
    { key: 'title',           label: 'Titre' },
    { key: 'due_date',        label: 'Date limite' },
    { key: 'homework_status', label: 'Statut', exportFormat: (v: unknown) => (v === 'submitted' ? 'Soumis' : 'En attente') },
];
</script>
