<template>
    <!-- Conteneur de la cloche -->
    <div ref="bellRef" class="relative">
        <button
            class="relative w-9 h-9 flex items-center justify-center rounded-full
                   bg-gray-100 dark:bg-gray-800
                   text-gray-500 dark:text-gray-400
                   hover:bg-primary-50 dark:hover:bg-primary-900/20
                   hover:text-primary-600 transition-colors"
            :class="{ 'ring-2 ring-primary-300 dark:ring-primary-600': hasNew }"
            @click="toggle"
            aria-label="Notifications"
        >
            <!-- Icône cloche avec animation si nouvelle notif -->
            <svg
                :class="['transition-transform duration-300', hasNew ? 'animate-bell' : '']"
                style="width:18px;height:18px"
                fill="none" stroke="currentColor" viewBox="0 0 24 24"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002
                         6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388
                         6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3
                         0 11-6 0v-1m6 0H9"/>
            </svg>

            <!-- Badge compteur -->
            <span
                v-if="unreadCount > 0"
                class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1
                       bg-red-500 text-white text-[10px] font-bold
                       rounded-full flex items-center justify-center leading-none
                       ring-2 ring-white dark:ring-gray-900 transition-all duration-300"
            >
                {{ unreadCount > 99 ? '99+' : unreadCount }}
            </span>
        </button>

        <!-- Dropdown -->
        <Transition
            enter-active-class="transition duration-150 ease-out"
            enter-from-class="opacity-0 translate-y-1 scale-95"
            enter-to-class="opacity-100 translate-y-0 scale-100"
            leave-active-class="transition duration-100 ease-in"
            leave-from-class="opacity-100 translate-y-0 scale-100"
            leave-to-class="opacity-0 translate-y-1 scale-95"
        >
            <div
                v-if="open"
                class="absolute right-0 top-full mt-2 w-[360px]
                       bg-white dark:bg-gray-800 rounded-2xl
                       border border-gray-100 dark:border-gray-700
                       shadow-[0_8px_30px_rgb(0,0,0,0.12)] overflow-hidden z-50"
            >
                <!-- En-tête -->
                <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700
                            flex items-center justify-between bg-gray-50 dark:bg-gray-800/80">
                    <div class="flex items-center gap-2">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Notifications</p>
                        <span v-if="unreadCount > 0"
                              class="px-2 py-0.5 text-[10px] font-bold rounded-full
                                     bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400">
                            {{ unreadCount }} non lu{{ unreadCount > 1 ? 'es' : 'e' }}
                        </span>
                    </div>
                    <div class="flex items-center gap-1">
                        <button
                            v-if="unreadCount > 0"
                            class="text-xs text-primary-600 dark:text-primary-400 hover:underline px-1.5 py-1 rounded transition-colors hover:bg-primary-50 dark:hover:bg-primary-900/20"
                            @click="markAllRead"
                            title="Tout marquer comme lu"
                        >
                            Tout lire
                        </button>
                        <button
                            v-if="notifications.length > 0"
                            class="text-xs text-gray-400 hover:text-red-500 px-1.5 py-1 rounded transition-colors hover:bg-red-50 dark:hover:bg-red-900/20"
                            @click="clearRead"
                            title="Supprimer les notifications lues"
                        >
                            Nettoyer
                        </button>
                    </div>
                </div>

                <!-- Liste -->
                <div class="max-h-80 overflow-y-auto">
                    <!-- Chargement -->
                    <div v-if="loading" class="flex items-center justify-center py-10">
                        <svg class="animate-spin w-5 h-5 text-primary-500" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                        </svg>
                    </div>

                    <!-- Vide -->
                    <div v-else-if="notifications.length === 0"
                         class="flex flex-col items-center justify-center py-10 gap-2 text-gray-400">
                        <svg class="w-10 h-10 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002
                                     6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388
                                     6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3
                                     0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <p class="text-sm">Aucune notification</p>
                    </div>

                    <!-- Items -->
                    <template v-else>
                        <div
                            v-for="notif in notifications"
                            :key="notif.id"
                            :class="[
                                'flex items-start gap-3 px-4 py-3 border-b border-gray-50 dark:border-gray-700/30 last:border-0 transition-colors group',
                                !notif.read_at
                                    ? 'bg-violet-50/50 dark:bg-violet-900/10 hover:bg-violet-50 dark:hover:bg-violet-900/20'
                                    : 'hover:bg-gray-50 dark:hover:bg-gray-700/40',
                            ]"
                        >
                            <!-- Icône colorée -->
                            <div :class="['w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5', iconBg(notif.color)]">
                                <component :is="iconComponent(notif.icon)" class="w-4 h-4" />
                            </div>

                            <!-- Texte — cliquable si url -->
                            <component
                                :is="notif.url ? 'a' : 'div'"
                                :href="notif.url ?? undefined"
                                class="flex-1 min-w-0 cursor-pointer"
                                @click="handleClick(notif)"
                            >
                                <div class="flex items-start justify-between gap-2">
                                    <p :class="['text-sm font-medium truncate', !notif.read_at ? 'text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-300']">
                                        {{ notif.title }}
                                    </p>
                                    <!-- Point violet si non lu -->
                                    <span v-if="!notif.read_at" class="w-2 h-2 rounded-full bg-violet-500 flex-shrink-0 mt-1.5" />
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-2 leading-relaxed">
                                    {{ notif.message }}
                                </p>
                                <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-1">
                                    {{ notif.created_at }}
                                </p>
                            </component>

                            <!-- Actions au survol -->
                            <div class="flex flex-col gap-1 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0">
                                <button
                                    v-if="!notif.read_at"
                                    class="w-6 h-6 rounded flex items-center justify-center text-gray-400 hover:text-green-500 hover:bg-green-50 dark:hover:bg-green-900/20 transition-colors"
                                    title="Marquer comme lu"
                                    @click.stop="markRead(notif.id)"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </button>
                                <button
                                    class="w-6 h-6 rounded flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                                    title="Supprimer"
                                    @click.stop="removeNotif(notif.id)"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </Transition>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, h, defineComponent } from 'vue';
