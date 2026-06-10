<template>
    <div class="space-y-6">
        <!-- Header -->
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Emploi du temps</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                <span v-if="classInfo && subject">
                    {{ classInfo.name }} — {{ subject.name }}
                </span>
                <span v-else>Horaires de cours</span>
            </p>
        </div>

        <div v-if="activeSlots.length">
            <!-- Grille semaine -->
            <div class="card overflow-hidden mb-4">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Horaires hebdomadaires</h2>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-7 gap-2">
                        <div
                            v-for="day in allWeek"
                            :key="day.week_id"
                            class="rounded-lg p-3 text-center min-h-[90px] flex flex-col items-center justify-center border transition-all"
                            :class="day.start_time
                                ? 'border-primary-300 dark:border-primary-700 bg-primary-50 dark:bg-primary-900/20'
                                : 'border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/30'"
                        >
                            <p class="text-xs font-semibold uppercase mb-1"
                               :class="day.start_time ? 'text-primary-600 dark:text-primary-400' : 'text-gray-400 dark:text-gray-500'">
                                {{ day.week_name.slice(0, 3) }}
                            </p>
                            <template v-if="day.start_time">
                                <p class="text-sm font-bold text-primary-700 dark:text-primary-300 leading-tight">{{ day.start_time }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 leading-tight">{{ day.end_time }}</p>
                                <p v-if="day.room_number" class="text-xs text-gray-400 dark:text-gray-500 mt-1 bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded">
                                    {{ day.room_number }}
                                </p>
                            </template>
                            <span v-else class="text-gray-300 dark:text-gray-600 text-xl">—</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Détail liste -->
            <div class="card overflow-hidden">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Détail des créneaux</h2>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    <div
                        v-for="day in activeSlots"
                        :key="day.week_id"
                        class="flex items-center justify-between px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors"
                    >
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full bg-primary-500 flex-shrink-0"/>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ day.week_name }}</span>
                        </div>
                        <div class="flex items-center gap-4 text-right">
                            <div>
                                <p class="text-sm font-semibold text-primary-600 dark:text-primary-400">
                                    {{ day.start_time }} – {{ day.end_time }}
                                </p>
                            </div>
                            <div v-if="day.room_number" class="text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-lg">
                                Salle {{ day.room_number }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-else class="card p-12 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <p class="text-gray-500 dark:text-gray-400 font-medium">Aucun horaire défini.</p>
            <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">L'emploi du temps de cette matière n'a pas encore été configuré.</p>
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

// Tous les jours de la semaine à partir du premier entry
const allWeek = computed<WeekDay[]>(() =>
    props.timetable[0]?.week ?? []
);

// Uniquement les jours avec des créneaux
const activeSlots = computed<WeekDay[]>(() =>
    allWeek.value.filter(d => d.start_time)
);
</script>
