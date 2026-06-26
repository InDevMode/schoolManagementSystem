<template>
    <div class="space-y-6">
        <PageHeader title="Notes à valider" :subtitle="`${grades.total} note(s) en attente de validation`" color="amber">
            <template #icon>
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </template>
        </PageHeader>

        <!-- Groupes par évaluation -->
        <div v-if="groupedGrades.length" class="space-y-4">
            <div v-for="group in groupedGrades" :key="group.evaluation_id" class="card overflow-hidden">

                <!-- ── En-tête du groupe ── -->
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-start justify-between gap-4">
                    <!-- Titre évaluation -->
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="w-2.5 h-2.5 rounded-full flex-shrink-0"
                            :style="{ background: typeColors[group.eval_type] ?? '#6366f1' }"/>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                {{ typeLabels[group.eval_type] ?? group.eval_type }}
                                — {{ group.subject_name }} · {{ group.class_name }}
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ formatDate(group.eval_date) }} · {{ group.count }} note(s) saisie(s) / {{ group.totalStudents }} apprenant(s)
                            </p>
                            <!-- Avertissement notes manquantes -->
                            <p v-if="!group.isComplete" class="text-xs text-amber-600 dark:text-amber-400 mt-0.5 flex items-center gap-1">
                                <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                {{ group.missing }} note(s) manquante(s) — saisie requise avant validation globale
                            </p>
                        </div>
                    </div>

                    <!-- Actions du groupe -->
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <!-- Valider tout — actif uniquement si toutes les notes présentes -->
                        <AppButton
                            v-if="can('action.marks.manage')"
                            size="sm"
                            variant="success"
                            :loading="validatingId === group.evaluation_id"
                            :disabled="!group.isComplete"
                            :title="group.isComplete ? 'Valider toutes les notes de cette évaluation' : `${group.missing} note(s) manquante(s) — impossible de valider`"
                            @click="validateGroup(group.evaluation_id)">
                            <template #icon>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </template>
                            Valider tout ({{ group.count }})
                        </AppButton>

                        <!-- Annuler l'évaluation — exclue des moyennes -->
                        <AppButton
                            v-if="can('action.exams.edit')"
                            size="sm"
                            variant="warning"
                            :loading="cancellingId === group.evaluation_id"
                            title="Annuler cette évaluation — elle sera exclue du calcul des moyennes"
                            @click="confirmCancelEval(group)">
                            <template #icon>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                </svg>
                            </template>
                            Annuler l'évaluation
                        </AppButton>
                    </div>
                </div>

                <!-- ── Liste des notes ── -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800/60">
                            <tr>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">apprenant</th>
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
                                <td class="px-4 py-3 text-sm italic text-gray-400 dark:text-gray-500">
                                    {{ g.observation || '—' }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- ✓ Valider la note -->
                                        <button
                                            v-if="can('action.marks.manage')"
                                            class="p-1.5 rounded-xl transition-all duration-150
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
                                        <!-- ✕ Rejeter la note (devra être re-saisie) -->
                                        <button
                                            v-if="can('action.marks.manage')"
                                            class="p-1.5 rounded-xl transition-all duration-150
                                                   text-white bg-red-500 hover:bg-red-600 active:bg-red-700
                                                   shadow-sm shadow-red-200 dark:shadow-red-900/40
                                                   disabled:opacity-50 disabled:cursor-not-allowed"
                                            :disabled="actioningId === g.id"
                                            title="Rejeter cette note — devra être re-saisie"
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
            <p class="text-xs text-gray-400 mt-1">Toutes les notes ont été traitées.</p>
        </div>

        <!-- ── Modale de confirmation note : Rejeter ── -->
        <AppModal v-model="showConfirm" :title="confirmConfig.title" size="sm">
            <p class="text-sm text-gray-600 dark:text-gray-300">{{ confirmConfig.message }}</p>
            <template #footer>
                <AppButton variant="ghost" @click="showConfirm = false">Annuler</AppButton>
                <AppButton variant="danger" :loading="actioningId !== null" @click="runConfirmedAction">
                    {{ confirmConfig.label }}
                </AppButton>
            </template>
        </AppModal>

        <!-- ── Modale de confirmation : Annuler l'évaluation ── -->
        <AppModal v-model="showCancelEval" title="Annuler l'évaluation" size="sm">
            <div class="space-y-3">
                <p class="text-sm text-gray-600 dark:text-gray-300">
                    Vous êtes sur le point d'annuler l'évaluation
                    <strong class="text-gray-900 dark:text-white">{{ cancelEvalTarget?.label }}</strong>.
                </p>
                <div class="flex items-start gap-2 p-3 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800">
                    <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <p class="text-xs text-amber-700 dark:text-amber-300">
                        Cette évaluation sera <strong>exclue du calcul des moyennes</strong>.
                        Les notes sont conservées mais l'évaluation est marquée comme annulée.
                        Elle n'apparaîtra plus dans les bulletins.
                    </p>
                </div>
            </div>
            <template #footer>
                <AppButton variant="ghost" @click="showCancelEval = false">Retour</AppButton>
                <AppButton variant="warning" :loading="cancellingId !== null" @click="runCancelEval">
                    Annuler l'évaluation
                </AppButton>
            </template>
        </AppModal>
    </div>
</template>

<script setup lang="ts">
import { fmtDate } from '@/utils/dateFormat';
import { computed, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { PageHeader, AppButton, AppModal } from '@/Components/UI';
import { useCan } from '@/Composables/useCan';
import { useToast } from '@/Composables/useToast';
import axios from 'axios';

const toast = useToast();
const { can } = useCan();

const props = defineProps<{
    grades:     { data: any[]; total: number; from: number; to: number; links: any[] };
    evalCounts: Record<number, number>;
}>();

const validatingId  = ref<number | null>(null);
const actioningId   = ref<number | null>(null);
const cancellingId  = ref<number | null>(null);

// ── Modale rejeter note ───────────────────────────────────────────────────
const showConfirm   = ref(false);
const confirmConfig = ref({ title: '', message: '', label: '', gradeId: 0 });

const confirmAction = (action: 'reject', g: any) => {
    confirmConfig.value = {
        title:   'Rejeter la note',
        message: `La note de ${g.student_last_name} ${g.student_name} sera supprimée et devra être re-saisie par l'enseignant. Confirmer ?`,
        label:   'Rejeter',
        gradeId: g.id,
    };
    showConfirm.value = true;
};

const runConfirmedAction = async () => {
    const { gradeId } = confirmConfig.value;
    actioningId.value = gradeId;
    showConfirm.value = false;
    try {
        const res = await axios.post('/admin/evaluations/grades/reject', { grade_ids: [gradeId] });
        if (res.data.success) {
            toast.success(res.data.message);
            router.reload();
        } else {
            toast.error(res.data.message);
        }
    } catch (err: any) {
        toast.error(err?.response?.data?.message ?? 'Erreur lors du rejet.');
    } finally {
        actioningId.value = null;
    }
};

// Les IDs d'évaluations annulées localement (disparaissent immédiatement du tableau)
const cancelledEvalIds = ref<Set<number>>(new Set());

// ── Modale annuler évaluation ─────────────────────────────────────────────
const showCancelEval    = ref(false);
const cancelEvalTarget  = ref<{ evaluation_id: number; label: string } | null>(null);

const confirmCancelEval = (group: any) => {
    cancelEvalTarget.value = {
        evaluation_id: group.evaluation_id,
        label: `${typeLabels[group.eval_type] ?? group.eval_type} — ${group.subject_name} · ${group.class_name} (${formatDate(group.eval_date)})`,
    };
    showCancelEval.value = true;
};

const runCancelEval = async () => {
    if (!cancelEvalTarget.value) return;
    const evalId = cancelEvalTarget.value.evaluation_id;
    cancellingId.value = evalId;
    showCancelEval.value = false;
    try {
        const res = await axios.post('/admin/evaluations/cancel', { evaluation_id: evalId });
        if (res.data.success) {
            toast.success(res.data.message);
            // Suppression immédiate du groupe dans l'interface
            cancelledEvalIds.value = new Set([...cancelledEvalIds.value, evalId]);
        } else {
            toast.error(res.data.message);
        }
    } catch (err: any) {
        toast.error(err?.response?.data?.message ?? "Erreur lors de l'annulation.");
    } finally {
        cancellingId.value = null;
        cancelEvalTarget.value = null;
    }
};

// ── Labels ────────────────────────────────────────────────────────────────
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

// ── Groupement ────────────────────────────────────────────────────────────
const groupedGrades = computed(() => {
    const map = new Map<number, any>();
    for (const g of props.grades.data) {
        const eid = g.evaluation_id ?? 0;
        if (!map.has(eid)) {
            map.set(eid, {
                evaluation_id: eid,
                eval_type:     g.eval_type,
                eval_date:     g.eval_date,
                subject_name:  g.subject_name,
                class_name:    g.class_name,
                max_score:     g.max_score,
                grades:        [],
                count:         0,
            });
        }
        const group = map.get(eid)!;
        group.grades.push(g);
        group.count++;
    }
    return Array.from(map.values())
        .filter(group => !cancelledEvalIds.value.has(group.evaluation_id)) // ← exclure immédiatement les annulées
        .map(group => {
            const total = props.evalCounts?.[group.evaluation_id] ?? group.count;
            return {
                ...group,
                totalStudents: total,
                isComplete:    group.count >= total,
                missing:       Math.max(0, total - group.count),
            };
        });
});

// ── Validation ────────────────────────────────────────────────────────────
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
    } catch (err: any) {
        toast.error(err?.response?.data?.message ?? 'Erreur lors de la validation.');
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
    } catch (err: any) {
        toast.error(err?.response?.data?.message ?? 'Erreur lors de la validation.');
    } finally {
        actioningId.value = null;
    }
};

// ── Helpers ───────────────────────────────────────────────────────────────
const scoreClass = (score: number | null, maxScore: number) => {
    if (score === null) return 'text-gray-400';
    const on20 = (score / maxScore) * 20;
    if (on20 >= 14) return 'text-success-600 dark:text-success-400';
    if (on20 >= 10) return 'text-warning-600 dark:text-warning-400';
    return 'text-danger-600 dark:text-danger-400';
};

const formatDate = fmtDate;
</script>
