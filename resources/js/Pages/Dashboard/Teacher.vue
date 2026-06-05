<template>
    <div class="space-y-6">

        <!-- En-tête -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tableau de bord</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Bienvenue, {{ $page.props.auth.user?.last_name }} {{ $page.props.auth.user?.name }}
                    <span v-if="currentPeriod" class="ml-2 px-2 py-0.5 rounded-full bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 text-xs font-medium">
                        {{ currentPeriod.name }}
                    </span>
                </p>
            </div>
        </div>

        <!-- Stats principales -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            <BigStatCard label="Mes apprenants"  :value="totalTeacherStudent" icon="user-group"            color="primary"   href="/teacher/my_student"/>
            <BigStatCard label="Mes classes"     :value="totalTeacherClass"   icon="building-library"     color="info"      href="/teacher/class_subject"/>
            <BigStatCard label="Mes matières"    :value="totalTeacherSubject" icon="book-open"            color="success"   href="/teacher/class_subject"/>
            <BigStatCard label="Examens today"   :value="totalExamTeacherToday" icon="calendar"           color="warning"   href="/teacher/my_exam_timetable"/>
        </div>

        <!-- Stats secondaires -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <StatCard label="Total examens"       :value="totalExamTeacher"    icon="clipboard-document-list" color="primary" />
            <StatCard label="Notifications"       :value="totalNoticeBoardTeacher" icon="bell"             color="secondary" href="/teacher/my_noticeboard"/>
            <StatCard label="Évals à valider"     :value="0"                   icon="check-badge"             color="warning"  href="/teacher/evaluations"/>
            <StatCard label="Devoirs assignés"    :value="0"                   icon="pencil"                  color="info"     href="/teacher/practicalworks/homework/list"/>
        </div>

        <!-- Évaluations récentes -->
        <div v-if="myRecentEvaluations?.length" class="card p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900 dark:text-white text-sm">Mes évaluations récentes</h3>
                <a href="/teacher/evaluations" class="text-xs text-primary-600 dark:text-primary-400 hover:underline">Voir tout</a>
            </div>
            <div class="flex flex-col gap-2">
                <div v-for="ev in myRecentEvaluations" :key="ev.id"
                    class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors">
                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" :style="{ background: typeColors[ev.type] ?? '#6366f1' }"/>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                            {{ typeLabels[ev.type] ?? ev.type }}
                            <span class="text-gray-400 font-normal">— {{ ev.subject_name }} · {{ ev.class_name }}</span>
                        </p>
                        <p class="text-xs text-gray-400">{{ formatDate(ev.eval_date) }}</p>
                    </div>
                    <span :class="['text-xs px-2 py-0.5 rounded-full font-medium', statusClass(ev.status)]">
                        {{ statusLabel(ev.status) }}
                    </span>
                    <a :href="`/teacher/evaluations/grade-entry?evaluation_id=${ev.id}`"
                        class="text-xs font-medium text-primary-600 dark:text-primary-400 hover:underline flex-shrink-0">
                        Saisir →
                    </a>
                </div>
            </div>
        </div>

        <!-- Prochains événements -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <UpcomingEvents :events="upcomingEvents ?? []" see-all-href="/admin/staff/events/list"/>

            <!-- Emploi du temps du jour -->
            <div class="card p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-900 dark:text-white text-sm">Aujourd'hui</h3>
                    <span class="text-xs text-gray-400">
                        {{ new Date().toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long' }) }}
                    </span>
                </div>
                <a href="/teacher/class_subject"
                    class="flex items-center gap-3 p-4 rounded-xl bg-primary-50 dark:bg-primary-900/20 hover:bg-primary-100 dark:hover:bg-primary-900/30 transition-colors">
                    <div class="w-10 h-10 rounded-xl bg-primary-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-primary-700 dark:text-primary-300">Voir mon emploi du temps</p>
                        <p class="text-xs text-primary-500 dark:text-primary-400">{{ totalExamTeacher }} cours planifiés</p>
                    </div>
                </a>
            </div>
        </div>

    </div>
</template>

<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import type { PageProps } from '@/types';
import StatCard from '@/Components/Dashboard/StatCard.vue';
import BigStatCard from '@/Components/Dashboard/BigStatCard.vue';
import UpcomingEvents from '@/Components/Dashboard/UpcomingEvents.vue';

const $page = usePage<PageProps>();

defineProps<{
    totalTeacherStudent:    number;
    totalTeacherClass:      number;
    totalTeacherSubject:    number;
    totalExam:              number;
    totalExamTeacherToday:  number;
    totalExamTeacher:       number;
    totalNoticeBoardTeacher: number;
    upcomingEvents?:        any[];
    calendarEvents?:        any[];
    currentPeriod?:         any;
    myRecentEvaluations?:   any[];
    [key: string]: unknown;
}>();

const typeLabels: Record<string, string> = {
    interrogation:    'Interrogation',
    devoir_surveille: 'Devoir surveillé',
    travail_maison:   'Travail de maison',
    examen_blanc:     'Examen blanc',
};
const typeColors: Record<string, string> = {
    interrogation:    '#3b82f6',
    devoir_surveille: '#f59e0b',
    travail_maison:   '#10b981',
    examen_blanc:     '#ef4444',
};

const statusClass = (s: string) => ({
    draft:     'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300',
    open:      'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400',
    closed:    'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400',
    validated: 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400',
}[s] ?? 'bg-gray-100 text-gray-600');

const statusLabel = (s: string) => ({
    draft: 'Brouillon', open: 'Ouverte', closed: 'Fermée', validated: 'Validée',
}[s] ?? s);

const formatDate = (d: string) =>
    d ? new Date(d).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' }) : '—';
</script>
