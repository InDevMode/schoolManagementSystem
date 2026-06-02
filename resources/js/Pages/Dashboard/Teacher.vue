<template>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tableau de bord</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Bienvenue, {{ $page.props.auth.user?.last_name }} {{ $page.props.auth.user?.name }}</p>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            <StatCard label="Mes Apprenants"   :value="totalTeacherStudent" icon="user-group"   color="primary" />
            <StatCard label="Mes Classes"       :value="totalTeacherClass"  icon="building-library" color="secondary" />
            <StatCard label="Mes Matières"      :value="totalTeacherSubject" icon="book-open"   color="success" />
            <StatCard label="Examens"           :value="totalExam"          icon="clipboard-document-list" color="warning" />
            <StatCard label="Examens Aujourd'hui" :value="totalExamTeacherToday" icon="calendar" color="info" />
            <StatCard label="Notifications"     :value="totalNoticeBoardTeacher" icon="bell"    color="primary" />
        </div>
    </div>
</template>

<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import type { PageProps } from '@/types';
import StatCard from '@/Components/Dashboard/StatCard.vue';

const $page = usePage<PageProps>();

defineProps<{
    totalTeacherStudent: number;
    totalTeacherClass: number;
    totalTeacherSubject: number;
    totalExam: number;
    totalExamTeacherToday: number;
    totalNoticeBoardTeacher: number;
    [key: string]: unknown;
}>();
</script>
