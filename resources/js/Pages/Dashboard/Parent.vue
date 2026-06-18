<template>
    <div class="space-y-5">

        <!-- ══ HEADER ═══════════════════════════════════════════════════════ -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-black text-gray-900 dark:text-white">Mon espace parent</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $page.props.auth.user?.last_name }} {{ $page.props.auth.user?.name }}</span>
                    <span v-if="currentPeriod" class="ml-2 inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 text-xs font-semibold">
                        <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        {{ currentPeriod.name }}
                    </span>
                </p>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-orange-600 text-white text-xs font-semibold shadow w-fit">
                <span class="w-2 h-2 rounded-full bg-green-300 animate-pulse inline-block"/>Parent
            </span>
        </div>

        <!-- ══ KPI ═══════════════════════════════════════════════════════════ -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <KpiCard label="Mes enfants"     :value="totalParentStudent"            color="violet" href="/parent/my_student"
                icon="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            <KpiCard label="Devoirs à rendre" :value="totalHomeworkStudent ?? 0"   color="warning"
                icon="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
            <KpiCard label="Notifications"   :value="totalNoticeBoardParent ?? 0"  color="info"    href="/parent/my_noticeboard"
                icon="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            <KpiCard label="Présences totales" :value="totalByAttendanceTypeStudentPresent ?? 0" color="success"
                icon="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
        </div>

        <!-- ══ TABS ══════════════════════════════════════════════════════════ -->
        <DashTabs :tabs="tabs">
            <template #default="{ active }">

                <!-- ── VUE GÉNÉRALE ───────────────────────────────────────── -->
                <div v-show="active === 'overview'" class="space-y-4">
                    <!-- Présences -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <AttendanceBadge label="Présent"      :value="totalByAttendanceTypeStudentPresent  ?? 0" color="success" icon="user-check"/>
                        <AttendanceBadge label="En retard"    :value="totalByAttendanceTypeStudentLate     ?? 0" color="warning" icon="clock"/>
                        <AttendanceBadge label="Absent"       :value="totalByAttendanceTypeStudentAbsent   ?? 0" color="danger"  icon="user-minus"/>
                        <AttendanceBadge label="Demi-journée" :value="totalByAttendanceTypeStudentHalfDay  ?? 0" color="info"    icon="calendar-days"/>
                    </div>

                    <!-- Bulletins enfants -->
                    <div v-if="childrenBulletins?.length">
                        <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Bulletins de mes enfants</h2>
                        <div class="space-y-4">
                            <div v-for="child in childrenBulletins" :key="child.student.id" class="card p-5">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center font-bold text-white text-sm">
                                        {{ (child.student.last_name?.[0] ?? child.student.name?.[0] ?? '?').toUpperCase() }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ child.student.last_name }} {{ child.student.name }}</p>
                                        <p class="text-xs text-gray-400">{{ child.student.class_name }}</p>
                                    </div>
                                    <a :href="`/parent/my_student/${child.student.id}/bulletins`" class="ml-auto text-xs text-primary-600 dark:text-primary-400 hover:underline">
                                        Tous les bulletins →
                                    </a>
                                </div>
                                <div v-if="child.bulletins?.length" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <a v-for="b in child.bulletins" :key="b.id"
                                        :href="`/parent/my_student/${child.student.id}/bulletins/${b.id}`"
                                        class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 dark:border-gray-700 hover:border-primary-300 dark:hover:border-primary-700 hover:shadow-sm transition-all">
                                        <div class="w-14 h-14 rounded-xl flex flex-col items-center justify-center flex-shrink-0"
                                            :class="Number(b.average) >= 10 ? 'bg-gradient-to-br from-emerald-400 to-emerald-600' : 'bg-gradient-to-br from-red-400 to-red-600'">
                                            <span class="text-base font-black text-white">{{ b.average ? Number(b.average).toFixed(1) : '—' }}</span>
                                            <span class="text-[9px] text-white/80">/20</span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-semibold text-gray-700 dark:text-gray-200">{{ b.period_name }}</p>
                                            <p class="text-xs text-gray-400 mt-0.5">{{ b.appreciation ?? '—' }}</p>
                                            <p class="text-[10px] text-gray-400">Rang {{ b.rank ? `${b.rank}/${b.total_students}` : '—' }}</p>
                                        </div>
                                    </a>
                                </div>
                                <p v-else class="text-xs text-gray-400 italic">Aucun bulletin publié pour le moment.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Pas d'enfants -->
                    <div v-else-if="!totalParentStudent" class="card p-10 text-center border border-dashed border-gray-200 dark:border-gray-700">
                        <p class="text-sm text-gray-500">Aucun enfant associé à votre compte.</p>
                        <p class="text-xs text-gray-300 dark:text-gray-600 mt-1">Contactez l'administration pour faire le lien.</p>
                    </div>

                    <!-- Événements -->
                    <div v-if="upcomingEvents?.length" class="card p-5">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Prochains événements</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <div v-for="ev in (upcomingEvents ?? []).slice(0,4)" :key="ev.id"
                                class="flex items-center gap-2 p-2.5 rounded-lg border border-gray-100 dark:border-gray-700">
                                <div class="w-8 h-8 rounded-lg flex-shrink-0 flex items-center justify-center text-white text-xs font-bold"
                                    :style="{ background: typeColors[ev.event_type] ?? '#f97316' }">
                                    {{ fmtDay(ev.event_date ?? ev.start) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-gray-900 dark:text-white truncate">{{ ev.title }}</p>
                                    <p class="text-[10px] text-gray-400">{{ fmtDate(ev.event_date ?? ev.start) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ── PRÉSENCES ───────────────────────────────────────────── -->
                <div v-show="active === 'attendance'" class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Présences de mes enfants</h2>
                        <PeriodFilter v-model="attendancePeriod" />
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <AttendanceBadge label="Présent"      :value="totalByAttendanceTypeStudentPresent  ?? 0" color="success" icon="user-check"/>
                        <AttendanceBadge label="En retard"    :value="totalByAttendanceTypeStudentLate     ?? 0" color="warning" icon="clock"/>
                        <AttendanceBadge label="Absent"       :value="totalByAttendanceTypeStudentAbsent   ?? 0" color="danger"  icon="user-minus"/>
                        <AttendanceBadge label="Demi-journée" :value="totalByAttendanceTypeStudentHalfDay  ?? 0" color="info"    icon="calendar-days"/>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <div class="card p-5">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Taux de présence</h3>
                            <ApexRadial
                                :series="attendanceRadial"
                                :labels="['Présent','Retard','Absent','Demi-j.']"
                                :colors="['#10B981','#F59E0B','#EF4444','#3B82F6']"
                                :height="240"
                            />
                        </div>
                        <div class="card p-5">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Évolution mensuelle</h3>
                            <ApexBar
                                :series="attSeries"
                                :categories="months"
                                :colors="['#10B981','#F59E0B','#EF4444']"
                                :height="240"
                            />
                        </div>
                    </div>
                </div>

                <!-- ── CONTRIBUTIONS ───────────────────────────────────────── -->
                <div v-show="active === 'fees'" class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Contributions de mes enfants</h2>
                        <PeriodFilter v-model="feesPeriod" />
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <KpiCard label="Total dû" :value="totalFeesCollectionsAmountStudents ?? 0" suffix="FCFA" color="violet" icon="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2"/>
                        <KpiCard label="Déjà payé" :value="totalFeesCollectionsAmoutPaidByStudents ?? 0" suffix="FCFA" color="success" icon="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </div>
                    <div v-for="child in childrenBulletins" :key="child.student.id" class="card p-5">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-8 h-8 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center font-bold text-orange-600 text-sm">
                                {{ (child.student.last_name?.[0] ?? '?').toUpperCase() }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ child.student.last_name }} {{ child.student.name }}</p>
                                <p class="text-xs text-gray-400">{{ child.student.class_name }}</p>
                            </div>
                            <a :href="`/parent/my_student/feescollections/${child.student.id}`" class="ml-auto text-xs text-primary-600 hover:underline">Payer →</a>
                        </div>
                        <ProgressBar label="Paiement" :percent="feesProgress(child.student.id)" :value="0" color="success" />
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
import ProgressBar     from '@/Components/Dashboard/ProgressBar.vue';
import ApexBar         from '@/Components/Dashboard/ApexBar.vue';
import ApexRadial      from '@/Components/Dashboard/ApexRadial.vue';

const $page = usePage<PageProps>();
const props = defineProps<{
    totalParentStudent:     number;
    totalNoticeBoardParent?: number;
    totalHomeworkStudent?:  number;
    totalByAttendanceTypeStudentPresent?:  number;
    totalByAttendanceTypeStudentLate?:     number;
    totalByAttendanceTypeStudentAbsent?:   number;
    totalByAttendanceTypeStudentHalfDay?:  number;
    totalFeesCollectionsAmountStudents?:       number;
    totalFeesCollectionsAmoutPaidByStudents?:  number;
    childrenBulletins?: { student: any; bulletins: any[] }[];
    upcomingEvents?:    any[];
    currentPeriod?:     any;
    [key: string]: unknown;
}>();

const months = ['Jan','Fév','Mar','Avr','Mai','Jun','Jul','Aoû','Sep','Oct','Nov','Déc'];
const attendancePeriod = ref('month');
const feesPeriod = ref('month');

const tabs = [
    { key: 'overview',   label: 'Vue générale',    icon: 'chart-bar' },
    { key: 'attendance', label: 'Présences',        icon: 'user-check' },
    { key: 'fees',       label: 'Contributions',    icon: 'banknotes' },
];

const totalAtt = computed(() =>
    (props.totalByAttendanceTypeStudentPresent ?? 0) + (props.totalByAttendanceTypeStudentLate ?? 0) +
    (props.totalByAttendanceTypeStudentAbsent ?? 0)  + (props.totalByAttendanceTypeStudentHalfDay ?? 0) || 1
);
const attendanceRadial = computed(() => [
    Math.round((props.totalByAttendanceTypeStudentPresent  ?? 0) / totalAtt.value * 100),
    Math.round((props.totalByAttendanceTypeStudentLate     ?? 0) / totalAtt.value * 100),
    Math.round((props.totalByAttendanceTypeStudentAbsent   ?? 0) / totalAtt.value * 100),
    Math.round((props.totalByAttendanceTypeStudentHalfDay  ?? 0) / totalAtt.value * 100),
]);
const attSeries = [
    { name: 'Présent', data: Array(12).fill(props.totalByAttendanceTypeStudentPresent ?? 0) },
    { name: 'Retard',  data: Array(12).fill(props.totalByAttendanceTypeStudentLate ?? 0) },
    { name: 'Absent',  data: Array(12).fill(props.totalByAttendanceTypeStudentAbsent ?? 0) },
];

const feesProgress = (_studentId: number) => {
    const total = props.totalFeesCollectionsAmountStudents ?? 0;
    const paid  = props.totalFeesCollectionsAmoutPaidByStudents ?? 0;
    if (!total) return 0;
    return Math.min(100, Math.round((paid / total) * 100));
};

const typeColors: Record<string, string> = { academic: '#3b82f6', cultural: '#8b5cf6', administrative: '#f59e0b', exam: '#ef4444', ceremony: '#10b981', trip: '#06b6d4' };
const fmtDay  = (d: string) => d ? new Date(d).getDate() : '';
const fmtDate = (d: string) => d ? new Date(d).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' }) : '—';
</script>
