<template>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Rapport de présences</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Historique des présences de vos classes</p>
        </div>

        <div class="card overflow-hidden">
            <AppTable :columns="columns" :rows="attendance.data" :pagination="attendance" row-key="id">
                <template #cell-student_name="{ row }">
                    {{ row.student_last_name }} {{ row.student_name }}
                </template>
                <template #cell-attendance_type="{ row }">
                    <AppBadge :variant="typeVariant(row.attendance_type)">
                        {{ typeLabel(row.attendance_type) }}
                    </AppBadge>
                </template>
                <template #cell-attendance_date="{ row }">
                    <span class="text-xs text-gray-500">{{ formatDate(row.attendance_date) }}</span>
                </template>
            </AppTable>
        </div>
    </div>
</template>

<script setup lang="ts">
import { AppTable, AppBadge } from '@/Components/UI';

interface AttendanceRecord {
    id: number;
    student_name: string;
    student_last_name: string;
    class_name: string;
    attendance_type: string;
    attendance_date: string;
}

defineProps<{
    classes: { id: number; class_name: string }[];
    attendance: {
        data: AttendanceRecord[];
        total: number;
        from: number;
        to: number;
        links: { url: string | null; label: string; active: boolean }[];
    };
}>();

const columns = [
    { key: 'student_name',    label: 'Apprenant' },
    { key: 'class_name',      label: 'Classe' },
    { key: 'attendance_type', label: 'Statut' },
    { key: 'attendance_date', label: 'Date' },
];

const typeLabel = (type: string) => ({
    present:  'Présent',
    late:     'En retard',
    absent:   'Absent',
    half_day: 'Demi-journée',
}[type] ?? type);

const typeVariant = (type: string) => ({
    present:  'success',
    late:     'warning',
    absent:   'danger',
    half_day: 'info',
}[type] as any ?? 'gray');

const formatDate = (d: string) =>
    new Date(d).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' });
</script>
