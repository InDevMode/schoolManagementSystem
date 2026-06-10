<template>
    <div class="space-y-6">

        <!-- En-tête -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Mon tableau de bord</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    {{ $page.props.auth.user?.last_name }} {{ $page.props.auth.user?.name }}
                    <span v-if="currentPeriod" class="ml-2 px-2 py-0.5 rounded-full bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 text-xs font-medium">
                        {{ currentPeriod.name }}
                    </span>
                </p>
            </div>
        </div>

        <!-- Stats principales -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <BigStatCard label="Mes matières"   :value="totalStudentSubject"  icon="book-open"            color="primary"  href="/student/my_subject"/>
            <BigStatCard label="Mes devoirs"    :value="totalHomeworkStudent ?? 0" icon="pencil"          color="warning"  href="/student/my_homework"/>
            <BigStatCard label="Mes examens"    :value="totalExamStudent ?? 0"   icon="clipboard-document-list" color="info" href="/student/my_exam_timetable"/>
            <BigStatCard label="Mes présences"  :value="totalAttendanceStudent ?? 0" icon="user-check"   color="success"  href="/student/my_attendance"/>
        </div>

        <!-- Présences détaillées -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <AttendanceBadge label="Présent"      :value="totalByAttendanceTypeStudentPresent ?? 0"  color="success" icon="user-check"/>
            <AttendanceBadge label="En retard"    :value="totalByAttendanceTypeStudentLate ?? 0"     color="warning" icon="calendar"/>
            <AttendanceBadge label="Absent"       :value="totalByAttendanceTypeStudentAbsent ?? 0"   color="danger"  icon="user-check"/>
            <AttendanceBadge label="Demi-journée" :value="totalByAttendanceTypeStudentHalfDay ?? 0"  color="info"    icon="calendar-days"/>
        </div>

        <!-- Bulletins récents -->
        <div v-if="myBulletins?.length" class="card p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900 dark:text-white text-sm">Mes bulletins</h3>
                <Link href="/student/my_bulletins" class="text-xs text-primary-600 dark:text-primary-400 hover:underline">Voir tout</Link>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                <Link v-for="b in myBulletins" :key="b.id" :href="`/student/my_bulletins/${b.id}`"
                    class="flex items-center gap-3 p-4 rounded-lg border border-gray-100 dark:border-gray-700 hover:border-primary-300 dark:hover:border-primary-700 hover:shadow-sm transition-all">
                    <!-- Icône bulletin -->
                    <div class="w-10 h-10 rounded-lg flex-shrink-0 flex items-center justify-center"
                        :class="b.average >= 10 ? 'bg-success-50 dark:bg-success-900/20' : 'bg-danger-50 dark:bg-danger-900/20'">
                        <svg class="w-5 h-5" :class="b.average >= 10 ? 'text-success-600 dark:text-success-400' : 'text-danger-600 dark:text-danger-400'"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ b.period_name }}</p>
                        <p class="text-lg font-black" :class="b.average >= 10 ? 'text-success-600 dark:text-success-400' : 'text-danger-600 dark:text-danger-400'">
                            {{ b.average ? Number(b.average).toFixed(2) : '—' }}/20
                        </p>
                        <p class="text-xs text-gray-400">
                            Rang : {{ b.rank ? `${b.rank}/${b.total_students}` : '—' }}
                        </p>
                    </div>
                </Link>
            </div>
        </div>

        <!-- Pas encore de bulletins -->
        <div v-else class="card p-6 border border-dashed border-gray-200 dark:border-gray-700">
            <div class="text-center">
                <svg class="w-10 h-10 text-gray-200 dark:text-gray-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-sm text-gray-400">Aucun bulletin disponible pour le moment.</p>
                <p class="text-xs text-gray-300 dark:text-gray-600 mt-1">Ils apparaîtront ici une fois publiés par votre école.</p>
            </div>
        </div>

        <!-- Contributions + événements -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <!-- Contributions -->
            <div class="card p-5">
                <h3 class="font-semibold text-gray-900 dark:text-white text-sm mb-4">Mes contributions</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Montant total</span>
                        <span class="text-sm font-bold text-gray-900 dark:text-white">
                            {{ (totalFeesCollectionsAmountStudent ?? 0).toLocaleString('fr-FR') }} FCFA
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Déjà payé</span>
                        <span class="text-sm font-bold text-success-600 dark:text-success-400">
                            {{ (totalFeesCollectionsAmoutPaidByStudent ?? 0).toLocaleString('fr-FR') }} FCFA
                        </span>
                    </div>
                    <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2">
                        <div class="h-2 rounded-full bg-success-500 transition-all"
                            :style="{ width: paymentProgress + '%' }"/>
                    </div>
                    <Link href="/student/my_fees" class="block text-center text-xs font-medium text-primary-600 dark:text-primary-400 hover:underline mt-1">
                        Gérer mes contributions →
                    </Link>
                </div>
            </div>

            <!-- Prochains événements -->
            <UpcomingEvents :events="upcomingEvents ?? []"/>
        </div>

    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import type { PageProps } from '@/types';
import StatCard from '@/Components/Dashboard/StatCard.vue';
import BigStatCard from '@/Components/Dashboard/BigStatCard.vue';
import AttendanceBadge from '@/Components/Dashboard/AttendanceBadge.vue';
import UpcomingEvents from '@/Components/Dashboard/UpcomingEvents.vue';

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

const paymentProgress = computed(() => {
    const total = props.totalFeesCollectionsAmountStudent ?? 0;
    const paid  = props.totalFeesCollectionsAmoutPaidByStudent ?? 0;
    if (!total) return 0;
    return Math.min(100, Math.round((paid / total) * 100));
});
</script>
