<script setup lang="ts">
defineProps<{ message?: string }>();
defineOptions({ layout: null }); // Pas de layout (pas de sidebar)
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 p-6">
        <div class="text-center max-w-md">
            <!-- Icône -->
            <div class="w-24 h-24 rounded-full bg-red-100 dark:bg-red-900/30
                        flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                </svg>
            </div>

            <h1 class="text-6xl font-black text-gray-900 dark:text-white mb-2">403</h1>
            <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-3">Accès refusé</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                Vous n'avez pas la permission d'accéder à cette page.
            </p>
            <p v-if="message" class="text-xs font-mono text-red-500 dark:text-red-400 bg-red-50 dark:bg-red-900/20
                                      px-3 py-2 rounded-xl mb-6">
                {{ message }}
            </p>

            <div class="flex items-center justify-center gap-3 mt-6">
                <a href="javascript:history.back()"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 dark:border-gray-600
                          text-sm font-medium text-gray-700 dark:text-gray-300
                          hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Retour
                </a>
                <a href="/logout"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl
                          bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Se déconnecter
                </a>
            </div>
        </div>
    </div>
</template>
