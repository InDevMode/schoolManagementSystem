<template>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Mon emploi du temps</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Horaires de cours par matière</p>
        </div>

        <div v-if="timetable.length" class="space-y-4">
            <div v-for="subject in timetable" :key="subject.name" class="card overflow-hidden">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">{{ subject.name }}</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jour</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Début</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fin</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Salle</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-for="day in subject.week" :key="day.week_id" class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="px-4 py-2 font-medium text-gray-900 dark:text-white">{{ day.week_name }}</td>
                                <td class="px-4 py-2 text-gray-600 dark:text-gray-400">{{ day.start_time || '—' }}</td>
                                <td class="px-4 py-2 text-gray-600 dark:text-gray-400">{{ day.end_time || '—' }}</td>
                                <td class="px-4 py-2 text-gray-600 dark:text-gray-400">{{ day.room_number || '—' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div v-else class="card p-8 text-center text-gray-500 dark:text-gray-400">
            Aucun emploi du temps disponible.
        </div>
    </div>
</template>

<script setup lang="ts">
interface WeekDay {
    week_id: number;
    week_name: string;
    day?: string;
    start_time: string;
    end_time: string;
    room_number: string;
}

interface SubjectTimetable {
    name: string;
    week: WeekDay[];
}

defineProps<{
    timetable: SubjectTimetable[];
}>();
</script>
