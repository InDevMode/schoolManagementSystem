<template>
    <div class="space-y-6">

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Saisie des notes</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Saisie et validation selon le système béninois</p>
            </div>
            <AppButton v-if="evaluation && savedCount > 0" variant="success" :loading="validating" @click="validateAll">
                <template #icon>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </template>
                Valider toutes les notes
            </AppButton>
        </div>

        <!-- Sélecteurs -->
        <div class="card p-5">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Sélectionner une évaluation</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <AppSelect v-model="selectedClass" label="Classe" :options="classOptions" :block="true" @change="onClassChange" />
                <AppSelect v-model="selectedPeriod" label="Période" :options="periodOptions" :block="true" @change="onPeriodChange" />
                <AppSelect v-model="selectedEval" label="Évaluation" :options="evalOptions" :block="true" @change="loadGrades" />
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
                <p class="text-xs text-gray-400 mb-1">Élèves</p>
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
                    Saisie des notes — {{ evaluation.subject_name }}
                </h3>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-400">{{ savedCount }} / {{ localGrades.length }} saisis</span>
                    <AppButton size="sm" :loading="saving" @click="saveAll">
                        <template #icon>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </template>
                        Enregistrer
                    </AppButton>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800/60">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">N°</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Élève</th>
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
                            :class="['transition-colors', g.validated ? 'bg-green-50/30 dark:bg-green-900/10' : 'hover:bg-gray-50 dark:hover:bg-gray-700/40']">
                            <td class="px-4 py-3 text-sm text-gray-500 w-10">{{ i + 1 }}</td>
                            <td class="px-4 py-3">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ g.last_name }} {{ g.name }}
                                </p>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-400">{{ g.admission_number ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <input
                                    v-model="g.score"
                                    type="number"
                                    :min="0"
                                    :max="evaluation.max_score"
                                    step="0.5"
                                    :disabled="g.validated"
                                    :class="[
                                        'w-20 mx-auto block text-center text-sm rounded-lg border px-2 py-1.5 transition-colors',
                                        g.validated
                                            ? 'bg-gray-100 dark:bg-gray-700 border-gray-200 dark:border-gray-600 text-gray-400 cursor-not-allowed'
                                            : scoreClass(g),
                                    ]"
                                    @input="g.dirty = true"
                                />
                            </td>
                            <td class="px-4 py-3 text-center text-sm font-medium" :class="scoreTextClass(g)">
                                {{ g.score !== null && g.score !== '' ? ((Number(g.score) / evaluation.max_score) * 20).toFixed(2) : '—' }}
                            </td>
                            <td class="px-4 py-3">
                                <input
                                    v-model="g.observation"
                                    type="text"
                                    placeholder="Absent, Dispensé..."
                                    :disabled="g.validated"
                                    class="w-28 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-transparent px-2 py-1 dark:text-gray-300 placeholder-gray-300 dark:placeholder-gray-600 disabled:opacity-40"
                                    @input="g.dirty = true"
                                />
                            </td>
                            <td class="px-4 py-3 text-center">
                                <AppBadge :variant="g.validated ? 'success' : (g.score !== null ? 'info' : 'secondary')" dot>
                                    {{ g.validated ? 'Validé' : (g.score !== null ? 'Saisi' : 'En attente') }}
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
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { AppButton, AppSelect, AppBadge } from '@/Components/UI';
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
}

const props = defineProps<{
    classes:       { id: number; name: string }[];
    periods:       { id: number; name: string }[];
    currentPeriod?: { id: number; name: string } | null;
    evaluations:   any[];
    evaluation?:   EvalInfo;
    grades?:       GradeRow[];
    stats?:        { min: number; max: number; average: number; count: number } | null;
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

const selectedClass  = ref('');
const selectedPeriod = ref(props.currentPeriod ? String(props.currentPeriod.id) : '');
const selectedEval   = ref(props.evaluation ? String(props.evaluation.id) : '');
const localGrades    = ref<GradeRow[]>((props.grades ?? []).map(g => ({ ...g, dirty: false })));
const evalList       = ref<any[]>(props.evaluations ?? []);
const saving         = ref(false);
const validating     = ref(false);

const classOptions  = computed(() => props.classes.map(c => ({ value: String(c.id), label: c.name })));
// Saisie des notes : uniquement la période courante dans le select
const periodOptions = computed(() =>
    props.currentPeriod
        ? [{ value: String(props.currentPeriod.id), label: props.currentPeriod.name }]
        : props.periods.map(p => ({ value: String(p.id), label: p.name }))
);
const evalOptions   = computed(() => evalList.value.map(e => ({
    value: String(e.id),
    label: `${typeLabels[e.type] ?? e.type} — ${e.subject_name} (${e.eval_date})`,
})));

const savedCount = computed(() => localGrades.value.filter(g => g.score !== null && g.score !== '').length);

const onClassChange = async () => {
    if (!selectedClass.value || !selectedPeriod.value) return;
    loadEvaluations();
};
const onPeriodChange = () => {
    if (selectedClass.value) loadEvaluations();
};

const loadEvaluations = async () => {
    try {
        const res = await axios.get('/admin/evaluations/by-class-period', {
            params: { class_id: selectedClass.value, period_id: selectedPeriod.value },
        });
        evalList.value = res.data;
    } catch {
        evalList.value = [];
    }
};

const loadGrades = () => {
    if (!selectedEval.value) return;
    router.get('/admin/evaluations/grade-entry', { evaluation_id: selectedEval.value }, {
        preserveState: true,
        onSuccess: (page: any) => {
            localGrades.value = (page.props.grades ?? []).map((g: GradeRow) => ({ ...g, dirty: false }));
        },
    });
};

const saveAll = async () => {
    if (!props.evaluation) return;
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
            localGrades.value.forEach(g => (g.dirty = false));
        } else {
            toast.error(res.data.message);
        }
    } catch {
        toast.error('Erreur lors de l\'enregistrement.');
    } finally {
        saving.value = false;
    }
};

const validateAll = async () => {
    if (!props.evaluation) return;
    validating.value = true;
    try {
        const res = await axios.post('/admin/evaluations/grades/validate', {
            evaluation_id: props.evaluation.id,
        });
        if (res.data.success) {
            toast.success(res.data.message);
            router.reload();
        } else {
            toast.error(res.data.message);
        }
    } catch {
        toast.error('Erreur lors de la validation.');
    } finally {
        validating.value = false;
    }
};

// Classe CSS input selon la note
const scoreClass = (g: GradeRow) => {
    if (g.score === null || g.score === '') return 'border-gray-200 dark:border-gray-600 bg-transparent dark:text-gray-300';
    const v = Number(g.score);
    const max = props.evaluation?.max_score ?? 20;
    const pct = (v / max) * 20;
    if (pct >= 14) return 'border-success-400 bg-success-50 dark:bg-success-900/20 text-success-700 dark:text-success-300';
    if (pct >= 10) return 'border-warning-400 bg-warning-50 dark:bg-warning-900/20 text-warning-700 dark:text-warning-300';
    return 'border-danger-400 bg-danger-50 dark:bg-danger-900/20 text-danger-700 dark:text-danger-300';
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
