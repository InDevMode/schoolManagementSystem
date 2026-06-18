<template>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Mon bulletin</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ detail.bulletin?.period_name }}</p>
            </div>
            <a :href="`/student/my_bulletins/${detail.bulletin?.id}/print`" target="_blank">
                <AppButton variant="secondary">
                    <template #icon>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                    </template>
                    Télécharger / Imprimer
                </AppButton>
            </a>
        </div>

        <!-- Carte résumé -->
        <div class="card overflow-hidden">
            <!-- Bandeau drapeau -->
            <div class="h-1.5 bg-gradient-to-r from-green-600 via-yellow-400 to-red-500"/>

            <div class="p-6 flex flex-col sm:flex-row items-start sm:items-center gap-4">
                <!-- Photo -->
                <div class="w-16 h-20 rounded-lg border-2 border-gray-200 dark:border-gray-600 overflow-hidden bg-gray-50 dark:bg-gray-700 flex-shrink-0">
                    <img v-if="detail.bulletin?.profile_picture" :src="`/upload/profile/${detail.bulletin.profile_picture}`" class="w-full h-full object-cover"/>
                    <div v-else class="w-full h-full flex items-center justify-center bg-gray-100 dark:bg-gray-700">
                        <svg class="w-8 h-8 text-gray-300 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                </div>
                <div class="flex-1">
                    <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">{{ detail.bulletin?.period_type === 'semestre' ? 'BULLETIN SEMESTRIEL' : 'BULLETIN TRIMESTRIEL' }}</p>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                        {{ detail.bulletin?.student_last_name }} {{ detail.bulletin?.student_name }}
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Classe : {{ detail.bulletin?.class_name }}</p>
                </div>
                <!-- Moyenne -->
                <div class="text-center p-4 rounded-2xl bg-primary-600 text-white min-w-[100px]">
                    <p class="text-xs opacity-80 uppercase tracking-wider">Moyenne</p>
                    <p class="text-3xl font-black">{{ detail.bulletin?.average ? Number(detail.bulletin.average).toFixed(2) : '—' }}</p>
                    <p class="text-xs opacity-80">/20</p>
                </div>
            </div>

            <!-- Stats résumé -->
            <div class="grid grid-cols-3 border-t border-gray-100 dark:border-gray-700">
                <div class="p-4 text-center border-r border-gray-100 dark:border-gray-700">
                    <p class="text-xs text-gray-400">Rang</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">
                        {{ detail.bulletin?.rank ? `${detail.bulletin.rank}ᵉ/${detail.bulletin.total_students}` : '—' }}
                    </p>
                </div>
                <div class="p-4 text-center border-r border-gray-100 dark:border-gray-700">
                    <p class="text-xs text-gray-400">Taux de réussite</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ detail.bulletin?.class_success_rate ? detail.bulletin.class_success_rate + '%' : '—' }}</p>
                </div>
                <div class="p-4 text-center">
                    <p class="text-xs text-gray-400">Appréciation</p>
                    <p class="text-sm font-bold text-primary-600 dark:text-primary-400">{{ detail.bulletin?.appreciation ?? '—' }}</p>
                </div>
            </div>
        </div>

        <!-- Tableau des matières -->
        <div class="card overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h3 class="font-semibold text-gray-900 dark:text-white">Résultats par matière</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800/60">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Matière</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Notes</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Coef.</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Moyenne</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Appréciation</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                        <tr v-for="sub in detail.subjects" :key="sub.subject_id"
                            class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors">
                            <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white">{{ sub.subject_name }}</td>
                            <td class="px-4 py-3 text-center text-sm text-gray-500 dark:text-gray-400">
                                {{ sub.average ? Number(sub.average).toFixed(2) + ' / 20' : '—' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="font-bold text-primary-600 dark:text-primary-400">{{ sub.coefficient }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="font-bold text-sm" :class="avgClass(Number(sub.average))">
                                    {{ sub.average ? Number(sub.average).toFixed(2) : '—' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm italic text-gray-500 dark:text-gray-400">{{ sub.appreciation ?? '—' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Commentaire prof -->
        <div v-if="detail.bulletin?.teacher_comment" class="card p-5">
            <p class="text-xs font-semibold text-gray-400 uppercase mb-2">Appréciation du professeur principal</p>
            <p class="text-sm italic text-gray-700 dark:text-gray-300 border-l-4 border-primary-500 pl-4">
                {{ detail.bulletin.teacher_comment }}
            </p>
        </div>
    </div>
</template>

<script setup lang="ts">
import { AppButton } from '@/Components/UI';

defineProps<{
    detail:   { bulletin: any; subjects: any[] };
    settings?: any;
}>();

const avgClass = (avg: number) => {
    if (!avg) return 'text-gray-400';
    if (avg >= 14) return 'text-success-600 dark:text-success-400';
    if (avg >= 10) return 'text-warning-600 dark:text-warning-400';
    return 'text-danger-600 dark:text-danger-400';
};
</script>
