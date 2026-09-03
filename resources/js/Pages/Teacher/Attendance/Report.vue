<template>
    <div class="space-y-6">
        <PageHeader title="Rapport de Présences" subtitle="Historique des Présences de vos classes" color="cyan">
            <template #icon>
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </template>
        </PageHeader>

        <div class="card overflow-hidden">
            <DataTable
                :columns="columns"
                :rows="attendance.data"
                row-key="id"
                export-filename="rapport-presences"
            >
                <template #cell-student_name="{ row }">
                    {{ row.student_last_name }} {{ row.student_name }}
                </template>
                <template #cell-attendance_type="{ row }">
                    <AppBadge :variant="typeVariant(row.attendance_type as string | number)">
                        {{ typeLabel(row.attendance_type as string | number) }}
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
import { fmtDate } from '@/Utils/dateFormat';
import { PageHeader, AppBadge, DataTable } from '@/Components/UI';

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
    { key: 'attendance_type', label: 'Statut', exportFormat: (v: unknown) => {
        const legacyMap: Record<string, string> = { '1': 'present', '2': 'late', '3': 'absent', '4': 'half_day', '0': 'present' };
        const normalized = legacyMap[String(v ?? '').trim()] ?? String(v ?? '').trim();
        return { present: 'Présent', late: 'En retard', absent: 'Absent', half_day: 'Demi-journée' }[normalized] ?? String(v ?? '—');
    } },
    { key: 'attendance_date', label: 'Date' },
];

// Normalise les anciens entiers (1,2,3,4) et les nouvelles chaînes vers une clé uniforme
const normalizeType = (type: string | number): string => {
    const legacyMap: Record<string, string> = {
        '1': 'present',
        '2': 'late',
        '3': 'absent',
        '4': 'half_day',
        '0': 'present',
    };
    const str = String(type ?? '').trim();
    return legacyMap[str] ?? str;
};

const typeLabel = (type: string | number) => ({
    present:  'Présent',
    late:     'En retard',
    absent:   'Absent',
    half_day: 'Demi-journée',
}[normalizeType(type)] ?? String(type));

const typeVariant = (type: string | number): 'success' | 'warning' | 'danger' | 'info' => ({
    present:  'success',
    late:     'warning',
    absent:   'danger',
    half_day: 'info',
}[normalizeType(type)] as any ?? 'gray');

const formatDate = fmtDate;
</script>
