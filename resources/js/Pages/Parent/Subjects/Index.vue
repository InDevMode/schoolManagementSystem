<template>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Matières de {{ student?.last_name }} {{ student?.name }}</h1>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div v-for="subject in subjects.data" :key="subject.id" class="card p-5">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-lg bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ subject.subject_name }}</p>
                        <p class="text-xs text-gray-500">{{ subject.subject_type }}</p>
                    </div>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Coefficient</span>
                    <AppBadge variant="primary">{{ subject.coefficient ?? 1 }}</AppBadge>
                </div>
            </div>
            <p v-if="!subjects.data?.length" class="text-gray-400 col-span-3 text-center py-8">Aucune matière assignée.</p>
        </div>
    </div>
</template>
<script setup lang="ts">
import { AppBadge } from '@/Components/UI';
defineProps<{ subjects: any; student: any }>();
</script>
