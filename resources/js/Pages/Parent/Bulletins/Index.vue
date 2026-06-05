<template>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Bulletins de {{ student.last_name }} {{ student.name }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Classe : {{ student.class_name }}</p>
        </div>

        <div v-if="bulletins.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            <div v-for="b in bulletins" :key="b.id" class="card p-5 flex flex-col gap-4 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <p class="text-base font-bold text-gray-900 dark:text-white">{{ b.period_name }}</p>
                    <AppBadge :variant="Number(b.average) >= 10 ? 'success' : 'danger'" dot>
                        {{ Number(b.average) >= 10 ? 'Admis' : 'Non admis' }}
                    </AppBadge>
                </div>

                <div class="flex items-center gap-3">
                    <div class="flex-1 text-center p-3 rounded-xl"
                        :class="Number(b.average) >= 10 ? 'bg-success-50 dark:bg-success-900/20' : 'bg-danger-50 dark:bg-danger-900/20'">
                        <p class="text-2xl font-black" :class="Number(b.average) >= 10 ? 'text-success-600 dark:text-success-400' : 'text-danger-600 dark:text-danger-400'">
                            {{ b.average ? Number(b.average).toFixed(2) : '—' }}<span class="text-sm font-normal">/20</span>
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ b.appreciation }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xl font-bold text-gray-900 dark:text-white">{{ b.rank ? `${b.rank}ᵉ` : '—' }}</p>
                        <p class="text-xs text-gray-400">sur {{ b.total_students }}</p>
                    </div>
                </div>

                <div class="flex gap-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                    <a :href="`/parent/my_student/${student.id}/bulletins/${b.id}`"
                        class="flex-1 text-center text-xs font-medium py-2 rounded-lg bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400 hover:bg-primary-100 transition-colors">
                        Voir le détail
                    </a>
                    <a :href="`/parent/my_student/${student.id}/bulletins/${b.id}/print`" target="_blank"
                        class="p-2 rounded-lg text-gray-400 hover:text-primary-600 hover:bg-primary-50 transition-colors" title="Imprimer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <div v-else class="card p-12 text-center">
            <p class="text-sm text-gray-400">Aucun bulletin publié pour cet élève.</p>
        </div>
    </div>
</template>

<script setup lang="ts">
import { AppBadge } from '@/Components/UI';
defineProps<{ bulletins: any[]; student: any }>();
</script>
