<template>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Ma Présence</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Historique de vos présences</p>
        </div>

        <!-- Stats -->
        <div v-if="classStudent" class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="card p-4 text-center">
                <p class="text-2xl font-bold text-success-600">{{ classStudent.present ?? 0 }}</p>
                <p class="text-xs text-gray-500 mt-1">Présent</p>
            </div>
            <div class="card p-4 text-center">
                <p class="text-2xl font-bold text-warning-600">{{ classStudent.late ?? 0 }}</p>
                <p class="text-xs text-gray-500 mt-1">En retard</p>
            </div>
            <div class="card p-4 text-center">
                <p class="text-2xl font-bold text-danger-600">{{ classStudent.absent ?? 0 }}</p>
                <p class="text-xs text-gray-500 mt-1">Absent</p>
            </div>
            <div class="card p-4 text-center">
                <p class="text-2xl font-bold text-info-600">{{ classStudent.half_day ?? 0 }}</p>
                <p class="text-xs text-gray-500 mt-1">Demi-journée</p>
            </div>
        </div>

        <!-- Table -->
        <div class="card overflow-hidden">
            <DataTable
                :columns="columns"
                :rows="attendance.data"
                row-key="id"
                export-filename="mes-presences"
            >
                <template #cell-attendance_type="{ row }">
                    <AppBadge :variant="typeVariant(row.attendance_type as string)">
                        {{ typeLabel(row.attendance_type as string) }}
                    </AppBadge>
                </template>
                <template #cell-attendance_date="{ row }">
                    <span class="text-xs text-gray-500">{{ formatDate(row.attendance_date as string) }}</span>
                </template>
            </DataTable>
        </div>
    </div>
</template>

<script setup lang="ts">
import { AppBadge, DataTable } from '@/Components/UI';

interface AttendanceRecord {
    id: number;
    class_name: string;
    attendance_type: string;
    attendance_date: string;
}

defineProps<{
    attendance: {
        data: AttendanceRecord[];
        total: number;
        from: number;
        to: number;
        links: { url: string | null; label: string; active: boolean }[];
    };
    classStudent: Record<string, number> | null;
}>();

const columns = [
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
