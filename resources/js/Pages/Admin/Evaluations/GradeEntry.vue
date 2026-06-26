<template>
    <div class="space-y-6">

        <PageHeader :title="evaluation?.status === 'validated' ? 'Notes (lecture seule)' : evaluation?.status === 'cancelled' ? 'Notes (évaluation annulée)' : 'Saisie des notes'" subtitle="Saisie et validation selon le système béninois" color="amber">
            <template #icon>
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
            </template>
        </PageHeader>

        <!-- Sélecteurs -->
        <div class="card p-5">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Sélectionner une évaluation</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <AppSelect v-model="selectedClass" label="Classe" :options="classOptions" :block="true" />
                <AppSelect v-model="selectedPeriod" label="Période" :options="periodOptions" :block="true" />
                <AppSelect v-model="selectedEval" label="Évaluation" :options="evalOptions" :block="true" />
            </div>
        </div>

        <!-- Bandeau évaluation validée (lecture seule) -->
        <div v-if="evaluation && evaluation.status === 'validated'"
            class="flex items-center gap-3 px-4 py-3 rounded-xl
                   bg-success-50 dark:bg-success-900/20 border border-success-200 dark:border-success-700">
            <svg class="w-5 h-5 text-success-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            <div>
                <p class="text-sm font-semibold text-success-700 dark:text-success-300">
                    Évaluation validée — consultation en lecture seule
                </p>
                <p class="text-xs text-success-600 dark:text-success-400 mt-0.5">
                    La saisie et la modification des notes sont bloquées. Pour corriger une erreur, annulez la
                    validation depuis la page <strong>« À valider »</strong>.
                </p>
            </div>
        </div>

        <!-- Bandeau évaluation annulée (lecture seule) -->
        <div v-if="evaluation && evaluation.status === 'cancelled'"
            class="flex items-center gap-3 px-4 py-3 rounded-xl
                   bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700">
            <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
            </svg>
            <div>
                <p class="text-sm font-semibold text-amber-700 dark:text-amber-300">
                    Évaluation annulée — consultation en lecture seule
                </p>
                <p class="text-xs text-amber-600 dark:text-amber-400 mt-0.5">
                    Cette évaluation a été annulée et <strong>ne sera pas prise en compte</strong> dans le calcul des moyennes.
                    Les notes sont conservées à titre d'historique uniquement.
                </p>
            </div>
        </div>

        <!-- Infos de l'évaluation courante -->
        <div v-if="evaluation" class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="card p-4 text-center">
                <p class="text-xs text-gray-400 mb-1">Type</p>
                <span class="text-sm font-semibold" :style="{ color: typeColors[evaluation.type] }">
                    {{ typeLabels[evaluation.type] ?? evaluation.type }}
                </span>
            </div>
            <div class="card p-4 text-center">
                <p class="text-xs text-gray-400 mb-1">Coefficient</p>
                <span class="text-2xl font-bold text-primary-600 dark:text-primary-400">×{{ evaluation.coefficient }}</span>
            </div>
            <div class="card p-4 text-center">
                <p class="text-xs text-gray-400 mb-1">Note max</p>
                <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ evaluation.max_score }}</span>
            </div>
            <div class="card p-4 text-center">
                <p class="text-xs text-gray-400 mb-1">apprenants</p>
                <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ localGrades.length }}</span>
            </div>
        </div>

        <!-- Stats de la classe -->
        <div v-if="stats && stats.count > 0" class="grid grid-cols-3 gap-3">
            <div class="card p-4 text-center">
                <p class="text-xs text-gray-400 mb-1">Minimum</p>
                <span class="text-xl font-bold text-danger-600">{{ stats.min }}/20</span>
            </div>
            <div class="card p-4 text-center">
                <p class="text-xs text-gray-400 mb-1">Moyenne classe</p>
                <span class="text-xl font-bold text-warning-600">{{ stats.average }}/20</span>
            </div>
            <div class="card p-4 text-center">
                <p class="text-xs text-gray-400 mb-1">Maximum</p>
                <span class="text-xl font-bold text-success-600">{{ stats.max }}/20</span>
            </div>
        </div>

        <!-- Table de saisie -->
        <div v-if="evaluation && localGrades.length" class="card overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <h3 class="font-semibold text-gray-900 dark:text-white">
                    {{ evaluation.status === 'validated'
                        ? 'Notes validées (lecture seule)'
                        : evaluation.status === 'cancelled'
                            ? 'Notes (évaluation annulée — lecture seule)'
                            : 'Saisie des notes' }}
                    — {{ evaluation.subject_name }}
                </h3>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-400">{{ savedCount }} / {{ localGrades.length }} saisis</span>
                    <AppButton v-if="evaluation.status !== 'validated' && evaluation.status !== 'cancelled'" size="sm" :loading="saving" :disabled="!allEditableFilled" @click="saveAll">
                        <template #icon>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </template>
                        Enregistrer
                    </AppButton>
                </div>
            </div>

            <!-- Bandeau d'instructions (affiché si éval active, ni validée ni annulée) -->
            <div v-if="evaluation.status !== 'validated' && evaluation.status !== 'cancelled'"
                class="flex items-start gap-3 mx-5 mt-4 px-4 py-3 rounded-xl
                       bg-violet-50 dark:bg-violet-900/20 border border-violet-200 dark:border-violet-800">
                <svg class="w-4 h-4 text-violet-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="text-xs text-violet-700 dark:text-violet-300 space-y-0.5">
                    <p class="font-semibold">Veuillez saisir toutes les notes avant de pouvoir enregistrer.</p>
                    <p>Les valeurs acceptées sont comprises entre <strong>0</strong> et <strong>20</strong>. N'oubliez pas d'ajouter une observation si possible (Absent, Passable, Bien…) — ce n'est pas obligatoire.</p>
                    <p>Une fois toutes les notes saisies, le bouton <strong>Enregistrer</strong> s'activera. La validation se fait ensuite depuis la page <strong>« À valider »</strong>.</p>
                </div>
            </div>

            <!-- Bandeau alerte : notes rejetées à re-saisir -->
            <div v-if="rejectedCount > 0"
                class="flex items-center gap-3 mx-5 mt-3 px-4 py-2.5 rounded-xl
                       bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <p class="text-xs text-red-700 dark:text-red-300">
                    <span class="font-semibold">{{ rejectedCount }} note(s) rejetée(s)</span>
                    — ces notes ont été annulées par l'administrateur et doivent être <strong>re-saisies</strong>.
                    Elles sont indiquées en rouge dans le tableau ci-dessous.
                </p>
            </div>

            <!-- Bandeau notes verrouillées -->
            <div v-if="validatedCount > 0"
                class="flex items-center gap-3 mx-5 mt-3 px-4 py-2.5 rounded-xl
                       bg-success-50 dark:bg-success-900/20 border border-success-200 dark:border-success-800">
                <svg class="w-4 h-4 text-success-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                <p class="text-xs text-success-700 dark:text-success-300">
                    <span class="font-semibold">{{ validatedCount }} note(s) validée(s)</span>
                    — ces notes sont verrouillées et ne peuvent plus être modifiées ni supprimées.
                </p>
            </div>

            <div class="overflow-x-auto mt-2">
                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800/60">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">N°</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">apprenant</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Matricule</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">
                                Note /{{ evaluation.max_score }}
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Sur 20</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Observation</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                        <tr v-for="(g, i) in localGrades" :key="g.student_id"
                            :class="[
                                'transition-colors',
                                g.validated
                                    ? 'bg-success-50/40 dark:bg-success-900/10 opacity-75'
                                    : evaluation.status === 'cancelled'
                                        ? 'bg-gray-50/60 dark:bg-gray-800/40 opacity-70'
                                        : g.rejected
                                            ? 'bg-red-50/60 dark:bg-red-900/10'
                                            : 'hover:bg-gray-50 dark:hover:bg-gray-700/40'
                            ]">
                            <td class="px-4 py-3 text-sm text-gray-500 w-10">{{ i + 1 }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-1.5">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ g.last_name }} {{ g.name }}
                                    </p>
                                    <!-- Indicateur note rejetée -->
                                    <svg v-if="g.rejected" class="w-3.5 h-3.5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" title="Note rejetée — à re-saisir">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-400">{{ g.admission_number ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <!-- Note verrouillée (validée ou éval annulée) -->
                                <div v-if="g.validated || evaluation.status === 'cancelled'"
                                    :class="['w-20 mx-auto flex items-center justify-center gap-1.5 px-2 py-1.5 rounded-xl border',
                                             evaluation.status === 'cancelled'
                                                ? 'bg-gray-100 dark:bg-gray-700/50 border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400'
                                                : lockedBadgeClass(g)]">
                                    <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path v-if="evaluation.status === 'cancelled'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                        <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                    <span class="text-sm font-bold">{{ g.score ?? '—' }}</span>
                                </div>
                                <!-- Note éditable -->
                                <input
                                    v-else
                                    v-model="g.score"
                                    type="number"
                                    :min="0"
                                    :max="20"
                                    step="0.5"
                                    :class="['w-20 mx-auto block text-center text-sm rounded-xl border px-2 py-1.5 transition-colors', scoreClass(g)]"
                                    @input="onScoreInput(g)"
                                />
                            </td>
                            <td class="px-4 py-3 text-center text-sm font-medium" :class="scoreTextClass(g)">
                                {{ g.score !== null && g.score !== '' ? ((Number(g.score) / evaluation.max_score) * 20).toFixed(2) : '—' }}
                            </td>
                            <td class="px-4 py-3">
                                <input
                                    v-model="g.observation"
                                    type="text"
                                    placeholder="Absent, Dispensé,Médicore, Passable, Assez-Bien,Bien, Très-Bien..."
                                    :disabled="g.validated || evaluation.status === 'cancelled'"
                                    class="w-full text-xs rounded-xl border border-gray-200 dark:border-gray-600 bg-transparent px-2 py-1 dark:text-gray-300 placeholder-gray-300 dark:placeholder-gray-600 disabled:opacity-40 disabled:cursor-not-allowed"
                                    @input="g.dirty = true"
                                />
                            </td>
                            <td class="px-4 py-3 text-center">
                                <AppBadge
                                    :variant="evaluation.status === 'cancelled'
                                        ? 'warning'
                                        : g.validated
                                            ? 'success'
                                            : g.rejected
                                                ? 'danger'
                                                : (g.score !== null && g.score !== '' ? 'info' : 'secondary')"
                                    dot>
                                    <span class="inline-flex items-center gap-1">
                                        <!-- Annulée -->
                                        <template v-if="evaluation.status === 'cancelled'">
                                            <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                            </svg>
                                            Annulée
                                        </template>
                                        <!-- Validée -->
                                        <template v-else-if="g.validated">
                                            <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                            </svg>
                                            Validée
                                        </template>
                                        <!-- Rejetée -->
                                        <template v-else-if="g.rejected">
                                            <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                            </svg>
                                            Rejetée
                                        </template>
                                        <!-- Saisie ou En attente -->
                                        <template v-else>
                                            {{ g.score !== null && g.score !== '' ? 'Saisi' : 'En attente' }}
                                        </template>
                                    </span>
                                </AppBadge>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- État vide -->
        <div v-else-if="!evaluation" class="card p-12 text-center">
            <div class="w-16 h-16 rounded-2xl bg-primary-50 dark:bg-primary-900/20 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Sélectionnez une classe, une période et une évaluation pour commencer la saisie.</p>
        </div>

    </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { PageHeader, AppButton, AppSelect, AppBadge } from '@/Components/UI';
