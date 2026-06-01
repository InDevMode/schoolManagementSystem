<template>
    <div class="space-y-6">

        <!-- ── En-tête ── -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tableau de bord</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    {{ today.toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }) }}
                </p>
            </div>
            <!-- Badge date -->
            <div class="hidden sm:flex items-center gap-2 px-4 py-2 rounded-xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-card">
                <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ today.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' }) }}
                </span>
            </div>
        </div>

        <!-- ── 4 grandes stat cards ── -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <BigStatCard
                label="Apprenants"
                :value="totalStudent"
                icon="user-group"
                color="primary"
                trend="+12%"
                href="/admin/student/list"
            />
            <BigStatCard
                label="Professeurs"
                :value="totalTeacher"
                icon="academic-cap"
                color="info"
                trend="+3%"
                href="/admin/teacher/list"
            />
            <BigStatCard
                label="Parents"
                :value="totalParent"
                icon="users"
                color="warning"
                trend="+8%"
                href="/admin/parent/list"
            />
            <BigStatCard
                label="Contributions"
                :value="totalFeesCollections"
                icon="banknotes"
                color="success"
                trend="+21%"
                href="/admin/feescollections/collections/list"
                prefix="FCFA"
            />
        </div>

        <!-- ── Ligne 2 : grille de petites stats ── -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
            <StatCard label="Administrateurs" :value="totalAdmin"    icon="shield"                  color="primary"   href="/admin/admin/list" />
            <StatCard label="Classes"         :value="totalClass"    icon="building-library"        color="secondary" href="/admin/class/list" />
            <StatCard label="Matières"        :value="totalSubject"  icon="book-open"               color="info"      href="/admin/subject/list" />
            <StatCard label="Examens"         :value="totalExam"     icon="clipboard-document-list" color="warning"   href="/admin/examinations/exam/list" />
            <StatCard label="Devoirs"         :value="totalHomework" icon="pencil"                  color="success"   href="/admin/practicalworks/homework/list" />
        </div>

        <!-- ── Ligne 3 : Présences du jour ── -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <AttendanceBadge label="Présents"    :value="totalAttendanceStudentPresent" color="success" icon="user-check" />
            <AttendanceBadge label="En retard"   :value="totalAttendanceStudentLate"    color="warning" icon="calendar" />
            <AttendanceBadge label="Absents"     :value="totalAttendanceStudentAbsent"  color="danger"  icon="user-check" />
            <AttendanceBadge label="Demi-journée":value="totalAttendanceStudentHalfDay" color="info"    icon="calendar-days" />
        </div>

        <!-- ── Ligne 4 : Graphique + Donut ── -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

            <!-- Graphique barres (2/3) -->
            <div class="lg:col-span-2 card p-5">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">Résultats des examens</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Apprenants & Professeurs</p>
                    </div>
                    <div class="flex items-center gap-3 text-xs text-gray-500">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-primary-500 inline-block"/>
                            Professeurs
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-warning-500 inline-block"/>
                            Apprenants
                        </span>
                    </div>
                </div>
                <!-- Graphique SVG simplifié -->
                <div class="relative h-48">
                    <svg viewBox="0 0 600 180" class="w-full h-full" preserveAspectRatio="none">
                        <!-- Grille -->
                        <line x1="0" y1="0"   x2="600" y2="0"   stroke="currentColor" stroke-width="0.5" class="text-gray-100 dark:text-gray-700"/>
                        <line x1="0" y1="45"  x2="600" y2="45"  stroke="currentColor" stroke-width="0.5" class="text-gray-100 dark:text-gray-700"/>
                        <line x1="0" y1="90"  x2="600" y2="90"  stroke="currentColor" stroke-width="0.5" class="text-gray-100 dark:text-gray-700"/>
                        <line x1="0" y1="135" x2="600" y2="135" stroke="currentColor" stroke-width="0.5" class="text-gray-100 dark:text-gray-700"/>
                        <line x1="0" y1="180" x2="600" y2="180" stroke="currentColor" stroke-width="0.5" class="text-gray-100 dark:text-gray-700"/>

                        <!-- Barres Professeurs (violet) -->
                        <g fill="#7c3aed" opacity="0.85">
                            <rect x="18"  y="60"  width="22" height="120" rx="4"/>
                            <rect x="68"  y="40"  width="22" height="140" rx="4"/>
                            <rect x="118" y="20"  width="22" height="160" rx="4"/>
                            <rect x="168" y="50"  width="22" height="130" rx="4"/>
                            <rect x="218" y="10"  width="22" height="170" rx="4"/>
                            <rect x="268" y="35"  width="22" height="145" rx="4"/>
                            <rect x="318" y="55"  width="22" height="125" rx="4"/>
                            <rect x="368" y="25"  width="22" height="155" rx="4"/>
                            <rect x="418" y="45"  width="22" height="135" rx="4"/>
                            <rect x="468" y="15"  width="22" height="165" rx="4"/>
                            <rect x="518" y="30"  width="22" height="150" rx="4"/>
                            <rect x="568" y="50"  width="22" height="130" rx="4"/>
                        </g>
                        <!-- Barres Apprenants (orange) -->
                        <g fill="#f59e0b" opacity="0.75">
                            <rect x="42"  y="80"  width="22" height="100" rx="4"/>
                            <rect x="92"  y="65"  width="22" height="115" rx="4"/>
                            <rect x="142" y="50"  width="22" height="130" rx="4"/>
                            <rect x="192" y="70"  width="22" height="110" rx="4"/>
                            <rect x="242" y="40"  width="22" height="140" rx="4"/>
                            <rect x="292" y="60"  width="22" height="120" rx="4"/>
                            <rect x="342" y="75"  width="22" height="105" rx="4"/>
                            <rect x="392" y="45"  width="22" height="135" rx="4"/>
                            <rect x="442" y="65"  width="22" height="115" rx="4"/>
                            <rect x="492" y="35"  width="22" height="145" rx="4"/>
                            <rect x="542" y="55"  width="22" height="125" rx="4"/>
                            <rect x="592" y="70"  width="22" height="110" rx="4"/>
                        </g>
                    </svg>
                    <!-- Labels mois -->
                    <div class="absolute bottom-0 left-0 right-0 flex justify-between px-2 text-[10px] text-gray-400">
                        <span v-for="m in months" :key="m">{{ m }}</span>
                    </div>
                </div>
            </div>

            <!-- Donut apprenants (1/3) -->
            <div class="card p-5 flex flex-col">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-900 dark:text-white">Apprenants</h3>
                    <button class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-400 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                        </svg>
                    </button>
                </div>
                <!-- Donut SVG -->
                <div class="flex-1 flex items-center justify-center">
                    <div class="relative w-40 h-40">
                        <svg viewBox="0 0 100 100" class="w-full h-full -rotate-90">
                            <!-- Fond -->
                            <circle cx="50" cy="50" r="38" fill="none" stroke="#f3f4f6" stroke-width="14" class="dark:stroke-gray-700"/>
                            <!-- Filles (orange) — 40% -->
                            <circle cx="50" cy="50" r="38" fill="none" stroke="#f59e0b" stroke-width="14"
                                    stroke-dasharray="95.5 143.3" stroke-dashoffset="0" stroke-linecap="round"/>
                            <!-- Garçons (violet) — 60% -->
                            <circle cx="50" cy="50" r="38" fill="none" stroke="#7c3aed" stroke-width="14"
                                    stroke-dasharray="143.3 95.5" stroke-dashoffset="-95.5" stroke-linecap="round"/>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ totalStudent.toLocaleString('fr-FR') }}</span>
                            <span class="text-xs text-gray-400">Total</span>
                        </div>
                    </div>
                </div>
                <!-- Légende -->
                <div class="flex justify-center gap-6 mt-4">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-primary-600 inline-block"/>
                        <span class="text-xs text-gray-600 dark:text-gray-400">Garçons</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-warning-500 inline-block"/>
                        <span class="text-xs text-gray-600 dark:text-gray-400">Filles</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Ligne 5 : Calendrier style Events (light/dark adaptatif) ── -->
        <div :class="[
            'rounded-2xl overflow-hidden border transition-colors duration-300',
            isDark
                ? 'border-white/8'
                : 'border-gray-200 shadow-card-md',
        ]" :style="isDark ? 'background:#1a1a2e' : 'background:#f8f7ff'">
            <div class="flex flex-col xl:flex-row">

                <!-- ── Zone calendrier ── -->
                <div class="flex-1 p-6">

                    <!-- Header calendrier -->
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <h3 :class="['text-xl font-bold', isDark ? 'text-white' : 'text-gray-900']">
                                {{ monthName }}
                            </h3>
                            <span class="text-xl font-bold text-primary-500">{{ calYear }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <button
                                :class="[
                                    'w-9 h-9 rounded-xl flex items-center justify-center transition-all',
                                    isDark
                                        ? 'bg-white/10 hover:bg-primary-600 text-white'
                                        : 'bg-gray-200 hover:bg-primary-600 hover:text-white text-gray-600',
                                ]"
                                @click="prevMonth"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </button>
                            <button
                                :class="[
                                    'px-4 py-1.5 rounded-xl text-xs font-semibold transition-all',
                                    isDark
                                        ? 'bg-white/10 hover:bg-primary-600 text-white'
                                        : 'bg-gray-200 hover:bg-primary-600 hover:text-white text-gray-600',
                                ]"
                                @click="goToday"
                            >
                                Aujourd'hui
                            </button>
                            <button
                                :class="[
                                    'w-9 h-9 rounded-xl flex items-center justify-center transition-all',
                                    isDark
                                        ? 'bg-white/10 hover:bg-primary-600 text-white'
                                        : 'bg-gray-200 hover:bg-primary-600 hover:text-white text-gray-600',
                                ]"
                                @click="nextMonth"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Jours de la semaine -->
                    <div class="grid grid-cols-7 gap-2 mb-2">
                        <div
                            v-for="day in weekDays"
                            :key="day"
                            :class="[
                                'text-center text-xs font-semibold py-2',
                                isDark ? 'text-white/45' : 'text-gray-400',
                            ]"
                        >
                            {{ day }}
                        </div>
                    </div>

                    <!-- Grille des jours -->
                    <div class="grid grid-cols-7 gap-2">
                        <!-- Cases vides avant le 1er -->
                        <div
                            v-for="_ in firstDayOfMonth"
                            :key="`empty-${_}`"
                            class="rounded-xl h-16"
                            :style="isDark ? 'background:rgba(255,255,255,0.03)' : 'background:rgba(0,0,0,0.03)'"
                        />

                        <!-- Jours du mois -->
                        <button
                            v-for="day in daysInMonth"
                            :key="day"
                            :class="[
                                'relative rounded-xl h-16 flex flex-col items-start justify-start p-2 transition-all duration-150 text-left',
                                isToday(day) ? 'ring-2 ring-primary-400' : '',
                            ]"
                            :style="getDayStyle(day)"
                            @click="selectedDay = day"
                        >
                            <!-- Numéro du jour -->
                            <span :class="['text-sm font-semibold leading-none', getDayTextColor(day)]">
                                {{ day }}
                            </span>

                            <!-- Mini label événement -->
                            <div
                                v-if="getDayEvents(day).length"
                                class="absolute bottom-1.5 left-2 right-2 text-[9px] font-medium truncate leading-none"
                                :style="{ color: getDayEvents(day)[0].highlightBg ? 'rgba(255,255,255,0.9)' : getDayEvents(day)[0].color }"
                            >
                                {{ getDayEvents(day)[0].title }}
                            </div>

                            <!-- Points si plusieurs événements -->
                            <div v-if="getDayEvents(day).length > 1" class="flex gap-1 mt-auto">
                                <span
                                    v-for="(ev, i) in getDayEvents(day).slice(0, 3)"
                                    :key="i"
                                    class="w-2 h-2 rounded-full"
                                    :style="{ background: ev.color }"
                                />
                            </div>
                        </button>
                    </div>
                </div>

                <!-- ── Panneau latéral : liste d'événements ── -->
                <div
                    :class="[
                        'xl:w-72 border-t xl:border-t-0 xl:border-l flex flex-col',
                        isDark ? 'border-white/8' : 'border-gray-200',
                    ]"
                >
                    <div :class="['p-5 border-b', isDark ? 'border-white/8' : 'border-gray-200']">
                        <h4 :class="['font-semibold text-sm', isDark ? 'text-white' : 'text-gray-900']">
                            Liste des événements
                        </h4>
                        <p :class="['text-xs mt-0.5', isDark ? 'text-white/40' : 'text-gray-400']">
                            {{ monthName }} {{ calYear }}
                        </p>
                    </div>

                    <div :class="['flex-1 overflow-y-auto', isDark ? 'divide-white/6' : 'divide-gray-100', 'divide-y']">
                        <div
                            v-for="ev in calendarEvents"
                            :key="ev.id"
                            :class="[
                                'p-4 transition-colors cursor-pointer',
                                isDark ? 'hover:bg-white/5' : 'hover:bg-gray-50',
                            ]"
                        >
                            <!-- Date + menu -->
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs font-semibold" :style="{ color: ev.color }">
                                    {{ ev.dateLabel }}
                                </span>
                                <button :class="isDark ? 'text-white/30 hover:text-white/70' : 'text-gray-300 hover:text-gray-500'" class="transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01"/>
                                    </svg>
                                </button>
                            </div>

                            <!-- Titre -->
                            <p :class="['text-sm font-bold leading-tight', isDark ? 'text-white' : 'text-gray-900']">
                                {{ ev.title }}
                            </p>

                            <!-- Horaire + prix -->
                            <div class="flex items-center justify-between mt-2">
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" :class="isDark ? 'text-white/40' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span :class="['text-xs', isDark ? 'text-white/45' : 'text-gray-500']">{{ ev.time }}</span>
                                </div>
                                <span :class="['text-xs font-bold', isDark ? 'text-white' : 'text-gray-800']">{{ ev.price }}</span>
                            </div>

                            <!-- Barre de progression -->
                            <div :class="['mt-2.5 h-1 rounded-full', isDark ? 'bg-white/10' : 'bg-gray-200']">
                                <div class="h-1 rounded-full transition-all" :style="{ width: ev.progress + '%', background: ev.color }"/>
                            </div>
                            <p :class="['text-[10px] mt-1 text-right', isDark ? 'text-white/35' : 'text-gray-400']">
                                {{ ev.seats }} places restantes
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { useDark } from '@vueuse/core';
import StatCard from '@/Components/Dashboard/StatCard.vue';
import BigStatCard from '@/Components/Dashboard/BigStatCard.vue';
import AttendanceBadge from '@/Components/Dashboard/AttendanceBadge.vue';

