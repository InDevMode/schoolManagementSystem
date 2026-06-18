<template>
    <div class="space-y-6">
        <PageHeader title="Mes bulletins" subtitle="Vos résultats par période" color="emerald">
            <template #icon>
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </template>
        </PageHeader>

        <div v-if="bulletins.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            <div v-for="b in bulletins" :key="b.id"
                class="card p-5 hover:shadow-md transition-shadow flex flex-col gap-4">
                <!-- Période -->
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">{{ b.period_type === 'semestre' ? 'Semestre' : 'Trimestre' }}</p>
                        <p class="text-base font-bold text-gray-900 dark:text-white">{{ b.period_name }}</p>
                    </div>
                    <AppBadge :variant="Number(b.average) >= 10 ? 'success' : 'danger'" dot>
                        {{ Number(b.average) >= 10 ? 'Admis' : 'Non admis' }}
                    </AppBadge>
                </div>

                <!-- Moyenne + rang -->
                <div class="flex items-center gap-4">
                    <div class="flex-1 text-center p-4 rounded-lg"
                        :class="Number(b.average) >= 10 ? 'bg-success-50 dark:bg-success-900/20' : 'bg-danger-50 dark:bg-danger-900/20'">
                        <p class="text-xs text-gray-400 mb-1">Moyenne générale</p>
                        <p class="text-3xl font-black"
                            :class="Number(b.average) >= 10 ? 'text-success-600 dark:text-success-400' : 'text-danger-600 dark:text-danger-400'">
                            {{ b.average ? Number(b.average).toFixed(2) : '—' }}
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">/20</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-gray-400 mb-1">Rang</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-white">
                            {{ b.rank ? `${b.rank}ᵉ` : '—' }}
                        </p>
                        <p class="text-xs text-gray-400">sur {{ b.total_students }}</p>
                    </div>
                </div>

                <!-- Appréciation -->
                <p class="text-xs text-center italic text-gray-500 dark:text-gray-400">
                    "{{ b.appreciation ?? 'Aucune appréciation' }}"
                </p>

                <!-- Actions -->
                <div class="flex gap-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                    <Link :href="`/student/my_bulletins/${b.id}`"
                        class="flex-1 text-center text-xs font-medium py-2 rounded-lg bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400 hover:bg-primary-100 transition-colors">
                        Voir le détail
                    </Link>
                    <a :href="`/student/my_bulletins/${b.id}/print`" target="_blank"
                        class="p-2 rounded-lg text-gray-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors"
                        title="Télécharger / Imprimer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Aucun bulletin -->
        <div v-else class="card p-16 text-center">
            <div class="w-16 h-16 rounded-2xl bg-gray-50 dark:bg-gray-700 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Aucun bulletin disponible.</p>
            <p class="text-xs text-gray-400 mt-1">Vos bulletins seront publiés par votre administration après chaque période.</p>
        </div>
    </div>
</template>

<script setup lang="ts">
import { PageHeader, AppBadge } from '@/Components/UI';
import { Link } from '@inertiajs/vue3';

defineProps<{ bulletins: any[] }>();
</script>
