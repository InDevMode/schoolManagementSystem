<template>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Mes Notifications</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ notices.total }} notification(s)</p>
        </div>

        <div v-if="notices.data.length" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            <div
                v-for="notice in notices.data"
                :key="notice.id"
                class="card p-5 space-y-3 hover:shadow-md transition-shadow"
            >
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">{{ notice.title }}</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ notice.message }}</p>
                <div class="flex items-center gap-3 text-xs text-gray-400 dark:text-gray-500 pt-2 border-t border-gray-100 dark:border-gray-700">
                    <span>Publié: {{ formatDate(notice.publish_date) }}</span>
                    <span>Date: {{ formatDate(notice.notice_date) }}</span>
                </div>
            </div>
        </div>

        <div v-else class="card p-12 text-center">
            <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <p class="text-sm text-gray-500 dark:text-gray-400">Aucune notification disponible</p>
        </div>

        <!-- Pagination -->
        <div v-if="notices.links?.length > 3" class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400">
            <span>Affichage de <strong>{{ notices.from }}</strong> à <strong>{{ notices.to }}</strong> sur <strong>{{ notices.total }}</strong></span>
            <div class="flex items-center gap-1">
                <template v-for="link in notices.links" :key="link.label">
                    <component
                        :is="link.url ? 'a' : 'span'"
                        :href="link.url ?? undefined"
                        v-html="link.label"
                        :class="['px-3 py-1.5 rounded-lg text-xs font-medium transition-colors', link.active ? 'bg-primary-600 text-white' : link.url ? 'hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer' : 'opacity-40 cursor-not-allowed']"
                    />
                </template>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
interface Notice {
    id: number;
    title: string;
    message: string;
    notice_date: string;
    publish_date: string;
}

defineProps<{
    notices: {
        data: Notice[];
        total: number;
        from: number;
        to: number;
        links: { url: string | null; label: string; active: boolean }[];
    };
}>();

const formatDate = (d: string) =>
    d ? new Date(d).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' }) : '—';
</script>
