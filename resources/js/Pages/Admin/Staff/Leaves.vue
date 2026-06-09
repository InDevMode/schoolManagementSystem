<template>
    <div class="space-y-6">

        <!-- En-tête -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Congés du personnel</h1>
                <div class="flex items-center gap-2 mt-1">
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ leaves.total }} demande(s)</p>
                    <span v-if="pendingCount > 0"
                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-warning-100 dark:bg-warning-900/30 text-warning-700 dark:text-warning-300">
                        <span class="w-1.5 h-1.5 rounded-full bg-warning-500 animate-pulse inline-block"/>
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
        <div class="card p-4">
            <div class="flex flex-row flex-wrap items-center gap-3">
                <div class="flex-1 min-w-[160px]">
                    <AppSelect v-model="filters.status" :options="statusFilterOpts" placeholder="Tous les statuts" @change="applyFilters"/>
                </div>
                <div class="flex-1 min-w-[200px]">
                    <AppSelect v-model="filters.staff_id" :options="staffOptions" placeholder="Tout le personnel" @change="applyFilters"/>
                </div>
                <button v-if="filters.status || filters.staff_id"
                    @click="filters = { status: '', staff_id: '' }; applyFilters()"
                    class="flex-shrink-0 px-3 py-2 rounded-lg text-xs font-medium
                           text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200
                           bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600
                           transition-colors whitespace-nowrap">
                    Réinitialiser
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/60">
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Membre</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Type</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Période</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Durée</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Statut</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                        <tr v-if="!leaves.data.length">
                            <td colspan="6" class="px-4 py-10 text-center text-sm text-gray-400 dark:text-gray-500">
                                Aucune demande de congé trouvée.
                            </td>
                        </tr>
                        <tr v-for="row in leaves.data" :key="row.id"
                            class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">

                            <!-- Membre -->
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full flex-shrink-0 flex items-center justify-center text-xs font-bold text-white"
                                        :style="{ background: avatarColor(row.last_name) }">
                                        {{ (row.last_name?.[0] ?? '?').toUpperCase() }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white leading-tight">
                                            {{ row.last_name }} {{ row.first_name }}
                                        </p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500 leading-tight">
                                            {{ roleLabels[row.staff_role] ?? row.staff_role }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <!-- Type -->
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold"
                                    :style="{ background: (row.leave_type_color ?? '#6366f1') + '22', color: row.leave_type_color ?? '#6366f1' }">
                                    <span class="w-1.5 h-1.5 rounded-full flex-shrink-0"
                                        :style="{ background: row.leave_type_color ?? '#6366f1' }"/>
                                    {{ row.leave_type_name }}
                                </span>
                            </td>

                            <!-- Période -->
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                {{ formatDate(row.start_date) }}
                                <span v-if="row.end_date" class="text-gray-400 dark:text-gray-500">
                                    → {{ formatDate(row.end_date) }}
                                </span>
                                <span v-else class="text-xs italic text-warning-500 dark:text-warning-400">
                                    (durée indéterminée)
                                </span>
                            </td>

                            <!-- Durée -->
                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                {{ row.end_date ? computeDays(row.start_date, row.end_date) + ' j.' : '—' }}
                            </td>

                            <!-- Statut -->
                            <td class="px-4 py-3">
                                <span :class="['inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold', statusClass(row.status)]">
                                    <span class="w-1.5 h-1.5 rounded-full flex-shrink-0" :class="statusDotClass(row.status)"/>
                                    {{ statusLabel(row.status) }}
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <template v-if="row.status === 'pending'">
                                        <!-- Approuver -->
                                        <button
                                            class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors
                                                   bg-success-50 dark:bg-success-900/20 text-success-600 dark:text-success-400
                                                   hover:bg-success-100 dark:hover:bg-success-900/40"
                                            title="Approuver"
                                            @click="openApprove(row, 'approved')">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                        <!-- Rejeter -->
                                        <button
                                            class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors
                                                   bg-danger-50 dark:bg-danger-900/20 text-danger-600 dark:text-danger-400
                                                   hover:bg-danger-100 dark:hover:bg-danger-900/40"
                                            title="Rejeter"
                                            @click="openApprove(row, 'rejected')">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </template>

                                    <!-- Note admin si déjà traité -->
                                    <button v-if="row.admin_note"
                                        class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors
                                               bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400
                                               hover:bg-primary-100 dark:hover:bg-primary-900/40"
                                        :title="'Note : ' + row.admin_note"
                                        @click="openNote(row)">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                        </svg>
                                    </button>

                                    <!-- Supprimer -->
                                    <button
                                        class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors
                                               bg-danger-50 dark:bg-danger-900/20 text-danger-400 dark:text-danger-500
                                               hover:bg-danger-100 dark:hover:bg-danger-900/40 hover:text-danger-600 dark:hover:text-danger-400"
                                        title="Supprimer"
                                        @click="openDelete(row)">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="leaves.last_page > 1"
                class="flex items-center justify-between px-4 py-3 border-t border-gray-100 dark:border-gray-700">
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ leaves.from }}–{{ leaves.to }} sur {{ leaves.total }} résultat(s)
                </p>
                <div class="flex items-center gap-1">
                    <!-- Précédent -->
                    <button
                        :disabled="!leaves.prev_page_url"
                        @click="leaves.prev_page_url && router.visit(leaves.prev_page_url, { preserveState: true })"
                        :class="[
                            'w-8 h-8 rounded-lg flex items-center justify-center text-xs transition-colors',
                            leaves.prev_page_url
                                ? 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'
                                : 'text-gray-300 dark:text-gray-600 cursor-not-allowed',
                        ]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>

                    <!-- Numéros de page -->
                    <template v-for="link in leaves.links.slice(1, -1)" :key="link.label">
                        <button
                            @click="link.url && router.visit(link.url, { preserveState: true })"
                            :class="[
                                'w-8 h-8 rounded-lg flex items-center justify-center text-xs font-medium transition-colors',
                                link.active
                                    ? 'bg-primary-600 text-white'
                                    : link.url
                                        ? 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'
                                        : 'text-gray-300 dark:text-gray-600 cursor-not-allowed',
                            ]">
                            {{ link.label }}
                        </button>
                    </template>

                    <!-- Suivant -->
                    <button
                        :disabled="!leaves.next_page_url"
                        @click="leaves.next_page_url && router.visit(leaves.next_page_url, { preserveState: true })"
                        :class="[
                            'w-8 h-8 rounded-lg flex items-center justify-center text-xs transition-colors',
                            leaves.next_page_url
                                ? 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'
                                : 'text-gray-300 dark:text-gray-600 cursor-not-allowed',
                        ]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- ── Modal : Nouvelle demande ───────────────────────────────────── -->
        <AppModal v-model="showCreate" title="Nouvelle demande de congé" size="md">
            <form id="leave-form" @submit.prevent="submitLeave" class="space-y-4">
                <AppSelect
                    v-model="leaveForm.staff_id"
                    label="Membre du personnel"
                    :options="staffSelectOptions"
                    required
                    :error="leaveForm.errors.staff_id"
                />
                <AppSelect
                    v-model="leaveForm.leave_type_id"
                    label="Type de congé"
                    :options="leaveTypeOptions"
                    required
                    :error="leaveForm.errors.leave_type_id"
                />
                <div class="grid grid-cols-2 gap-4">
                    <AppInput v-model="leaveForm.start_date" label="Date de début" type="date" required/>
                    <AppInput v-model="leaveForm.end_date"   label="Date de fin" type="date" hint="Laisser vide si durée indéterminée"/>
                </div>
                <AppInput v-model="leaveForm.reason" label="Motif" placeholder="Motif de la demande..."/>
            </form>
            <template #footer>
                <AppButton variant="ghost" @click="showCreate = false">Annuler</AppButton>
                <AppButton type="submit" form="leave-form" :loading="leaveForm.processing">
                    Enregistrer
                </AppButton>
            </template>
        </AppModal>

        <!-- ── Modal : Approuver / Rejeter ───────────────────────────────── -->
        <AppModal
            v-model="showApprove"
            :title="approveAction === 'approved' ? 'Approuver ce congé' : 'Rejeter cette demande'"
            size="sm"
            persistent>
            <div class="space-y-4">
                <!-- Résumé de la demande -->
                <div class="flex items-start gap-3 p-3 rounded-xl bg-gray-50 dark:bg-gray-700/40 border border-gray-200 dark:border-gray-600">
                    <div class="w-9 h-9 rounded-full flex-shrink-0 flex items-center justify-center text-xs font-bold text-white"
                        :style="{ background: avatarColor(approveTarget?.last_name ?? '') }">
                        {{ (approveTarget?.last_name?.[0] ?? '?').toUpperCase() }}
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ approveTarget?.last_name }} {{ approveTarget?.first_name }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            {{ approveTarget?.leave_type_name }} ·
                            {{ formatDate(approveTarget?.start_date) }}
                            <template v-if="approveTarget?.end_date"> → {{ formatDate(approveTarget?.end_date) }}</template>
                        </p>
                    </div>
                </div>

                <!-- Message contextuel -->
                <div :class="['flex items-start gap-2.5 p-3 rounded-xl text-xs', approveAction === 'approved'
                    ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-700/50'
                    : 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-700/50']">
                    <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path v-if="approveAction === 'approved'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span>
                        {{ approveAction === 'approved'
                            ? 'Cette action approuvera la demande et notifiera le membre du personnel.'
                            : 'Cette action rejettera la demande. Vous pouvez ajouter un commentaire ci-dessous.'
                        }}
                    </span>
                </div>

                <!-- Note admin -->
                <AppInput
                    v-model="adminNote"
                    label="Note administrative (optionnel)"
                    :placeholder="approveAction === 'approved' ? 'Commentaire pour l\'intéressé...' : 'Motif du rejet...'"
                />
            </div>
            <template #footer>
                <AppButton variant="ghost" @click="showApprove = false">Annuler</AppButton>
                <AppButton
                    :variant="approveAction === 'approved' ? 'success' : 'danger'"
                    :loading="approving"
                    @click="confirmApprove">
                    <template #icon>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path v-if="approveAction === 'approved'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </template>
                    {{ approveAction === 'approved' ? 'Confirmer l\'approbation' : 'Confirmer le rejet' }}
                </AppButton>
            </template>
        </AppModal>

        <!-- ── Modal : Supprimer ─────────────────────────────────────────── -->
        <AppModal v-model="showDelete" title="Supprimer la demande" size="sm" persistent>
            <div class="space-y-3">
                <div class="flex items-center gap-3 p-3 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700/50">
                    <div class="w-9 h-9 rounded-full bg-red-100 dark:bg-red-900/40 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-red-700 dark:text-red-300">Confirmation requise</p>
                        <p class="text-xs text-red-600 dark:text-red-400 mt-0.5">
                            La demande sera masquée. Le super administrateur peut la retrouver dans l'historique.
                        </p>
                    </div>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400 px-1">
                    Voulez-vous supprimer la demande de congé de
                    <span class="font-semibold text-gray-900 dark:text-white">
                        {{ deleteTarget?.last_name }} {{ deleteTarget?.first_name }}
                    </span> ?
                </p>
            </div>
            <template #footer>
                <AppButton variant="ghost" @click="showDelete = false">Annuler</AppButton>
                <AppButton variant="danger" :loading="deleting" @click="confirmDelete">
                    <template #icon>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </template>
                    Masquer la demande
                </AppButton>
            </template>
        </AppModal>

        <!-- ── Modal : Voir note admin ────────────────────────────────────── -->
        <AppModal v-model="showNote" title="Note administrative" size="sm">
            <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-700/40 border border-gray-200 dark:border-gray-600">
                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                    {{ noteTarget?.admin_note ?? '—' }}
                </p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">
                    Ajoutée par {{ noteTarget?.approver_name ?? 'Admin' }}
                </p>
            </div>
            <template #footer>
                <AppButton variant="ghost" @click="showNote = false">Fermer</AppButton>
            </template>
        </AppModal>

    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { AppButton, AppInput, AppSelect, AppModal } from '@/Components/UI';
import { useToast } from '@/Composables/useToast';

const toast = useToast();

// ── Props ─────────────────────────────────────────────────────────────────────
const props = defineProps<{
    leaves:       { data: any[]; total: number; from: number; to: number; last_page: number; prev_page_url: string|null; next_page_url: string|null; links: any[] };
    leaveTypes:   { id: number; name: string; color: string }[];
    staff:        any[];
    pendingCount: number;
}>();

// ── Labels ────────────────────────────────────────────────────────────────────
const roleLabels: Record<string, string> = {
    teacher: 'Professeur', director: 'Directeur', accountant: 'Comptable',
    supervisor: 'Surveillant', secretary: 'Secrétaire', librarian: 'Bibliothécaire', other: 'Autre',
};

// ── États locaux ──────────────────────────────────────────────────────────────
const showCreate  = ref(false);
const showApprove = ref(false);
const showDelete  = ref(false);
const showNote    = ref(false);
const approving   = ref(false);
const deleting    = ref(false);

const approveTarget = ref<any>(null);
const approveAction = ref<'approved' | 'rejected'>('approved');
const adminNote     = ref('');
const deleteTarget  = ref<any>(null);
const noteTarget    = ref<any>(null);

const filters = ref({ status: '', staff_id: '' });

// ── Options select ────────────────────────────────────────────────────────────
const staffOptions = computed(() => [
    { value: '', label: 'Tout le personnel' },
    ...props.staff.map(s => ({ value: String(s.id), label: `${s.last_name} ${s.name}` })),
]);

const staffSelectOptions = computed(() =>
    props.staff.map(s => ({ value: String(s.id), label: `${s.last_name} ${s.name}` }))
);

const leaveTypeOptions = computed(() =>
    props.leaveTypes.map(t => ({ value: String(t.id), label: t.name }))
);

const statusFilterOpts = [
    { value: '',         label: 'Tous les statuts' },
    { value: 'pending',  label: 'En attente' },
    { value: 'approved', label: 'Approuvé' },
    { value: 'rejected', label: 'Rejeté' },
];

// ── Formulaire création ───────────────────────────────────────────────────────
const leaveForm = useForm({
    staff_id:      '',
    leave_type_id: '',
    start_date:    '',
    end_date:      '',
    reason:        '',
});

const submitLeave = () => {
    leaveForm.post('/admin/staff/leaves/add', {
        onSuccess: () => { showCreate.value = false; toast.success('Demande enregistrée avec succès.'); },
        onError:   () => toast.error('Vérifiez les informations saisies.'),
    });
};

// ── Approuver / Rejeter ───────────────────────────────────────────────────────
const openApprove = (row: any, action: 'approved' | 'rejected') => {
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
        onFinish:  () => { approving.value = false; showApprove.value = false; },
        onSuccess: () => toast.success(
            approveAction.value === 'approved' ? 'Congé approuvé avec succès.' : 'Demande rejetée.'
        ),
        onError: () => toast.error('Une erreur est survenue.'),
    });
};