const isDark = useDark();

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

// ── Calendrier ───────────────────────────────────────────────────────────────
const today       = new Date();
const calMonth    = ref(today.getMonth());
const calYear     = ref(today.getFullYear());
const selectedDay = ref(today.getDate());

const weekDays   = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
const months     = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
const monthNames = ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'];

const monthName = computed(() => monthNames[calMonth.value]);

const daysInMonth = computed(() =>
    new Date(calYear.value, calMonth.value + 1, 0).getDate()
);

const firstDayOfMonth = computed(() => {
    const d = new Date(calYear.value, calMonth.value, 1).getDay();
    return d === 0 ? 6 : d - 1;
});

const isToday   = (day: number) => day === today.getDate() && calMonth.value === today.getMonth() && calYear.value === today.getFullYear();
const isWeekend = (day: number) => { const dow = new Date(calYear.value, calMonth.value, day).getDay(); return dow === 0 || dow === 6; };

const prevMonth = () => { if (calMonth.value === 0) { calMonth.value = 11; calYear.value--; } else calMonth.value--; selectedDay.value = 1; };
const nextMonth = () => { if (calMonth.value === 11) { calMonth.value = 0; calYear.value++; } else calMonth.value++; selectedDay.value = 1; };
const goToday   = () => { calMonth.value = today.getMonth(); calYear.value = today.getFullYear(); selectedDay.value = today.getDate(); };

