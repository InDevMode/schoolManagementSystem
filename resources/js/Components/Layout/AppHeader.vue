<template>
    <div class="sticky top-0 z-50">

        <!-- Barre principale -->
        <header class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="w-full px-6">
                <div class="flex items-center h-16 gap-6">

                    <!-- Logo -->
                    <a :href="homeLink" class="flex items-center gap-3 flex-shrink-0">
                        <img :src="logoUrl" alt="Logo" class="h-8 w-auto object-contain" />
                        <span class="hidden xl:block text-sm font-bold text-gray-800 dark:text-white whitespace-nowrap">
                            {{ schoolName }}
                        </span>
                    </a>

                    <div class="h-6 w-px bg-gray-200 dark:bg-gray-700 flex-shrink-0" />

                    <!-- Navigation principale -->
                    <nav class="flex-1 flex items-center overflow-x-auto no-scrollbar">
                        <div class="flex items-center">
                            <template v-for="item in navItems" :key="item.id">

                                <!-- Lien simple (pas d'enfants) -->
                                <a
                                    v-if="!item.children"
                                    :href="item.href"
                                    :class="[
                                        'relative flex items-center gap-2 px-4 h-16 text-sm font-medium transition-colors whitespace-nowrap',
                                        isActive(item)
                                            ? 'text-primary-600 dark:text-primary-400'
                                            : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'
                                    ]"
                                    @click="activeParent = null"
                                >
                                    <NavIcon :name="item.icon" class="w-4 h-4 flex-shrink-0" />
                                    {{ item.label }}
                                    <span v-if="isActive(item)" class="absolute bottom-0 left-0 right-0 h-0.5 bg-primary-600 rounded-t-full" />
                                </a>

                                <!-- Menu parent (avec enfants) — clic pour afficher sous-barre -->
                                <button
                                    v-else
                                    :class="[
                                        'relative flex items-center gap-2 px-4 h-16 text-sm font-medium transition-colors whitespace-nowrap',
                                        isActive(item) || activeParent === item.id
                                            ? 'text-primary-600 dark:text-primary-400'
                                            : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'
                                    ]"
                                    @click="toggleParent(item.id)"
                                >
                                    <NavIcon :name="item.icon" class="w-4 h-4 flex-shrink-0" />
                                    {{ item.label }}
                                    <svg
                                        :class="['w-3.5 h-3.5 transition-transform duration-200', activeParent === item.id ? 'rotate-180' : '']"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                    </svg>
                                    <span v-if="isActive(item) || activeParent === item.id" class="absolute bottom-0 left-0 right-0 h-0.5 bg-primary-600 rounded-t-full" />
                                </button>

                            </template>
                        </div>
                    </nav>

                    <!-- Actions droite -->
                    <div class="flex items-center gap-1 flex-shrink-0">

                        <!-- Dark mode -->
                        <button class="p-2.5 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-700 transition-colors" @click="toggleDark">
                            <svg v-if="isDark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                        </button>

                        <!-- Notifications -->
                        <div ref="notifRef" class="relative">
                            <button
                                class="relative p-2.5 rounded-lg text-gray-500 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors"
                                @click="notifOpen = !notifOpen"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                <!-- Badge -->
                                <span v-if="notifications.length > 0" class="absolute top-1.5 right-1.5 w-2 h-2 bg-danger-500 rounded-full ring-2 ring-white dark:ring-gray-900" />
                            </button>

                            <!-- Dropdown notifications -->
                            <Transition enter-active-class="transition duration-150 ease-out" enter-from-class="opacity-0 translate-y-1" enter-to-class="opacity-100 translate-y-0" leave-active-class="transition duration-100 ease-in" leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 translate-y-1">
                                <div v-if="notifOpen" class="absolute right-0 top-full mt-1 w-80 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-xl overflow-hidden z-50">
                                    <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Notifications</p>
                                        <span class="text-xs text-gray-400">{{ notifications.length }}</span>
                                    </div>
                                    <div class="max-h-72 overflow-y-auto">
                                        <template v-if="notifications.length">
                                            <a
                                                v-for="notif in notifications"
                                                :key="notif.id"
                                                :href="notifLink"
                                                class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors border-b border-gray-50 dark:border-gray-700/50 last:border-0"
                                            >
                                                <div class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center flex-shrink-0 mt-0.5">
                                                    <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                                                    </svg>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ notif.title }}</p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-2">{{ stripHtml(notif.message) }}</p>
                                                </div>
                                            </a>
                                        </template>
                                        <p v-else class="px-4 py-6 text-sm text-center text-gray-400">Aucune notification</p>
                                    </div>
                                    <div class="px-4 py-2.5 border-t border-gray-100 dark:border-gray-700">
                                        <a :href="notifLink" class="text-xs text-primary-600 dark:text-primary-400 font-medium hover:underline">
                                            Voir toutes les notifications →
                                        </a>
                                    </div>
                                </div>
                            </Transition>
                        </div>

                        <!-- Chat / Messages -->
                        <div class="relative">
                            <a href="/chat" class="relative p-2.5 rounded-lg text-gray-500 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors block">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                <!-- Badge messages non lus -->
                                <span v-if="unreadMessages.length > 0" class="absolute top-1.5 right-1.5 min-w-[16px] h-4 px-1 bg-danger-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center ring-2 ring-white dark:ring-gray-900">
                                    {{ unreadMessages.length > 9 ? '9+' : unreadMessages.length }}
                                </span>
                            </a>
                        </div>

                        <div class="h-6 w-px bg-gray-200 dark:bg-gray-700 mx-1" />

                        <!-- Profil -->
                        <div ref="profileRef" class="relative">
                            <button
                                class="flex items-center gap-2.5 px-3 py-2 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                @click="profileOpen = !profileOpen"
                            >
                                <img :src="avatarUrl" :alt="user?.name" class="w-8 h-8 rounded-full object-cover ring-2 ring-primary-200 dark:ring-primary-700" />
                                <div class="hidden md:block text-left">
                                    <p class="text-sm font-semibold text-gray-800 dark:text-white leading-tight">{{ user?.last_name }} {{ user?.name }}</p>
                                    <p class="text-xs text-primary-600 dark:text-primary-400">{{ roleLabel }}</p>
                                </div>
                                <svg :class="['w-4 h-4 text-gray-400 transition-transform duration-200', profileOpen ? 'rotate-180' : '']" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Dropdown profil -->
                            <Transition
                                enter-active-class="transition duration-150 ease-out"
                                enter-from-class="opacity-0 translate-y-1"
                                enter-to-class="opacity-100 translate-y-0"
                                leave-active-class="transition duration-100 ease-in"
                                leave-from-class="opacity-100 translate-y-0"
                                leave-to-class="opacity-0 translate-y-1"
                            >
                                <div v-if="profileOpen" class="absolute right-0 top-full mt-1 w-72 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-xl overflow-hidden z-50">
                                    <div class="px-5 py-4 bg-gradient-to-br from-primary-50 to-secondary-50 dark:from-primary-900/20 dark:to-secondary-900/20 border-b border-gray-100 dark:border-gray-700">
                                        <div class="flex items-center gap-3">
                                            <img :src="avatarUrl" :alt="user?.name" class="w-12 h-12 rounded-full object-cover ring-2 ring-primary-300" />
                                            <div>
                                                <p class="font-bold text-gray-900 dark:text-white">{{ user?.last_name }} {{ user?.name }}</p>
                                                <p class="text-xs text-primary-600 dark:text-primary-400 font-medium mt-0.5">{{ roleLabel }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ user?.email }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="py-2">
                                        <a v-for="link in profileLinks" :key="link.href" :href="link.href"
                                            class="flex items-center gap-3 px-5 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-primary-600 dark:hover:text-primary-400 transition-colors"
                                        >
                                            <span class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                                                <NavIcon :name="link.icon" class="w-4 h-4 text-gray-500" />
                                            </span>
                                            {{ link.label }}
                                        </a>
                                    </div>
                                    <div class="border-t border-gray-100 dark:border-gray-700 py-2">
                                        <a href="/logout" class="flex items-center gap-3 px-5 py-3 text-sm text-danger-600 dark:text-danger-400 hover:bg-danger-50 dark:hover:bg-danger-900/20 transition-colors">
                                            <span class="w-8 h-8 rounded-lg bg-danger-50 dark:bg-danger-900/20 flex items-center justify-center flex-shrink-0">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                                </svg>
                                            </span>
                                            Déconnexion
                                        </a>
                                    </div>
                                </div>
                            </Transition>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Sous-barre de navigation — s'affiche quand un menu parent est actif ou sélectionné -->
        <Transition
            enter-active-class="transition duration-150 ease-out"
            enter-from-class="opacity-0 -translate-y-1"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition duration-100 ease-in"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 -translate-y-1"
        >
            <div v-if="visibleChildren.length" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 shadow-sm">
                <div class="w-full px-6">
                    <div class="flex items-center gap-1 overflow-x-auto no-scrollbar py-1.5">
                        <a
                            v-for="child in visibleChildren"
                            :key="child.id"
                            :href="child.href"
                            :class="[
                                'flex items-center gap-2 px-3.5 py-2 rounded-lg text-sm font-medium whitespace-nowrap transition-colors flex-shrink-0',
                                isActiveChild(child)
                                    ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300'
                                    : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white'
                            ]"
                        >
                            <span :class="['w-5 h-5 rounded-md flex items-center justify-center flex-shrink-0', isActiveChild(child) ? 'bg-primary-100 dark:bg-primary-900/40' : 'bg-gray-100 dark:bg-gray-700']">
                                <NavIcon :name="child.icon" class="w-3 h-3" />
                            </span>
                            {{ child.label }}
                        </a>
                    </div>
                </div>
            </div>
        </Transition>

    </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useNavigation } from '@/Composables/useNavigation';
