<template>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Congés du personnel</h1>
                <div class="flex items-center gap-3 mt-1">
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ leaves.total }} demande(s)</p>
                    <span v-if="pendingCount > 0"
                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-warning-100 dark:bg-warning-900/30 text-warning-700 dark:text-warning-400">
                        {{ pendingCount }} en attente
                    </span>
                </div>
            </div>
            <AppButton @click="showCreate = true">
                <template #icon>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </template>
                Nouvelle demande
            </AppButton>
        </div>

        <!-- Filtres -->
        <div class="flex flex-wrap gap-3">
            <AppSelect v-model="filters.status" :options="statusFilterOpts" placeholder="Tous les statuts" class="w-44" @change="applyFilters"/>
            <AppSelect v-model="filters.staff_id" :options="staffOptions" placeholder="Tout le personnel" class="w-56" @change="applyFilters"/>
        </div>

        <!-- Table -->
        <DataTable :columns="columns" :rows="leaves.data" row-key="id" :pagination="leaves">
            <template #cell-name="{ row }">
                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ row.last_name }} {{ row.first_name }}</p>
                <p class="text-xs text-gray-400">{{ roleLabels[row.staff_role] ?? row.staff_role }}</p>
            </template>
            <template #cell-leave_type_name="{ row }">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold"
                    :style="{ background: (row.leave_type_color ?? '#6366f1') + '20', color: row.leave_type_color ?? '#6366f1' }">
                    {{ row.leave_type_name }}
                </span>
            </template>
            <template #cell-duration="{ row }">
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    {{ formatDate(row.start_date) }}
                    <span v-if="row.end_date"> → {{ formatDate(row.end_date) }}</span>
                    <span v-else class="text-warning-500 italic"> (durée indéterminée)</span>
                </span>
            </template>
            <template #cell-status="{ row }">
                <AppBadge :variant="statusVariant(row.status)" dot>{{ statusLabel(row.status) }}</AppBadge>
            </template>
            <template #actions="{ row }">
                <div class="flex items-center justify-end gap-1">
                    <template v-if="row.status === 'pending'">
                        <button
                            class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-success-50 dark:bg-success-900/20 text-success-700 dark:text-success-400 hover:bg-success-100 transition-colors"
                            @click="approveLeave(row, 'approved')">
                            Approuver
                        </button>
                        <button
                            class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-danger-50 dark:bg-danger-900/20 text-danger-700 dark:text-danger-400 hover:bg-danger-100 transition-colors"
                            @click="approveLeave(row, 'rejected')">
                            Rejeter
                        </button>
                    </template>
                    <button
                        class="p-1.5 rounded-lg text-gray-400 hover:text-danger-600 hover:bg-danger-50 dark:hover:bg-danger-900/20 transition-colors"
                        @click="deleteLeave(row.id)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </template>
        </DataTable>

        <!-- Modal créer -->
        <AppModal v-model="showCreate" title="Nouvelle demande de congé" size="md">
            <form id="leave-form" @submit.prevent="submitLeave" class="space-y-4">
                <AppSelect v-model="leaveForm.staff_id"      label="Membre du personnel" :options="staffOptions.slice(1)" required :error="leaveForm.errors.staff_id"/>
                <AppSelect v-model="leaveForm.leave_type_id" label="Type de congé"       :options="leaveTypeOptions"     required :error="leaveForm.errors.leave_type_id"/>
                <div class="grid grid-cols-2 gap-4">
                    <AppInput v-model="leaveForm.start_date" label="Date de début" type="date" required/>
                    <AppInput v-model="leaveForm.end_date"   label="Date de fin (vide = durée indéterminée)" type="date"/>
                </div>
                <AppInput v-model="leaveForm.reason" label="Motif" placeholder="Raison du congé..."/>
            </form>
            <template #footer>
                <AppButton variant="ghost" @click="showCreate = false">Annuler</AppButton>
                <AppButton type="submit" form="leave-form" :loading="leaveForm.processing">Enregistrer</AppButton>
            </template>
        </AppModal>

        <!-- Modal approuver/rejeter -->
        <AppModal v-model="showApprove" :title="approveAction === 'approved' ? 'Approuver le congé' : 'Rejeter le congé'" size="sm">
            <div class="space-y-3">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ approveAction === 'approved' ? 'Confirmer l\'approbation de ce congé ?' : 'Rejeter cette demande ?' }}
                </p>
                <AppInput v-model="adminNote" label="Note (optionnel)" placeholder="Raison du rejet ou commentaire..."/>
            </div>
            <template #footer>
                <AppButton variant="ghost" @click="showApprove = false">Annuler</AppButton>
                <AppButton :variant="approveAction === 'approved' ? 'success' : 'danger'" :loading="approving" @click="confirmApprove">
                    {{ approveAction === 'approved' ? 'Approuver' : 'Rejeter' }}
                </AppButton>
            </template>
        </AppModal>
    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { AppButton, AppInput, AppSelect, AppModal, AppBadge, DataTable } from '@/Components/UI';
