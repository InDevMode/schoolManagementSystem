<template>
    <div class="space-y-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
            Calendrier de {{ student?.last_name }} {{ student?.name }}
        </h1>

        <div class="card p-5">
            <h2 class="font-semibold text-gray-900 dark:text-white mb-4">Emploi du temps</h2>
            <div v-if="timetable?.length" class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700">
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Matière</th>
                            <th v-for="day in days" :key="day" class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">{{ day }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <tr v-for="subject in timetable" :key="subject.name">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ subject.name }}</td>
                            <td v-for="week in subject.weeks" :key="week.week_name" class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                <span v-if="week.start_time" class="text-xs">{{ week.start_time }} - {{ week.end_time }}<br/>Salle {{ week.room_number }}</span>
                                <span v-else class="text-gray-300">—</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p v-else class="text-gray-400 text-center py-4">Aucun emploi du temps disponible.</p>
        </div>

        <div class="card p-5">
            <h2 class="font-semibold text-gray-900 dark:text-white mb-4">Calendrier des examens</h2>
            <div v-if="examTimetable?.length" class="space-y-4">
                <div v-for="exam in examTimetable" :key="exam.name">
                    <h3 class="text-sm font-semibold text-primary-600 mb-2">{{ exam.name }}</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700">
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Matière</th>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Horaire</th>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Salle</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <tr v-for="e in exam.exams" :key="e.subject_name">
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ e.subject_name }}</td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ e.exam_date }}</td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ e.start_time }} - {{ e.end_time }}</td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ e.room_number }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <p v-else class="text-gray-400 text-center py-4">Aucun examen planifié.</p>
        </div>
    </div>
</template>
<script setup lang="ts">
defineProps<{ timetable: any[]; examTimetable: any[]; student: any }>();
const days = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
</script>
