<template>
    <div class="space-y-6">

        <!-- Header avec bouton retour -->
        <div class="flex items-center gap-4">
            <Link href="/parent/my_student"
                  class="p-2 rounded-lg border border-gray-200 dark:border-gray-700 text-gray-500
                         hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </Link>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Bulletins de {{ student.last_name }} {{ student.name }}
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    {{ student.class_name ?? 'Aucune classe' }}
                    <span v-if="bulletins.length" class="ml-1 text-gray-400">· {{ bulletins.length }} bulletin{{ bulletins.length > 1 ? 's' : '' }}</span>
                </p>
            </div>
        </div>

        <!-- Empty state -->
        <div v-if="!bulletins.length" class="card p-16 text-center">
            <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Aucun bulletin disponible</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Les bulletins seront disponibles après la clôture de chaque période.</p>
        </div>

        <!-- Bulletins grid -->
        <div v-else class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
            <div
                v-for="b in bulletins"
                :key="b.id"
                class="card overflow-hidden hover:shadow-md transition-all duration-200 flex flex-col"
            >
                <!-- Top bar couleur selon résultat -->
                <div :class="['h-1', Number(b.average) >= 10 ? 'bg-gradient-to-r from-emerald-400 to-green-500' : 'bg-gradient-to-r from-red-400 to-rose-500']"/>

                <!-- Card body -->
                <div class="p-5 flex flex-col gap-5 flex-1">

                    <!-- Période + badge -->
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <p class="text-base font-bold text-gray-900 dark:text-white">{{ b.period_name }}</p>
                            <p v-if="b.appreciation" class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 italic">{{ b.appreciation }}</p>
                        </div>
                        <span :class="[
                            'flex-shrink-0 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold',
                            Number(b.average) >= 10
                                ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-700/50'
                                : 'bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-700/50'
                        ]">
                            <span :class="['w-1.5 h-1.5 rounded-full', Number(b.average) >= 10 ? 'bg-emerald-500' : 'bg-red-500']"/>
                            {{ Number(b.average) >= 10 ? 'Admis' : 'Non admis' }}
                        </span>
                    </div>

                    <!-- Stats -->
                    <div class="grid grid-cols-2 gap-3">
                        <!-- Moyenne -->
                        <div :class="[
                            'rounded-xl p-4 text-center border',
                            Number(b.average) >= 10
                                ? 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-100 dark:border-emerald-700/40'
                                : 'bg-red-50 dark:bg-red-900/20 border-red-100 dark:border-red-700/40'
                        ]">
                            <p class="text-[10px] font-semibold uppercase tracking-wide mb-1"
                               :class="Number(b.average) >= 10 ? 'text-emerald-500' : 'text-red-400'">
                                Moyenne
                            </p>
                            <p class="text-2xl font-black leading-none"
                               :class="Number(b.average) >= 10 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400'">
                                {{ b.average ? Number(b.average).toFixed(2) : '—' }}
                            </p>
                            <p class="text-[10px] text-gray-400 mt-0.5">/20</p>
                        </div>

                        <!-- Rang -->
                        <div class="rounded-xl p-4 text-center border bg-gray-50 dark:bg-gray-800/60 border-gray-100 dark:border-gray-700">
                            <p class="text-[10px] font-semibold uppercase tracking-wide text-gray-400 mb-1">Rang</p>
                            <p class="text-2xl font-black text-gray-800 dark:text-white leading-none">
                                {{ b.rank ? `${b.rank}` : '—' }}<sup v-if="b.rank" class="text-sm font-semibold">e</sup>
                            </p>
                            <p class="text-[10px] text-gray-400 mt-0.5">sur {{ b.total_students ?? '?' }}</p>
                        </div>
                    </div>

                </div>

                <!-- Card footer actions -->
                <div class="px-5 pb-4 flex items-center gap-2">
                    <Link
                        :href="`/parent/my_student/${student.id}/bulletins/${b.id}`"
                        class="flex-1 flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl
                               bg-primary-600 hover:bg-primary-700 text-white
                               text-xs font-semibold transition-colors duration-150 shadow-sm shadow-primary-200 dark:shadow-primary-900/40"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Voir le détail
                    </Link>
                    <a
                        :href="`/parent/my_student/${student.id}/bulletins/${b.id}/print`"
                        target="_blank"
                        title="Imprimer ce bulletin"
                        class="p-2.5 rounded-xl text-gray-500 dark:text-gray-400
                               hover:text-primary-600 dark:hover:text-primary-400
                               hover:bg-primary-50 dark:hover:bg-primary-900/20
                               border border-gray-200 dark:border-gray-700
                               transition-colors duration-150"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

    </div>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
defineProps<{ bulletins: any[]; student: any }>();
</script>