import { useToast } from '@/Composables/useToast';

const toast = useToast();

const props = defineProps<{
    leaves:       { data: any[]; total: number; from: number; to: number; links: any[] };
    leaveTypes:   { id: number; name: string; color: string }[];
    staff:        any[];
    pendingCount: number;
}>();

const roleLabels: Record<string, string> = {
    teacher: 'Professeur', director: 'Directeur', accountant: 'Comptable',
    supervisor: 'Surveillant', secretary: 'Secrétaire', librarian: 'Bibliothécaire', other: 'Autre',
};

const showCreate  = ref(false);
const showApprove = ref(false);
const approving   = ref(false);
const approveTarget = ref<any>(null);
const approveAction = ref<'approved' | 'rejected'>('approved');
const adminNote     = ref('');
const filters = ref({ status: '', staff_id: '' });

const staffOptions = computed(() => [
    { value: '', label: 'Tout le personnel' },
    ...props.staff.map(s => ({ value: String(s.id), label: `${s.last_name} ${s.name}` })),
]);
const leaveTypeOptions = computed(() =>
    props.leaveTypes.map(t => ({ value: String(t.id), label: t.name }))
);

const statusFilterOpts = [
    { value: '',         label: 'Tous les statuts' },
    { value: 'pending',  label: 'En attente' },
    { value: 'approved', label: 'Approuvé' },
    { value: 'rejected', label: 'Rejeté' },
];

const leaveForm = useForm({
    staff_id:      '',
    leave_type_id: '',
    start_date:    '',
    end_date:      '',
    reason:        '',
});

const columns = [
    { key: 'name',            label: 'Membre' },
    { key: 'leave_type_name', label: 'Type' },
    { key: 'duration',        label: 'Période' },
    { key: 'status',          label: 'Statut' },
];

const submitLeave = () => {
    leaveForm.post('/admin/staff/leaves/add', {
        onSuccess: () => { showCreate.value = false; toast.success('Demande enregistrée.'); },
    });
};

const approveLeave = (row: any, action: 'approved' | 'rejected') => {
    approveTarget.value = row;
    approveAction.value = action;
    adminNote.value     = '';
    showApprove.value   = true;
};

const confirmApprove = () => {
    if (!approveTarget.value) return;
    approving.value = true;
    router.post(`/admin/staff/leaves/approve/${approveTarget.value.id}`, {
        status:     approveAction.value,
        admin_note: adminNote.value,
    }, {
        onFinish: () => { approving.value = false; showApprove.value = false; },
        onSuccess: () => toast.success(approveAction.value === 'approved' ? 'Congé approuvé.' : 'Demande rejetée.'),
    });
};

const deleteLeave = (id: number) => {
    if (!confirm('Supprimer cette demande de congé ?')) return;
    router.get(`/admin/staff/leaves/delete/${id}`, {}, {
        onSuccess: () => toast.success('Demande supprimée.'),
    });
};

const applyFilters = () => {
    router.get('/admin/staff/leaves/list', {
        status:   filters.value.status   || undefined,
        staff_id: filters.value.staff_id || undefined,
    }, { preserveState: true });
};

const statusVariant = (s: string): any => ({ pending: 'warning', approved: 'success', rejected: 'danger' }[s] ?? 'secondary');
const statusLabel   = (s: string) => ({ pending: 'En attente', approved: 'Approuvé', rejected: 'Rejeté' }[s] ?? s);
const formatDate    = (d: string) => d ? new Date(d).toLocaleDateString('fr-FR') : '—';
</script>
