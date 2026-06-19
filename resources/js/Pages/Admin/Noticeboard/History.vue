<template>
    <div class="space-y-6">

        <!-- ── Header ───────────────────────────────────────────────────────── -->
        <PageHeader title="Historique des suppressions" :subtitle="`${deleted.total} notification${deleted.total > 1 ? 's' : ''} supprimée${deleted.total > 1 ? 's' : ''}`" color="red">
            <template #icon>
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </template>
            <template #actions>
                <Link
                    href="/admin/communicate/noticeboard/list"
                    class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700
                           text-sm font-medium text-gray-500 hover:text-primary-600 hover:border-primary-400
                           dark:text-gray-400 dark:hover:text-primary-400 transition-colors"
                    title="Retour au tableau d'affichage"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Retour
                </Link>
            </template>
        </PageHeader>

        <!-- ── Empty state ──────────────────────────────────────────────────── -->
        <div v-if="!deleted.data.length" class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-gray-900 dark:text-white">Aucune suppression</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">L'historique des notifications supprimées apparaîtra ici.</p>
        </div>

        <!-- ── Cards grid ────────────────────────────────────────────────────── -->
        <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-5">
            <article
                v-for="notice in deleted.data"
                :key="notice.id"
                class="bg-white dark:bg-gray-800 rounded-2xl border border-dashed border-danger-200 dark:border-danger-800/50
                       shadow-sm flex flex-col overflow-hidden opacity-80 hover:opacity-100 transition-opacity duration-200"
            >
                <!-- ── Top : badge supprimé ── -->
                <div class="flex items-center justify-between px-4 pt-4 pb-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold
                                 bg-danger-100 dark:bg-danger-900/30 text-danger-700 dark:text-danger-400">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Supprimée
                    </span>
                    <span v-if="notice.deleted_at" class="text-xs text-gray-400 dark:text-gray-500">
                        {{ formatDate(notice.deleted_at) }}
                    </span>
                </div>

                <!-- ── Titre + message ── -->
                <div class="px-4 pb-3 flex-1 flex flex-col gap-1.5">
                    <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 leading-snug line-clamp-2">
                        {{ notice.title }}
                    </h3>
                    <p class="text-xs text-gray-400 dark:text-gray-500 line-clamp-3 leading-relaxed flex-1">
                        {{ stripped(notice.message) }}
                    </p>
                </div>

                <!-- ── Dates originales ── -->
                <div class="px-4 pt-2 pb-3 space-y-1.5 border-t border-gray-50 dark:border-gray-700/50">
                    <div class="flex items-center gap-1.5 text-xs text-gray-400 dark:text-gray-500">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="font-medium">Publication :</span>
                        <span>{{ formatDate(notice.publish_date) }}</span>
                    </div>
                    <div class="flex items-center gap-1.5 text-xs text-gray-400 dark:text-gray-500">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span class="font-medium">Notice :</span>
                        <span>{{ formatDate(notice.notice_date) }}</span>
                    </div>
                </div>

                <!-- ── Footer : créateur + bouton restaurer ── -->
                <div class="px-4 py-3 bg-gray-50 dark:bg-gray-800/60 border-t border-gray-100 dark:border-gray-700
                            flex items-center justify-between gap-2">
                    <span class="text-xs text-gray-400 dark:text-gray-500 truncate flex-1">
                        Par <strong class="text-gray-500 dark:text-gray-400 font-medium">{{ notice.created_by_name }}</strong>
                    </span>
                    <button
                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-semibold
                               bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400
                               border border-green-200 dark:border-green-800
                               hover:bg-green-100 dark:hover:bg-green-900/40 transition-colors"
                        :disabled="restoring === notice.id"
                        @click="restoreNotice(notice)"
                        title="Restaurer cette notification"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Restaurer
                    </button>
                </div>
            </article>
        </div>

        <!-- ── Pagination ────────────────────────────────────────────────────── -->
        <AppPagination :pagination="deleted" />

    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import { PageHeader, AppPagination } from '@/Components/UI';
import { stripHtml } from '@/Utils/html';

interface DeletedNotice {
    id: number;
    title: string;
    message: string;
    notice_date: string;
    publish_date: string;
    deleted_at?: string;
    created_by_name?: string;
}

defineProps<{
    deleted: {
        data: DeletedNotice[];
        total: number;
        from: number;
        to: number;
        last_page: number;
        prev_page_url: string | null;
        next_page_url: string | null;
        links: { url: string | null; label: string; active: boolean }[];
    };
}>();

const restoring = ref<number | null>(null);

const formatDate = (d?: string) =>
    d ? new Date(d).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' }) : '—';

const stripped = (html: string) => stripHtml(html, 140);

const restoreNotice = (notice: DeletedNotice) => {
    restoring.value = notice.id;
    router.post(`/admin/communicate/noticeboard/restore/${notice.id}`, {}, {
        onFinish: () => { restoring.value = null; },
    });
};
</script>
