<template>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Saisie des notes</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    <span v-if="evaluation">
                        {{ typeLabels[evaluation.type] ?? evaluation.type }} —
                        {{ evaluation.subject_name }} · {{ evaluation.class_name }}
                    </span>
                    <span v-else>Sélectionnez une évaluation</span>
                </p>
            </div>
            <AppButton
                v-if="evaluation && savedCount > 0"
                variant="success"
                :loading="saving"
                @click="saveAll">
                <template #icon>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </template>
                Enregistrer ({{ savedCount }})
            </AppButton>
        </div>

        <!-- Sélecteurs -->
        <div class="card p-5">
            <div class="flex flex-wrap gap-4">
                <AppSelect v-model="selectedClass"  label="Classe"   :options="classOptions"  class="w-44" @change="onClassChange"/>
                <AppSelect v-model="selectedPeriod" label="Période"  :options="periodOptions" class="w-44" @change="loadEvals"/>
                <AppSelect v-model="selectedEval"   label="Évaluation" :options="evalOptions" class="w-64" @change="loadGrades"/>
            </div>
        </div>

        <!-- Infos évaluation + stats -->
        <div v-if="evaluation" class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="card p-4 text-center">
                <p class="text-xs text-gray-400 mb-1">Type</p>
                <span class="text-sm font-bold" :style="{ color: typeColors[evaluation.type] }">
                    {{ typeLabels[evaluation.type] ?? evaluation.type }}
                </span>
            </div>
            <div class="card p-4 text-center">
                <p class="text-xs text-gray-400 mb-1">Coefficient</p>
                <span class="text-2xl font-black text-primary-600 dark:text-primary-400">×{{ evaluation.coefficient }}</span>
            </div>
            <div class="card p-4 text-center">
                <p class="text-xs text-gray-400 mb-1">Note max</p>
                <span class="text-2xl font-bold text-gray-900 dark:text-white">/{{ evaluation.max_score }}</span>
            </div>
            <div class="card p-4 text-center">
                <p class="text-xs text-gray-400 mb-1">Élèves</p>
                <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ localGrades.length }}</span>
            </div>
        </div>

        <!-- Stats classe -->
        <div v-if="stats && stats.count > 0" class="grid grid-cols-3 gap-3">
            <div class="card p-4 text-center border border-danger-100 dark:border-danger-900/30">
                <p class="text-xs text-gray-400 mb-1">Min</p>
                <span class="text-xl font-bold text-danger-600">{{ stats.min }}/20</span>
            </div>
            <div class="card p-4 text-center border border-warning-100 dark:border-warning-900/30">
                <p class="text-xs text-gray-400 mb-1">Moy. classe</p>
                <span class="text-xl font-bold text-warning-600">{{ stats.average }}/20</span>
            </div>
            <div class="card p-4 text-center border border-success-100 dark:border-success-900/30">
                <p class="text-xs text-gray-400 mb-1">Max</p>
                <span class="text-xl font-bold text-success-600">{{ stats.max }}/20</span>
            </div>
        </div>

        <!-- Grille de saisie -->
        <div v-if="evaluation && localGrades.length" class="card overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between bg-gray-50 dark:bg-gray-800/60">
                <p class="text-sm font-semibold text-gray-900 dark:text-white">
                    Liste des élèves — {{ evaluation.subject_name }}
                </p>
                <div class="flex items-center gap-3">
                    <span class="text-xs text-gray-400">{{ savedCount }}/{{ localGrades.length }} notes saisies</span>
                    <!-- Barre de progression mini -->
                    <div class="w-24 h-1.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                        <div class="h-1.5 bg-primary-500 rounded-full transition-all"
                            :style="{ width: (savedCount / localGrades.length * 100) + '%' }"/>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                    <thead class="bg-white dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase w-8">#</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Élève</th>
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
                            :class="['transition-colors', g.validated ? 'bg-success-50/40 dark:bg-success-900/10' : 'hover:bg-gray-50 dark:hover:bg-gray-700/40']">
                            <td class="px-4 py-3 text-sm text-gray-400 font-mono">{{ i + 1 }}</td>
                            <td class="px-4 py-3">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ g.last_name }} {{ g.name }}
                                </p>
                                <p v-if="g.admission_number" class="text-xs text-gray-400 font-mono">{{ g.admission_number }}</p>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <input
                                    v-model="g.score"
                                    type="number"
                                    :min="0"
                                    :max="evaluation.max_score"
                                    step="0.5"
                                    :disabled="g.validated"
                                    :class="[
                                        'w-20 mx-auto block text-center text-sm rounded-lg border px-2 py-1.5 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-400',
                                        g.validated
                                            ? 'bg-gray-100 dark:bg-gray-700 border-gray-200 dark:border-gray-600 text-gray-400 cursor-not-allowed'
                                            : inputBorderClass(g),
                                    ]"
                                    @input="g.dirty = true"
                                />
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="text-sm font-bold" :class="scoreTextClass(g)">
                                    {{ computeOn20(g) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <input
                                    v-model="g.observation"
                                    type="text"
                                    placeholder="Absent…"
                                    :disabled="g.validated"
                                    class="w-28 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-transparent px-2 py-1 dark:text-gray-300 placeholder-gray-300 disabled:opacity-40"
                                    @input="g.dirty = true"
                                />
                            </td>
                            <td class="px-4 py-3 text-center">
                                <AppBadge :variant="g.validated ? 'success' : (g.score !== null && g.score !== '' ? 'info' : 'secondary')" dot>
                                    {{ g.validated ? 'Validé' : (g.score !== null && g.score !== '' ? 'Saisi' : 'Vide') }}
                                </AppBadge>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Actions bas de tableau -->
            <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between bg-gray-50 dark:bg-gray-800/60">
                <p class="text-xs text-gray-400">
                    Saisie automatiquement envoyée après enregistrement. L'admin validera les notes.
                </p>
                <AppButton :loading="saving" @click="saveAll">
                    <template #icon>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </template>
                    Enregistrer les notes
                </AppButton>
            </div>
        </div>

        <!-- État vide -->
        <div v-else-if="!evaluation" class="card p-12 text-center">
            <svg class="w-12 h-12 text-gray-200 dark:text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
            </svg>
            <p class="text-sm text-gray-400">Sélectionnez une classe, une période et une évaluation pour commencer.</p>
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
    student_id:       number;
    name:             string;
    last_name:        string;
    admission_number: string | null;
    grade_id:         number | null;
    score:            number | string | null;
    validated:        boolean;
    observation:      string | null;
    dirty?:           boolean;
}

interface EvalInfo {
    id:           number;
    type:         string;
    coefficient:  number;
    max_score:    number;
    eval_date:    string;
    subject_name: string;
    class_name:   string;
    class_id:     number;
}

const props = defineProps<{
    classes:     { id: number; name: string }[];
    periods:     { id: number; name: string }[];
    evaluations: any[];
    evaluation?: EvalInfo;
    grades?:     GradeRow[];
    stats?:      { min: number; max: number; average: number; count: number } | null;
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
const selectedPeriod = ref('');
const selectedEval   = ref(props.evaluation ? String(props.evaluation.id) : '');
const localGrades    = ref<GradeRow[]>((props.grades ?? []).map(g => ({ ...g, dirty: false })));
const evalList       = ref<any[]>(props.evaluations ?? []);
const saving         = ref(false);

const classOptions  = computed(() => props.classes.map(c => ({ value: String(c.id), label: c.name })));
const periodOptions = computed(() => props.periods.map(p => ({ value: String(p.id), label: p.name })));
const evalOptions   = computed(() => evalList.value.map(e => ({
    value: String(e.id),
    label: `${typeLabels[e.type] ?? e.type} — ${e.subject_name} (${e.eval_date})`,
})));
const savedCount = computed(() =>
    localGrades.value.filter(g => g.score !== null && g.score !== '').length
);

const onClassChange = async () => {
    if (selectedClass.value && selectedPeriod.value) loadEvals();
};

const loadEvals = async () => {
    if (!selectedClass.value || !selectedPeriod.value) return;
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
    router.get('/teacher/evaluations/grade-entry', { evaluation_id: selectedEval.value }, {
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
        const res = await axios.post('/teacher/evaluations/grades/save', {
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

const computeOn20 = (g: GradeRow): string => {
    if (g.score === null || g.score === '') return '—';
    const max = props.evaluation?.max_score ?? 20;
    return ((Number(g.score) / max) * 20).toFixed(2);
};

const inputBorderClass = (g: GradeRow) => {
    if (g.score === null || g.score === '') return 'border-gray-200 dark:border-gray-600 bg-transparent dark:text-gray-300';
    const v = (Number(g.score) / (props.evaluation?.max_score ?? 20)) * 20;
    if (v >= 14) return 'border-success-400 bg-success-50 dark:bg-success-900/20 text-success-700 dark:text-success-300';
    if (v >= 10) return 'border-warning-400 bg-warning-50 dark:bg-warning-900/20 text-warning-700 dark:text-warning-300';
    return 'border-danger-400 bg-danger-50 dark:bg-danger-900/20 text-danger-700 dark:text-danger-300';
};

const scoreTextClass = (g: GradeRow) => {
    if (g.score === null || g.score === '') return 'text-gray-400';
    const v = (Number(g.score) / (props.evaluation?.max_score ?? 20)) * 20;
    if (v >= 14) return 'text-success-600 dark:text-success-400';
    if (v >= 10) return 'text-warning-600 dark:text-warning-400';
    return 'text-danger-600 dark:text-danger-400';
};
</script>
