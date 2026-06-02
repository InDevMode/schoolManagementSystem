<template>
    <div class="space-y-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Mon Calendrier</h1>

        <div class="card p-5">
            <h2 class="font-semibold text-gray-900 dark:text-white mb-4">Emploi du temps par classe</h2>
            <div v-if="classTimetable?.length" class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700">
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Classe</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Matière</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Jour</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Horaire</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Salle</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <tr v-for="(item, i) in classTimetable" :key="i">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ item.class_name }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ item.subject_name }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ item.week_name }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ item.start_time }} - {{ item.end_time }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ item.room_number }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p v-else class="text-gray-400 text-center py-4">Aucun emploi du temps disponible.</p>
        </div>

        <div class="card p-5">
            <h2 class="font-semibold text-gray-900 dark:text-white mb-4">Calendrier des examens</h2>
            <div v-if="examTimetable?.length" class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700">
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Examen</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Matière</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Horaire</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <tr v-for="(item, i) in examTimetable" :key="i">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ item.exam_name }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ item.subject_name }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ item.exam_date }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ item.start_time }} - {{ item.end_time }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p v-else class="text-gray-400 text-center py-4">Aucun examen planifié.</p>
        </div>
    </div>
</template>
<script setup lang="ts">
defineProps<{ classTimetable: any[]; examTimetable: any[] }>();
</script>
