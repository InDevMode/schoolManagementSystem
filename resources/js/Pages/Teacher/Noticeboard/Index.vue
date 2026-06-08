<template>
    <div class="space-y-6">

        <!-- ── Header ────────────────────────────────────────────────────────── -->
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tableau d'affichage</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                {{ notices.total }} notification{{ notices.total > 1 ? 's' : '' }}
            </p>
        </div>

        <!-- ── Empty state ───────────────────────────────────────────────────── -->
        <div v-if="!notices.data.length" class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-gray-900 dark:text-white">Aucune notification</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Aucune notification ne vous est destinée pour le moment.</p>
        </div>

        <!-- ── Cards grid ─────────────────────────────────────────────────────── -->
        <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-5">
            <article
                v-for="notice in notices.data"
                :key="notice.id"
                class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700
                       shadow-sm hover:shadow-md transition-shadow duration-200
                       flex flex-col overflow-hidden"
            >
                <!-- ── Card top row : badges destinataires ── -->
                <div class="flex items-center justify-between px-4 pt-4 pb-2">
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span
                            v-for="r in notice.recipients"
                            :key="r"
                            :class="['inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold', recipientBadge(r).class]"
                        >
                            {{ recipientBadge(r).label }}
                        </span>
                        <span v-if="!notice.recipients?.length"
                              class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                            Tous
                        </span>
                    </div>
                    <!-- Badge statut actif (lecture seule) -->
                    <span :class="['text-xs font-medium', notice.is_active ? 'text-green-600 dark:text-green-400' : 'text-gray-400 dark:text-gray-500']">
                        {{ notice.is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>

                <!-- ── Titre + message ── -->
                <div class="px-4 pb-3 flex-1 flex flex-col gap-1.5">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white leading-snug line-clamp-2">
                        {{ notice.title }}
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-3 leading-relaxed flex-1">
                        {{ stripped(notice.message) }}
                    </p>
                    <button
                        v-if="stripped(notice.message).length > 100"
                        class="text-xs text-primary-600 dark:text-primary-400 font-medium hover:underline text-left mt-0.5"
                        @click="openDetail(notice)"
                    >
                        Lire plus
                    </button>
                </div>

                <!-- ── Dates ── -->
                <div class="px-4 pt-2 pb-3 space-y-1.5 border-t border-gray-50 dark:border-gray-700/50">
                    <div class="flex items-center gap-1.5 text-xs text-gray-400 dark:text-gray-500">
                        <svg class="w-3.5 h-3.5 text-primary-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-gray-500 dark:text-gray-400 font-medium">Publication :</span>
                        <span>{{ formatDate(notice.publish_date) }}</span>
                    </div>
                    <div class="flex items-center gap-1.5 text-xs text-gray-400 dark:text-gray-500">
                        <svg class="w-3.5 h-3.5 text-secondary-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span class="text-gray-500 dark:text-gray-400 font-medium">Notice :</span>
                        <span>{{ formatDate(notice.notice_date) }}</span>
                    </div>
                </div>

                <!-- ── Footer : créateur + bouton détails ── -->
                <div class="px-4 py-3 bg-gray-50 dark:bg-gray-800/60 border-t border-gray-100 dark:border-gray-700
                            flex items-center justify-between gap-2">
                    <span class="text-xs text-gray-400 dark:text-gray-500 truncate flex-1">
                        Par <strong class="text-gray-600 dark:text-gray-300 font-medium">{{ notice.created_by_name }}</strong>
                    </span>
                    <button
                        class="p-1.5 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors"
                        title="Voir les détails"
                        @click="openDetail(notice)"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
            </article>
        </div>

        <!-- ── Pagination ─────────────────────────────────────────────────────── -->
        <div v-if="notices.links?.length > 3"
             class="flex flex-col sm:flex-row items-center justify-between gap-3 text-sm text-gray-600 dark:text-gray-400">
            <span class="text-xs">
                Affichage de <strong class="text-gray-900 dark:text-white">{{ notices.from }}</strong>
                à <strong class="text-gray-900 dark:text-white">{{ notices.to }}</strong>
                sur <strong class="text-gray-900 dark:text-white">{{ notices.total }}</strong>
            </span>
            <div class="flex items-center gap-1">
                <template v-for="link in notices.links" :key="link.label">
                    <component
                        :is="link.url ? 'a' : 'span'"
                        :href="link.url ?? undefined"
                        :class="[
                            'px-3 py-1.5 rounded-lg text-xs font-medium transition-colors',
                            link.active
                                ? 'bg-primary-600 text-white shadow-sm'
                                : link.url
                                    ? 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer'
                                    : 'opacity-40 cursor-not-allowed text-gray-400',
                        ]"
                        v-html="link.label"
                    />
                </template>
            </div>
        </div>

        <!-- ── Modal Détails ──────────────────────────────────────────────────── -->
        <AppModal v-model="showDetail" title="Détails de la notification" size="lg">
            <div v-if="detailTarget" class="space-y-4">
                <!-- Badges destinataires + statut -->
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span
                            v-for="r in detailTarget.recipients"
                            :key="r"
                            :class="['inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold', recipientBadge(r).class]"
                        >
                            {{ recipientBadge(r).label }}
                        </span>
                    </div>
                    <span :class="[
                        'inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold',
                        detailTarget.is_active
                            ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300'
                            : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400',
                    ]">
                        <span :class="['w-1.5 h-1.5 rounded-full', detailTarget.is_active ? 'bg-green-500' : 'bg-gray-400']"/>
                        {{ detailTarget.is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>

                <!-- Titre -->
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">{{ detailTarget.title }}</h2>

                <!-- Dates -->
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-primary-50 dark:bg-primary-900/20 rounded-xl px-3 py-2.5">
                        <p class="text-xs text-primary-600 dark:text-primary-400 font-medium uppercase tracking-wide">Publication</p>
                        <p class="text-sm font-semibold text-gray-800 dark:text-white mt-0.5">{{ formatDate(detailTarget.publish_date) }}</p>
                    </div>
                    <div class="bg-secondary-50 dark:bg-secondary-900/20 rounded-xl px-3 py-2.5">
                        <p class="text-xs text-secondary-600 dark:text-secondary-400 font-medium uppercase tracking-wide">Envoi notification</p>
                        <p class="text-sm font-semibold text-gray-800 dark:text-white mt-0.5">{{ formatDate(detailTarget.notice_date) }}</p>
                    </div>
                </div>

                <!-- Message HTML -->
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium uppercase tracking-wide mb-2">Message</p>
                    <div
                        class="prose prose-sm dark:prose-invert max-w-none text-gray-700 dark:text-gray-300
                               bg-gray-50 dark:bg-gray-700/40 rounded-xl p-4 max-h-64 overflow-y-auto"
                        v-html="detailTarget.message"
                    />
                </div>

                <!-- Créé par -->
                <p class="text-xs text-gray-400 dark:text-gray-500">
                    Créé par <strong class="text-gray-600 dark:text-gray-300">{{ detailTarget.created_by_name }}</strong>
                </p>
            </div>
            <template #footer>
                <AppButton variant="ghost" @click="showDetail = false">Fermer</AppButton>
            </template>
        </AppModal>

    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { AppButton, AppModal } from '@/Components/UI';
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

defineProps<{
    notices: {
        data: Notice[];
        total: number;
        from: number;
        to: number;
        links: { url: string | null; label: string; active: boolean }[];
    };
}>();

const showDetail   = ref(false);
const detailTarget = ref<Notice | null>(null);

const badgeStyles: Record<string, { class: string; label: string }> = {
    '2': { class: 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300',  label: 'Professeurs' },
    '3': { class: 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300', label: 'Apprenants'  },
    '4': { class: 'bg-teal-100  dark:bg-teal-900/30   text-teal-700   dark:text-teal-300',    label: 'Parents'     },
};
const recipientBadge = (r: string) =>
    badgeStyles[r] ?? { class: 'bg-gray-100 dark:bg-gray-700 text-gray-500', label: `Groupe ${r}` };

const formatDate = (d: string) =>
    d ? new Date(d).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' }) : '—';

const stripped = (html: string) => stripHtml(html, 160);

const openDetail = (notice: Notice) => {
    detailTarget.value = notice;
    showDetail.value   = true;
};
</script>