import { useToast } from '@/Composables/useToast';
import axios from 'axios';

const toast = useToast();

interface GradeRow {
    student_id: number;
    name: string;
    last_name: string;
    admission_number: string | null;
    grade_id: number | null;
    score: number | string | null;
    validated: boolean;
    observation: string | null;
    rejected?: boolean;
    dirty?: boolean;
}

interface EvalInfo {
    id: number;
    type: string;
    coefficient: number;
    max_score: number;
    eval_date: string;
    subject_name: string;
    class_id: number;
    status: string;
}

const props = defineProps<{
    classes:           { id: number; name: string }[];
    periods:           { id: number; name: string }[];
    currentPeriod?:    { id: number; name: string } | null;
    evaluations:       any[];
    evaluation?:       EvalInfo;
    grades?:           GradeRow[];
    stats?:            { min: number; max: number; average: number; count: number } | null;
    selectedClassId?:  number | null;
    selectedPeriodId?: number | null;
}>();

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

const selectedClass  = ref(props.selectedClassId  ? String(props.selectedClassId)  : (props.evaluation ? String(props.evaluation.class_id) : ''));
const selectedPeriod = ref(props.selectedPeriodId ? String(props.selectedPeriodId) : (props.currentPeriod ? String(props.currentPeriod.id) : ''));
const selectedEval   = ref(props.evaluation ? String(props.evaluation.id) : '');
const localGrades    = ref<GradeRow[]>((props.grades ?? []).map(g => ({ ...g, dirty: false })));
const evalList       = ref<any[]>(props.evaluations ?? []);
const saving         = ref(false);

