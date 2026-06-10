<template>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Notes à valider</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    {{ grades.total }} note(s) en attente de validation
                </p>
            </div>
        </div>

        <!-- Validation en masse par évaluation -->
        <div v-if="groupedGrades.length" class="space-y-4">
            <div v-for="group in groupedGrades" :key="group.evaluation_id" class="card overflow-hidden">
                <!-- En-tête groupe -->
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="w-2.5 h-2.5 rounded-full"
                            :style="{ background: typeColors[group.eval_type] ?? '#6366f1' }"/>
                        <div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                {{ typeLabels[group.eval_type] ?? group.eval_type }}
                                — {{ group.subject_name }} · {{ group.class_name }}
                            </p>
                            <p class="text-xs text-gray-400">{{ formatDate(group.eval_date) }} · {{ group.count }} élève(s)</p>
                        </div>
                    </div>
                    <AppButton size="sm" variant="success" :loading="validatingId === group.evaluation_id" @click="validateGroup(group.evaluation_id)">
                        <template #icon>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </template>
                        Valider tout ({{ group.count }})
                    </AppButton>                </div>

                <!-- Liste des élèves -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800/60">
                            <tr>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Élève</th>
                                <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Note</th>
                                <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Sur 20</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Observation</th>
                                <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-for="g in group.grades" :key="g.id"
                                class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">
                                    {{ g.student_last_name }} {{ g.student_name }}
                                </td>
                                <td class="px-4 py-3 text-center text-sm font-bold" :class="scoreClass(g.score, g.max_score)">
                                    {{ g.score !== null ? g.score : '—' }}
                                </td>
                                <td class="px-4 py-3 text-center text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ g.score !== null && g.max_score ? ((g.score / g.max_score) * 20).toFixed(2) : '—' }}
                                </td>
                                <td class="px-4 py-3 text-sm italic text-gray-400 dark:text-gray-500">{{ g.observation || '—' }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- Valider ✓ -->
                                        <button
                                            class="p-1.5 rounded-lg transition-all duration-150
                                                   text-white bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700
                                                   shadow-sm shadow-emerald-200 dark:shadow-emerald-900/40
                                                   disabled:opacity-50 disabled:cursor-not-allowed"
                                            :disabled="actioningId === g.id"
                                            title="Valider cette note"
                                            @click="validateSingle(g.id, group.evaluation_id)">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                        <!-- Annuler (remet en attente) ↩ -->
                                        <button
                                            class="p-1.5 rounded-lg transition-all duration-150
                                                   text-white bg-amber-500 hover:bg-amber-600 active:bg-amber-700
                                                   shadow-sm shadow-amber-200 dark:shadow-amber-900/40
                                                   disabled:opacity-50 disabled:cursor-not-allowed"
                                            :disabled="actioningId === g.id"
                                            title="Annuler et remettre en attente"
                                            @click="confirmAction('cancel', g)">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                            </svg>
                                        </button>
                                        <!-- Rejeter ✕ -->
                                        <button
                                            class="p-1.5 rounded-lg transition-all duration-150
                                                   text-white bg-red-500 hover:bg-red-600 active:bg-red-700
                                                   shadow-sm shadow-red-200 dark:shadow-red-900/40
                                                   disabled:opacity-50 disabled:cursor-not-allowed"
                                            :disabled="actioningId === g.id"
                                            title="Rejeter cette note (devra être re-saisie)"
                                            @click="confirmAction('reject', g)">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- État vide -->
        <div v-else class="card p-16 text-center">
            <div class="w-16 h-16 rounded-2xl bg-success-50 dark:bg-success-900/20 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-success-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Aucune note en attente de validation.</p>
            <p class="text-xs text-gray-400 mt-1">Toutes les notes ont été validées.</p>
        </div>

        <!-- Modale de confirmation Rejeter / Annuler -->
        <AppModal v-model="showConfirm" :title="confirmConfig.title" size="sm">
            <p class="text-sm text-gray-600 dark:text-gray-300">{{ confirmConfig.message }}</p>
            <template #footer>
                <AppButton variant="ghost" @click="showConfirm = false">Annuler</AppButton>
                <AppButton :variant="confirmConfig.variant" :loading="actioningId !== null" @click="runConfirmedAction">
                    {{ confirmConfig.label }}
                </AppButton>
            </template>
        </AppModal>
    </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { AppButton, AppModal } from '@/Components/UI';
import { useToast } from '@/Composables/useToast';
import axios from 'axios';

const toast = useToast();

const props = defineProps<{
    grades: { data: any[]; total: number; from: number; to: number; links: any[] };
}>();

const validatingId = ref<number | null>(null);
const actioningId  = ref<number | null>(null);

// ── Modale de confirmation ─────────────────────────────────────────────────
const showConfirm   = ref(false);
const confirmConfig = ref({
    title:   '',
    message: '',
    label:   '',
    variant: 'danger' as 'danger' | 'warning',
    action:  '' as 'reject' | 'cancel',
    gradeId: 0,
    evalId:  0,
});

const confirmAction = (action: 'reject' | 'cancel', g: any) => {
    if (action === 'reject') {
        confirmConfig.value = {
            title:   'Rejeter la note',
            message: `La note de ${g.student_last_name} ${g.student_name} sera supprimée et devra être re-saisie. Confirmer ?`,
            label:   'Rejeter',
            variant: 'danger',
            action,
            gradeId: g.id,
            evalId:  g.evaluation_id,
        };
    } else {
        confirmConfig.value = {
            title:   'Annuler la validation',
            message: `La note de ${g.student_last_name} ${g.student_name} repassera en attente de validation. Confirmer ?`,
            label:   'Annuler la validation',
            variant: 'warning',
            action,
            gradeId: g.id,
            evalId:  g.evaluation_id,
        };
    }
    showConfirm.value = true;
};

const runConfirmedAction = async () => {
    const { action, gradeId } = confirmConfig.value;
    actioningId.value = gradeId;
    showConfirm.value = false;

    try {
        const url = action === 'reject'
            ? '/admin/evaluations/grades/reject'
            : '/admin/evaluations/grades/cancel-validation';

        const res = await axios.post(url, { grade_ids: [gradeId] });
        if (res.data.success) {
            toast.success(res.data.message);
            router.reload();
        } else {
            toast.error(res.data.message);
        }
    } catch {
        toast.error('Erreur lors de l\'opération.');
    } finally {
        actioningId.value = null;
    }
};

const typeLabels: Record<string, string> = {
    interrogation:    'Interrogation',
    devoir_surveille: 'Devoir surveillé',
    travail_maison:   'Travail de maison',
    examen_blanc:     'Examen blanc',
};
const typeColors: Record<string, string> = {
    interrogation:    '#3b82f6',
    devoir_surveille: '#f59e0b',
    travail_maison:   '#10b981',
    examen_blanc:     '#ef4444',
};

// Regrouper les notes par évaluation
const groupedGrades = computed(() => {
    const map = new Map<number, any>();
    for (const g of props.grades.data) {
        const eid = g.evaluation_id ?? 0;
        if (!map.has(eid)) {
            map.set(eid, {
                evaluation_id: eid,
                eval_type:    g.eval_type,
                eval_date:    g.eval_date,
                subject_name: g.subject_name,
                class_name:   g.class_name,
                max_score:    g.max_score,
                grades:       [],
                count:        0,
            });
        }
        const group = map.get(eid)!;
        group.grades.push(g);
        group.count++;
    }
    return Array.from(map.values());
});

const validateGroup = async (evaluationId: number) => {
    validatingId.value = evaluationId;
    try {
        const res = await axios.post('/admin/evaluations/grades/validate', { evaluation_id: evaluationId });
        if (res.data.success) {
            toast.success(res.data.message);
            router.reload();
        } else {
            toast.error(res.data.message);
        }
    } catch {
        toast.error('Erreur lors de la validation.');
    } finally {
        validatingId.value = null;
    }
};

const validateSingle = async (gradeId: number, evaluationId: number) => {
    actioningId.value = gradeId;
    try {
        const res = await axios.post('/admin/evaluations/grades/validate', {
            evaluation_id: evaluationId,
            grade_ids:     [gradeId],
        });
        if (res.data.success) {
            toast.success('Note validée.');
            router.reload();
        } else {
            toast.error(res.data.message);
        }
    } catch {
        toast.error('Erreur lors de la validation.');
    } finally {
        actioningId.value = null;
    }
};

const scoreClass = (score: number | null, maxScore: number) => {
    if (score === null) return 'text-gray-400';
    const on20 = (score / maxScore) * 20;
    if (on20 >= 14) return 'text-success-600 dark:text-success-400';
    if (on20 >= 10) return 'text-warning-600 dark:text-warning-400';
    return 'text-danger-600 dark:text-danger-400';
};

const formatDate = (d: string) =>
    d ? new Date(d).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' }) : '—';
</script>
