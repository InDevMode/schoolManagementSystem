<template>
    <div class="space-y-6">

        <!-- Header -->
        <PageHeader title="Mes Enfants" :subtitle="`${myStudents.data.length} enfant${myStudents.data.length > 1 ? 's' : ''} enregistré${myStudents.data.length > 1 ? 's' : ''}`" color="violet">
            <template #icon>
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </template>
        </PageHeader>

        <!-- Empty state -->
        <div v-if="!myStudents.data.length"
             class="card p-16 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Aucun enfant assigné</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Contactez l'administration pour associer vos enfants à votre compte.</p>
        </div>

        <!-- Students grid -->
        <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            <div
                v-for="student in myStudents.data"
                :key="student.id"
                class="card overflow-hidden hover:shadow-md transition-shadow duration-200"
            >
                <!-- Card header -->
                <div class="relative px-5 pt-5 pb-4 bg-gradient-to-br from-primary-50 to-indigo-50 dark:from-primary-900/20 dark:to-indigo-900/20 border-b border-gray-100 dark:border-gray-700">
                    <div class="flex items-center gap-4">
                        <!-- Avatar -->
                        <div class="w-14 h-14 rounded-xl overflow-hidden flex-shrink-0 bg-primary-100 dark:bg-primary-900/40 flex items-center justify-center shadow-md">
                            <img v-if="student.profile_picture"
                                 :src="`/upload/profile/${student.profile_picture}`"
                                 class="w-full h-full object-cover"/>
                            <span v-else class="text-xl font-bold text-primary-700 dark:text-primary-300">
                                {{ (student.last_name?.[0] ?? '') }}{{ (student.name?.[0] ?? '') }}
                            </span>
                        </div>
                        <!-- Name + class -->
                        <div class="flex-1 min-w-0">
                            <h2 class="text-base font-bold text-gray-900 dark:text-white truncate">
                                {{ student.last_name }} {{ student.name }}
                            </h2>
                            <p v-if="student.class_name"
                               class="mt-1 inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1 rounded-full
                                      bg-primary-100 dark:bg-primary-900/40 text-primary-700 dark:text-primary-300">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                {{ student.class_name }}
                            </p>
                            <p v-else class="mt-1 text-xs text-gray-400">Aucune classe assignée</p>
                        </div>
                    </div>

                    <!-- Admission number badge -->
                    <div v-if="student.admission_number"
                         class="absolute top-3 right-3 text-[10px] font-mono font-semibold px-2 py-0.5
                                rounded-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600
                                text-gray-500 dark:text-gray-400 shadow-sm">
                        {{ student.admission_number }}
                    </div>
                </div>

                <!-- Quick actions -->
                <div class="px-4 py-3 grid grid-cols-2 gap-2">
                    <Link
                        :href="`/parent/my_student/${student.id}/subject`"
                        class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl
                               bg-violet-50 dark:bg-violet-900/20 hover:bg-violet-100 dark:hover:bg-violet-900/30
                               text-violet-700 dark:text-violet-400 text-xs font-semibold
                               transition-colors duration-150 border border-violet-100 dark:border-violet-700/50"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        Matières
                    </Link>

                    <button
                        v-if="student.class_id"
                        @click="openTimetable(student)"
                        class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl
                               bg-violet-50 dark:bg-violet-900/20 hover:bg-violet-100 dark:hover:bg-violet-900/30
                               text-violet-700 dark:text-violet-400 text-xs font-semibold
                               transition-colors duration-150 border border-violet-100 dark:border-violet-700/50"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Emploi du temps
                    </button>
                    <span v-else
                          class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl
                                 bg-gray-50 dark:bg-gray-800/60 text-gray-400 dark:text-gray-500
                                 text-xs font-medium cursor-not-allowed border border-gray-100 dark:border-gray-700">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Emploi du temps
                    </span>

                    <Link
                        :href="`/parent/my_student/${student.id}/bulletins`"
                        class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl
                               bg-emerald-50 dark:bg-emerald-900/20 hover:bg-emerald-100 dark:hover:bg-emerald-900/30
                               text-emerald-700 dark:text-emerald-400 text-xs font-semibold
                               transition-colors duration-150 border border-emerald-100 dark:border-emerald-700/50"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Bulletins
                    </Link>

                    <Link
                        :href="`/parent/my_student/attendance/${student.id}`"
                        class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl
                               bg-amber-50 dark:bg-amber-900/20 hover:bg-amber-100 dark:hover:bg-amber-900/30
                               text-amber-700 dark:text-amber-400 text-xs font-semibold
                               transition-colors duration-150 border border-amber-100 dark:border-amber-700/50"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        Présences
                    </Link>
                </div>
            </div>
        </div>

        <!-- ── Timetable Modal ──────────────────────────────────────────────── -->
        <AppModal v-model="showTimetable" :title="timetableTitle" size="xl">
            <div v-if="loadingTimetable" class="flex items-center justify-center py-16">
                <svg class="w-8 h-8 animate-spin text-primary-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
            </div>

            <div v-else-if="timetableData.length" class="space-y-5">
                <!-- Grille matricielle : jours en colonnes, matières en lignes -->
                <div class="overflow-x-auto rounded-xl border border-gray-100 dark:border-gray-700">
                    <table class="min-w-full text-xs">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-800/80">
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[130px]">
                                    Matière
                                </th>
                                <th
                                    v-for="day in weekDays"
                                    :key="day.week_id"
                                    class="px-3 py-3 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[80px]"
                                >
                                    {{ day.week_name.slice(0, 3) }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50 bg-white dark:bg-gray-800">
                            <tr
                                v-for="subject in timetableData"
                                :key="subject.name"
                                class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors"
                            >
                                <td class="px-4 py-3">
                                    <span class="font-semibold text-gray-800 dark:text-gray-200 text-xs">{{ subject.name }}</span>
                                </td>
                                <td
                                    v-for="day in subject.week"
                                    :key="day.week_id"
                                    class="px-2 py-2 text-center"
                                >
                                    <div v-if="day.start_time"
                                         class="rounded-xl px-2 py-1.5 bg-primary-50 dark:bg-primary-900/30
                                                border border-primary-100 dark:border-primary-700/50">
                                        <p class="font-bold text-primary-700 dark:text-primary-300 text-[11px] leading-tight">{{ day.start_time }}</p>
                                        <p class="text-gray-500 dark:text-gray-400 text-[10px] leading-tight">{{ day.end_time }}</p>
                                        <p v-if="day.room_number"
                                           class="mt-0.5 text-[9px] text-gray-400 bg-gray-100 dark:bg-gray-700 rounded px-1 py-0.5 inline-block">
                                            {{ day.room_number }}
                                        </p>
                                    </div>
                                    <span v-else class="text-gray-300 dark:text-gray-600 text-base">—</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Légende -->
                <p class="text-[11px] text-gray-400 dark:text-gray-500 text-center">
                    Emploi du temps de la classe <strong>{{ activeStudent?.class_name }}</strong>
                </p>
            </div>

            <div v-else class="py-16 text-center">
                <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                    <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Aucun emploi du temps disponible pour cette classe.</p>
            </div>

            <template #footer>
                <AppButton variant="close" @click="showTimetable = false">Fermer</AppButton>
            </template>
        </AppModal>

    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { PageHeader, AppModal, AppButton } from '@/Components/UI';

interface Student {
    id: number;
    name: string;
    last_name: string;
    class_name: string | null;
    class_id: number | null;
    admission_number: string | null;
    profile_picture: string | null;
}

interface WeekSlot {
    week_id: number;
    week_name: string;
    start_time: string;
    end_time: string;
    room_number: string;
}

interface SubjectTimetable {
    name: string;
    week: WeekSlot[];
}

const props = defineProps<{
    myStudents: { data: Student[] };
}>();

const showTimetable    = ref(false);
const loadingTimetable = ref(false);
const timetableData    = ref<SubjectTimetable[]>([]);
const activeStudent    = ref<Student | null>(null);

const timetableTitle = computed(() =>
    activeStudent.value
        ? `Emploi du temps — ${activeStudent.value.last_name} ${activeStudent.value.name}`
        : 'Emploi du temps'
);

const weekDays = computed<WeekSlot[]>(() =>
    timetableData.value[0]?.week ?? []
);

const openTimetable = async (student: Student) => {
    if (!student.class_id) return;
    activeStudent.value    = student;
    showTimetable.value    = true;
    loadingTimetable.value = true;
    timetableData.value    = [];

    try {
        const res  = await fetch(`/parent/my_student/${student.class_id}/timetable/full`, {
            headers: { Accept: 'application/json' },
        });
        const data = await res.json();
        timetableData.value = data.timetable ?? [];
    } catch {
        timetableData.value = [];
    } finally {
        loadingTimetable.value = false;
    }
};
</script>