const classOptions  = computed(() => props.classes.map(c => ({ value: String(c.id), label: c.name })));
const periodOptions = computed(() =>
    props.currentPeriod
        ? [{ value: String(props.currentPeriod.id), label: props.currentPeriod.name }]
        : props.periods.map(p => ({ value: String(p.id), label: p.name }))
);
const evalOptions    = computed(() => evalList.value.map(e => ({
    value: String(e.id),
    label: `${typeLabels[e.type] ?? e.type} — ${e.subject_name} (${e.eval_date})`,
})));

const savedCount     = computed(() => localGrades.value.filter(g => g.score !== null && g.score !== '').length);
const validatedCount = computed(() => localGrades.value.filter(g => g.validated).length);
const rejectedCount  = computed(() => localGrades.value.filter(g => g.rejected).length);

// Le bouton Enregistrer n'est actif que si :
// 1. Il y a au moins une note éditable (non validée, y compris les rejetées)
// 2. TOUTES les notes éditables ont une valeur saisie (les rejetées doivent être re-saisies)
// 3. TOUTES les valeurs saisies sont dans la plage 0-20
const allEditableFilled = computed(() => {
    const editables = localGrades.value.filter(g => !g.validated);
    if (editables.length === 0) return false;
    return editables.every(g => {
        if (g.score === null || g.score === '') return false;
        const v = Number(g.score);
        return !isNaN(v) && v >= 0 && v <= 20;
    });
});

