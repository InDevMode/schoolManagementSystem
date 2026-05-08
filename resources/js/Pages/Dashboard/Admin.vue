<template>
    <div class="space-y-6">

        <!-- Page header -->
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tableau de bord</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                {{ today.toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }) }}
            </p>
        </div>

        <!-- Stats grid — 7 colonnes, 2 lignes (14 cards) -->
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3">
            <StatCard label="Administrateurs"  :value="totalAdmin"                    icon="shield"                  color="primary"   href="/admin/admin/list" />
            <StatCard label="Professeurs"       :value="totalTeacher"                  icon="academic-cap"            color="secondary" href="/admin/teacher/list" />
            <StatCard label="Apprenants"        :value="totalStudent"                  icon="user-group"              color="success"   href="/admin/student/list" />
            <StatCard label="Parents"           :value="totalParent"                   icon="users"                   color="warning"   href="/admin/parent/list" />
            <StatCard label="Classes"           :value="totalClass"                    icon="building-library"        color="info"      href="/admin/class/list" />
            <StatCard label="Matières"          :value="totalSubject"                  icon="book-open"               color="primary"   href="/admin/subject/list" />
            <StatCard label="Examens"           :value="totalExam"                     icon="clipboard-document-list" color="secondary" href="/admin/examinations/exam/list" />
            <StatCard label="Contributions"     :value="totalFeesCollections"          icon="banknotes"               color="success"   href="/admin/feescollections/collections/list" />
            <StatCard label="Présences"         :value="totalAttendance"               icon="user-check"              color="info"      href="/admin/attendance/students/list" />
            <StatCard label="Devoirs"           :value="totalHomework"                 icon="pencil"                  color="warning"   href="/admin/practicalworks/homework/list" />
            <StatCard label="Présents"          :value="totalAttendanceStudentPresent" icon="user-check"              color="success"   href="/admin/attendance/students/list" />
            <StatCard label="En retard"         :value="totalAttendanceStudentLate"    icon="calendar"                color="warning"   href="/admin/attendance/students/list" />
            <StatCard label="Absents"           :value="totalAttendanceStudentAbsent"  icon="user-check"              color="danger"    href="/admin/attendance/students/list" />
            <StatCard label="Demi-journée"      :value="totalAttendanceStudentHalfDay" icon="calendar-days"           color="info"      href="/admin/attendance/students/list" />
        </div>

        <!-- Calendrier — section séparée en bas -->
        <div class="card p-5">
            <!-- Header calendrier -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900 dark:text-white">
                    {{ monthName }} {{ calYear }}
                </h3>
                <div class="flex items-center gap-1">
                    <button class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500 transition-colors" @click="prevMonth">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                    </button>
                    <button class="px-3 py-1 text-xs font-medium rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-400 transition-colors" @click="goToday">
                        Aujourd'hui
                    </button>
                    <button class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500 transition-colors" @click="nextMonth">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </button>
                </div>
            </div>

            <!-- Jours de la semaine -->
            <div class="grid grid-cols-7 gap-1 mb-1">
                <div v-for="day in weekDays" :key="day" class="text-center text-xs font-semibold text-gray-400 dark:text-gray-500 py-1.5">
                    {{ day }}
                </div>
            </div>

            <!-- Grille des jours — rectangulaires -->
            <div class="grid grid-cols-7 gap-1.5">
                <!-- Cases vides avant le 1er -->
                <div v-for="_ in firstDayOfMonth" :key="`e-${_}`" class="h-12" />

                <!-- Jours -->
                <button
                    v-for="day in daysInMonth"
                    :key="day"
                    :class="[
                        'h-12 flex items-center justify-center rounded-xl text-sm font-medium transition-all',
                        isToday(day)
                            ? 'bg-primary-700 text-white font-bold ring-2 ring-primary-400 ring-offset-1'
                            : isSelected(day)
                                ? 'bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300'
                                : isWeekend(day)
                                    ? 'text-gray-400 dark:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700'
                                    : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700',
                    ]"
                    @click="selectedDay = day"
                >
                    {{ day }}
                </button>
            </div>

            <!-- Pied du calendrier -->
            <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    <span class="font-semibold text-gray-900 dark:text-white">{{ selectedDay }} {{ monthName }} {{ calYear }}</span>
                </p>
                <span v-if="isToday(selectedDay)" class="text-xs bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 px-2.5 py-1 rounded-full font-medium">
                    Aujourd'hui
                </span>
            </div>
        </div>

    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import StatCard from '@/Components/Dashboard/StatCard.vue';

defineProps<{
    totalAdmin: number;
    totalTeacher: number;
    totalStudent: number;
    totalParent: number;
    totalClass: number;
    totalSubject: number;
    totalExam: number;
    totalFeesCollections: number;
    totalAttendance: number;
    totalHomework: number;
    totalAttendanceStudentPresent: number;
    totalAttendanceStudentLate: number;
    totalAttendanceStudentAbsent: number;
    totalAttendanceStudentHalfDay: number;
    [key: string]: unknown;
}>();

// ── Calendrier ──────────────────────────────────────────────────────────────
const today       = new Date();
const calMonth    = ref(today.getMonth());
const calYear     = ref(today.getFullYear());
const selectedDay = ref(today.getDate());

const weekDays   = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
const monthNames = ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'];

const monthName = computed(() => monthNames[calMonth.value]);

const daysInMonth = computed(() =>
    new Date(calYear.value, calMonth.value + 1, 0).getDate()
);

// Lundi = 0 … Dimanche = 6
const firstDayOfMonth = computed(() => {
    const d = new Date(calYear.value, calMonth.value, 1).getDay();
    return d === 0 ? 6 : d - 1;
});

const isToday = (day: number) =>
    day === today.getDate() &&
    calMonth.value === today.getMonth() &&
    calYear.value  === today.getFullYear();

const isSelected = (day: number) => day === selectedDay.value && !isToday(day);

// Samedi et dimanche
const isWeekend = (day: number) => {
    const dow = new Date(calYear.value, calMonth.value, day).getDay();
    return dow === 0 || dow === 6;
};

const prevMonth = () => {
    if (calMonth.value === 0) { calMonth.value = 11; calYear.value--; }
    else calMonth.value--;
    selectedDay.value = 1;
};

const nextMonth = () => {
    if (calMonth.value === 11) { calMonth.value = 0; calYear.value++; }
    else calMonth.value++;
    selectedDay.value = 1;
};

const goToday = () => {
    calMonth.value    = today.getMonth();
    calYear.value     = today.getFullYear();
    selectedDay.value = today.getDate();
};
</script>
