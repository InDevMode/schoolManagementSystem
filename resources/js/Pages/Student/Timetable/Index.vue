<template>
    <div class="space-y-6">

        <!-- ── Header ──────────────────────────────────────────────────── -->
        <div class="flex items-center gap-4">
            <div>
                <PageHeader title="Mon emploi du temps" subtitle="Horaires de cours de la semaine" color="primary">
                    <template #icon>
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </template>
                </PageHeader>
            </div>
        </div>

        <div v-if="hasData" class="space-y-5">

            <!-- ── Grille matricielle jours × matières ────────────────── -->
            <div class="card overflow-hidden">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50/80 dark:bg-gray-800/60 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Vue hebdomadaire</h2>
                    <span class="text-xs text-gray-400">{{ timetable.length }} matière{{ timetable.length > 1 ? 's' : '' }}</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm border-collapse">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-800/60">
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider border-b border-r border-gray-200 dark:border-gray-700 w-32">
                                    Jour
                                </th>
                                <th
                                    v-for="subject in timetable"
                                    :key="subject.name"
                                    class="px-4 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider border-b border-r border-gray-200 dark:border-gray-700 min-w-[130px]"
                                >
                                    <div class="flex flex-col items-center gap-1">
                                        <div class="w-6 h-6 rounded-lg flex items-center justify-center"
                                             :style="{ backgroundColor: subjectColors[subject.name] + '20', border: '1.5px solid ' + subjectColors[subject.name] + '60' }">
                                            <span class="w-2 h-2 rounded-full" :style="{ backgroundColor: subjectColors[subject.name] }"/>
                                        </div>
                                        <span>{{ subject.name }}</span>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="day in allDays"
                                :key="day.week_id"
                                class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors"
                                :class="day.isToday ? 'bg-primary-50/50 dark:bg-primary-900/10' : ''"
                            >
                                <!-- Jour -->
                                <td class="px-4 py-3 border-r border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center gap-2.5">
                                        <div
                                            class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 text-xs font-bold transition-all"
                                            :class="day.isToday
                                                ? 'bg-primary-500 text-white shadow-md shadow-primary-500/30'
                                                : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400'"
                                        >{{ day.week_name.slice(0, 3) }}</div>
                                        <div>
                                            <span
                                                class="text-sm font-semibold block leading-tight"
                                                :class="day.isToday ? 'text-primary-700 dark:text-primary-300' : 'text-gray-700 dark:text-gray-300'"
                                            >{{ day.week_name }}</span>
                                            <span v-if="day.isToday" class="text-xs text-primary-500 font-medium">Aujourd'hui</span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Créneaux par matière -->
                                <td
                                    v-for="subject in timetable"
                                    :key="subject.name + day.week_id"
                                    class="px-3 py-2 border-r border-gray-100 dark:border-gray-700 text-center"
                                >
                                    <div v-if="getSlot(subject, day.week_id)"
                                         class="rounded-lg p-2 border transition-all hover:opacity-90"
                                         :style="{
                                             backgroundColor: subjectColors[subject.name] + '15',
                                             borderColor: subjectColors[subject.name] + '40',
                                             borderLeftColor: subjectColors[subject.name],
                                             borderLeftWidth: '3px',
                                         }"
                                    >
                                        <p class="text-xs font-bold leading-tight" :style="{ color: subjectColors[subject.name] }">
                                            {{ getSlot(subject, day.week_id)!.start_time }}
                                        </p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500 leading-tight mt-0.5">
                                            {{ getSlot(subject, day.week_id)!.end_time }}
                                        </p>
                                        <p v-if="getSlot(subject, day.week_id)!.room_number"
                                           class="text-xs text-gray-400 dark:text-gray-500 mt-1 bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded-md">
                                            Salle {{ getSlot(subject, day.week_id)!.room_number }}
                                        </p>
                                    </div>
                                    <span v-else class="text-gray-300 dark:text-gray-600 text-base">–</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ── Cards par matière ───────────────────────────────────── -->
            <div>
                <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Détail par matière</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div
                        v-for="subject in timetable"
                        :key="'card-' + subject.name"
                        class="card overflow-hidden hover:shadow-md transition-all"
                    >
                        <!-- En-tête carte -->
                        <div class="p-4 flex items-center gap-3 border-b border-gray-100 dark:border-gray-700"
                             :style="{ background: `linear-gradient(135deg, ${subjectColors[subject.name]}12 0%, transparent 80%)` }">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm"
                                 :style="{ backgroundColor: subjectColors[subject.name] }">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ subject.name }}</h3>
                                <p class="text-xs mt-0.5" :style="{ color: subjectColors[subject.name] }">
                                    {{ activeDayCount(subject) }} jour{{ activeDayCount(subject) > 1 ? 's' : '' }} / semaine
                                </p>
                            </div>
                        </div>

                        <!-- Créneaux -->
                        <div class="p-3 space-y-1.5">
                            <template v-for="day in subject.week" :key="day.week_id">
                                <div v-if="day.start_time"
                                     class="flex items-center justify-between py-1.5 px-2.5 rounded-lg bg-gray-50 dark:bg-gray-800/40 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors">
                                    <div class="flex items-center gap-2">
                                        <div class="w-1.5 h-1.5 rounded-full flex-shrink-0"
                                             :style="{ backgroundColor: subjectColors[subject.name] }"/>
                                        <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">{{ day.week_name }}</span>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-xs font-bold" :style="{ color: subjectColors[subject.name] }">
                                            {{ day.start_time }} – {{ day.end_time }}
                                        </span>
                                        <p v-if="day.room_number" class="text-xs text-gray-400">Salle {{ day.room_number }}</p>
                                    </div>
                                </div>
                            </template>
                            <p v-if="activeDayCount(subject) === 0" class="text-xs text-gray-400 text-center py-3 italic">
                                Aucun horaire défini
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── État vide ───────────────────────────────────────────────── -->
        <div v-else class="card p-14 text-center">
            <div class="w-20 h-20 mx-auto mb-5 rounded-3xl bg-gradient-to-br from-primary-100 to-indigo-100 dark:from-primary-900/40 dark:to-indigo-900/40 flex items-center justify-center">
                <svg class="w-10 h-10 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <p class="text-gray-700 dark:text-gray-300 font-semibold">Aucun emploi du temps disponible</p>
            <p class="text-gray-400 dark:text-gray-500 text-sm mt-1.5">Votre emploi du temps n'a pas encore été configuré.</p>
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

const PALETTE = [
    '#6366f1','#3b82f6','#8b5cf6','#06b6d4','#10b981',
    '#f59e0b','#ef4444','#ec4899','#84cc16','#f97316',
];

const props = defineProps<{
    timetable: SubjectTimetable[];
}>();

// Couleur par matière
const subjectColors = computed(() => {
    const map: Record<string, string> = {};
    props.timetable.forEach((s, i) => { map[s.name] = PALETTE[i % PALETTE.length]; });
    return map;
});

const allDays = computed(() => {
    if (!props.timetable.length) return [];
    const todayNum = new Date().getDay();
    return props.timetable[0].week.map(w => ({
        week_id:   w.week_id,
        week_name: w.week_name,
        isToday:   w.day === todayNum,
    }));
});

const hasData = computed(() => props.timetable.length > 0);

const getSlot = (subject: SubjectTimetable, weekId: number): WeekDay | null => {
    const day = subject.week.find(w => w.week_id === weekId);
    return day?.start_time ? day : null;
};

const activeDayCount = (subject: SubjectTimetable) =>
    subject.week.filter(w => w.start_time).length;
</script>
