<template>
    <div class="space-y-6">
        <PageHeader title="Mes notes" subtitle="Résultats par période et par matière" color="amber">
            <template #icon>
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </template>
            <template #actions>
                <div class="flex-shrink-0 w-40">
                    <AppSelect v-model="selectedPeriod" :options="periodOptions" @change="changePeriod"/>
                </div>
            </template>
        </PageHeader>

        <!-- Résumé si notes disponibles -->
        <div v-if="gradesBySub.length" class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="card p-4 text-center">
                <p class="text-xs text-gray-400 mb-1">Matières évaluées</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ gradesBySub.length }}</p>
            </div>
            <div class="card p-4 text-center">
                <p class="text-xs text-gray-400 mb-1">Notes saisies</p>
                <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">{{ totalGrades }}</p>
            </div>
            <div class="card p-4 text-center">
                <p class="text-xs text-gray-400 mb-1">Notes validées</p>
                <p class="text-2xl font-bold text-success-600 dark:text-success-400">{{ validatedGrades }}</p>
            </div>
            <div class="card p-4 text-center">
                <p class="text-xs text-gray-400 mb-1">En attente</p>
                <p class="text-2xl font-bold text-warning-600 dark:text-warning-400">{{ totalGrades - validatedGrades }}</p>
            </div>
        </div>

        <!-- Notes par matière -->
        <div v-if="gradesBySub.length" class="space-y-4">
            <div v-for="sub in gradesBySub" :key="sub.subject_id" class="card overflow-hidden">
                <!-- En-tête matière -->
                <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between bg-gray-50 dark:bg-gray-800/60">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ sub.subject_name }}</p>
                    <div class="text-right">
                        <span class="text-xs text-gray-400">Moyenne matière : </span>
                        <span class="text-sm font-bold" :class="avgClass(sub.average)">
                            {{ sub.average !== null ? sub.average + '/20' : '—' }}
                        </span>
                    </div>
                </div>

                <!-- Liste évaluations -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                        <thead>
                            <tr class="bg-white dark:bg-gray-800">
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-400 uppercase">Type</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-400 uppercase">Titre</th>
                                <th class="px-4 py-2 text-center text-xs font-semibold text-gray-400 uppercase">Date</th>
                                <th class="px-4 py-2 text-center text-xs font-semibold text-gray-400 uppercase">Coef.</th>
                                <th class="px-4 py-2 text-center text-xs font-semibold text-gray-400 uppercase">Note</th>
                                <th class="px-4 py-2 text-center text-xs font-semibold text-gray-400 uppercase">/20</th>
                                <th class="px-4 py-2 text-center text-xs font-semibold text-gray-400 uppercase">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-for="g in sub.grades" :key="g.id ?? g.eval_date"
                                class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors">
                                <td class="px-4 py-2.5">
                                    <span class="inline-flex items-center gap-1.5 text-xs font-medium px-2 py-0.5 rounded-full"
                                        :style="{ background: typeColors[g.eval_type] + '20', color: typeColors[g.eval_type] }">
                                        {{ typeLabels[g.eval_type] ?? g.eval_type }}
                                    </span>
                                </td>
                                <td class="px-4 py-2.5 text-sm text-gray-600 dark:text-gray-400">{{ g.eval_title || '—' }}</td>
                                <td class="px-4 py-2.5 text-center text-xs text-gray-400">{{ formatDate(g.eval_date) }}</td>
                                <td class="px-4 py-2.5 text-center font-bold text-primary-600 dark:text-primary-400 text-sm">×{{ g.eval_coeff }}</td>
                                <td class="px-4 py-2.5 text-center text-sm font-bold" :class="scoreClass(g.score, g.max_score)">
                                    {{ g.score !== null ? g.score : '—' }}
                                </td>
                                <td class="px-4 py-2.5 text-center text-sm" :class="scoreClass(g.score, g.max_score)">
                                    {{ g.score !== null && g.max_score
                                        ? ((g.score / g.max_score) * 20).toFixed(2)
                                        : '—'
                                    }}
                                </td>
                                <td class="px-4 py-2.5 text-center">
                                    <AppBadge :variant="g.validated ? 'success' : 'warning'" dot>
                                        {{ g.validated ? 'Validé' : 'En attente' }}
                                    </AppBadge>
                                </td>
                            </tr>
                        </tbody>
                        <!-- Footer ligne récapitulatif -->
                        <tfoot>
                            <tr class="bg-gray-50 dark:bg-gray-800/60 border-t border-gray-200 dark:border-gray-700">
                                <td colspan="4" class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase text-right">
                                    Moyenne matière
                                </td>
                                <td colspan="2" class="px-4 py-2 text-center">
                                    <span class="text-base font-black" :class="avgClass(sub.average)">
                                        {{ sub.average !== null ? sub.average + '/20' : '—' }}
                                    </span>
                                </td>
                                <td class="px-4 py-2"/>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pas de notes -->
        <div v-else class="card p-16 text-center">
            <div class="w-16 h-16 rounded-2xl bg-primary-50 dark:bg-primary-900/20 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Aucune note disponible pour cette période.</p>
            <p class="text-xs text-gray-400 mt-1">Les notes apparaîtront ici une fois saisies et validées par vos professeurs.</p>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { AppSelect, AppBadge } from '@/Components/UI';

