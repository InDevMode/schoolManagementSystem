<template>
    <div class="space-y-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
            Devoirs de {{ student?.last_name }} {{ student?.name }}
        </h1>
        <div class="card overflow-hidden">
            <AppTable :columns="columns" :rows="works.data ?? []" row-key="id">
                <template #cell-status="{ row }">
                    <AppBadge :variant="row.homework_status === 'submitted' ? 'success' : 'warning'">
                        {{ row.homework_status === 'submitted' ? 'Soumis' : 'En attente' }}
                    </AppBadge>
                </template>
            </AppTable>
        </div>
    </div>
</template>
<script setup lang="ts">
import { AppTable, AppBadge } from '@/Components/UI';
defineProps<{ works: any; student: any }>();
const columns = [
    { key: 'subject_name', label: 'Matière' },
    { key: 'title', label: 'Titre' },
    { key: 'due_date', label: 'Date limite' },
    { key: 'status', label: 'Statut' },
];
</script>
