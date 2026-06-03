<template>
    <header class="sticky top-0 z-20 h-14 flex items-center px-5 gap-4
                   bg-white dark:bg-gray-900
                   border-b border-gray-100 dark:border-gray-800
                   shadow-sm">

        <!-- ── Hamburger mobile ── -->
        <button
            class="lg:hidden w-8 h-8 flex items-center justify-center rounded-lg
                   text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors flex-shrink-0"
            @click="$emit('openMobile')"
            aria-label="Menu"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        <!-- ── GAUCHE : Barre de recherche ── -->
        <div class="relative w-56 xl:w-72 flex-shrink-0">
            <input
                type="text"
                placeholder="Rechercher..."
                class="w-full pl-4 pr-10 py-2 text-sm rounded-full border-0
                       bg-gray-100 dark:bg-gray-800
                       text-gray-700 dark:text-gray-300
                       placeholder-gray-400 dark:placeholder-gray-500
                       focus:outline-none focus:ring-2 focus:ring-primary-500/30
                       transition-all duration-200"
            />
            <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>

        <!-- ── CENTRE : Liens de navigation rapide ── -->
        <nav class="flex-1 hidden md:flex items-center justify-center gap-6">
            <a v-for="link in quickLinks" :key="link.href" :href="link.href"
               :class="[
                   'flex items-center gap-1.5 text-sm font-medium transition-colors whitespace-nowrap',
                   link.active
                       ? 'text-gray-900 dark:text-white'
                       : 'text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200',
               ]"
            >
                <!-- Point coloré pour les liens "live" -->
                <span v-if="link.dot" class="w-1.5 h-1.5 rounded-full flex-shrink-0" :style="{ background: link.dot }"/>
                {{ link.label }}
            </a>
        </nav>

        <!-- ── DROITE : Icônes rondes avec badges + Avatar ── -->
        <div class="flex items-center gap-2 flex-shrink-0">

            <!-- Notifications -->
            <div ref="notifRef" class="relative">
                <button
                    class="relative w-9 h-9 flex items-center justify-center rounded-full
                           bg-gray-100 dark:bg-gray-800
                           text-gray-500 dark:text-gray-400
                           hover:bg-primary-50 dark:hover:bg-primary-900/20
                           hover:text-primary-600 transition-colors"
                    @click="notifOpen = !notifOpen"
                    aria-label="Notifications"
                >
                    <svg style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span v-if="notifCount > 0"
                          class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1
                                 bg-primary-600 text-white text-[10px] font-bold
                                 rounded-full flex items-center justify-center leading-none ring-2 ring-white dark:ring-gray-900">
                        {{ notifCount > 99 ? '99+' : notifCount }}
                    </span>
                </button>

                <!-- Dropdown notifications -->
                <Transition
                    enter-active-class="transition duration-150 ease-out"
                    enter-from-class="opacity-0 translate-y-1 scale-95"
                    enter-to-class="opacity-100 translate-y-0 scale-100"
                    leave-active-class="transition duration-100 ease-in"
                    leave-from-class="opacity-100 translate-y-0 scale-100"
                    leave-to-class="opacity-0 translate-y-1 scale-95"
                >
                    <div v-if="notifOpen"
                         class="absolute right-0 top-full mt-2 w-80
                                bg-white dark:bg-gray-800 rounded-2xl
                                border border-gray-100 dark:border-gray-700
                                shadow-card-lg overflow-hidden z-50">
                        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">Notifications</p>
                            <span class="badge bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300">
                                {{ notifCount }}
                            </span>
                        </div>
                        <div class="max-h-72 overflow-y-auto">
                            <template v-if="notifications.length">
                                <a v-for="notif in notifications" :key="notif.id" :href="notifLink"
                                   class="flex items-start gap-3 px-4 py-3
                                          hover:bg-gray-50 dark:hover:bg-gray-700/50
                                          transition-colors border-b border-gray-50 dark:border-gray-700/30 last:border-0">
                                    <div class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900/30
                                                flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ notif.title }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-2">{{ stripHtml(notif.message) }}</p>
                                    </div>
                                </a>
                            </template>
                            <p v-else class="px-4 py-8 text-sm text-center text-gray-400">Aucune notification</p>
                        </div>
                        <div class="px-4 py-2.5 border-t border-gray-100 dark:border-gray-700">
                            <a :href="notifLink" class="text-xs text-primary-600 dark:text-primary-400 font-medium hover:underline">
                                Voir toutes →
                            </a>
                        </div>
                    </div>
                </Transition>
            </div>

            <!-- Messages -->
            <a href="/chat"
               class="relative w-9 h-9 flex items-center justify-center rounded-full
                      bg-gray-100 dark:bg-gray-800
                      text-gray-500 dark:text-gray-400
                      hover:bg-blue-50 dark:hover:bg-blue-900/20
                      hover:text-blue-600 transition-colors"
               aria-label="Messages"
            >
                <svg style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <span v-if="msgCount > 0"
                      class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1
                             bg-blue-500 text-white text-[10px] font-bold
                             rounded-full flex items-center justify-center leading-none ring-2 ring-white dark:ring-gray-900">
                    {{ msgCount > 99 ? '99+' : msgCount }}
                </span>
            </a>

            <!-- Paramètres — icône neutre, non active -->
            <span
               class="w-9 h-9 flex items-center justify-center rounded-full
                      bg-gray-100 dark:bg-gray-800
                      text-gray-300 dark:text-gray-600
                      opacity-50 cursor-not-allowed"
               aria-label="Paramètres"
            >
                <svg style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </span>

            <!-- Séparateur -->
            <div class="w-px h-6 bg-gray-200 dark:bg-gray-700 mx-1"/>

            <!-- Avatar + nom + rôle -->
            <div ref="profileRef" class="relative">
                <button
                    class="flex items-center gap-2 px-2 py-1 rounded-xl
                           hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                    @click="profileOpen = !profileOpen"
                >
                    <img :src="avatarUrl" :alt="user?.name"
                         class="w-8 h-8 rounded-full object-cover ring-2 ring-primary-200 dark:ring-primary-700 flex-shrink-0"/>
                    <div class="hidden sm:block text-left">
                        <p class="text-sm font-semibold text-gray-800 dark:text-white leading-tight whitespace-nowrap">
                            {{ user?.last_name }} {{ user?.name }}
                        </p>
                        <p class="text-xs text-primary-600 dark:text-primary-400 leading-tight">{{ roleLabel }}</p>
                    </div>
                    <svg :class="['w-3.5 h-3.5 text-gray-400 transition-transform duration-200 flex-shrink-0', profileOpen ? 'rotate-180' : '']"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <!-- Dropdown profil -->
                <Transition
                    enter-active-class="transition duration-150 ease-out"
                    enter-from-class="opacity-0 translate-y-1 scale-95"
                    enter-to-class="opacity-100 translate-y-0 scale-100"
                    leave-active-class="transition duration-100 ease-in"
                    leave-from-class="opacity-100 translate-y-0 scale-100"
                    leave-to-class="opacity-0 translate-y-1 scale-95"
                >
                    <div v-if="profileOpen"
                         class="absolute right-0 top-full mt-2 w-72
                                bg-white dark:bg-gray-800 rounded-2xl
                                border border-gray-100 dark:border-gray-700
                                shadow-card-lg overflow-hidden z-50">
                        <div class="px-5 py-4 bg-gradient-to-br from-primary-50 to-secondary-50
                                    dark:from-primary-900/20 dark:to-secondary-900/20
                                    border-b border-gray-100 dark:border-gray-700">
                            <div class="flex items-center gap-3">
                                <img :src="avatarUrl" :alt="user?.name"
                                     class="w-12 h-12 rounded-full object-cover ring-2 ring-primary-300"/>
                                <div>
                                    <p class="font-bold text-gray-900 dark:text-white">{{ user?.last_name }} {{ user?.name }}</p>
                                    <p class="text-xs text-primary-600 dark:text-primary-400 font-medium mt-0.5">{{ roleLabel }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ user?.email }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="py-2">
                            <a v-for="link in profileLinks" :key="link.href" :href="link.href"
                               class="flex items-center gap-3 px-5 py-3 text-sm
                                      text-gray-700 dark:text-gray-300
                                      hover:bg-gray-50 dark:hover:bg-gray-700
                                      hover:text-primary-600 dark:hover:text-primary-400
                                      transition-colors">
                                <span class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                                    <NavIcon :name="link.icon" class="w-4 h-4 text-gray-500"/>
                                </span>
                                {{ link.label }}
                            </a>
                        </div>
                        <div class="border-t border-gray-100 dark:border-gray-700 py-2">
                            <a href="/logout"
                               class="flex items-center gap-3 px-5 py-3 text-sm
                                      text-danger-600 dark:text-danger-400
                                      hover:bg-danger-50 dark:hover:bg-danger-900/20 transition-colors">
                                <span class="w-8 h-8 rounded-lg bg-danger-50 dark:bg-danger-900/20 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                </span>
                                Déconnexion
                            </a>
                        </div>
                    </div>
                </Transition>
            </div>
        </div>

    </header>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useNavigation } from '@/Composables/useNavigation';