// ── Événements du calendrier (exemples statiques) ────────────────────────────
interface CalEvent {
    id: number;
    day: number;
    month: number; // 0-indexed
    title: string;
    color: string;
    dateLabel: string;
    time: string;
    price: string;
    progress: number;
    seats: number;
    highlight?: boolean; // case colorée en rose/violet
    highlightBg?: string;
}

const calendarEvents: CalEvent[] = [
    { id: 1, day: 3,  month: today.getMonth(), title: 'Réunion parents',    color: '#f472b6', dateLabel: `3 ${months[today.getMonth()]}`,  time: '08:00 - 10:00', price: 'Gratuit', progress: 72, seats: 23, highlight: true,  highlightBg: '#ec4899' },
    { id: 2, day: 8,  month: today.getMonth(), title: 'Examen Maths',       color: '#a78bfa', dateLabel: `8 ${months[today.getMonth()]}`,  time: '09:00 - 12:00', price: 'Gratuit', progress: 45, seats: 17, highlight: false },
    { id: 3, day: 15, month: today.getMonth(), title: 'Sortie scolaire',    color: '#34d399', dateLabel: `15 ${months[today.getMonth()]}`, time: '07:00 - 18:00', price: '5 000 F', progress: 90, seats: 4,  highlight: true,  highlightBg: '#7c3aed' },
    { id: 4, day: 22, month: today.getMonth(), title: 'Conseil de classe',  color: '#fb923c', dateLabel: `22 ${months[today.getMonth()]}`, time: '14:00 - 16:00', price: 'Gratuit', progress: 60, seats: 13, highlight: false },
    { id: 5, day: 28, month: today.getMonth(), title: 'Remise des bulletins',color: '#60a5fa', dateLabel: `28 ${months[today.getMonth()]}`, time: '10:00 - 12:00', price: 'Gratuit', progress: 30, seats: 30, highlight: false },
];

