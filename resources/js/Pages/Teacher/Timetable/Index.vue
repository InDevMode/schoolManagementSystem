<template>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Emploi du temps</h1>
            <p v-if="classInfo && subject" class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                {{ classInfo.name }} — {{ subject.name }}
            </p>
        </div>

        <div v-if="timetable.length" class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jour</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Heure début</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Heure fin</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Salle</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <template v-for="(entry, i) in timetable" :key="i">
                            <tr v-for="day in entry.week" :key="day.week_id"
                                class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ day.week_name }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ day.start_time || '—' }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ day.end_time || '—' }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ day.room_number || '—' }}</td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <div v-else class="card p-8 text-center text-gray-500 dark:text-gray-400">
            Aucun horaire disponible.
        </div>
    </div>
</template>

<script setup lang="ts">
interface WeekDay {
    week_id: number;
    week_name: string;
    start_time: string;
    end_time: string;
    room_number: string;
}

interface ClassInfo { id: number; name: string; }
interface SubjectInfo { id: number; name: string; }

// "class" est un mot réservé — on utilise classInfo comme alias via la prop "class"
const props = defineProps<{
    classInfo: ClassInfo | null;
    subject:   SubjectInfo | null;
    timetable: { week: WeekDay[] }[];
}>();
</script>