import { useDark } from '@vueuse/core';
import NavIcon from '@/Components/Layout/NavIcon.vue';
import type { NavItem, PageProps } from '@/types';

const page = usePage<PageProps>();
const { navItems, currentMenu, currentSubItem } = useNavigation();

const isDark     = useDark();
const toggleDark = () => { isDark.value = !isDark.value; };

// Menu parent actuellement sélectionné manuellement (clic)
const activeParent = ref<string | null>(null);
const profileOpen  = ref(false);
const profileRef   = ref<HTMLElement | null>(null);
const notifOpen    = ref(false);
const notifRef     = ref<HTMLElement | null>(null);

// Notifications et messages depuis les props Inertia partagées
const notifications  = computed(() => (page.props.notifications  as any[]) ?? []);
const unreadMessages = computed(() => (page.props.unreadMessages as any[]) ?? []);

const notifLinksMap: Record<number, string> = {
    1: '/admin/communicate/noticeboard/list',
    2: '/teacher/my_noticeboard',
    3: '/student/my_noticeboard',
    4: '/parent/my_noticeboard',
};
const notifLink = computed(() => notifLinksMap[user.value?.user_type ?? 0] ?? '#');
const stripHtml = (html: string) => html?.replace(/<[^>]*>/g, '') ?? '';