// ── Watch : recharger les évaluations dès que classe ou période changent ─────
let initialLoad = true;
watch([selectedClass, selectedPeriod], ([cls, per]) => {
    if (initialLoad) {
        // Premier appel au montage : si les évals sont déjà dans les props, on ne refait pas la requête
        initialLoad = false;
        if (cls && per && evalList.value.length === 0) loadEvaluations(/* keepSelection= */true);
        return;
    }
    evalList.value     = [];
    selectedEval.value = '';
    localGrades.value  = [];
    if (cls && per) loadEvaluations();
}, { immediate: true });

// ── Watch : charger les notes quand on choisit une évaluation ─────────────
watch(selectedEval, (val, oldVal) => {
    if (val && val !== oldVal) loadGrades();
    else if (!val) localGrades.value = [];
});

const loadEvaluations = async (keepSelection = false) => {
    try {
        const res = await axios.get('/admin/evaluations/by-class-period', {
            params: { class_id: selectedClass.value, period_id: selectedPeriod.value },
        });
        evalList.value = res.data;
        // Si on ne garde pas la sélection (changement de classe/période), reset
        if (!keepSelection) {
            selectedEval.value = '';
            localGrades.value  = [];
        }
    } catch {
        evalList.value = [];
    }
};

const loadGrades = () => {
    if (!selectedEval.value) return;
    router.get('/admin/evaluations/grade-entry', {
        evaluation_id: selectedEval.value,
        class_id:      selectedClass.value,
        period_id:     selectedPeriod.value,
    }, {
        preserveState: false,
        preserveScroll: true,
    });
};

