<template>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Mon Tableau de bord</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Bienvenue, {{ $page.props.auth.user?.last_name }} {{ $page.props.auth.user?.name }}</p>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            <StatCard label="Mes Matières"    :value="totalStudentSubject"  icon="book-open"   color="primary" />
            <StatCard label="Mes Examens"     :value="totalExamStudent"     icon="clipboard-document-list" color="secondary" />
            <StatCard label="Mes Devoirs"     :value="totalHomeworkStudent" icon="pencil"       color="warning" />
            <StatCard label="Ma Présence"     :value="totalAttendanceStudent" icon="user-check" color="success" />
            <StatCard label="Présent"         :value="totalByAttendanceTypeStudentPresent" icon="user-check" color="success" />
            <StatCard label="Absent"          :value="totalByAttendanceTypeStudentAbsent" icon="user-check" color="danger" />
            <StatCard label="Notifications"   :value="totalNoticeBoardStudent" icon="bell"      color="info" />
        </div>
    </div>
</template>

<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import type { PageProps } from '@/types';
import StatCard from '@/Components/Dashboard/StatCard.vue';

const $page = usePage<PageProps>();

defineProps<{
    totalStudentSubject: number;
    totalExamStudent: number;
    totalHomeworkStudent: number;
    totalAttendanceStudent: number;
    totalByAttendanceTypeStudentPresent: number;
    totalByAttendanceTypeStudentAbsent: number;
    totalNoticeBoardStudent: number;
    [key: string]: unknown;
}>();
</script>
