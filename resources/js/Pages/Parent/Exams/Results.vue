<template>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Résultats d'examens</h1>
            <p v-if="student" class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                {{ student.last_name }} {{ student.name }}
            </p>
        </div>

        <div v-if="examResult.length" class="space-y-6">
            <div v-for="exam in examResult" :key="exam.exam_id" class="card overflow-hidden">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">{{ exam.exam_name }}</h2>
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
}

defineProps<{
    examResult: ExamResult[];
    student: { id: number; name: string; last_name: string } | null;
}>();
</script>
