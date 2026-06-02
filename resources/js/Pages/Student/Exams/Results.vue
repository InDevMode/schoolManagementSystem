<template>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Mes résultats d'examens</h1>
        </div>

        <div v-if="examResult.length" class="space-y-6">
            <div v-for="exam in examResult" :key="exam.exam_id" class="card overflow-hidden">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 flex items-center justify-between">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">{{ exam.exam_name }}</h2>
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-gray-500">{{ exam.percentage }}%</span>
                        <AppBadge v-if="exam.grade" variant="primary">{{ exam.grade }}</AppBadge>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Matière</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Cours</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Devoir</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Test</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Examen</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Max</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-for="subject in exam.subject" :key="subject.subject_name" class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="px-4 py-2 font-medium text-gray-900 dark:text-white">{{ subject.subject_name }}</td>
                                <td class="px-4 py-2 text-right text-gray-600 dark:text-gray-400">{{ subject.class_work }}</td>
                                <td class="px-4 py-2 text-right text-gray-600 dark:text-gray-400">{{ subject.home_work }}</td>
                                <td class="px-4 py-2 text-right text-gray-600 dark:text-gray-400">{{ subject.test_work }}</td>
                                <td class="px-4 py-2 text-right text-gray-600 dark:text-gray-400">{{ subject.exam_work }}</td>
                                <td class="px-4 py-2 text-right font-semibold text-gray-900 dark:text-white">{{ subject.score_marks }}</td>
                                <td class="px-4 py-2 text-right text-gray-500">{{ subject.full_marks }}</td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <td class="px-4 py-2 font-semibold text-gray-900 dark:text-white">Total</td>
                                <td class="px-4 py-2 text-right font-semibold">{{ exam.total_class_work }}</td>
                                <td class="px-4 py-2 text-right font-semibold">{{ exam.total_home_work }}</td>
                                <td class="px-4 py-2 text-right font-semibold">{{ exam.total_test_work }}</td>
                                <td class="px-4 py-2 text-right font-semibold">{{ exam.total_exam_work }}</td>
                                <td class="px-4 py-2 text-right font-semibold text-primary-600">{{ exam.total_score }}</td>
                                <td class="px-4 py-2 text-right font-semibold">{{ exam.full_marks }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div v-else class="card p-8 text-center text-gray-500 dark:text-gray-400">
            Aucun résultat disponible.
        </div>
    </div>
</template>

<script setup lang="ts">
import { AppBadge } from '@/Components/UI';

interface SubjectResult {
    subject_name: string;
    class_work: number;
    home_work: number;
    test_work: number;
    exam_work: number;
    score_marks: number;
    passing_marks: number;
    full_marks: number;
}

interface ExamResult {
    exam_name: string;
    exam_id: number;
    subject: SubjectResult[];
    total_class_work: number;
    total_home_work: number;
    total_test_work: number;
    total_exam_work: number;
    total_score: number;
    passing_marks: number;
    full_marks: number;
    percentage: number;
    grade: string | null;
}

defineProps<{
    examResult: ExamResult[];
}>();
</script>
