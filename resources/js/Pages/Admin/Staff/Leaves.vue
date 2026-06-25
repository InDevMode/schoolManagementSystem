<template>
    <div class="space-y-6">

        <!-- En-tête -->
        <PageHeader title="Congés du personnel" :subtitle="`${leaves.total} demande(s)${pendingCount > 0 ? ` · ${pendingCount} en attente` : ''}`" color="amber">
            <template #icon>
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </template>
            <template #actions>
                <AppButton @click="showCreate = true">
                    <template #icon>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </template>
                    Nouvelle demande
                </AppButton>
            </template>
        </PageHeader>

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
                    class="flex-shrink-0 px-3 py-2 rounded-xl text-xs font-medium
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
                    <thead class="sticky top-0 z-10">
                        <tr class="border-b-2 border-primary-700/40"
                            style="background: linear-gradient(135deg, #7B74F0, #9189f5);">
                            <th class="px-4 py-2.5 text-left text-[12px] font-bold uppercase tracking-wider whitespace-nowrap text-white">Membre</th>
                            <th class="px-4 py-2.5 text-left text-[12px] font-bold uppercase tracking-wider whitespace-nowrap text-white">Type de congé</th>
                            <th class="px-4 py-2.5 text-left text-[12px] font-bold uppercase tracking-wider whitespace-nowrap text-white">Période</th>
                            <th class="px-4 py-2.5 text-center text-[12px] font-bold uppercase tracking-wider whitespace-nowrap text-white">Durée</th>
                            <th class="px-4 py-2.5 text-center text-[12px] font-bold uppercase tracking-wider whitespace-nowrap text-white">Statut</th>
                            <th class="px-4 py-2.5 text-right text-[12px] font-bold uppercase tracking-wider whitespace-nowrap text-white pr-5">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
                        <!-- Empty state -->
                        <tr v-if="!leaves.data.length">
                            <td colspan="6" class="px-5 py-14 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-14 h-14 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                        <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Aucune demande de congé trouvée</p>
                                </div>
                            </td>
                        </tr>

                        <tr v-for="row in leaves.data" :key="row.id"
                            class="group hover:bg-primary-50/40 dark:hover:bg-primary-900/10 transition-colors">

                            <!-- Membre -->
                            <td class="px-4 py-1.5">
                                <div class="flex items-center gap-2 min-w-[160px]">
                                    <div class="w-7 h-7 rounded-full flex-shrink-0 flex items-center justify-center text-xs font-bold text-white shadow-sm"
                                        :style="{ background: avatarColor(row.last_name) }">
                                        {{ (row.last_name?.[0] ?? '?').toUpperCase() }}
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-gray-900 dark:text-white leading-snug">
                                            {{ row.last_name }} {{ row.first_name }}
                                        </p>
                                        <p class="text-[10px] text-gray-400 dark:text-gray-500 leading-snug">
                                            {{ roleLabels[row.staff_role] ?? row.staff_role }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <!-- Type -->
                            <td class="px-4 py-1.5">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold border"
                                    :style="{
                                        background: (row.leave_type_color ?? '#6366f1') + '18',
                                        color: row.leave_type_color ?? '#6366f1',
                                        borderColor: (row.leave_type_color ?? '#6366f1') + '40',
                                    }">
                                    <span class="w-2 h-2 rounded-full flex-shrink-0"
                                        :style="{ background: row.leave_type_color ?? '#6366f1' }"/>
                                    {{ row.leave_type_name }}
                                </span>
                            </td>

                            <!-- Période -->
                            <td class="px-5 py-2 min-w-[200px]">
                                <div class="flex items-center gap-1.5 text-sm text-gray-700 dark:text-gray-300">
                                    <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="font-medium">{{ formatDate(row.start_date) }}</span>
                                    <template v-if="row.end_date">
                                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5-5 5M6 12h12"/>
                                        </svg>
                                        <span class="font-medium">{{ formatDate(row.end_date) }}</span>
                                    </template>
                                    <span v-else class="text-xs italic text-amber-500 dark:text-amber-400 font-normal">
                                        (durée indéterminée)
                                    </span>
                                </div>
                            </td>

                            <!-- Durée -->
                            <td class="px-5 py-2 text-center">
                                <span v-if="row.end_date"
                                    class="inline-flex items-center justify-center px-2.5 py-1 rounded-xl text-xs font-bold
                                           bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                    {{ computeDays(row.start_date, row.end_date) }} j.
                                </span>
                                <span v-else class="text-gray-400 dark:text-gray-500 text-sm">—</span>
                            </td>

                            <!-- Statut -->
                            <td class="px-5 py-2 text-center">
                                <span :class="['inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold border', statusClass(row.status)]">
                                    <span class="w-1.5 h-1.5 rounded-full flex-shrink-0" :class="statusDotClass(row.status)"/>
                                    {{ statusLabel(row.status) }}
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="px-4 py-1.5">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Approuver + Rejeter uniquement si en attente -->
                                    <template v-if="row.status === 'pending'">
                                        <button
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold
                                                   transition-all duration-150 text-white
                                                   bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700
                                                   shadow-sm shadow-emerald-200 dark:shadow-emerald-900/40"
                                            title="Approuver"
                                            @click="openApprove(row, 'approved')">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Approuver
                                        </button>
                                        <button
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold
                                                   transition-all duration-150 text-white
                                                   bg-orange-500 hover:bg-orange-600 active:bg-orange-700
                                                   shadow-sm shadow-orange-200 dark:shadow-orange-900/40"
                                            title="Rejeter"
                                            @click="openApprove(row, 'rejected')">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                            Rejeter
                                        </button>
                                    </template>

                                    <!-- Note admin si déjà traité -->
                                    <button v-if="row.admin_note"
                                        class="p-1.5 rounded-xl transition-all duration-150 text-white
                                               bg-violet-500 hover:bg-violet-600 active:bg-violet-700
                                               shadow-sm shadow-violet-200 dark:shadow-violet-900/40"
                                        title="Voir la note"
                                        @click="openNote(row)">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                        </svg>
                                    </button>

                                    <!-- Supprimer -->
                                    <button
                                        class="p-1.5 rounded-xl transition-all duration-150 text-white
                                               bg-red-500 hover:bg-red-600 active:bg-red-700
                                               shadow-sm shadow-red-200 dark:shadow-red-900/40"
                                        title="Supprimer"
                                        @click="openDelete(row)">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                            'w-8 h-8 rounded-xl flex items-center justify-center text-xs transition-colors',
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
                                'w-8 h-8 rounded-xl flex items-center justify-center text-xs font-medium transition-colors',
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
                            'w-8 h-8 rounded-xl flex items-center justify-center text-xs transition-colors',
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
                <AppButton variant="close" @click="showNote = false">Fermer</AppButton>
            </template>
        </AppModal>

    </div>