const saveAll = async () => {
    saving.value = true;
    try {
        const res = await axios.post('/admin/evaluations/grades/save', {
            evaluation_id: props.evaluation.id,
            grades: localGrades.value.map(g => ({
                student_id:  g.student_id,
                score:       g.score !== '' ? g.score : null,
                observation: g.observation,
            })),
        });
        if (res.data.success) {
            toast.success(res.data.message);
            // Mise à jour locale immédiate : les notes re-saisies passent en "Saisi"
            // et perdent leur flag rejected sans attendre un rechargement serveur
            localGrades.value = localGrades.value.map(g => ({
                ...g,
                dirty:    false,
                rejected: g.rejected && (g.score === null || g.score === '') ? true : false,
            }));
        } else {
            toast.error(res.data.message);
        }
    } catch {
        toast.error('Erreur lors de l\'enregistrement.');
    } finally {
        saving.value = false;
    }
};

// Badge couleur pour les notes verrouillées (validées) — suit la même logique que scoreTextClass
const lockedBadgeClass = (g: GradeRow) => {
    const v   = Number(g.score);
    const max = props.evaluation?.max_score ?? 20;
    const on20 = (v / max) * 20;
    if (on20 >= 14) return 'bg-success-50 dark:bg-success-900/20 border-success-200 dark:border-success-700 text-success-700 dark:text-success-400';
    if (on20 >= 10) return 'bg-warning-50 dark:bg-warning-900/20 border-warning-200 dark:border-warning-700 text-warning-700 dark:text-warning-400';
    return 'bg-danger-50 dark:bg-danger-900/20 border-danger-200 dark:border-danger-700 text-danger-700 dark:text-danger-400';
};

// Classe CSS input selon la note
const scoreClass = (g: GradeRow) => {
    if (g.score === null || g.score === '') return 'border-gray-200 dark:border-gray-600 bg-transparent dark:text-gray-300';
    const v = Number(g.score);
    if (isNaN(v) || v < 0 || v > 20) return 'border-danger-500 bg-danger-50 dark:bg-danger-900/20 text-danger-700 dark:text-danger-300 ring-1 ring-danger-400';
    const max = props.evaluation?.max_score ?? 20;
    const pct = (v / max) * 20;
    if (pct >= 14) return 'border-success-400 bg-success-50 dark:bg-success-900/20 text-success-700 dark:text-success-300';
    if (pct >= 10) return 'border-warning-400 bg-warning-50 dark:bg-warning-900/20 text-warning-700 dark:text-warning-300';
    return 'border-danger-400 bg-danger-50 dark:bg-danger-900/20 text-danger-700 dark:text-danger-300';
};

// Handler input : marque dirty et clampe doucement sans couper la frappe
const onScoreInput = (g: GradeRow) => {
    g.dirty = true;
    if (g.score === '' || g.score === null) return;
    const v = Number(g.score);
    if (!isNaN(v)) {
        if (v < 0) g.score = 0;
        if (v > 20) g.score = 20;
    }
};

const scoreTextClass = (g: GradeRow) => {
    if (g.score === null || g.score === '') return 'text-gray-400';
    const v = Number(g.score);
    const max = props.evaluation?.max_score ?? 20;
    const pct = (v / max) * 20;
    if (pct >= 14) return 'text-success-600 dark:text-success-400';
    if (pct >= 10) return 'text-warning-600 dark:text-warning-400';
    return 'text-danger-600 dark:text-danger-400';
};
</script>
