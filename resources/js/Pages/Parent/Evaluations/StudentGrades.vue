<template>
    <div class="space-y-6">
        <PageHeader :title="`Notes de ${student.last_name} ${student.name}`" :subtitle="`Classe : ${student.class_name}`" color="amber">
            <template #icon>
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </template>
            <template #actions>
                <AppSelect v-model="selectedPeriod" :options="periodOptions" class="w-48" @change="changePeriod"/>
            </template>
        </PageHeader>

        <!-- Notes par matière -->
        <div v-if="gradesBySub.length" class="space-y-4">
            <div v-for="sub in gradesBySub" :key="sub.subject_id" class="card overflow-hidden">
                <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between bg-gray-50 dark:bg-gray-800/60">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ sub.subject_name }}</p>
                    <span class="text-sm font-bold" :class="avgClass(sub.average)">
                        Moy : {{ sub.average !== null ? sub.average + '/20' : '—' }}
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                        <thead>
                            <tr class="bg-white dark:bg-gray-800">
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-400 uppercase">Type</th>
                                <th class="px-4 py-2 text-center text-xs font-semibold text-gray-400 uppercase">Date</th>
                                <th class="px-4 py-2 text-center text-xs font-semibold text-gray-400 uppercase">Coef.</th>
                                <th class="px-4 py-2 text-center text-xs font-semibold text-gray-400 uppercase">Note</th>
                                <th class="px-4 py-2 text-center text-xs font-semibold text-gray-400 uppercase">/20</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-for="g in sub.grades" :key="g.eval_date + g.eval_type"
                                class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                                <td class="px-4 py-2.5">
                                    <span class="inline-flex items-center gap-1 text-xs font-medium px-2 py-0.5 rounded-full"
                                        :style="{ background: typeColors[g.eval_type] + '20', color: typeColors[g.eval_type] }">
                                        {{ typeLabels[g.eval_type] ?? g.eval_type }}
                                    </span>
                                </td>
                                <td class="px-4 py-2.5 text-center text-xs text-gray-400">{{ formatDate(g.eval_date) }}</td>
                                <td class="px-4 py-2.5 text-center font-bold text-primary-600 dark:text-primary-400 text-sm">×{{ g.eval_coeff }}</td>
                                <td class="px-4 py-2.5 text-center text-sm font-bold" :class="scoreClass(g.score, g.max_score)">
                                    {{ g.score !== null ? g.score : '—' }}
                                </td>
                                <td class="px-4 py-2.5 text-center text-sm font-medium" :class="scoreClass(g.score, g.max_score)">
                                    {{ g.score !== null && g.max_score ? ((g.score / g.max_score) * 20).toFixed(2) : '—' }}
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-50 dark:bg-gray-800/60">
                                <td colspan="3" class="px-4 py-2 text-right text-xs font-semibold text-gray-500">Moyenne matière</td>
                                <td colspan="2" class="px-4 py-2 text-center">
                                    <span class="text-base font-black" :class="avgClass(sub.average)">
                                        {{ sub.average !== null ? sub.average + '/20' : '—' }}
                                    </span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div v-else class="card p-12 text-center">
            <p class="text-sm text-gray-400">Aucune note disponible pour cette période.</p>
        </div>
    </div>
</template>

<script setup lang="ts">
import { fmtDate } from '@/Utils/dateFormat';
import { computed, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { PageHeader, AppSelect } from '@/Components/UI';

const props = defineProps<{
    student:             any;
    grades:              any[];
    periods:             { id: number; name: string }[];
    selected_period_id?: number;
}>();

const selectedPeriod = ref(props.selected_period_id ? String(props.selected_period_id) : '');
const periodOptions  = computed(() => props.periods.map(p => ({ value: String(p.id), label: p.name })));

const typeLabels: Record<string, string> = {
    interrogation: 'Interrogation', devoir_surveille: 'Devoir surveillé',
    travail_maison: 'Travail de maison', examen_blanc: 'Examen blanc',
};
const typeColors: Record<string, string> = {
    interrogation: '#3b82f6', devoir_surveille: '#f59e0b',
    travail_maison: '#10b981', examen_blanc: '#ef4444',
};

const gradesBySub = computed(() => {
    const map = new Map<number, any>();
    for (const g of props.grades) {
        if (!map.has(g.subject_id)) {
            map.set(g.subject_id, { subject_id: g.subject_id, subject_name: g.subject_name, grades: [], average: null });
        }
        map.get(g.subject_id)!.grades.push(g);
    }
    for (const sub of map.values()) {
        let tw = 0, tc = 0;
        for (const g of sub.grades) {
            if (g.score !== null && g.max_score) {
                tw += (g.score / g.max_score) * 20 * (g.eval_coeff ?? 1);
                tc += (g.eval_coeff ?? 1);
            }
        }
        sub.average = tc > 0 ? parseFloat((tw / tc).toFixed(2)) : null;
    }
    return Array.from(map.values()).sort((a, b) => a.subject_name.localeCompare(b.subject_name));
});

const changePeriod = () => {
    router.get(`/parent/my_student/${props.student.id}/grades`, { period_id: selectedPeriod.value }, { preserveState: true });
};

const avgClass = (avg: number | null) => {
    if (!avg) return 'text-gray-400';
    if (avg >= 14) return 'text-success-600 dark:text-success-400';
    if (avg >= 10) return 'text-warning-600 dark:text-warning-400';
    return 'text-danger-600 dark:text-danger-400';
};

const scoreClass = (score: number | null, max: number) => {
    if (score === null) return 'text-gray-400';
    const v = (score / max) * 20;
    if (v >= 14) return 'text-success-600 dark:text-success-400';
    if (v >= 10) return 'text-warning-600 dark:text-warning-400';
    return 'text-danger-600 dark:text-danger-400';
};

const formatDate = fmtDate;
</script>