import NavIcon from '@/Components/Layout/NavIcon.vue';
import type { PageProps } from '@/types';

defineEmits<{ openMobile: [] }>();

const page = usePage<PageProps>();
const { currentMenu, currentSubItem, user } = useNavigation();

// ── Utilisateur ──────────────────────────────────────────────────────────────
const avatarUrl = computed(() => {
    const pic = user.value?.profile_picture;
    return pic ? `/upload/profile/${pic}` : '/upload/default.jpg';
});

const roleLabelMap: Record<number, string> = {
    0: 'Super Admin',
    1: 'Administrateur',
    2: 'Professeur',
    3: 'Apprenant',
    4: 'Parent',
};
const roleLabel = computed(() => {
    const ut = user.value?.user_type ?? -1;
    if (roleLabelMap[ut]) return roleLabelMap[ut];
    return user.value?.role_label ?? user.value?.roles?.[0] ?? 'Utilisateur';
});

const settingsLinksMap: Record<number, string> = {
    0: '/superadmin/config/settings',
    1: '/admin/settings',
};
const settingsLink = computed(() => settingsLinksMap[user.value?.user_type ?? 1] ?? '/admin/settings');

const profileLinksMap: Record<number, { href: string; icon: string; label: string }[]> = {
    0: [
        { href: '/superadmin/account',          icon: 'user',         label: 'Mon profil' },
        { href: '/superadmin/change_password',  icon: 'lock',         label: 'Mot de passe' },
        { href: '/superadmin/config/settings',  icon: 'cog-6-tooth',  label: 'Paramètres' },
        { href: '/superadmin/config/roles',     icon: 'shield-check', label: 'Rôles' },
        { href: '/superadmin/config/assign',    icon: 'key',          label: 'Permissions' },
    ],
    1: [
        { href: '/admin/account',          icon: 'user',        label: 'Mon profil' },
        { href: '/admin/change_password',  icon: 'lock',        label: 'Mot de passe' },
        { href: '/admin/settings',         icon: 'cog-6-tooth', label: 'Paramètres' },
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
const profileLinks = computed(() => {
    const ut = user.value?.user_type ?? 1;
    // Rôles custom (≥5) : mêmes liens que admin
    return profileLinksMap[ut] ?? profileLinksMap[1] ?? [];
});

// ── Liens de navigation rapide (centre du header) ────────────────────────────
const quickLinksMap: Record<number, { label: string; href: string; dot?: string; active?: boolean }[]> = {
    0: [
        { label: 'Tableau de bord',   href: '/superadmin/dashboard' },
        { label: 'Rôles',             href: '/superadmin/config/roles', dot: '#7B74F0' },
        { label: 'Permissions',       href: '/superadmin/config/permissions' },
        { label: 'Attribution',       href: '/superadmin/config/assign' },
    ],
    1: [
        { label: 'Tableau de bord', href: '/admin/dashboard' },
        { label: 'Apprenants',      href: '/admin/student/list', dot: '#7B74F0' },
        { label: 'Professeurs',     href: '/admin/teacher/list' },
        { label: 'Paramètres',      href: '/admin/settings' },
    ],
    2: [
        { label: 'Tableau de bord', href: '/teacher/dashboard' },
        { label: 'Mes Apprenants',  href: '/teacher/my_student', dot: '#7B74F0' },
        { label: 'Calendrier',      href: '/teacher/my_calendar' },
    ],
    3: [
        { label: 'Tableau de bord', href: '/student/dashboard' },
        { label: 'Mes Matières',    href: '/student/my_subject', dot: '#7B74F0' },
        { label: 'Calendrier',      href: '/student/my_timetable' },
    ],
    4: [
        { label: 'Tableau de bord', href: '/parent/dashboard' },
        { label: 'Mes Enfants',     href: '/parent/my_student', dot: '#7B74F0' },
    ],
};

const quickLinks = computed(() => {
    const links = quickLinksMap[user.value?.user_type ?? 0] ?? [];
    const path  = window.location.pathname;
    return links.map(l => ({ ...l, active: path.startsWith(l.href) }));
});

// ── Notifications / Messages ─────────────────────────────────────────────────
const notifications  = computed(() => (page.props.notifications  as any[]) ?? []);
const unreadMessages = computed(() => (page.props.unreadMessages as any[]) ?? []);
const notifCount     = computed(() => notifications.value.length);
const msgCount       = computed(() => unreadMessages.value.length);

const notifLinksMap: Record<number, string> = {
    1: '/admin/communicate/noticeboard/list',
    2: '/teacher/my_noticeboard',
    3: '/student/my_noticeboard',
    4: '/parent/my_noticeboard',
};
const notifLink = computed(() => notifLinksMap[user.value?.user_type ?? 0] ?? '#');
const stripHtml = (html: string) => html?.replace(/<[^>]*>/g, '') ?? '';

// ── Dropdowns ────────────────────────────────────────────────────────────────
const notifOpen   = ref(false);
const profileOpen = ref(false);
const notifRef    = ref<HTMLElement | null>(null);
const profileRef  = ref<HTMLElement | null>(null);

const handleClickOutside = (e: MouseEvent) => {
    if (notifRef.value   && !notifRef.value.contains(e.target as Node))   notifOpen.value   = false;
    if (profileRef.value && !profileRef.value.contains(e.target as Node)) profileOpen.value = false;
};

onMounted(()  => document.addEventListener('mousedown', handleClickOutside));
onUnmounted(() => document.removeEventListener('mousedown', handleClickOutside));
</script>
