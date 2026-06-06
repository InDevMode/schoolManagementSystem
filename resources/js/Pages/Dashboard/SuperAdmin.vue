<template>
    <div class="space-y-5">

        <!-- ══ EN-TÊTE ══════════════════════════════════════════════════════ -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-white">Tableau de bord</h1>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    {{ today.toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }) }}
                </p>
            </div>
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 text-xs font-medium text-gray-600 dark:text-gray-300">
                <svg class="w-3.5 h-3.5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                {{ today.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' }) }}
            </div>
        </div>

        <!-- ══ SECTION 1 : UTILISATEURS (4 grandes cards) ════════════════════ -->
        <section>
            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-2">Utilisateurs</p>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                <HeroCard label="Apprenants"  :value="totalStudent"  icon="user-group"   color="violet"  trend="+12%" href="/admin/student/list"/>
                <HeroCard label="Professeurs" :value="totalTeacher"  icon="academic-cap" color="blue"    trend="+3%"  href="/admin/teacher/list"/>
                <HeroCard label="Parents"     :value="totalParent"   icon="users"        color="amber"   trend="+8%"  href="/admin/parent/list"/>
                <HeroCard label="Contributions" :value="totalFeesCollections" icon="banknotes" color="green" trend="+21%" href="/admin/feescollections/collections/list" prefix="FCFA"/>
            </div>
        </section>

        <!-- ══ SECTION 2 : ACADÉMIQUE (5 petites cards) ═══════════════════════ -->
        <section>
            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-2">Académique</p>
            <div class="grid grid-cols-3 sm:grid-cols-5 gap-2.5">
                <MiniCard label="Administrateurs" :value="totalAdmin"   icon="shield"                  color="slate"   href="/admin/admin/list"/>
                <MiniCard label="Classes"         :value="totalClass"   icon="building-library"        color="sky"     href="/admin/class/list"/>
                <MiniCard label="Matières"        :value="totalSubject" icon="book-open"               color="teal"    href="/admin/subject/list"/>
                <MiniCard label="Sessions"        :value="totalExam"    icon="clipboard-document-list" color="orange"  href="/admin/examinations/exam/list"/>
                <MiniCard label="Devoirs"         :value="totalHomework" icon="pencil"                 color="rose"    href="/admin/practicalworks/homework/list"/>
            </div>
        </section>

        <!-- ══ SECTION 3 : SYSTÈME RBAC (2 cards) ════════════════════════════ -->
        <section>
            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-2">Système & Sécurité</p>
            <div class="grid grid-cols-2 gap-2.5">
                <MiniCard label="Rôles système"       :value="totalRoles"       icon="shield-check" color="violet" href="/superadmin/config/roles"/>
                <MiniCard label="Permissions système" :value="totalPermissions" icon="key"          color="blue"   href="/superadmin/config/permissions"/>
            </div>
        </section>

        <!-- ══ SECTION 4 : RESSOURCES HUMAINES (4 cards) ════════════════════ -->
        <section>
            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-2">Ressources Humaines</p>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5">
                <AlertCard label="Personnel actif"    :value="totalStaff ?? 0"          icon="user-group"    variant="default" href="/admin/staff/list"/>
                <AlertCard label="Congés en attente"  :value="totalPendingLeaves ?? 0"  icon="calendar-days" variant="warning" href="/admin/staff/leaves/list"/>
                <AlertCard label="Notes à valider"    :value="totalPendingGrades ?? 0"  icon="check-badge"   variant="danger"  href="/admin/evaluations/grades/pending"/>
                <AlertCard label="Événements à venir" :value="totalUpcomingEvents ?? 0" icon="sparkles"      variant="info"    href="/admin/staff/events/list"/>
            </div>
        </section>

        <!-- ══ SECTION 5 : PRÉSENCES DU JOUR (4 badges) ═════════════════════ -->
        <section>
            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-2">Présences du jour</p>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5">
                <AttendanceBadge label="Présents"     :value="totalAttendanceStudentPresent"  color="success" icon="user-check"/>
                <AttendanceBadge label="En retard"    :value="totalAttendanceStudentLate"     color="warning" icon="calendar"/>
                <AttendanceBadge label="Absents"      :value="totalAttendanceStudentAbsent"   color="danger"  icon="user-check"/>
                <AttendanceBadge label="Demi-journée" :value="totalAttendanceStudentHalfDay"  color="info"    icon="calendar-days"/>
            </div>
        </section>

        <!-- ══ SECTION 6 : ÉVÉNEMENTS + CONGÉS ══════════════════════════════ -->
        <section class="grid grid-cols-1 lg:grid-cols-2 gap-4">

            <!-- Prochains événements -->
            <div class="card p-0 overflow-hidden">
                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Prochains événements</h3>
                    <a href="/admin/staff/events/list" class="text-xs text-primary-600 dark:text-primary-400 hover:underline">Voir tout</a>
                </div>
                <div class="divide-y divide-gray-50 dark:divide-gray-700/50">
                    <div v-if="!upcomingEvents?.length" class="px-4 py-6 text-center text-xs text-gray-400">
                        Aucun événement à venir
                    </div>
                    <div v-for="ev in (upcomingEvents ?? []).slice(0, 5)" :key="ev.id"
                        class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                        <!-- Pastille date -->
                        <div class="flex-shrink-0 w-10 h-10 rounded-xl flex flex-col items-center justify-center text-white font-bold text-xs"
                            :style="{ background: eventTypeColor(ev.event_type ?? ev.extendedProps?.type) }">
                            <span class="text-sm leading-none">{{ fmtDay(ev.event_date ?? ev.start) }}</span>
                            <span class="text-[9px] leading-none mt-0.5 uppercase">{{ fmtMonth(ev.event_date ?? ev.start) }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-gray-900 dark:text-white truncate">{{ ev.title }}</p>
                            <p class="text-[10px] text-gray-400 truncate">
                                {{ eventTypeLabel(ev.event_type ?? ev.extendedProps?.type) }}
                                <template v-if="ev.location"> · {{ ev.location }}</template>
                                <template v-else-if="ev.extendedProps?.location"> · {{ ev.extendedProps.location }}</template>
                            </p>
                            <p v-if="ev.start_time" class="text-[10px] text-gray-400">{{ ev.start_time }}{{ ev.end_time ? ' – ' + ev.end_time : '' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Personnel en congé -->
            <div class="card p-0 overflow-hidden">
                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Personnel en congé</h3>
                    <a href="/admin/staff/leaves/list" class="text-xs text-primary-600 dark:text-primary-400 hover:underline">Gérer les congés</a>
                </div>
                <div class="divide-y divide-gray-50 dark:divide-gray-700/50">
                    <div v-if="!currentLeaves?.length" class="px-4 py-6 text-center text-xs text-gray-400">
                        Aucun congé en cours
                    </div>
                    <div v-for="leave in (currentLeaves ?? []).slice(0, 5)" :key="leave.id ?? leave.staff_id"
                        class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                        <!-- Avatar -->
                        <div class="w-8 h-8 rounded-full flex-shrink-0 flex items-center justify-center text-white text-xs font-bold"
                            :style="{ background: leave.color ?? '#6366f1' }">
                            {{ ((leave.last_name ?? leave.name ?? '?')[0]).toUpperCase() }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-gray-900 dark:text-white truncate">
                                {{ leave.last_name }} {{ leave.name }}
                            </p>
                            <p class="text-[10px] text-gray-400 truncate">
                                {{ leave.leave_type_name ?? 'Congé' }}
                                <template v-if="leave.start_date"> · depuis {{ fmtDate(leave.start_date) }}</template>
                            </p>
                        </div>
                        <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold bg-warning-50 dark:bg-warning-900/20 text-warning-700 dark:text-warning-400 flex-shrink-0">
                            En cours
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <!-- ══ SECTION 7 : GRAPHIQUES ════════════════════════════════════════ -->
        <section class="grid grid-cols-1 lg:grid-cols-3 gap-4">

            <!-- Barres résultats -->
            <div class="lg:col-span-2 card p-4">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Résultats des examens</h3>
                        <p class="text-[10px] text-gray-400 mt-0.5">Apprenants & Professeurs</p>
                    </div>
                    <div class="flex items-center gap-3 text-[10px] text-gray-500">
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-primary-500 inline-block"/>Professeurs</span>
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-warning-500 inline-block"/>Apprenants</span>
                    </div>
                </div>
                <div class="relative h-40">
                    <svg viewBox="0 0 600 150" class="w-full h-full" preserveAspectRatio="none">
                        <line x1="0" y1="0"   x2="600" y2="0"   stroke="currentColor" stroke-width="0.5" class="text-gray-100 dark:text-gray-700"/>
                        <line x1="0" y1="37"  x2="600" y2="37"  stroke="currentColor" stroke-width="0.5" class="text-gray-100 dark:text-gray-700"/>
                        <line x1="0" y1="75"  x2="600" y2="75"  stroke="currentColor" stroke-width="0.5" class="text-gray-100 dark:text-gray-700"/>
                        <line x1="0" y1="112" x2="600" y2="112" stroke="currentColor" stroke-width="0.5" class="text-gray-100 dark:text-gray-700"/>
                        <line x1="0" y1="150" x2="600" y2="150" stroke="currentColor" stroke-width="0.5" class="text-gray-100 dark:text-gray-700"/>
                        <g fill="#7B74F0" opacity="0.85">
                            <rect x="10"  y="50"  width="18" height="100" rx="3"/><rect x="58"  y="35"  width="18" height="115" rx="3"/>
                            <rect x="106" y="18"  width="18" height="132" rx="3"/><rect x="154" y="42"  width="18" height="108" rx="3"/>
                            <rect x="202" y="10"  width="18" height="140" rx="3"/><rect x="250" y="30"  width="18" height="120" rx="3"/>
                            <rect x="298" y="48"  width="18" height="102" rx="3"/><rect x="346" y="22"  width="18" height="128" rx="3"/>
                            <rect x="394" y="38"  width="18" height="112" rx="3"/><rect x="442" y="12"  width="18" height="138" rx="3"/>
                            <rect x="490" y="28"  width="18" height="122" rx="3"/><rect x="538" y="44"  width="18" height="106" rx="3"/>
                        </g>
                        <g fill="#f59e0b" opacity="0.75">
                            <rect x="30"  y="68"  width="18" height="82"  rx="3"/><rect x="78"  y="55"  width="18" height="95"  rx="3"/>
                            <rect x="126" y="42"  width="18" height="108" rx="3"/><rect x="174" y="60"  width="18" height="90"  rx="3"/>
                            <rect x="222" y="34"  width="18" height="116" rx="3"/><rect x="270" y="52"  width="18" height="98"  rx="3"/>
                            <rect x="318" y="64"  width="18" height="86"  rx="3"/><rect x="366" y="38"  width="18" height="112" rx="3"/>
                            <rect x="414" y="56"  width="18" height="94"  rx="3"/><rect x="462" y="30"  width="18" height="120" rx="3"/>
                            <rect x="510" y="48"  width="18" height="102" rx="3"/><rect x="558" y="60"  width="18" height="90"  rx="3"/>
                        </g>
                    </svg>
                    <div class="absolute bottom-0 left-0 right-0 flex justify-between px-2 text-[9px] text-gray-400">
                        <span v-for="m in months" :key="m">{{ m }}</span>
                    </div>
                </div>
            </div>

            <!-- Donut apprenants -->
            <div class="card p-4 flex flex-col">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Apprenants</h3>
                </div>
                <div class="flex-1 flex items-center justify-center">
                    <div class="relative w-32 h-32">
                        <svg viewBox="0 0 100 100" class="w-full h-full -rotate-90">
                            <circle cx="50" cy="50" r="38" fill="none" stroke="#f3f4f6" stroke-width="14" class="dark:stroke-gray-700"/>
                            <circle cx="50" cy="50" r="38" fill="none" stroke="#f59e0b" stroke-width="14"
                                    stroke-dasharray="95.5 143.3" stroke-dashoffset="0" stroke-linecap="round"/>
                            <circle cx="50" cy="50" r="38" fill="none" stroke="#7B74F0" stroke-width="14"
                                    stroke-dasharray="143.3 95.5" stroke-dashoffset="-95.5" stroke-linecap="round"/>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-xl font-black text-gray-900 dark:text-white">{{ totalStudent.toLocaleString('fr-FR') }}</span>
                            <span class="text-[9px] text-gray-400">Total</span>
                        </div>
                    </div>
                </div>
                <div class="flex justify-center gap-4 mt-3">
                    <div class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-primary-600 inline-block"/>
                        <span class="text-[10px] text-gray-500 dark:text-gray-400">Garçons</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-warning-500 inline-block"/>
                        <span class="text-[10px] text-gray-500 dark:text-gray-400">Filles</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- ══ SECTION 8 : CALENDRIER DYNAMIQUE ══════════════════════════════ -->
        <section>
            <div :class="['rounded-2xl overflow-hidden border transition-colors', isDark ? 'border-white/8 bg-[#1a1a2e]' : 'border-gray-200 bg-[#f8f7ff] shadow-sm']">
                <div class="flex flex-col xl:flex-row">

                    <!-- Grille calendrier -->
                    <div class="flex-1 p-5">
                        <!-- Header -->
                        <div class="flex items-center justify-between mb-5">
                            <div class="flex items-center gap-2">
                                <h3 :class="['text-base font-bold', isDark ? 'text-white' : 'text-gray-900']">{{ monthName }}</h3>
                                <span class="text-base font-bold text-primary-500">{{ calYear }}</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <button :class="navBtnClass" @click="prevMonth">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                </button>
                                <button :class="['px-3 py-1.5 rounded-lg text-[11px] font-semibold transition-all', isDark ? 'bg-white/10 hover:bg-primary-600 text-white' : 'bg-gray-200 hover:bg-primary-600 hover:text-white text-gray-600']" @click="goToday">
                                    Aujourd'hui
                                </button>
                                <button :class="navBtnClass" @click="nextMonth">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Jours semaine -->
                        <div class="grid grid-cols-7 gap-1.5 mb-1.5">
                            <div v-for="d in weekDays" :key="d"
                                :class="['text-center text-[10px] font-semibold py-1', isDark ? 'text-white/40' : 'text-gray-400']">
                                {{ d }}
                            </div>
                        </div>

                        <!-- Grille jours -->
                        <div class="grid grid-cols-7 gap-1.5">
                            <div v-for="_ in firstDayOfMonth" :key="`e${_}`" class="rounded-lg h-14"
                                :style="isDark ? 'background:rgba(255,255,255,0.02)' : 'background:rgba(0,0,0,0.02)'"/>

                            <button v-for="day in daysInMonth" :key="day"
                                :class="['relative rounded-lg h-14 flex flex-col items-start justify-start p-1.5 transition-all text-left group', isToday(day) ? 'ring-2 ring-primary-400' : '']"
                                :style="getDayStyle(day)"
                                @click="selectedDay = day">
                                <span :class="['text-xs font-bold leading-none', getDayTextColor(day)]">{{ day }}</span>
                                <!-- Événement -->
                                <div v-if="getDbDayEvents(day).length"
                                    class="absolute bottom-1 left-1 right-1 text-[8px] font-semibold truncate leading-none rounded px-0.5 py-0.5"
                                    :style="{ color: getDbDayEvents(day)[0].highlightBg ? 'rgba(255,255,255,0.95)' : getDbDayEvents(day)[0].color, background: getDbDayEvents(day)[0].highlightBg ? 'transparent' : 'rgba(0,0,0,0.06)' }">
                                    {{ getDbDayEvents(day)[0].title }}
                                </div>
                                <!-- Plusieurs événements → points -->
                                <div v-if="getDbDayEvents(day).length > 1" class="flex gap-0.5 absolute top-1 right-1">
                                    <span v-for="(ev, i) in getDbDayEvents(day).slice(0,3)" :key="i"
                                        class="w-1.5 h-1.5 rounded-full" :style="{ background: ev.color }"/>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Panneau latéral événements -->
                    <div :class="['xl:w-64 border-t xl:border-t-0 xl:border-l flex flex-col', isDark ? 'border-white/8' : 'border-gray-200']">
                        <div :class="['px-4 py-3 border-b', isDark ? 'border-white/8' : 'border-gray-200']">
                            <h4 :class="['text-xs font-bold', isDark ? 'text-white' : 'text-gray-900']">Liste des événements</h4>
                            <p :class="['text-[10px] mt-0.5', isDark ? 'text-white/40' : 'text-gray-400']">{{ monthName }} {{ calYear }}</p>
                        </div>
                        <div class="flex-1 overflow-y-auto max-h-72 xl:max-h-none">
                            <div v-if="!allCalEvents.length" class="px-4 py-6 text-center text-[10px] text-gray-400">
                                Aucun événement ce mois
                            </div>
                            <div v-for="ev in allCalEvents" :key="ev.id"
                                :class="['px-4 py-3 transition-colors cursor-pointer border-b last:border-0', isDark ? 'border-white/5 hover:bg-white/5' : 'border-gray-50 hover:bg-white']">
                                <!-- Date + type -->
                                <div class="flex items-center gap-1.5 mb-1">
                                    <span class="w-1.5 h-1.5 rounded-full flex-shrink-0" :style="{ background: ev.color }"/>
                                    <span class="text-[10px] font-bold" :style="{ color: ev.color }">
                                        {{ fmtDay(ev.start) }} {{ fmtMonth(ev.start) }}
                                    </span>
                                    <span :class="['text-[9px] ml-auto px-1.5 py-0.5 rounded-full font-medium', isDark ? 'bg-white/10 text-white/60' : 'bg-gray-100 text-gray-500']">
                                        {{ eventTypeLabel(ev.extendedProps?.type) }}
                                    </span>
                                </div>
                                <p :class="['text-[11px] font-semibold leading-tight truncate', isDark ? 'text-white' : 'text-gray-900']">{{ ev.title }}</p>
                                <div v-if="ev.extendedProps?.start_time" class="flex items-center gap-1 mt-1">
                                    <svg class="w-2.5 h-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span :class="['text-[9px]', isDark ? 'text-white/40' : 'text-gray-400']">
                                        {{ ev.extendedProps.start_time }}{{ ev.extendedProps.end_time ? ' – ' + ev.extendedProps.end_time : '' }}
                                    </span>
                                </div>
                                <!-- Barre progression -->
                                <div :class="['mt-1.5 h-0.5 rounded-full', isDark ? 'bg-white/10' : 'bg-gray-200']">
                                    <div class="h-0.5 rounded-full" :style="{ width: ev.progress + '%', background: ev.color }"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { useDark } from '@vueuse/core';
import AttendanceBadge from '@/Components/Dashboard/AttendanceBadge.vue';
import HeroCard        from '@/Components/Dashboard/HeroCard.vue';
import MiniCard        from '@/Components/Dashboard/MiniCard.vue';
import AlertCard       from '@/Components/Dashboard/AlertCard.vue';

const isDark = useDark();
const props = defineProps<{
    totalAdmin: number;
    totalTeacher: number;
    totalStudent: number;
    totalParent: number;
    totalClass: number;
    totalSubject: number;
    totalExam: number;
    totalFeesCollections: number;
    totalHomework: number;
    totalAttendance: number;
    totalAttendanceStudentPresent: number;
    totalAttendanceStudentLate: number;
    totalAttendanceStudentAbsent: number;
    totalAttendanceStudentHalfDay: number;
    totalRoles: number;
    totalPermissions: number;
    totalStaff?: number;
    totalPendingLeaves?: number;
    totalPendingGrades?: number;
    totalUpcomingEvents?: number;
    upcomingEvents?: any[];
    currentLeaves?: any[];
    calendarEvents?: any[];
    [key: string]: unknown;
}>();

// ── Constantes ────────────────────────────────────────────────────────────────
const today    = new Date();
const months   = ['Jan','Fév','Mar','Avr','Mai','Jun','Jul','Aoû','Sep','Oct','Nov','Déc'];
const monthNames = ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'];
const weekDays = ['Lun','Mar','Mer','Jeu','Ven','Sam','Dim'];

const typeLabels: Record<string, string> = {
    academic: 'Académique', cultural: 'Culturel', administrative: 'Administratif',
    exam: 'Examen', ceremony: 'Cérémonie', trip: 'Sortie',
};
const typeColors: Record<string, string> = {
    academic: '#3b82f6', cultural: '#8b5cf6', administrative: '#f59e0b',
    exam: '#ef4444', ceremony: '#10b981', trip: '#06b6d4',
};

// ── Calendrier ────────────────────────────────────────────────────────────────
const calMonth    = ref(today.getMonth());
const calYear     = ref(today.getFullYear());
const selectedDay = ref(today.getDate());

const monthName       = computed(() => monthNames[calMonth.value]);
const daysInMonth     = computed(() => new Date(calYear.value, calMonth.value + 1, 0).getDate());
const firstDayOfMonth = computed(() => {
    const d = new Date(calYear.value, calMonth.value, 1).getDay();
    return d === 0 ? 6 : d - 1;
});

const prevMonth = () => { if (calMonth.value === 0) { calMonth.value = 11; calYear.value--; } else calMonth.value--; selectedDay.value = 1; };
const nextMonth = () => { if (calMonth.value === 11) { calMonth.value = 0; calYear.value++; } else calMonth.value++; selectedDay.value = 1; };
const goToday   = () => { calMonth.value = today.getMonth(); calYear.value = today.getFullYear(); selectedDay.value = today.getDate(); };

const isToday   = (d: number) => d === today.getDate() && calMonth.value === today.getMonth() && calYear.value === today.getFullYear();
const isWeekend = (d: number) => { const dw = new Date(calYear.value, calMonth.value, d).getDay(); return dw === 0 || dw === 6; };

// Événements DB → format calendrier pour le mois courant
const allCalEvents = computed(() => {
    if (!props.calendarEvents?.length) return [];
    return (props.calendarEvents as any[]).filter(ev => {
        const d = new Date(ev.start);
        return d.getMonth() === calMonth.value && d.getFullYear() === calYear.value;
    }).map(ev => ({
        ...ev,
        progress: Math.floor(Math.random() * 60) + 30, // décoratif
    }));
});

// Événements pour un jour donné (depuis la BDD via calendarEvents)
const getDbDayEvents = (day: number) => {
    if (!props.calendarEvents?.length) return [];
    return (props.calendarEvents as any[]).filter(ev => {
        const d = new Date(ev.start);
        return d.getDate() === day && d.getMonth() === calMonth.value && d.getFullYear() === calYear.value;
    }).map(ev => ({
        id:          ev.id,
        title:       ev.title,
        color:       ev.color ?? typeColors[ev.extendedProps?.type] ?? '#7B74F0',
        highlightBg: ev.color ?? null,
    }));
};

// Styles des cases calendrier
const getDayStyle = (day: number): Record<string, string> => {
    if (isToday(day)) return { background: '#7B74F0' };
    const evs = getDbDayEvents(day);
    if (evs.length) return { background: (evs[0].color ?? '#7B74F0') + '33' };
    if (day === selectedDay.value) {
        return isDark.value
            ? { background: 'rgba(124,58,237,0.25)' }
            : { background: 'rgba(124,58,237,0.10)', border: '1px solid rgba(124,58,237,0.25)' };
    }
    return isDark.value ? { background: 'rgba(255,255,255,0.04)' } : { background: 'rgba(0,0,0,0.03)' };
};

const getDayTextColor = (day: number): string => {
    if (isToday(day)) return 'text-white';
    const evs = getDbDayEvents(day);
    if (evs.length) return isDark.value ? 'text-white' : 'text-gray-900';
    if (day === selectedDay.value) return isDark.value ? 'text-white' : 'text-primary-700';
    if (isWeekend(day)) return isDark.value ? 'text-white/35' : 'text-gray-400';
    return isDark.value ? 'text-white/75' : 'text-gray-700';
};

// ── Helpers ───────────────────────────────────────────────────────────────────
const navBtnClass = computed(() =>
    `w-8 h-8 rounded-lg flex items-center justify-center transition-all ${isDark.value ? 'bg-white/10 hover:bg-primary-600 text-white' : 'bg-gray-200 hover:bg-primary-600 hover:text-white text-gray-600'}`
);

const fmtDay   = (d: string) => d ? new Date(d).getDate() : '';
const fmtMonth = (d: string) => d ? months[new Date(d).getMonth()] : '';
const fmtDate  = (d: string) => d ? new Date(d).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' }) : '—';

const eventTypeColor = (type: string) => typeColors[type] ?? '#6366f1';
const eventTypeLabel = (type: string) => typeLabels[type] ?? type ?? '—';
</script>
