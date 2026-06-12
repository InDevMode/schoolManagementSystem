<template>
    <div class="space-y-5">

        <!-- ══ HEADER ═══════════════════════════════════════════════════════ -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-black text-gray-900 dark:text-white">Tableau de bord — Professeur</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    Bienvenue, <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $page.props.auth.user?.last_name }} {{ $page.props.auth.user?.name }}</span>
                    <span v-if="currentPeriod" class="ml-2 inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 text-xs font-semibold">
                        <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        {{ currentPeriod.name }}
                    </span>
                </p>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-600 text-white text-xs font-semibold shadow w-fit">
                <span class="w-2 h-2 rounded-full bg-green-300 animate-pulse inline-block"/>Professeur
            </span>
        </div>

        <!-- ══ KPI ═══════════════════════════════════════════════════════════ -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <KpiCard label="Mes apprenants"  :value="totalTeacherStudent" color="violet" href="/teacher/my_student"
                icon="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            <KpiCard label="Mes classes"     :value="totalTeacherClass"   color="info"   href="/teacher/class_subject"
                icon="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            <KpiCard label="Mes matières"    :value="totalTeacherSubject" color="success" href="/teacher/class_subject"
                icon="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            <KpiCard label="Évals aujourd'hui" :value="totalExamTeacherToday ?? 0" color="warning" href="/teacher/evaluations"
                icon="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </div>

        <!-- ══ TABS ══════════════════════════════════════════════════════════ -->
        <DashTabs :tabs="tabs">
            <template #default="{ active }">

                <!-- ── VUE GÉNÉRALE ───────────────────────────────────────── -->
                <div v-show="active === 'overview'" class="space-y-4">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <StatCard label="Total évals"     :value="totalExamTeacher ?? 0"        icon="clipboard-document-list" color="primary" href="/teacher/evaluations"/>
                        <StatCard label="Notifications"   :value="totalNoticeBoardTeacher ?? 0" icon="bell"                    color="secondary" href="/teacher/my_noticeboard"/>
                        <StatCard label="Devoirs assignés" :value="totalTeacherHomework ?? 0"   icon="pencil"                  color="warning"  href="/teacher/practicalworks/homework/list"/>
                        <StatCard label="À valider"        :value="totalPendingEvals ?? 0"      icon="check-badge"             color="danger"   href="/teacher/evaluations"/>
                    </div>

                    <!-- Évals récentes + événements -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <!-- Évaluations récentes -->
                        <div class="card p-5">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Mes évaluations récentes</h3>
                                <a href="/teacher/evaluations" class="text-xs text-primary-600 dark:text-primary-400 hover:underline">Voir tout</a>
                            </div>
                            <div v-if="!myRecentEvaluations?.length" class="text-center py-8 text-xs text-gray-400">Aucune évaluation récente</div>
                            <div v-else class="space-y-2">
                                <div v-for="ev in myRecentEvaluations.slice(0, 6)" :key="ev.id"
                                    class="flex items-center gap-3 p-2.5 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                    <span class="w-3 h-3 rounded-full flex-shrink-0" :style="{ background: typeColors[ev.type] ?? '#6366f1' }"/>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-semibold text-gray-900 dark:text-white truncate">
                                            {{ typeLabels[ev.type] ?? ev.type }}
                                            <span class="text-gray-400 font-normal"> — {{ ev.subject_name }} · {{ ev.class_name }}</span>
                                        </p>
                                        <p class="text-[10px] text-gray-400">{{ fmtDate(ev.eval_date) }}</p>
                                    </div>
                                    <span :class="['text-[10px] px-2 py-0.5 rounded-full font-semibold', statusClass(ev.status)]">
                                        {{ statusLabel(ev.status) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Emploi du temps + événements -->
                        <div class="space-y-3">
                            <div class="card p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Aujourd'hui</h3>
                                    <span class="text-xs text-gray-400">{{ today.toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long' }) }}</span>
                                </div>
                                <a href="/teacher/class_subject"
                                    class="flex items-center gap-3 p-3 rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-700 text-white hover:opacity-90 transition-opacity">
                                    <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold">Mon emploi du temps</p>
                                        <p class="text-xs opacity-80">{{ totalExamTeacher ?? 0 }} cours planifiés</p>
                                    </div>
                                </a>
                            </div>

                            <div v-if="upcomingEvents?.length" class="card p-4">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Prochains événements</h3>
                                <div class="space-y-2">
                                    <div v-for="ev in (upcomingEvents ?? []).slice(0, 3)" :key="ev.id"
                                        class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-300">
                                        <span class="w-2 h-2 rounded-full flex-shrink-0" :style="{ background: typeColors[ev.event_type ?? ev.extendedProps?.type] ?? '#6366f1' }"/>
                                        <span class="truncate">{{ ev.title }}</span>
                                        <span class="ml-auto flex-shrink-0 text-gray-400">{{ fmtDate(ev.event_date ?? ev.start) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statuts évals -->
                    <div class="card p-5">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Statuts de mes évaluations</h3>
                        <ApexBar
                            :series="evalStatusSeries"
                            :categories="['Interrogation','Devoir surveillé','Travail maison','Examen blanc']"
                            :colors="['#3B82F6','#F59E0B','#10B981','#EF4444']"
                            :height="200"
                            stacked
                        />
                    </div>
                </div>

                <!-- ── ÉVALUATIONS ─────────────────────────────────────────── -->
                <div v-show="active === 'evaluations'" class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Mes évaluations</h2>
                        <PeriodFilter v-model="evalPeriod" />
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <KpiCard label="Total évals"      :value="totalExamTeacher ?? 0"  color="violet" icon="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        <KpiCard label="À valider"        :value="totalPendingEvals ?? 0" color="warning" icon="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        <KpiCard label="Devoirs"           :value="totalTeacherHomework ?? 0" color="info" icon="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        <KpiCard label="Aujourd'hui"       :value="totalExamTeacherToday ?? 0" color="success" icon="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <div class="card p-5">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Répartition par type</h3>
                            <ApexDonut
                                :series="[8, 5, 10, 3]"
                                :labels="['Interrogation','Devoir','Travail maison','Examen blanc']"
                                :colors="['#3B82F6','#F59E0B','#10B981','#EF4444']"
                                :height="200"
                            />
                        </div>
                        <div class="card p-5">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Évaluations par mois</h3>
                            <ApexArea
                                :series="[{ name: 'Évaluations', data: [2, 3, 5, 4, 6, 3, 7, 5, 4, 6, 5, 8] }]"
                                :categories="months"
                                :colors="['#7C3AED']"
                                :height="200"
                            />
                        </div>
                    </div>
                </div>

                <!-- ── PRÉSENCES ───────────────────────────────────────────── -->
                <div v-show="active === 'attendance'" class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Présences de mes classes</h2>
                        <PeriodFilter v-model="attendancePeriod" />
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <AttendanceBadge label="Présents"     :value="totalAttPresent ?? 0"  color="success" icon="user-check"/>
                        <AttendanceBadge label="En retard"    :value="totalAttLate ?? 0"     color="warning" icon="clock"/>
                        <AttendanceBadge label="Absents"      :value="totalAttAbsent ?? 0"   color="danger"  icon="user-minus"/>
                        <AttendanceBadge label="Demi-journée" :value="totalAttHalfDay ?? 0"  color="info"    icon="calendar-days"/>
                    </div>
                    <div class="card p-5">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Évolution des présences</h3>
                        <ApexBar
                            :series="teacherAttSeries"
                            :categories="months"
                            :colors="['#10B981','#F59E0B','#EF4444']"
                            :height="220"
                        />
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="card p-5">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Taux de présence</h3>
                            <ApexRadial
                                :series="teacherAttRadial"
                                :labels="['Présents','Retards','Absents']"
                                :colors="['#10B981','#F59E0B','#EF4444']"
                                :height="220"
                            />
                        </div>
                        <div class="card p-5">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Progression mensuelle</h3>
                            <div class="space-y-2.5 mt-1">
                                <ProgressBar v-for="(m, i) in months.slice(0, 8)" :key="m"
                                    :label="m" :value="attendanceMonthData[i] ?? 0" :max="totalTeacherStudent || 1"
                                    :color="['success','success','info','info','warning','warning','violet','violet'][i] as any"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ── DEVOIRS ─────────────────────────────────────────────── -->
                <div v-show="active === 'homework'" class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Devoirs & Travaux</h2>
                        <PeriodFilter v-model="hwPeriod" />
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        <KpiCard label="Devoirs assignés"   :value="totalTeacherHomework ?? 0" color="violet" href="/teacher/practicalworks/homework/list" icon="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        <KpiCard label="Apprenants"          :value="totalTeacherStudent"       color="info" href="/teacher/my_student" icon="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7"/>
                        <KpiCard label="Classes"             :value="totalTeacherClass"         color="success" href="/teacher/class_subject" icon="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5"/>
                    </div>
                    <div class="card p-5">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Devoirs par semaine</h3>
                        <ApexArea
                            :series="hwAreaSeries"
                            :categories="['S1','S2','S3','S4','S5','S6','S7','S8']"
                            :colors="['#7C3AED','#3B82F6']"
                            :height="200"
                        />
                    </div>
                    <div class="card p-5">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Progression par matière</h3>
                        <div class="space-y-3">
                            <ProgressBar label="Travaux assignés / Apprenants" :value="totalTeacherHomework ?? 0" :max="totalTeacherStudent || 1" color="violet" />
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
import StatCard       from '@/Components/Dashboard/StatCard.vue';
import KpiCard        from '@/Components/Dashboard/KpiCard.vue';
import AttendanceBadge from '@/Components/Dashboard/AttendanceBadge.vue';
import DashTabs       from '@/Components/Dashboard/DashTabs.vue';
import PeriodFilter   from '@/Components/Dashboard/PeriodFilter.vue';
import ProgressBar    from '@/Components/Dashboard/ProgressBar.vue';
import ApexDonut      from '@/Components/Dashboard/ApexDonut.vue';
import ApexBar        from '@/Components/Dashboard/ApexBar.vue';
import ApexArea       from '@/Components/Dashboard/ApexArea.vue';
import ApexRadial     from '@/Components/Dashboard/ApexRadial.vue';

const $page = usePage<PageProps>();

const props = defineProps<{
    totalTeacherStudent:    number;
    totalTeacherClass:      number;
    totalTeacherSubject:    number;
    totalExam?:             number;
    totalExamTeacherToday?: number;
    totalExamTeacher?:      number;
    totalNoticeBoardTeacher?: number;
    totalTeacherHomework?:  number;
    totalPendingEvals?:     number;
    totalAttPresent?:       number;
    totalAttLate?:          number;
    totalAttAbsent?:        number;
    totalAttHalfDay?:       number;
    upcomingEvents?:        any[];
    calendarEvents?:        any[];
    currentPeriod?:         any;
    myRecentEvaluations?:   any[];
    [key: string]: unknown;
}>();

const today  = new Date();
const months = ['Jan','Fév','Mar','Avr','Mai','Jun','Jul','Aoû','Sep','Oct','Nov','Déc'];
const evalPeriod       = ref('month');
const attendancePeriod = ref('month');
const hwPeriod         = ref('month');

const tabs = [
    { key: 'overview',     label: 'Vue générale',  icon: 'chart-bar' },
    { key: 'evaluations',  label: 'Évaluations',   icon: 'clipboard-document-list', badge: props.totalPendingEvals },
    { key: 'attendance',   label: 'Présences',      icon: 'user-check' },
    { key: 'homework',     label: 'Devoirs',        icon: 'pencil' },
];

const typeLabels: Record<string, string> = { interrogation: 'Interrogation', devoir_surveille: 'Devoir surveillé', travail_maison: 'Travail maison', examen_blanc: 'Examen blanc' };
const typeColors: Record<string, string> = { interrogation: '#3b82f6', devoir_surveille: '#f59e0b', travail_maison: '#10b981', examen_blanc: '#ef4444', academic: '#3b82f6', cultural: '#8b5cf6', administrative: '#f59e0b', exam: '#ef4444', ceremony: '#10b981', trip: '#06b6d4' };
const statusClass = (s: string) => ({ draft: 'bg-gray-100 dark:bg-gray-700 text-gray-600', open: 'bg-blue-100 dark:bg-blue-900/30 text-blue-700', closed: 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700', validated: 'bg-green-100 dark:bg-green-900/30 text-green-700' }[s] ?? 'bg-gray-100 text-gray-600');
const statusLabel = (s: string) => ({ draft: 'Brouillon', open: 'Ouverte', closed: 'Fermée', validated: 'Validée' }[s] ?? s);
const fmtDate = (d: string) => d ? new Date(d).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' }) : '—';

const evalStatusSeries = [
    { name: 'Brouillon', data: [2, 1, 3, 1] },
    { name: 'Ouvertes',  data: [3, 2, 4, 2] },
    { name: 'Validées',  data: [5, 4, 6, 3] },
];

const totalAtt = computed(() => (props.totalAttPresent ?? 0) + (props.totalAttLate ?? 0) + (props.totalAttAbsent ?? 0) + (props.totalAttHalfDay ?? 0) || 1);
const teacherAttSeries = [
    { name: 'Présents', data: Array(12).fill(0).map((_, i) => Math.max(0, (props.totalAttPresent ?? 0) - i * 1)) },
    { name: 'Retards',  data: Array(12).fill(0).map((_, i) => Math.max(0, (props.totalAttLate ?? 0) + i * 0)) },
    { name: 'Absents',  data: Array(12).fill(0).map((_, i) => Math.max(0, (props.totalAttAbsent ?? 0) + i * 0)) },
];
const teacherAttRadial = computed(() => [
    Math.round((props.totalAttPresent ?? 0) / totalAtt.value * 100),
    Math.round((props.totalAttLate ?? 0)    / totalAtt.value * 100),
    Math.round((props.totalAttAbsent ?? 0)  / totalAtt.value * 100),
]);
const attendanceMonthData = Array(12).fill(0).map((_, i) => Math.max(0, (props.totalAttPresent ?? 0) - i * 1));

const hwAreaSeries = [
    { name: 'Devoirs assignés', data: [2, 3, 2, 4, 3, 5, 4, 6] },
    { name: 'Rendus',           data: [1, 2, 2, 3, 3, 4, 4, 5] },
];
</script>
