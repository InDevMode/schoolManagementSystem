<template>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Calendrier des examens</h1>
        </div>

        <div v-if="examTimetable.length" class="space-y-4">
            <div v-for="exam in examTimetable" :key="exam.name" class="card overflow-hidden">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">{{ exam.name }}</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Matière</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Début</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fin</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Salle</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Note max</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Note min</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-for="subject in exam.getExams" :key="subject.subject_name" class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="px-4 py-2 font-medium text-gray-900 dark:text-white">{{ subject.subject_name }}</td>
                                <td class="px-4 py-2 text-gray-600 dark:text-gray-400">{{ subject.exam_date }}</td>
                                <td class="px-4 py-2 text-gray-600 dark:text-gray-400">{{ subject.start_time }}</td>
                                <td class="px-4 py-2 text-gray-600 dark:text-gray-400">{{ subject.end_time }}</td>
                                <td class="px-4 py-2 text-gray-600 dark:text-gray-400">{{ subject.room_number }}</td>
                                <td class="px-4 py-2 text-gray-600 dark:text-gray-400">{{ subject.full_marks }}</td>
                                <td class="px-4 py-2 text-gray-600 dark:text-gray-400">{{ subject.passing_marks }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div v-else class="card p-8 text-center text-gray-500 dark:text-gray-400">
            Aucun calendrier d'examen disponible.
        </div>
    </div>
</template>

<script setup lang="ts">
interface ExamSubject {
    subject_name: string;
    exam_date: string;
    start_time: string;
    end_time: string;
    room_number: string;
    full_marks: number;
    passing_marks: number;
}

interface ExamGroup {
    name: string;
    getExams: ExamSubject[];
}

defineProps<{
    examTimetable: ExamGroup[];
}>();
</script>
