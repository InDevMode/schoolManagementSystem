<template>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Mes Classes</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Classes qui vous sont assignées</p>
        </div>

        <div v-if="classSubjects.length" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            <div
                v-for="item in classSubjects"
                :key="item.id"
                class="card p-5 space-y-3 hover:shadow-md transition-shadow"
            >
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ item.class_name }}</p>
                        <AppBadge :variant="item.status == 1 ? 'success' : 'danger'" dot size="sm">
                            {{ item.status == 1 ? 'Actif' : 'Inactif' }}
                        </AppBadge>
                    </div>
                </div>
            </div>
        </div>

        <div v-else class="card p-12 text-center">
            <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            <p class="text-sm text-gray-500 dark:text-gray-400">Aucune classe assignée</p>
        </div>
    </div>
</template>

<script setup lang="ts">
import { AppBadge } from '@/Components/UI';

interface ClassSubject {
    id: number;
    class_id: number;
    class_name: string;
    status: number;
}

defineProps<{
    classSubjects: ClassSubject[];
}>();
</script>
