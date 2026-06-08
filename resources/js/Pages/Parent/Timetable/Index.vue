<template>
    <div class="space-y-6">
        <!-- Header -->
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Emploi du temps</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                <template v-if="student">{{ student.last_name }} {{ student.name }}</template>
                <template v-if="classInfo"> — {{ classInfo.name }}</template>
                <template v-if="subject"> — {{ subject.name }}</template>
            </p>
        </div>

        <!-- Infos élève -->
        <div v-if="student" class="card p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ student.last_name }} {{ student.name }}</p>
                <p v-if="classInfo" class="text-xs text-gray-500 dark:text-gray-400">{{ classInfo.name }}</p>
            </div>
        </div>

        <div v-if="activeSlots.length">
            <!-- Grille semaine -->
            <div class="card overflow-hidden mb-4">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Vue hebdomadaire — {{ subject?.name }}</h2>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-7 gap-2">
                        <div
                            v-for="day in allWeek"
                            :key="day.week_id"
                            class="rounded-xl p-3 text-center min-h-[90px] flex flex-col items-center justify-center border transition-all"
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

            <!-- Détail -->
            <div class="card overflow-hidden">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Détail des créneaux</h2>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    <div
                        v-for="day in activeSlots"
                        :key="day.week_id"
                        class="flex items-center justify-between px-4 py-3"
                    >
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full bg-primary-500 flex-shrink-0"/>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ day.week_name }}</span>
                        </div>
                        <div class="flex items-center gap-4 text-right">
                            <p class="text-sm font-semibold text-primary-600 dark:text-primary-400">
                                {{ day.start_time }} – {{ day.end_time }}
                            </p>
                            <div v-if="day.room_number" class="text-xs text-gray-500 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-md">
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
            <p class="text-gray-500 dark:text-gray-400 font-medium">Aucun horaire disponible.</p>
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
    class: { id: number; name: string } | null;
    subject: { id: number; name: string } | null;
    student: { id: number; name: string; last_name: string } | null;
    timetable: { week: WeekDay[] }[];
}>();

const classInfo = props.class;
const subject   = props.subject;
const student   = props.student;

const allWeek = computed<WeekDay[]>(() =>
    props.timetable[0]?.week ?? []
);

const activeSlots = computed<WeekDay[]>(() =>
    allWeek.value.filter(d => d.start_time)
);
</script>
