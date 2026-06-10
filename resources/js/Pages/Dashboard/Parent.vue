<template>
    <div class="space-y-6">

        <!-- En-tête -->
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Mon espace parent</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                {{ $page.props.auth.user?.last_name }} {{ $page.props.auth.user?.name }}
                <span v-if="currentPeriod" class="ml-2 px-2 py-0.5 rounded-full bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 text-xs font-medium">
                    {{ currentPeriod.name }}
                </span>
            </p>
        </div>

        <!-- Mes enfants inscrits -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <StatCard label="Mes enfants"      :value="totalParentStudent"        icon="user-group"   color="primary"  href="/parent/my_student"/>
            <StatCard label="Devoirs à rendre" :value="totalHomeworkStudent ?? 0" icon="pencil"       color="warning"  />
            <StatCard label="Notifications"    :value="totalNoticeBoardParent ?? 0" icon="bell"       color="info"     href="/parent/my_noticeboard"/>
            <StatCard label="Présences"        :value="totalByAttendanceTypeStudentPresent ?? 0" icon="user-check" color="success"/>
        </div>

        <!-- Bulletins des enfants -->
        <div v-if="childrenBulletins?.length">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-3">Bulletins scolaires</h2>
            <div class="space-y-4">
                <div v-for="child in childrenBulletins" :key="child.student.id" class="card p-5">
                    <!-- En-tête enfant -->
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center font-bold text-primary-600 dark:text-primary-400">
                            {{ (child.student.last_name?.[0] ?? child.student.name?.[0] ?? '?').toUpperCase() }}
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                {{ child.student.last_name }} {{ child.student.name }}
                            </p>
                            <p class="text-xs text-gray-400">{{ child.student.class_name }}</p>
                        </div>
                        <Link :href="`/parent/my_student/${child.student.id}/bulletins`"
                            class="ml-auto text-xs text-primary-600 dark:text-primary-400 hover:underline">
                            Tous les bulletins →
                        </Link>
                    </div>

                    <!-- Bulletins -->
                    <div v-if="child.bulletins?.length" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <Link v-for="b in child.bulletins" :key="b.id"
                            :href="`/parent/my_student/${child.student.id}/bulletins/${b.id}`"
                            class="flex items-center gap-3 p-3 rounded-lg border border-gray-100 dark:border-gray-700 hover:border-primary-300 dark:hover:border-primary-700 hover:shadow-sm transition-all">
                            <div class="w-12 h-12 rounded-lg flex flex-col items-center justify-center flex-shrink-0"
                                :class="Number(b.average) >= 10 ? 'bg-success-50 dark:bg-success-900/20' : 'bg-danger-50 dark:bg-danger-900/20'">
                                <span class="text-xs font-black"
                                    :class="Number(b.average) >= 10 ? 'text-success-700 dark:text-success-400' : 'text-danger-700 dark:text-danger-400'">
                                    {{ b.average ? Number(b.average).toFixed(1) : '—' }}
                                </span>
                                <span class="text-[9px] text-gray-400">/20</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-gray-700 dark:text-gray-300">{{ b.period_name }}</p>
                                <p class="text-xs text-gray-400">
                                    Rang : {{ b.rank ? `${b.rank}/${b.total_students}` : '—' }} ·
                                    {{ b.appreciation ?? '—' }}
                                </p>
                            </div>
                            <svg class="w-4 h-4 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </Link>
                    </div>
                    <p v-else class="text-xs text-gray-400 italic">Aucun bulletin publié pour le moment.</p>
                </div>
            </div>
        </div>

        <!-- Pas d'enfants -->
        <div v-else-if="!totalParentStudent" class="card p-10 text-center border border-dashed border-gray-200 dark:border-gray-700">
            <svg class="w-12 h-12 text-gray-200 dark:text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <p class="text-sm text-gray-500 dark:text-gray-400">Aucun enfant associé à votre compte.</p>
            <p class="text-xs text-gray-300 dark:text-gray-600 mt-1">Contactez l'administration pour faire le lien.</p>
        </div>

        <!-- Présences + Événements -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div class="card p-5">
                <h3 class="font-semibold text-gray-900 dark:text-white text-sm mb-4">Présences de mes enfants</h3>
                <div class="grid grid-cols-2 gap-3">
                    <AttendanceBadge label="Présent"      :value="totalByAttendanceTypeStudentPresent ?? 0"  color="success" icon="user-check"/>
                    <AttendanceBadge label="En retard"    :value="totalByAttendanceTypeStudentLate ?? 0"     color="warning" icon="calendar"/>
                    <AttendanceBadge label="Absent"       :value="totalByAttendanceTypeStudentAbsent ?? 0"   color="danger"  icon="user-check"/>
                    <AttendanceBadge label="Demi-journée" :value="totalByAttendanceTypeStudentHalfDay ?? 0"  color="info"    icon="calendar-days"/>
                </div>
            </div>
            <UpcomingEvents :events="upcomingEvents ?? []"/>
        </div>

    </div>
</template>

<script setup lang="ts">
import { usePage, Link } from '@inertiajs/vue3';
import type { PageProps } from '@/types';
import StatCard from '@/Components/Dashboard/StatCard.vue';
import AttendanceBadge from '@/Components/Dashboard/AttendanceBadge.vue';
import UpcomingEvents from '@/Components/Dashboard/UpcomingEvents.vue';

const $page = usePage<PageProps>();

defineProps<{
    totalParentStudent:     number;
    totalNoticeBoardParent?: number;
    totalHomeworkStudent?:  number;
    totalByAttendanceTypeStudentPresent?:  number;
    totalByAttendanceTypeStudentLate?:     number;
    totalByAttendanceTypeStudentAbsent?:   number;
    totalByAttendanceTypeStudentHalfDay?:  number;
    childrenBulletins?: { student: any; bulletins: any[] }[];
    upcomingEvents?:    any[];
    currentPeriod?:     any;
    [key: string]: unknown;
}>();
</script>