</template>

<script setup lang="ts">
import { fmtDate } from '@/utils/dateFormat';
import { ref, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { PageHeader, AppButton, AppInput, AppSelect, AppModal } from '@/Components/UI';
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
    pending:  'bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300 border border-amber-300 dark:border-amber-600/50',
    approved: 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-600/50',
    rejected: 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 border border-red-300 dark:border-red-600/50',
}[s] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-gray-600');

const statusDotClass = (s: string) => ({
    pending:  'bg-amber-500',
    approved: 'bg-emerald-500',
    rejected: 'bg-red-500',
}[s] ?? 'bg-gray-400');

const statusLabel = (s: string) => ({
    pending: 'En attente', approved: 'Approuvé', rejected: 'Rejeté',
}[s] ?? s);

const formatDate = fmtDate;

const computeDays = (start: string, end: string) => {
    if (!start || !end) return '—';
    const diff = Math.abs(new Date(end).getTime() - new Date(start).getTime());
    return Math.ceil(diff / (1000 * 60 * 60 * 24)) + 1;
};

// Couleur avatar déterministe basée sur la première lettre
const avatarColors = ['#7B74F0','#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#06b6d4','#ec4899'];
const avatarColor = (name: string) => avatarColors[(name?.charCodeAt(0) ?? 0) % avatarColors.length];
</script>