import axios from 'axios';

// ── Types ────────────────────────────────────────────────────────────────────
interface AppNotification {
    id: string;
    type: string;
    icon: string;
    color: string;
    title: string;
    message: string;
    url: string | null;
    created_at: string;
    read_at: string | null;
}

// ── État ─────────────────────────────────────────────────────────────────────
const open         = ref(false);
const loading      = ref(false);
const hasNew       = ref(false);
const unreadCount  = ref(0);
const notifications = ref<AppNotification[]>([]);
const bellRef      = ref<HTMLElement | null>(null);

let pollInterval: ReturnType<typeof setInterval> | null = null;

// ── Icônes inline (SVG minimal, sans dépendance) ─────────────────────────────
const iconSvgs: Record<string, string> = {
    key:           'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z',
    'shield-check':'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
    'check-circle':'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
    'x-circle':    'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
    'academic-cap':'M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222',
    calendar:      'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
    star:          'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z',
    'book-open':   'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
    bell:          'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9',
};

const iconComponent = (iconName: string) => defineComponent({
    render() {
        const d = iconSvgs[iconName] ?? iconSvgs['bell'];
        return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', 'stroke-width': '2', 'stroke-linecap': 'round', 'stroke-linejoin': 'round' }, [
            h('path', { d }),
        ]);
    },
});

const colorMap: Record<string, string> = {
    amber:  'bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400',
    violet: 'bg-violet-100 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400',
    indigo: 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400',
    green:  'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400',
    red:    'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400',
    blue:   'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400',
    teal:   'bg-teal-100 dark:bg-teal-900/30 text-teal-600 dark:text-teal-400',
    orange: 'bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400',
    purple: 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400',
    gray:   'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400',
};

const iconBg = (color: string) => colorMap[color] ?? colorMap['gray'];

// ── Polling ──────────────────────────────────────────────────────────────────
const fetchNotifications = async (silent = false) => {
    if (!silent) loading.value = true;
    try {
        const { data } = await axios.get('/notifications/poll');
        const prevCount = unreadCount.value;
        unreadCount.value  = data.unread_count ?? 0;
        notifications.value = data.notifications ?? [];

        // Animation cloche si nouvelles notifs arrivées
        if (data.unread_count > prevCount) {
            hasNew.value = true;
            setTimeout(() => { hasNew.value = false; }, 3000);
        }
    } catch {
        // Silencieux — l'utilisateur n'est peut-être pas auth
    } finally {
        loading.value = false;
    }
};

// ── Actions ──────────────────────────────────────────────────────────────────
const toggle = () => {
    open.value = !open.value;
    if (open.value) fetchNotifications();
};

const markRead = async (id: string) => {
    try {
        await axios.post(`/notifications/${id}/read`);
        const n = notifications.value.find(n => n.id === id);
        if (n) n.read_at = new Date().toISOString();
        unreadCount.value = Math.max(0, unreadCount.value - 1);
    } catch {}
};

const markAllRead = async () => {
    try {
        await axios.post('/notifications/read-all');
        notifications.value.forEach(n => { n.read_at = new Date().toISOString(); });
        unreadCount.value = 0;
    } catch {}
};

const removeNotif = async (id: string) => {
    try {
        const notif = notifications.value.find(n => n.id === id);
        await axios.delete(`/notifications/${id}`);
        if (notif && !notif.read_at) {
            unreadCount.value = Math.max(0, unreadCount.value - 1);
        }
        notifications.value = notifications.value.filter(n => n.id !== id);
    } catch {}
};

const clearRead = async () => {
    try {
        await axios.delete('/notifications-clear-read');
        notifications.value = notifications.value.filter(n => !n.read_at);
    } catch {}
};

const handleClick = (notif: AppNotification) => {
    if (!notif.read_at) markRead(notif.id);
    if (notif.url) open.value = false;
};

// ── Click outside ─────────────────────────────────────────────────────────────
const onClickOutside = (e: MouseEvent) => {
    if (bellRef.value && !bellRef.value.contains(e.target as Node)) {
        open.value = false;
    }
};

// ── Lifecycle ────────────────────────────────────────────────────────────────
onMounted(() => {
    fetchNotifications();
    // Poll toutes les 30 secondes (comme le chat)
    pollInterval = setInterval(() => fetchNotifications(true), 30_000);
    document.addEventListener('mousedown', onClickOutside);
});

onUnmounted(() => {
    if (pollInterval) clearInterval(pollInterval);
    document.removeEventListener('mousedown', onClickOutside);
});
</script>

<style scoped>
@keyframes bell-ring {
    0%, 100% { transform: rotate(0deg); }
    15%       { transform: rotate(15deg); }
    30%       { transform: rotate(-12deg); }
    45%       { transform: rotate(10deg); }
    60%       { transform: rotate(-8deg); }
    75%       { transform: rotate(5deg); }
}

.animate-bell {
    animation: bell-ring 0.8s ease-in-out;
}
</style>
