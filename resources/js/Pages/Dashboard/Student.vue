<template>
    <div class="space-y-5">

        <!-- ══ HEADER ═══════════════════════════════════════════════════════ -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-black text-gray-900 dark:text-white">Mon espace apprenant</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $page.props.auth.user?.last_name }} {{ $page.props.auth.user?.name }}</span>
                    <span v-if="currentPeriod" class="ml-2 inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 text-xs font-semibold">
                        <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        {{ currentPeriod.name }}
                    </span>
                </p>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-xs font-semibold shadow w-fit">
                <span class="w-2 h-2 rounded-full bg-green-300 animate-pulse inline-block"/>Apprenant
            </span>
        </div>

        <!-- ══ KPI ═══════════════════════════════════════════════════════════ -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <KpiCard label="Mes matières"   :value="totalStudentSubject"       color="violet" href="/student/my_subject"
                icon="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            <KpiCard label="Mes devoirs"    :value="totalHomeworkStudent ?? 0" color="warning" href="/student/my_homework"
                icon="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
            <KpiCard label="Mes examens"    :value="totalExamStudent ?? 0"     color="info"    href="/student/my_exam_timetable"
                icon="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            <KpiCard label="Mes présences"  :value="totalAttendanceStudent ?? 0" color="success" href="/student/my_attendance"
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

                    <!-- Bulletins + contributions -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <!-- Bulletins -->
                        <div class="card p-5">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Mes bulletins</h3>
                                <a href="/student/my_bulletins" class="text-xs text-primary-600 dark:text-primary-400 hover:underline">Voir tout</a>
                            </div>
                            <div v-if="!myBulletins?.length" class="flex flex-col items-center justify-center py-8 text-center">
                                <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-3">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <p class="text-sm text-gray-400">Aucun bulletin disponible</p>
                            </div>
                            <div v-else class="space-y-3">
                                <a v-for="b in myBulletins" :key="b.id" :href="`/student/my_bulletins/${b.id}`"
                                    class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 dark:border-gray-700 hover:border-primary-300 dark:hover:border-primary-700 hover:shadow-sm transition-all">
                                    <div class="w-12 h-12 rounded-xl flex flex-col items-center justify-center flex-shrink-0"
                                        :class="b.average >= 10 ? 'bg-emerald-50 dark:bg-emerald-900/20' : 'bg-red-50 dark:bg-red-900/20'">
                                        <span class="text-sm font-black" :class="b.average >= 10 ? 'text-emerald-600' : 'text-red-600'">
                                            {{ b.average ? Number(b.average).toFixed(1) : '—' }}
                                        </span>
                                        <span class="text-[9px] text-gray-400">/20</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-semibold text-gray-700 dark:text-gray-200">{{ b.period_name }}</p>
                                        <p class="text-[10px] text-gray-400">Rang {{ b.rank ? `${b.rank}/${b.total_students}` : '—' }} · {{ b.appreciation ?? '—' }}</p>
                                    </div>
                                    <span class="text-xs text-gray-300 dark:text-gray-600 flex-shrink-0">→</span>
                                </a>
                            </div>
                        </div>

                        <!-- Contributions -->
                        <div class="card p-5">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Mes contributions</h3>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">Montant total</span>
                                    <span class="text-base font-bold text-gray-900 dark:text-white">
                                        {{ (totalFeesCollectionsAmountStudent ?? 0).toLocaleString('fr-FR') }} FCFA
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">Déjà payé</span>
                                    <span class="text-base font-bold text-emerald-600 dark:text-emerald-400">
                                        {{ (totalFeesCollectionsAmoutPaidByStudent ?? 0).toLocaleString('fr-FR') }} FCFA
                                    </span>
                                </div>
                                <div>
                                    <div class="flex justify-between text-xs mb-1">
                                        <span class="text-gray-400">Progression</span>
                                        <span class="font-semibold text-emerald-600">{{ paymentProgress }}%</span>
                                    </div>
                                    <div class="w-full h-3 rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                                        <div class="h-3 rounded-full bg-gradient-to-r from-emerald-400 to-emerald-600 transition-all duration-700"
                                            :style="{ width: paymentProgress + '%' }"/>
                                    </div>
                                </div>
                                <a href="/student/my_fees" class="block w-full text-center py-2.5 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 text-xs font-semibold hover:bg-emerald-100 dark:hover:bg-emerald-900/30 transition-colors">
                                    Gérer mes contributions →
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Événements -->
                    <div v-if="upcomingEvents?.length" class="card p-5">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Prochains événements</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                            <div v-for="ev in (upcomingEvents ?? []).slice(0,6)" :key="ev.id"
                                class="flex items-center gap-2 p-2.5 rounded-lg border border-gray-100 dark:border-gray-700 hover:border-primary-200 dark:hover:border-primary-800 transition-colors">
                                <div class="w-8 h-8 rounded-lg flex-shrink-0 flex items-center justify-center text-white text-[10px] font-bold"
                                    :style="{ background: typeColors[ev.event_type] ?? '#6366f1' }">
                                    {{ fmtDay(ev.event_date ?? ev.start) }}
                                </div>
                                <p class="text-xs text-gray-700 dark:text-gray-300 font-medium truncate">{{ ev.title }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ── PRÉSENCES ───────────────────────────────────────────── -->
                <div v-show="active === 'attendance'" class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Mes présences</h2>
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
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Présences par mois</h3>
                            <ApexBar
                                :series="attSeries"
                                :categories="months"
                                :colors="['#10B981','#F59E0B','#EF4444']"
                                :height="240"
                            />
                        </div>
                    </div>
                </div>

                <!-- ── BULLETINS ───────────────────────────────────────────── -->
                <div v-show="active === 'bulletins'" class="space-y-4">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Mes bulletins scolaires</h2>
                    <div v-if="!myBulletins?.length" class="card p-10 text-center border border-dashed border-gray-200 dark:border-gray-700">
                        <p class="text-sm text-gray-400">Aucun bulletin disponible pour le moment.</p>
                    </div>
                    <div v-else>
                        <!-- Chart moyennes -->
                        <div class="card p-5">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Évolution de ma moyenne</h3>
                            <ApexArea
                                :series="[{ name: 'Moyenne', data: myBulletins.map(b => Number(b.average ?? 0)) }]"
                                :categories="myBulletins.map(b => b.period_name ?? '—')"
                                :colors="['#7C3AED']"
                                :height="200"
                            />
                        </div>
                        <!-- Liste -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                            <a v-for="b in myBulletins" :key="b.id" :href="`/student/my_bulletins/${b.id}`"
                                class="card p-4 hover:shadow-md hover:-translate-y-0.5 transition-all">
                                <div class="flex items-center gap-3">
                                    <div class="w-14 h-14 rounded-2xl flex flex-col items-center justify-center flex-shrink-0"
                                        :class="b.average >= 10 ? 'bg-gradient-to-br from-emerald-400 to-emerald-600' : 'bg-gradient-to-br from-red-400 to-red-600'">
                                        <span class="text-lg font-black text-white">{{ b.average ? Number(b.average).toFixed(1) : '—' }}</span>
                                        <span class="text-[9px] text-white/80">/20</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ b.period_name }}</p>
                                        <p class="text-xs text-gray-400 mt-0.5">{{ b.appreciation ?? '—' }}</p>
                                        <p class="text-xs text-gray-400">Rang : {{ b.rank ? `${b.rank}/${b.total_students}` : '—' }}</p>
                                    </div>
                                </div>
                                <ProgressBar label="Rang classe" :percent="b.rank ? Math.round((1 - (b.rank - 1) / (b.total_students || 1)) * 100) : 0"
                                    :value="0" :color="b.average >= 10 ? 'success' : 'danger'" class="mt-3" />
                            </a>
                        </div>
                    </div>
                </div>

                <!-- ── CONTRIBUTIONS ───────────────────────────────────────── -->
                <div v-show="active === 'fees'" class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Mes contributions</h2>
                        <PeriodFilter v-model="feesPeriod" />
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <KpiCard label="Montant total" :value="totalFeesCollectionsAmountStudent ?? 0" suffix="FCFA" color="violet" icon="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2"/>
                        <KpiCard label="Déjà payé" :value="totalFeesCollectionsAmoutPaidByStudent ?? 0" suffix="FCFA" color="success" icon="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        <KpiCard label="Reste à payer" :value="(totalFeesCollectionsAmountStudent ?? 0) - (totalFeesCollectionsAmoutPaidByStudent ?? 0)" suffix="FCFA" color="warning" icon="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </div>
                    <div class="card p-5">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Progression du paiement</h3>
                        <div class="flex items-center justify-center py-4">
                            <div class="relative w-40 h-40">
                                <ApexDonut
                                    :series="[totalFeesCollectionsAmoutPaidByStudent ?? 0, Math.max(0, (totalFeesCollectionsAmountStudent ?? 0) - (totalFeesCollectionsAmoutPaidByStudent ?? 0))]"
                                    :labels="['Payé','Reste']"
                                    :colors="['#10B981','#F3F4F6']"
                                    :center-label="'Payé'"
                                    :center-value="paymentProgress + '%'"
                                    :height="200"
                                />
                            </div>
                        </div>
                        <div class="mt-2 space-y-2">
                            <ProgressBar label="Paiement" :percent="paymentProgress" :value="0" color="success" />
                        </div>
                        <a href="/student/my_fees" class="mt-4 block text-center text-xs font-medium text-primary-600 dark:text-primary-400 hover:underline">
                            Effectuer un paiement →
                        </a>
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
import ApexDonut       from '@/Components/Dashboard/ApexDonut.vue';
import ApexBar         from '@/Components/Dashboard/ApexBar.vue';
import ApexArea        from '@/Components/Dashboard/ApexArea.vue';
import ApexRadial      from '@/Components/Dashboard/ApexRadial.vue';

