<template>
    <div v-if="pagination && pagination.last_page > 1"
         class="flex flex-col sm:flex-row items-center justify-between gap-3">
        <p class="text-xs text-gray-500 dark:text-gray-400">
            <template v-if="pagination.total > 0">
                {{ pagination.from }}–{{ pagination.to }} sur
                <span class="font-semibold text-gray-700 dark:text-gray-200">{{ pagination.total }}</span>
                résultat(s)
            </template>
            <template v-else>Aucun résultat</template>
        </p>
        <div class="flex items-center gap-1">
            <!-- Précédent -->
            <button :disabled="!pagination.prev_page_url"
                    @click="pagination.prev_page_url && navigate(pagination.prev_page_url)"
                    class="w-8 h-8 flex items-center justify-center rounded-xl text-sm transition-colors
                           disabled:opacity-30 disabled:cursor-not-allowed
                           text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>

            <!-- Numéros (on skip le premier = "Previous" et le dernier = "Next") -->
            <template v-for="link in pagination.links.slice(1, -1)" :key="link.label">
                <button @click="link.url && navigate(link.url)"
                        :class="['w-8 h-8 flex items-center justify-center rounded-xl text-sm font-medium transition-colors',
                            link.active
                                ? 'bg-primary-600 text-white shadow-sm'
                                : link.url
                                    ? 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'
                                    : 'text-gray-300 dark:text-gray-600 cursor-not-allowed']">
                    {{ link.label }}
                </button>
            </template>

            <!-- Suivant -->
            <button :disabled="!pagination.next_page_url"
                    @click="pagination.next_page_url && navigate(pagination.next_page_url)"
                    class="w-8 h-8 flex items-center justify-center rounded-xl text-sm transition-colors
                           disabled:opacity-30 disabled:cursor-not-allowed
                           text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>
    </div>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3';

const props = defineProps<{
    pagination: {
        total: number;
        from: number;
        to: number;
        last_page: number;
        prev_page_url: string | null;
        next_page_url: string | null;
        links: { url: string | null; label: string; active: boolean }[];
    } | null;
    preserveState?: boolean;
}>();

const navigate = (url: string) => {
    router.visit(url, { preserveState: props.preserveState ?? true });
};
</script>
