<template>
    <div class="space-y-6">
        <div class="flex items-center gap-4">
            <Link href="/parent/my_student"
                  class="p-2 rounded-lg border border-gray-200 dark:border-gray-700 text-gray-500
                         hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 transition-colors flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </Link>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Présence de mon apprenant</h1>
                <p v-if="student" class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    {{ student.last_name }} {{ student.name }}
                </p>
            </div>
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
        <DataTable
            :columns="columns"
            :rows="attendance.data"
            row-key="id"
            export-filename="presences_apprenant"
            :selectable="false"
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

        <!-- Total -->
        <div class="flex items-center gap-6 px-4 py-3 bg-gray-50 dark:bg-gray-800/60 rounded-lg border border-gray-200 dark:border-gray-700 text-sm">
            <span class="text-gray-500 font-medium">Total :</span>
            <span class="text-success-600 font-semibold">Présent : {{ classStudent?.present ?? 0 }}</span>
            <span class="text-warning-600 font-semibold">En retard : {{ classStudent?.late ?? 0 }}</span>
            <span class="text-danger-600 font-semibold">Absent : {{ classStudent?.absent ?? 0 }}</span>
            <span class="text-info-600 font-semibold">Demi-journée : {{ classStudent?.half_day ?? 0 }}</span>
        </div>
    </div>
</template>

<script setup lang="ts">
import { DataTable, AppBadge } from '@/Components/UI';
import { Link } from '@inertiajs/vue3';

interface AttendanceRecord {
    [key: string]: unknown;
    id: number;
    class_name: string;
    attendance_type: string;
    attendance_date: string;
}

interface Student { id: number; name: string; last_name: string; }

defineProps<{
    student: Student | null;
    attendance: { data: AttendanceRecord[]; total: number; from: number; to: number; links: any[] };
    classStudent: Record<string, number> | null;
}>();

const columns = [
    { key: 'class_name',      label: 'Classe' },
    { key: 'attendance_type', label: 'Statut' },
    { key: 'attendance_date', label: 'Date' },
];

const typeLabel = (type: string) => ({ present: 'Présent', late: 'En retard', absent: 'Absent', half_day: 'Demi-journée' }[type] ?? type);
const typeVariant = (type: string) => ({ present: 'success', late: 'warning', absent: 'danger', half_day: 'info' }[type] as any ?? 'gray');
const formatDate = (d: string) => new Date(d).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' });
</script>
