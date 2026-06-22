<template>
    <div class="space-y-6">

        <!-- ── Header ── -->
        <PageHeader title="Tableau d'affichage" :subtitle="`${activeNotices.length} notification${activeNotices.length > 1 ? 's' : ''}`" color="indigo">
            <template #icon>
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </template>
        </PageHeader>

        <!-- ── Empty state ── -->
        <div v-if="!activeNotices.length" class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-gray-900 dark:text-white">Aucune notification</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Aucune notification ne vous est destinée pour le moment.</p>
        </div>

        <!-- ── Cards grid ── -->
        <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-5">
            <article
                v-for="notice in activeNotices"
                :key="notice.id"
                class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700
                       shadow-sm hover:shadow-md transition-shadow duration-200
                       flex flex-col overflow-hidden cursor-pointer"
                @click="openDetail(notice)"
            >
                <!-- Top accent bar -->
                <div class="h-1 bg-gradient-to-r from-teal-400 to-primary-500"/>

                <!-- Title + preview -->
                <div class="px-4 pt-4 pb-3 flex-1 flex flex-col gap-2">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white leading-snug line-clamp-2">
                        {{ notice.title }}
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-3 leading-relaxed flex-1">
                        {{ stripped(notice.message) }}
                    </p>
                </div>

                <!-- Footer : date d'effet -->
                <div class="px-4 py-3 border-t border-gray-50 dark:border-gray-700/50 flex items-center justify-between gap-2">
                    <div class="flex items-center gap-1.5 text-xs text-gray-400 dark:text-gray-500">
                        <svg class="w-3.5 h-3.5 text-primary-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>{{ formatDate(notice.notice_date) }}</span>
                    </div>
                    <button
                        class="p-1.5 rounded-xl text-gray-400 hover:text-primary-600 hover:bg-primary-50
                               dark:hover:bg-primary-900/20 transition-colors"
                        title="Lire"
                        @click.stop="openDetail(notice)"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </article>
        </div>

        <!-- ── Pagination ── -->
        <AppPagination :pagination="notices" />

        <!-- ── Modal lecture ── -->
        <AppModal v-model="showDetail" :title="detailTarget?.title ?? 'Notification'" size="lg">
            <div v-if="detailTarget" class="space-y-4">

                <!-- Date d'effet -->
                <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                    <svg class="w-3.5 h-3.5 text-primary-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>{{ formatDate(detailTarget.notice_date) }}</span>
                </div>

                <!-- Contenu -->
                <div
                    class="prose prose-sm dark:prose-invert max-w-none text-gray-700 dark:text-gray-300
                           bg-gray-50 dark:bg-gray-700/40 rounded-xl p-4 max-h-72 overflow-y-auto"
                    v-html="detailTarget.message"
                />
            </div>
            <template #footer>
                <AppButton variant="ghost" @click="showDetail = false">Fermer</AppButton>
            </template>
        </AppModal>

    </div>
</template>

<script setup lang="ts">
import { fmtDate } from '@/utils/dateFormat';
import { ref, computed } from 'vue';
import { PageHeader, AppButton, AppModal, AppPagination } from '@/Components/UI';
import { stripHtml } from '@/Utils/html';

interface Notice {
    id: number;
    title: string;
    message: string;
    notice_date: string;
    publish_date: string;
    is_active: boolean | number;
    created_by_name?: string;
    recipients?: string[];
}

const props = defineProps<{
    notices: {
        data: Notice[];
        total: number;
        from: number;
        to: number;
        links: { url: string | null; label: string; active: boolean }[];
    };
}>();

// N'afficher que les notifications actives
const activeNotices = computed(() =>
    props.notices.data.filter(n => n.is_active)
);

const showDetail   = ref(false);
const detailTarget = ref<Notice | null>(null);

const formatDate = fmtDate;

const stripped = (html: string) => stripHtml(html, 160);

const openDetail = (notice: Notice) => {
    detailTarget.value = notice;
    showDetail.value   = true;
};
</script>
