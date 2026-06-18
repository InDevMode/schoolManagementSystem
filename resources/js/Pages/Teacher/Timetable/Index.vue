<template>
    <div class="space-y-6">

        <!-- ── Header ──────────────────────────────────────────────────── -->
        <PageHeader title="Emploi du temps" :subtitle="classInfo && subject ? `${classInfo.name} · ${subject.name}` : 'Horaires de cours'" color="primary">
            <template #icon>
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </template>
        </PageHeader>

        <div v-if="activeSlots.length">

            <!-- ── Grille visuelle semaine ─────────────────────────────── -->
            <div class="card overflow-hidden mb-4">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50/80 dark:bg-gray-800/60 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Vue hebdomadaire</h2>
                    <span class="flex items-center gap-1.5 text-xs font-semibold text-primary-700 dark:text-primary-300 bg-primary-50 dark:bg-primary-900/20 px-2.5 py-1 rounded-full border border-primary-200 dark:border-primary-800">
                        <span class="w-1.5 h-1.5 rounded-full bg-primary-500"/>
                        {{ activeSlots.length }} créneau{{ activeSlots.length > 1 ? 'x' : '' }}
                    </span>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-7 gap-2">
                        <div
                            v-for="day in allWeek"
                            :key="day.week_id"
                            class="rounded-xl p-3 text-center min-h-[96px] flex flex-col items-center justify-center border-2 transition-all"
                            :class="day.start_time
                                ? 'border-primary-400 dark:border-primary-600 bg-primary-500 shadow-lg shadow-primary-500/25'
                                : 'border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/30'"
                        >
                            <p
                                class="text-xs font-bold uppercase tracking-wide mb-2"
                                :class="day.start_time ? 'text-primary-100' : 'text-gray-400 dark:text-gray-500'"
                            >{{ day.week_name.slice(0, 3) }}</p>
                            <template v-if="day.start_time">
                                <p class="text-sm font-bold text-white leading-tight">{{ day.start_time }}</p>
                                <div class="w-4 h-px bg-primary-300 my-1"/>
                                <p class="text-xs text-primary-200 leading-tight">{{ day.end_time }}</p>
                                <p v-if="day.room_number"
                                   class="text-xs text-primary-200 mt-2 bg-primary-600/50 px-1.5 py-0.5 rounded-md truncate max-w-full">
                                    {{ day.room_number }}
                                </p>
                            </template>
                            <span v-else class="text-gray-300 dark:text-gray-600 text-xl font-light">–</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── Détail liste ────────────────────────────────────────── -->
            <div class="card overflow-hidden">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50/80 dark:bg-gray-800/60">
                    <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Détail des créneaux</h2>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-700/60">
                    <div
                        v-for="day in activeSlots"
                        :key="day.week_id"
                        class="flex items-center justify-between px-5 py-4 hover:bg-gray-50/70 dark:hover:bg-gray-800/40 transition-colors group"
                    >
                        <!-- Jour -->
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center flex-shrink-0 group-hover:bg-primary-200 dark:group-hover:bg-primary-900/50 transition-colors">
                                <span class="text-xs font-bold text-primary-700 dark:text-primary-300">{{ day.week_name.slice(0, 3) }}</span>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ day.week_name }}</p>
                                <p v-if="day.start_time && day.end_time" class="text-xs text-gray-400 mt-0.5">
                                    {{ getDuration(day.start_time, day.end_time) }}
                                </p>
                            </div>
                        </div>

                        <!-- Horaires + salle -->
                        <div class="flex items-center gap-3">
                            <div class="text-right">
                                <p class="text-sm font-bold text-primary-600 dark:text-primary-400">
                                    {{ day.start_time }}
                                </p>
                                <p class="text-xs text-gray-400">→ {{ day.end_time }}</p>
                            </div>
                            <div v-if="day.room_number"
                                 class="flex items-center gap-1.5 text-xs font-medium text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2.5 py-1.5 rounded-lg border border-gray-200 dark:border-gray-600">
                                <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                                </svg>
                                Salle {{ day.room_number }}
                            </div>
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
            <p class="text-gray-700 dark:text-gray-300 font-semibold">Aucun horaire défini</p>
            <p class="text-gray-400 dark:text-gray-500 text-sm mt-1.5">L'emploi du temps de cette matière n'a pas encore été configuré.</p>
        </div>

    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';

interface WeekDay {
    week_id: number;
    week_name: string;
    start_time: string;
    end_time: string;
    room_number: string;
}

const props = defineProps<{
    classInfo: { id: number; name: string } | null;
    subject:   { id: number; name: string } | null;
    timetable: { week: WeekDay[] }[];
}>();

const allWeek = computed<WeekDay[]>(() => props.timetable[0]?.week ?? []);
const activeSlots = computed<WeekDay[]>(() => allWeek.value.filter(d => d.start_time));

const getDuration = (start: string, end: string): string => {
    const [sh, sm] = start.split(':').map(Number);
    const [eh, em] = end.split(':').map(Number);
    const mins = (eh * 60 + em) - (sh * 60 + sm);
    if (mins <= 0) return '';
    const h = Math.floor(mins / 60), m = mins % 60;
    return h > 0 ? (m > 0 ? `${h}h${String(m).padStart(2,'0')}` : `${h}h`) : `${m}min`;
};
</script>