const props = defineProps<{
    grades:              any[];
    periods:             { id: number; name: string }[];
    selected_period_id?: number;
}>();

const selectedPeriod = ref(props.selected_period_id ? String(props.selected_period_id) : '');

const periodOptions = computed(() =>
    props.periods.map(p => ({ value: String(p.id), label: p.name }))
);

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

// Regrouper les notes par matière et calculer la moyenne béninoise
const gradesBySub = computed(() => {
    const map = new Map<number, any>();

    for (const g of props.grades) {
        if (!map.has(g.subject_id)) {
            map.set(g.subject_id, {
                subject_id:   g.subject_id,
                subject_name: g.subject_name,
                grades:       [],
                average:      null as number | null,
            });
        }
        map.get(g.subject_id)!.grades.push(g);
    }

    // Calculer la moyenne béninoise par matière : Σ(note_sur_20 × coeff) / Σ(coeffs)
    for (const sub of map.values()) {
        let totalWeighted = 0;
        let totalCoeff    = 0;
        for (const g of sub.grades) {
            if (g.score !== null && g.max_score) {
                const on20 = (g.score / g.max_score) * 20;
                totalWeighted += on20 * (g.eval_coeff ?? 1);
                totalCoeff    += (g.eval_coeff ?? 1);
            }
        }
        sub.average = totalCoeff > 0 ? parseFloat((totalWeighted / totalCoeff).toFixed(2)) : null;
    }

    return Array.from(map.values()).sort((a, b) => a.subject_name.localeCompare(b.subject_name));
});

const totalGrades     = computed(() => props.grades.length);
const validatedGrades = computed(() => props.grades.filter(g => g.validated).length);

const changePeriod = () => {
    router.get('/student/my_grades', { period_id: selectedPeriod.value }, { preserveState: true });
};

const avgClass = (avg: number | null) => {
    if (avg === null) return 'text-gray-400';
    if (avg >= 14) return 'text-success-600 dark:text-success-400';
    if (avg >= 10) return 'text-warning-600 dark:text-warning-400';
    return 'text-danger-600 dark:text-danger-400';
};

const scoreClass = (score: number | null, maxScore: number) => {
    if (score === null) return 'text-gray-400';
    const on20 = (score / maxScore) * 20;
    if (on20 >= 14) return 'text-success-600 dark:text-success-400';
    if (on20 >= 10) return 'text-warning-600 dark:text-warning-400';
    return 'text-danger-600 dark:text-danger-400';
};

const formatDate = (d: string) =>
    d ? new Date(d).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' }) : '—';
</script>
