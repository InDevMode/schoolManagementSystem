<template>
    <div class="space-y-6">
        <!-- Header -->
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Mon emploi du temps</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Horaires de cours de la semaine</p>
        </div>

        <!-- Grille visuelle semaine (vue calendrier) -->
        <div v-if="hasData" class="space-y-4">

            <!-- Grille matricielle : jours × matières -->
            <div class="card overflow-hidden">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Vue hebdomadaire</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm border-collapse">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-800">
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide border-b border-r border-gray-200 dark:border-gray-700 w-28">
                                    Jour
                                </th>
                                <th
                                    v-for="subject in timetable"
                                    :key="subject.name"
                                    class="px-3 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wide border-b border-r border-gray-200 dark:border-gray-700 min-w-[120px]"
                                >
                                    {{ subject.name }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="day in allDays"
                                :key="day.week_id"
                                class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors"
                                :class="day.isToday ? 'bg-primary-50/40 dark:bg-primary-900/10' : ''"
                            >
                                <!-- Jour -->
                                <td class="px-4 py-3 border-r border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="w-1.5 h-1.5 rounded-full flex-shrink-0"
                                            :class="day.isToday ? 'bg-primary-500' : 'bg-gray-300 dark:bg-gray-600'"
                                        />
                                        <span
                                            class="text-sm font-medium"
                                            :class="day.isToday ? 'text-primary-700 dark:text-primary-300' : 'text-gray-700 dark:text-gray-300'"
                                        >
                                            {{ day.week_name }}
                                        </span>
                                        <span v-if="day.isToday" class="text-xs text-primary-500 font-medium">(auj.)</span>
                                    </div>
                                </td>

                                <!-- Créneaux par matière -->
                                <td
                                    v-for="subject in timetable"
                                    :key="subject.name + day.week_id"
                                    class="px-3 py-2 border-r border-gray-100 dark:border-gray-700 text-center"
                                >
                                    <div v-if="getSlot(subject, day.week_id)" class="rounded-lg p-2 bg-primary-100/60 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800">
                                        <p class="text-xs font-bold text-primary-700 dark:text-primary-300">
                                            {{ getSlot(subject, day.week_id)!.start_time }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ getSlot(subject, day.week_id)!.end_time }}
                                        </p>
                                        <p v-if="getSlot(subject, day.week_id)!.room_number" class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                                            Salle {{ getSlot(subject, day.week_id)!.room_number }}
                                        </p>
                                    </div>
                                    <span v-else class="text-gray-300 dark:text-gray-600 text-sm">—</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Vue par matière (détail) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div
                    v-for="subject in timetable"
                    :key="'card-' + subject.name"
                    class="card overflow-hidden"
                >
                    <div class="p-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50/80 dark:bg-gray-800/60">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">{{ subject.name }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            {{ activeDayCount(subject) }} jour(s) par semaine
                        </p>
                    </div>
                    <div class="p-3 space-y-1.5">
                        <template v-for="day in subject.week" :key="day.week_id">
                            <div v-if="day.start_time" class="flex items-center justify-between py-1.5 px-2 rounded-lg bg-gray-50 dark:bg-gray-800/40">
                                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ day.week_name }}</span>
                                <div class="text-right">
                                    <span class="text-xs font-semibold text-primary-600 dark:text-primary-400">
                                        {{ day.start_time }} – {{ day.end_time }}
                                    </span>
                                    <p v-if="day.room_number" class="text-xs text-gray-400">Salle {{ day.room_number }}</p>
                                </div>
                            </div>
                        </template>
                        <p v-if="activeDayCount(subject) === 0" class="text-xs text-gray-400 text-center py-2">
                            Aucun horaire défini
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vide -->
        <div v-else class="card p-12 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <p class="text-gray-500 dark:text-gray-400 font-medium">Aucun emploi du temps disponible.</p>
            <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Votre emploi du temps n'a pas encore été configuré.</p>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';

interface WeekDay {
    week_id: number;
    week_name: string;
    day?: number;
    start_time: string;
    end_time: string;
    room_number: string;
}

interface SubjectTimetable {
    name: string;
    week: WeekDay[];
}

const props = defineProps<{
    timetable: SubjectTimetable[];
}>();

// Tous les jours de la semaine (depuis le premier sujet)
const allDays = computed(() => {
    if (!props.timetable.length) return [];
    const todayNum = new Date().getDay(); // 0=dim, 1=lun…
    return props.timetable[0].week.map(w => ({
        week_id:   w.week_id,
        week_name: w.week_name,
        isToday:   w.day === todayNum,
    }));
});

const hasData = computed(() => props.timetable.length > 0);

// Récupérer le créneau d'une matière pour un jour donné
const getSlot = (subject: SubjectTimetable, weekId: number): WeekDay | null => {
    const day = subject.week.find(w => w.week_id === weekId);
    return day?.start_time ? day : null;
};

// Nombre de jours actifs pour une matière
const activeDayCount = (subject: SubjectTimetable) =>
    subject.week.filter(w => w.start_time).length;
</script>
