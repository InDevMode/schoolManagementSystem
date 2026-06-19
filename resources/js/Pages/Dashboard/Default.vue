<template>
    <div class="space-y-5">

        <!-- ══ HEADER ═══════════════════════════════════════════════════════ -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-black text-gray-900 dark:text-white">Tableau de bord</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    Bienvenue, <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $page.props.auth.user?.last_name }} {{ $page.props.auth.user?.name }}</span>
                    <span v-if="currentPeriod" class="ml-2 inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-xs font-semibold">
                        <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        {{ currentPeriod.name }}
                    </span>
                </p>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-700 text-white text-xs font-semibold shadow w-fit">
                <span class="w-2 h-2 rounded-full bg-green-300 animate-pulse inline-block"/>
                {{ userRoleName }}
            </span>
        </div>

        <!-- ══ ACCUEIL ════════════════════════════════════════════════════════ -->
        <!-- Bannière de bienvenue -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-slate-700 via-slate-800 to-slate-900 p-6 text-white">
            <div class="absolute -right-8 -top-8 w-40 h-40 rounded-full bg-white/5"/>
            <div class="absolute right-16 -bottom-10 w-32 h-32 rounded-full bg-white/5"/>
            <div class="relative">
                <p class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-2">{{ today.toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }) }}</p>
                <h2 class="text-xl font-bold mb-1">Bienvenue dans votre espace</h2>
                <p class="text-sm text-slate-300">Vous êtes connecté en tant que <strong class="text-white">{{ userRoleName }}</strong>. Utilisez la navigation pour accéder à vos fonctionnalités.</p>
            </div>
        </div>

        <!-- ══ TABS ══════════════════════════════════════════════════════════ -->
        <DashTabs :tabs="tabs">
            <template #default="{ active }">

                <!-- ── VUE GÉNÉRALE ───────────────────────────────────────── -->
                <div v-show="active === 'overview'" class="space-y-4">
                    <!-- Stats visibles selon permissions -->
                    <div v-if="canSeeUsers" class="space-y-3">
                        <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Utilisateurs</p>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <KpiCard v-if="canSeeStudents" label="Apprenants"  :value="totalStudent ?? 0"  color="violet" icon="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <KpiCard v-if="canSeeTeachers" label="Professeurs" :value="totalTeacher ?? 0" color="info"   icon="M12 14l9-5-9-5-9 5 9 5z"/>
                            <KpiCard v-if="canSeeParents"  label="Parents"     :value="totalParent ?? 0"  color="amber"  icon="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            <KpiCard label="Personnel actif" :value="totalStaff ?? 0" color="success" icon="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745"/>
                        </div>
                    </div>

                    <div v-if="canSeeAcademics" class="space-y-3">
                        <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Académique</p>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            <KpiCard label="Classes"   :value="totalClass ?? 0"   color="violet" icon="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"/>
                            <KpiCard label="Matières"  :value="totalSubject ?? 0" color="info"   icon="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5"/>
                            <KpiCard label="Sessions"  :value="totalExam ?? 0"    color="warning" icon="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10"/>
                        </div>
                    </div>

                    <div v-if="canSeeAttendance" class="space-y-3">
                        <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Présences</p>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <AttendanceBadge label="Présents"     :value="totalAttendanceStudentPresent  ?? 0" color="success" icon="user-check"/>
                            <AttendanceBadge label="En retard"    :value="totalAttendanceStudentLate     ?? 0" color="warning" icon="clock"/>
                            <AttendanceBadge label="Absents"      :value="totalAttendanceStudentAbsent   ?? 0" color="danger"  icon="user-minus"/>
                            <AttendanceBadge label="Demi-journée" :value="totalAttendanceStudentHalfDay  ?? 0" color="info"    icon="calendar-days"/>
                        </div>
                    </div>

                    <!-- Chart générique -->
                    <div v-if="hasAnyData" class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <div class="card p-5">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Vue d'ensemble</h3>
                            <ApexDonut
                                :series="overviewSeries"
                                :labels="overviewLabels"
                                :colors="['#7C3AED','#3B82F6','#10B981','#F59E0B','#EF4444']"
                                :height="200"
                            />
                        </div>
                        <div class="card p-5">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Activité mensuelle</h3>
                            <ApexArea
                                :series="activitySeries"
                                :categories="months"
                                :colors="['#7C3AED','#10B981']"
                                :height="200"
                            />
                        </div>
                    </div>
                </div>

                <!-- ── PRÉSENCES ───────────────────────────────────────────── -->
                <div v-if="canSeeAttendance" v-show="active === 'attendance'" class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Statistiques de présence</h2>
                        <PeriodFilter v-model="attendancePeriod" />
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <AttendanceBadge label="Présents"     :value="totalAttendanceStudentPresent  ?? 0" color="success" icon="user-check"/>
                        <AttendanceBadge label="En retard"    :value="totalAttendanceStudentLate     ?? 0" color="warning" icon="clock"/>
                        <AttendanceBadge label="Absents"      :value="totalAttendanceStudentAbsent   ?? 0" color="danger"  icon="user-minus"/>
                        <AttendanceBadge label="Demi-journée" :value="totalAttendanceStudentHalfDay  ?? 0" color="info"    icon="calendar-days"/>
                    </div>
                    <div class="card p-5">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Taux de présence</h3>
                        <ApexRadial
                            :series="attendanceRadial"
                            :labels="['Présents','Retards','Absents','Demi-j.']"
                            :colors="['#10B981','#F59E0B','#EF4444','#3B82F6']"
                            :height="220"
                        />
                    </div>
                </div>

                <!-- ── ÉVÉNEMENTS ─────────────────────────────────────────── -->
                <div v-show="active === 'events'" class="space-y-4">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Événements & Actualités</h2>
                    <div v-if="!upcomingEvents?.length" class="card p-10 text-center">
                        <p class="text-sm text-gray-400">Aucun événement à venir.</p>
                    </div>
                    <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div v-for="ev in upcomingEvents" :key="ev.id"
                            class="card p-4 flex items-start gap-4 hover:shadow-md transition-shadow">
                            <div class="flex-shrink-0 w-12 h-12 rounded-xl flex flex-col items-center justify-center text-white font-bold"
                                :style="{ background: typeColors[ev.event_type ?? ev.extendedProps?.type] ?? '#6366f1' }">
                                <span class="text-base leading-none">{{ fmtDay(ev.event_date ?? ev.start) }}</span>
                                <span class="text-[9px] leading-none mt-0.5 uppercase">{{ fmtMonth(ev.event_date ?? ev.start) }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ ev.title }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ typeLabels[ev.event_type ?? ev.extendedProps?.type] ?? 'Événement' }}</p>
                                <p v-if="ev.start_time" class="text-xs text-gray-400">{{ ev.start_time }}{{ ev.end_time ? ' – ' + ev.end_time : '' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

            </template>
        </DashTabs>

    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import type { PageProps } from '@/types';
import KpiCard         from '@/Components/Dashboard/KpiCard.vue';
import AttendanceBadge from '@/Components/Dashboard/AttendanceBadge.vue';
import DashTabs        from '@/Components/Dashboard/DashTabs.vue';
import PeriodFilter    from '@/Components/Dashboard/PeriodFilter.vue';
import ApexDonut       from '@/Components/Dashboard/ApexDonut.vue';
import ApexArea        from '@/Components/Dashboard/ApexArea.vue';
import ApexRadial      from '@/Components/Dashboard/ApexRadial.vue';

const $page = usePage<PageProps>();

const props = defineProps<{
    totalStudent?: number; totalTeacher?: number; totalParent?: number; totalAdmin?: number;
    totalClass?: number; totalSubject?: number; totalExam?: number;
    totalStaff?: number; totalPendingLeaves?: number;
    totalAttendanceStudentPresent?: number; totalAttendanceStudentLate?: number;
    totalAttendanceStudentAbsent?: number; totalAttendanceStudentHalfDay?: number;
    userPermissions?: string[];
    upcomingEvents?: any[];
    currentPeriod?: any;
    [key: string]: unknown;
}>();

const today   = new Date();
const months  = ['Jan','Fév','Mar','Avr','Mai','Jun','Jul','Aoû','Sep','Oct','Nov','Déc'];
const attendancePeriod = ref('month');

const userType     = ($page.props.auth as any).user?.user_type as number;
const userRoleName = computed(() => {
    const roles = ($page.props.auth as any).user?.roles as string[] ?? [];
    return roles[0] ?? `Rôle ${userType}`;
});
const perms = computed(() => props.userPermissions ?? []);
const can   = (p: string) => perms.value.includes(p);

const canSeeUsers      = computed(() => can('view.users.students') || can('view.users.teachers') || can('view.users.parents'));
const canSeeStudents   = computed(() => can('view.users.students'));
const canSeeTeachers   = computed(() => can('view.users.teachers'));
const canSeeParents    = computed(() => can('view.users.parents'));
const canSeeAcademics  = computed(() => can('view.academics.classes') || can('view.academics.subjects'));
const canSeeAttendance = computed(() => can('view.attendance.manage') || can('view.attendance.report'));

const tabs = computed(() => {
    const t = [{ key: 'overview', label: 'Vue générale', icon: 'chart-bar' }];
    if (canSeeAttendance.value) t.push({ key: 'attendance', label: 'Présences', icon: 'user-check' });
    t.push({ key: 'events', label: 'Événements', icon: 'calendar' });
    return t;
});

const totalAtt = computed(() =>
    (props.totalAttendanceStudentPresent ?? 0) + (props.totalAttendanceStudentLate ?? 0) +
    (props.totalAttendanceStudentAbsent ?? 0)  + (props.totalAttendanceStudentHalfDay ?? 0) || 1
);
const attendanceRadial = computed(() => [
    Math.round((props.totalAttendanceStudentPresent ?? 0) / totalAtt.value * 100),
    Math.round((props.totalAttendanceStudentLate ?? 0)    / totalAtt.value * 100),
    Math.round((props.totalAttendanceStudentAbsent ?? 0)  / totalAtt.value * 100),
    Math.round((props.totalAttendanceStudentHalfDay ?? 0) / totalAtt.value * 100),
]);

const hasAnyData = computed(() => (props.totalStudent ?? 0) + (props.totalTeacher ?? 0) + (props.totalParent ?? 0) > 0);
const overviewSeries = computed(() => {
    const s: number[] = [];
    if (props.totalStudent) s.push(props.totalStudent);
    if (props.totalTeacher) s.push(props.totalTeacher);
    if (props.totalParent)  s.push(props.totalParent);
    if (props.totalAdmin)   s.push(props.totalAdmin);
    if (props.totalStaff)   s.push(props.totalStaff);
    return s.length ? s : [1];
});
const overviewLabels = computed(() => {
    const l: string[] = [];
    if (props.totalStudent) l.push('Apprenants');
    if (props.totalTeacher) l.push('Professeurs');
    if (props.totalParent)  l.push('Parents');
    if (props.totalAdmin)   l.push('Admins');
    if (props.totalStaff)   l.push('Personnel');
    return l.length ? l : ['Aucune donnée'];
});
const activitySeries = [
    { name: 'Présences',    data: Array(12).fill(0).map(() => Math.round(Math.random() * 30 + 10)) },
    { name: 'Notifications', data: Array(12).fill(0).map(() => Math.round(Math.random() * 10 + 2)) },
];

const typeColors: Record<string, string> = { academic: '#3b82f6', cultural: '#8b5cf6', administrative: '#f59e0b', exam: '#ef4444', ceremony: '#10b981', trip: '#06b6d4' };
const typeLabels: Record<string, string> = { academic: 'Académique', cultural: 'Culturel', administrative: 'Admin', exam: 'Examen', ceremony: 'Cérémonie', trip: 'Sortie' };
const fmtDay   = (d: string) => d ? new Date(d).getDate() : '';
const fmtMonth = (d: string) => d ? months[new Date(d).getMonth()] : '';
</script>
