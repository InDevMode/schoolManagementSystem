<template>
    <div class="space-y-6">
        <PageHeader title="Rapport de présences" subtitle="Historique des présences des apprenants" color="cyan">
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
                :actions="tableActions"
                @action="handleAction"
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

        <!-- Modal de confirmation de suppression -->
        <Teleport to="body">
            <Transition enter-active-class="transition duration-150 ease-out" enter-from-class="opacity-0"
                        enter-to-class="opacity-100" leave-active-class="transition duration-100 ease-in"
                        leave-from-class="opacity-100" leave-to-class="opacity-0">
                <div v-if="confirmDelete.show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md p-6 space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-white">Supprimer la présence</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                                    Cette action supprimera la présence de <strong>{{ confirmDelete.studentName }}</strong>
                                    du <strong>{{ confirmDelete.date }}</strong>.
                                    Vous pourrez ensuite la recréer depuis la saisie.
                                </p>
                            </div>
                        </div>
                        <div class="flex gap-2 justify-end pt-2">
                            <button @click="confirmDelete.show = false"
                                    class="px-4 py-2 text-sm font-medium rounded-lg border border-gray-200 dark:border-gray-600
                                           text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                Annuler
                            </button>
                            <button @click="doDelete" :disabled="deleting"
                                    class="px-4 py-2 text-sm font-medium rounded-lg bg-red-600 hover:bg-red-700 text-white
                                           disabled:opacity-60 transition-colors flex items-center gap-2">
                                <svg v-if="deleting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                                </svg>
                                Supprimer
                            </button>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, reactive } from 'vue';
import { router } from '@inertiajs/vue3';
import { PageHeader, AppBadge, DataTable } from '@/Components/UI';
import { useCan } from '@/Composables/useCan';

const { can } = useCan();

interface AttendanceRecord {
    id: number;
    student_name: string;
    student_last_name: string;
    class_name: string;
    attendance_type: string;
    attendance_date: string;
}

defineProps<{
    classes: { id: number; name: string }[];
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

const tableActions = computed(() => can('action.attendance.save') ? [
    {
        key: 'delete',
        label: 'Supprimer',
        icon: `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                       d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
               </svg>`,
        variant: 'danger' as const,
    },
] : []);

// ── Suppression ───────────────────────────────────────────────────────────────
const deleting = ref(false);
const confirmDelete = reactive({
    show:        false,
    id:          0,
    studentName: '',
    date:        '',
});

const handleAction = (key: string, row: Record<string, unknown>) => {
    if (key === 'delete') {
        confirmDelete.id          = row.id as number;
        confirmDelete.studentName = `${row.student_last_name} ${row.student_name}`;
        confirmDelete.date        = formatDate(row.attendance_date as string);
        confirmDelete.show        = true;
    }
};

const doDelete = async () => {
    deleting.value = true;
    const csrf = (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? '';
    try {
        const res = await fetch(`/admin/attendance/delete/${confirmDelete.id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
        });
        const data = await res.json();
        if (data.success) {
            confirmDelete.show = false;
            // Recharger la page pour rafraîchir le tableau
            router.reload({ only: ['attendance'] });
        }
    } finally {
        deleting.value = false;
    }
};

// ── Helpers ───────────────────────────────────────────────────────────────────
const normalizeType = (type: string | number): string => {
    const legacyMap: Record<string, string> = {
        '1': 'present', '2': 'late', '3': 'absent', '4': 'half_day', '0': 'present',
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

const formatDate = (d: string) =>
    new Date(d).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' });
</script>
