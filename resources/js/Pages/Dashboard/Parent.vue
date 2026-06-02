<template>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Mon Tableau de bord</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Bienvenue, {{ $page.props.auth.user?.last_name }} {{ $page.props.auth.user?.name }}</p>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            <StatCard label="Mes Enfants"     :value="totalParentStudent"  icon="user-group"  color="primary" />
            <StatCard label="Notifications"   :value="totalNoticeBoardParent" icon="bell"     color="secondary" />
            <StatCard label="Présences"       :value="totalAttendance"     icon="user-check"  color="success" />
        </div>
    </div>
</template>

<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import type { PageProps } from '@/types';
import StatCard from '@/Components/Dashboard/StatCard.vue';

const $page = usePage<PageProps>();

defineProps<{
    totalParentStudent: number;
    totalNoticeBoardParent: number;
    totalAttendance: number;
    [key: string]: unknown;
}>();
</script>