// Fermer les dropdowns si clic en dehors
const handleClickOutside = (e: MouseEvent) => {
    if (profileRef.value && !profileRef.value.contains(e.target as Node)) {
        profileOpen.value = false;
    }
    if (notifRef.value && !notifRef.value.contains(e.target as Node)) {
        notifOpen.value = false;
    }
};

// Synchroniser activeParent avec l'URL au montage
onMounted(() => {
    document.addEventListener('mousedown', handleClickOutside);
    if (currentMenu.value?.id) {
        activeParent.value = currentMenu.value.id;
    }
});
onUnmounted(() => document.removeEventListener('mousedown', handleClickOutside));

// Resynchroniser quand l'URL change (navigation Inertia)
watch(currentMenu, (menu) => {
    if (menu?.id && !activeParent.value) {
        activeParent.value = menu.id;
    }
}, { immediate: true });

const user       = computed(() => page.props.auth?.user);
const schoolName = computed(() => page.props.settings?.school_name ?? 'School MS');
const logoUrl    = computed(() => page.props.settings?.logo_url    ?? '/upload/logo.png');
const avatarUrl  = computed(() => {
    const pic = user.value?.profile_picture;
    return pic ? `/upload/profile/${pic}` : '/upload/default.jpg';
});

const roleLabelMap: Record<number, string> = {
    1: 'Administrateur', 2: 'Professeur', 3: 'Apprenant', 4: 'Parent',
};
const roleLabel = computed(() => roleLabelMap[user.value?.user_type ?? 0] ?? 'Utilisateur');

const homeLinks: Record<number, string> = {
    1: '/admin/dashboard', 2: '/teacher/dashboard', 3: '/student/dashboard', 4: '/parent/dashboard',
};
const homeLink = computed(() => homeLinks[user.value?.user_type ?? 0] ?? '/');

const profileLinksMap: Record<number, { href: string; icon: string; label: string }[]> = {
    1: [
        { href: '/admin/account',         icon: 'user',     label: 'Mon profil' },
        { href: '/admin/change_password',  icon: 'lock',     label: 'Mot de passe' },
        { href: '/admin/settings',         icon: 'settings', label: 'Paramètres' },
    ],
    2: [
        { href: '/teacher/account',        icon: 'user', label: 'Mon profil' },
        { href: '/teacher/change_password',icon: 'lock', label: 'Mot de passe' },
    ],
    3: [
        { href: '/student/account',        icon: 'user', label: 'Mon profil' },
        { href: '/student/change_password',icon: 'lock', label: 'Mot de passe' },
    ],
    4: [
        { href: '/parent/account',         icon: 'user', label: 'Mon profil' },
        { href: '/parent/change_password', icon: 'lock', label: 'Mot de passe' },
    ],
};
const profileLinks = computed(() => profileLinksMap[user.value?.user_type ?? 0] ?? []);

const isActive      = (item: NavItem) => {
    // Si un menu parent est manuellement sélectionné, seul lui est actif
    if (activeParent.value) {
        return item.id === activeParent.value;
    }
    // Sinon, basé sur l'URL
    return currentMenu.value?.id === item.id;
};
const isActiveChild = (item: NavItem) => currentSubItem.value?.id === item.id;

// Basculer le menu parent sélectionné
const toggleParent = (id: string) => {
    // Si déjà actif, fermer
    if (activeParent.value === id) {
        activeParent.value = null;
        return;
    }
    activeParent.value = id;

    // Naviguer vers le premier enfant du menu
    const found = navItems.value.find(i => i.id === id);
    const firstChild = found?.children?.[0];
    if (firstChild?.href) {
        window.location.href = firstChild.href;
    }
};

// Les enfants à afficher dans la sous-barre
const visibleChildren = computed<NavItem[]>(() => {
    // Priorité : menu sélectionné manuellement
    if (activeParent.value) {
        const found = navItems.value.find(i => i.id === activeParent.value);
        if (found?.children?.length) return found.children;
    }
    // Fallback : menu actif selon l'URL
    return currentMenu.value?.children ?? [];
});
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