const $page = usePage<PageProps>();
const props = defineProps<{
    totalStudentSubject:    number;
    totalHomeworkStudent?:  number;
    totalExamStudent?:      number;
    totalAttendanceStudent?: number;
    totalByAttendanceTypeStudentPresent?:  number;
    totalByAttendanceTypeStudentLate?:     number;
    totalByAttendanceTypeStudentAbsent?:   number;
    totalByAttendanceTypeStudentHalfDay?:  number;
    totalFeesCollectionsAmountStudent?:       number;
    totalFeesCollectionsAmoutPaidByStudent?:  number;
    myBulletins?:    any[];
    upcomingEvents?: any[];
    currentPeriod?:  any;
    [key: string]: unknown;
}>();

const months = ['Jan','Fév','Mar','Avr','Mai','Jun','Jul','Aoû','Sep','Oct','Nov','Déc'];
const attendancePeriod = ref('month');
const feesPeriod = ref('month');

const tabs = [
    { key: 'overview',   label: 'Vue générale',    icon: 'chart-bar' },
    { key: 'attendance', label: 'Présences',        icon: 'user-check' },
    { key: 'bulletins',  label: 'Bulletins',        icon: 'document-text', badge: props.myBulletins?.length },
    { key: 'fees',       label: 'Contributions',    icon: 'banknotes' },
];

const paymentProgress = computed(() => {
    const total = props.totalFeesCollectionsAmountStudent ?? 0;
    const paid  = props.totalFeesCollectionsAmoutPaidByStudent ?? 0;
    if (!total) return 0;
    return Math.min(100, Math.round((paid / total) * 100));
});

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
    { name: 'Présent',   data: Array(12).fill(0).map((_, i) => Math.max(0, (props.totalByAttendanceTypeStudentPresent ?? 0) - i)) },
    { name: 'Retard',    data: Array(12).fill(0).map(() => props.totalByAttendanceTypeStudentLate ?? 0) },
    { name: 'Absent',    data: Array(12).fill(0).map(() => props.totalByAttendanceTypeStudentAbsent ?? 0) },
];

const typeColors: Record<string, string> = { academic: '#3b82f6', cultural: '#8b5cf6', administrative: '#f59e0b', exam: '#ef4444', ceremony: '#10b981', trip: '#06b6d4' };
const fmtDay = (d: string) => d ? new Date(d).getDate() : '';
</script>