// ── Supprimer ─────────────────────────────────────────────────────────────────
const openDelete = (row: any) => {
    deleteTarget.value = row;
    showDelete.value   = true;
};

const confirmDelete = () => {
    if (!deleteTarget.value) return;
    deleting.value = true;
    router.get(`/admin/staff/leaves/delete/${deleteTarget.value.id}`, {}, {
        onFinish:  () => { deleting.value = false; showDelete.value = false; },
        onSuccess: () => toast.success('Demande supprimée.'),
        onError:   () => toast.error('Erreur lors de la suppression.'),
    });
};

// ── Voir note ─────────────────────────────────────────────────────────────────
const openNote = (row: any) => {
    noteTarget.value = row;
    showNote.value   = true;
};

// ── Filtres ───────────────────────────────────────────────────────────────────
const applyFilters = () => {
    router.get('/admin/staff/leaves/list', {
        status:   filters.value.status   || undefined,
        staff_id: filters.value.staff_id || undefined,
    }, { preserveState: true });
};

// ── Helpers visuels ───────────────────────────────────────────────────────────
const statusClass = (s: string) => ({
    pending:  'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300',
    approved: 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300',
    rejected: 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300',
}[s] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400');

const statusDotClass = (s: string) => ({
    pending:  'bg-amber-500',
    approved: 'bg-emerald-500',
    rejected: 'bg-red-500',
}[s] ?? 'bg-gray-400');

const statusLabel = (s: string) => ({
    pending: 'En attente', approved: 'Approuvé', rejected: 'Rejeté',
}[s] ?? s);

const formatDate = (d: string) =>
    d ? new Date(d).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' }) : '—';

const computeDays = (start: string, end: string) => {
    if (!start || !end) return '—';
    const diff = Math.abs(new Date(end).getTime() - new Date(start).getTime());
    return Math.ceil(diff / (1000 * 60 * 60 * 24)) + 1;
};

// Couleur avatar déterministe basée sur la première lettre
const avatarColors = ['#7B74F0','#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#06b6d4','#ec4899'];
const avatarColor = (name: string) => avatarColors[(name?.charCodeAt(0) ?? 0) % avatarColors.length];
</script>