// Événements pour un jour donné (mois courant)
const getDayEvents = (day: number): CalEvent[] =>
    calendarEvents.filter(e => e.day === day && e.month === calMonth.value);

// Style de fond d'une case — adaptatif light/dark
const getDayStyle = (day: number): Record<string, string> => {
    if (isToday(day)) {
        return { background: '#7c3aed' };
    }
    const evs = getDayEvents(day);
    if (evs.length && evs[0].highlight) {
        return { background: evs[0].highlightBg ?? '#ec4899' };
    }
    if (day === selectedDay.value) {
        return isDark.value
            ? { background: 'rgba(124,58,237,0.35)' }
            : { background: 'rgba(124,58,237,0.15)', border: '1px solid rgba(124,58,237,0.3)' };
    }
    return isDark.value
        ? { background: 'rgba(255,255,255,0.05)' }
        : { background: 'rgba(0,0,0,0.04)' };
};

// Couleur du texte selon le fond et le mode
const getDayTextColor = (day: number): string => {
    const evs = getDayEvents(day);
    if (evs.length && evs[0].highlight) return 'text-white';
    if (day === selectedDay.value) return isDark.value ? 'text-white' : 'text-primary-700';
    if (isWeekend(day)) return isDark.value ? 'text-white/40' : 'text-gray-400';
    return isDark.value ? 'text-white/80' : 'text-gray-700';
};
</script>
