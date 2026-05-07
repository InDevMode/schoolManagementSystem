<template>
    <div class="w-full">
        <!-- Toolbar -->
        <div v-if="$slots.toolbar" class="mb-4">
            <slot name="toolbar" />
        </div>

        <!-- Table wrapper -->
        <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800/60">
                    <tr>
                        <th
                            v-for="col in columns"
                            :key="col.key"
                            :class="['px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider whitespace-nowrap', col.class]"
                        >
                            {{ col.label }}
                        </th>
                        <th v-if="$slots.actions" class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                    <template v-if="loading">
                        <tr v-for="i in 5" :key="i">
                            <td :colspan="columns.length + ($slots.actions ? 1 : 0)" class="px-4 py-3">
                                <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded animate-pulse" />
                            </td>
                        </tr>
                    </template>
                    <template v-else-if="!rows.length">
                        <tr>
                            <td :colspan="columns.length + ($slots.actions ? 1 : 0)" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center gap-2 text-gray-400 dark:text-gray-500">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                    <span class="text-sm font-medium">{{ emptyText }}</span>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <template v-else>
                        <tr
                            v-for="(row, index) in rows"
                            :key="rowKey ? row[rowKey] : index"
                            class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors duration-100"
                        >
                            <td
                                v-for="col in columns"
                                :key="col.key"
                                :class="['px-4 py-3 text-sm text-gray-700 dark:text-gray-300', col.cellClass]"
                            >
                                <slot :name="`cell-${col.key}`" :row="row" :value="row[col.key]">
                                    {{ row[col.key] ?? '—' }}
                                </slot>
                            </td>
                            <td v-if="$slots.actions" class="px-4 py-3 text-right">
                                <slot name="actions" :row="row" :index="index" />
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div v-if="pagination" class="mt-4 flex items-center justify-between text-sm text-gray-600 dark:text-gray-400">
            <span>
                Affichage de <strong>{{ pagination.from }}</strong> à <strong>{{ pagination.to }}</strong>
                sur <strong>{{ pagination.total }}</strong> résultats
            </span>
            <div class="flex items-center gap-1">
                <template v-for="link in pagination.links" :key="link.label">
                    <component
                        :is="link.url ? 'a' : 'span'"
                        :href="link.url ?? undefined"
                        v-html="link.label"
                        :class="[
                            'px-3 py-1.5 rounded-lg text-xs font-medium transition-colors',
                            link.active
                                ? 'bg-primary-600 text-white'
                                : link.url
                                    ? 'hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer'
                                    : 'opacity-40 cursor-not-allowed',
                        ]"
                    />
                </template>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import type { PaginatedData, PaginationLink } from '@/types';

interface Column {
    key: string;
    label: string;
    class?: string;
    cellClass?: string;
}

interface Props {
    columns: Column[];
    rows: Record<string, unknown>[];
    rowKey?: string;
    loading?: boolean;
    emptyText?: string;
    pagination?: {
        from: number;
        to: number;
        total: number;
        links: PaginationLink[];
    };
}

withDefaults(defineProps<Props>(), {
    loading: false,
    emptyText: 'Aucune donnée disponible',
});
</script>
