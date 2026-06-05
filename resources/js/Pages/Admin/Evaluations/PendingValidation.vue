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
                    </AppButton>
                </div>

                <!-- Liste des élèves -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800/60">
                            <tr>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Élève</th>
                                <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-500 uppercase">Note</th>
                                <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-500 uppercase">Sur 20</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Observation</th>
                                <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase">Action</th>
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
                                <td class="px-4 py-3 text-center text-sm font-medium text-gray-500">
                                    {{ g.score !== null && g.max_score ? ((g.score / g.max_score) * 20).toFixed(2) : '—' }}
                                </td>
                                <td class="px-4 py-3 text-sm italic text-gray-400">{{ g.observation || '—' }}</td>
                                <td class="px-4 py-3 text-right">
                                    <button
                                        class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-success-50 dark:bg-success-900/20 text-success-700 dark:text-success-400 hover:bg-success-100 transition-colors"
                                        @click="validateSingle(g.id, group.evaluation_id)">
                                        Valider
                                    </button>
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
    </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { AppButton } from '@/Components/UI';
import { useToast } from '@/Composables/useToast';
import axios from 'axios';

const toast = useToast();

const props = defineProps<{
    grades: { data: any[]; total: number; from: number; to: number; links: any[] };
}>();

const validatingId = ref<number | null>(null);

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
