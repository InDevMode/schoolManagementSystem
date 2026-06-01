<template>
    <!-- Overlay mobile -->
    <Transition
        enter-active-class="transition-opacity duration-300"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-opacity duration-300"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="mobileOpen"
            class="fixed inset-0 z-30 bg-black/50 backdrop-blur-sm lg:hidden"
            @click="$emit('close')"
        />
    </Transition>

    <!-- Sidebar -->
    <aside
        :class="[
            'fixed top-0 left-0 h-full z-40 flex flex-col transition-all duration-300 ease-in-out select-none',
            // Largeur selon état collapsed
            collapsed ? 'w-[72px]' : 'w-64',
            // Mobile : slide depuis la gauche
            mobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
            // Couleurs
            'bg-white dark:bg-gray-900 border-r border-gray-100 dark:border-gray-800',
        ]"
    >
        <!-- ── En-tête sidebar ── -->
        <div class="flex items-center h-16 px-4 flex-shrink-0 border-b border-gray-100 dark:border-gray-800">
            <!-- Logo + nom -->
            <a :href="homeLink" class="flex items-center gap-3 flex-1 min-w-0">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 shadow-md"
                     style="background: linear-gradient(135deg, #8b5cf6, #6d28d9);">
                    <img v-if="logoUrl" :src="logoUrl" alt="Logo" class="w-6 h-6 object-contain rounded" />
                    <svg v-else class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 14l9-5-9-5-9 5 9 5z"/>
                    </svg>
                </div>
                <Transition
                    enter-active-class="transition-all duration-200"
                    enter-from-class="opacity-0 -translate-x-2"
                    enter-to-class="opacity-100 translate-x-0"
                    leave-active-class="transition-all duration-150"
                    leave-from-class="opacity-100"
                    leave-to-class="opacity-0"
                >
                    <span v-if="!collapsed" class="text-sm font-bold text-gray-800 dark:text-white truncate">
                        {{ schoolName }}
                    </span>
                </Transition>
            </a>

            <!-- Bouton collapse (desktop) -->
            <button
                class="hidden lg:flex w-7 h-7 rounded-full items-center justify-center flex-shrink-0
                       bg-primary-600 text-white shadow-md hover:bg-primary-700 transition-colors"
                @click="$emit('toggle')"
                :aria-label="collapsed ? 'Développer le menu' : 'Réduire le menu'"
            >
                <svg :class="['w-3.5 h-3.5 transition-transform duration-300', collapsed ? 'rotate-180' : '']"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
        </div>

        <!-- ── Recherche ── -->
        <div v-if="!collapsed" class="px-3 pt-4 pb-2 flex-shrink-0">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Rechercher..."
                    class="w-full pl-9 pr-3 py-2 text-sm rounded-xl border-0
                           bg-gray-100 dark:bg-gray-800
                           text-gray-700 dark:text-gray-300
                           placeholder-gray-400 dark:placeholder-gray-500
                           focus:outline-none focus:ring-2 focus:ring-primary-500/50
                           transition-all duration-200"
                />
            </div>
        </div>
        <div v-else class="px-3 pt-4 pb-2 flex-shrink-0 flex justify-center">
            <button
                class="w-9 h-9 rounded-xl flex items-center justify-center
                       bg-gray-100 dark:bg-gray-800 text-gray-400
                       hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-600
                       transition-colors"
                @click="$emit('toggle')"
                title="Rechercher"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </button>
        </div>

        <!-- ── Navigation ── -->
        <nav class="flex-1 overflow-y-auto overflow-x-hidden px-3 pb-3 space-y-0.5 scrollbar-thin">

            <template v-for="item in filteredNav" :key="item.id">

                <!-- Séparateur de section (label) -->
                <div v-if="item.type === 'separator' && !collapsed"
                     class="pt-4 pb-1 px-2">
                    <span class="text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">
                        {{ item.label }}
                    </span>
                </div>
                <div v-else-if="item.type === 'separator' && collapsed" class="pt-3 pb-1 flex justify-center">
                    <div class="w-5 h-px bg-gray-200 dark:bg-gray-700" />
                </div>

                <!-- Item simple (pas d'enfants) -->
                <a
                    v-else-if="!item.children"
                    :href="item.href"
                    :class="[
                        'group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150',
                        collapsed ? 'justify-center' : '',
                        isActive(item)
                            ? 'bg-primary-600 text-white shadow-md shadow-primary-600/25'
                            : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white',
                    ]"
                    :title="collapsed ? item.label : undefined"
                >
                    <!-- Icône -->
                    <span :class="[
                        'flex-shrink-0 w-5 h-5 flex items-center justify-center',
                        isActive(item) ? 'text-white' : 'text-gray-500 dark:text-gray-400 group-hover:text-primary-600 dark:group-hover:text-primary-400',
                    ]">
                        <NavIcon :name="item.icon" class="w-5 h-5" />
                    </span>

                    <!-- Label -->
                    <Transition
                        enter-active-class="transition-all duration-200"
                        enter-from-class="opacity-0"
                        enter-to-class="opacity-100"
                        leave-active-class="transition-all duration-100"
                        leave-from-class="opacity-100"
                        leave-to-class="opacity-0"
                    >
                        <span v-if="!collapsed" class="flex-1 truncate">{{ item.label }}</span>
                    </Transition>

                    <!-- Tooltip collapsed -->
                    <div v-if="collapsed"
                         class="absolute left-full ml-3 px-2.5 py-1.5 bg-gray-900 dark:bg-gray-700 text-white text-xs rounded-lg
                                opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap z-50
                                transition-opacity duration-150 shadow-lg">
                        {{ item.label }}
                    </div>
                </a>

                <!-- Item avec enfants (accordéon) -->
                <div v-else class="relative group/parent">
                    <button
                        :class="[
                            'w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150',
                            collapsed ? 'justify-center' : '',
                            isParentActive(item)
                                ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300'
                                : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white',
                        ]"
                        @click="toggleMenu(item.id)"
                        :title="collapsed ? item.label : undefined"
                    >
                        <!-- Icône -->
                        <span :class="[
                            'flex-shrink-0 w-5 h-5 flex items-center justify-center',
                            isParentActive(item) ? 'text-primary-600 dark:text-primary-400' : 'text-gray-500 dark:text-gray-400',
                        ]">
                            <NavIcon :name="item.icon" class="w-5 h-5" />
                        </span>

                        <Transition
                            enter-active-class="transition-all duration-200"
                            enter-from-class="opacity-0"
                            enter-to-class="opacity-100"
                            leave-active-class="transition-all duration-100"
                            leave-from-class="opacity-100"
                            leave-to-class="opacity-0"
                        >
                            <span v-if="!collapsed" class="flex-1 text-left truncate">{{ item.label }}</span>
                        </Transition>

                        <!-- Chevron -->
                        <svg
                            v-if="!collapsed"
                            :class="['w-3.5 h-3.5 flex-shrink-0 transition-transform duration-200 text-gray-400',
                                     openMenus.has(item.id) ? 'rotate-180' : '']"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                        </svg>

                        <!-- Tooltip collapsed -->
                        <div v-if="collapsed"
                             class="absolute left-full ml-3 px-2.5 py-1.5 bg-gray-900 dark:bg-gray-700 text-white text-xs rounded-lg
                                    opacity-0 group-hover/parent:opacity-100 pointer-events-none whitespace-nowrap z-50
                                    transition-opacity duration-150 shadow-lg">
                            {{ item.label }}
                        </div>
                    </button>

                    <!-- Sous-menu (accordéon) — mode expanded -->
                    <Transition
                        enter-active-class="transition-all duration-200 ease-out overflow-hidden"
                        enter-from-class="max-h-0 opacity-0"
                        enter-to-class="max-h-96 opacity-100"
                        leave-active-class="transition-all duration-150 ease-in overflow-hidden"
                        leave-from-class="max-h-96 opacity-100"
                        leave-to-class="max-h-0 opacity-0"
                    >
                        <div v-if="!collapsed && openMenus.has(item.id)" class="mt-0.5 ml-4 pl-3 border-l-2 border-gray-100 dark:border-gray-700 space-y-0.5">
                            <a
                                v-for="child in item.children"
                                :key="child.id"
                                :href="child.href"
                                :class="[
                                    'flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition-all duration-150',
                                    isActiveChild(child)
                                        ? 'bg-primary-600 text-white font-medium shadow-sm shadow-primary-600/20'
                                        : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-800 dark:hover:text-gray-200 font-normal',
                                ]"
                            >
                                <NavIcon :name="child.icon" class="w-4 h-4 flex-shrink-0" />
                                <span class="truncate">{{ child.label }}</span>
                            </a>
                        </div>
                    </Transition>

                    <!-- Flyout collapsed — popup au survol -->
                    <div v-if="collapsed"
                         class="absolute left-full top-0 ml-2 w-52 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700
                                shadow-card-lg opacity-0 group-hover/parent:opacity-100 pointer-events-none group-hover/parent:pointer-events-auto
                                transition-all duration-150 z-50 overflow-hidden">
                        <div class="px-3 py-2.5 border-b border-gray-100 dark:border-gray-700">
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ item.label }}</p>
                        </div>
                        <div class="py-1.5 px-2">
                            <a
                                v-for="child in item.children"
                                :key="child.id"
                                :href="child.href"
                                :class="[
                                    'flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-sm transition-colors',
                                    isActiveChild(child)
                                        ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 font-medium'
                                        : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700',
                                ]"
                            >
                                <NavIcon :name="child.icon" class="w-4 h-4 flex-shrink-0" />
                                {{ child.label }}
                            </a>
                        </div>
                    </div>
                </div>

            </template>
        </nav>

        <!-- ── Séparateur ── -->
        <div class="mx-3 border-t border-gray-100 dark:border-gray-800 flex-shrink-0" />

        <!-- ── Toggle Dark/Light ── -->
        <div class="px-3 py-3 flex-shrink-0">
            <div :class="[
                'flex rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-800 p-1',
                collapsed ? 'justify-center' : '',
            ]">
                <template v-if="!collapsed">
                    <button
                        :class="[
                            'flex-1 flex items-center justify-center gap-1.5 py-1.5 rounded-lg text-xs font-medium transition-all duration-200',
                            !isDark ? 'bg-white dark:bg-gray-700 text-gray-800 shadow-sm' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300',
                        ]"
                        @click="setLight"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        Light
                    </button>
                    <button
                        :class="[
                            'flex-1 flex items-center justify-center gap-1.5 py-1.5 rounded-lg text-xs font-medium transition-all duration-200',
                            isDark ? 'bg-gray-700 text-white shadow-sm' : 'text-gray-500 hover:text-gray-700',
                        ]"
                        @click="setDark"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                        Dark
                    </button>
                </template>
                <template v-else>
                    <button
                        class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-500 hover:text-primary-600 transition-colors"
                        @click="toggleDark()"
                        :title="isDark ? 'Mode clair' : 'Mode sombre'"
                    >
                        <svg v-if="isDark" class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                    </button>
                </template>
            </div>
        </div>

        <!-- ── Profil utilisateur ── -->
        <div class="px-3 pb-4 flex-shrink-0">
            <div :class="[
                'flex items-center gap-3 p-2.5 rounded-xl cursor-pointer transition-colors',
                'hover:bg-gray-100 dark:hover:bg-gray-800',
                collapsed ? 'justify-center' : '',
            ]"
                 @click="profileOpen = !profileOpen"
            >
                <img
                    :src="avatarUrl"
                    :alt="user?.name"
                    class="w-9 h-9 rounded-full object-cover ring-2 ring-primary-200 dark:ring-primary-700 flex-shrink-0"
                />
                <Transition
                    enter-active-class="transition-all duration-200"
                    enter-from-class="opacity-0"
                    enter-to-class="opacity-100"
                    leave-active-class="transition-all duration-100"
                    leave-from-class="opacity-100"
                    leave-to-class="opacity-0"
                >
                    <div v-if="!collapsed" class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800 dark:text-white truncate leading-tight">
                            {{ user?.last_name }} {{ user?.name }}
                        </p>
                        <p class="text-xs text-primary-600 dark:text-primary-400 truncate">{{ roleLabel }}</p>
                    </div>
                </Transition>
                <Transition
                    enter-active-class="transition-all duration-200"
                    enter-from-class="opacity-0"
                    enter-to-class="opacity-100"
                    leave-active-class="transition-all duration-100"
                    leave-from-class="opacity-100"
                    leave-to-class="opacity-0"
                >
                    <a v-if="!collapsed" href="/logout"
                       class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-400 hover:text-danger-500 hover:bg-danger-50 dark:hover:bg-danger-900/20 transition-colors flex-shrink-0"
                       title="Déconnexion"
                       @click.stop
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </a>
                </Transition>
            </div>
        </div>
    </aside>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useDark, useToggle } from '@vueuse/core';
import { useNavigation } from '@/Composables/useNavigation';
import NavIcon from '@/Components/Layout/NavIcon.vue';
import type { NavItem, PageProps } from '@/types';

// ── Props / Emits ────────────────────────────────────────────────────────────
const props = defineProps<{
    collapsed: boolean;
    mobileOpen: boolean;
}>();

defineEmits<{
    toggle: [];
    close: [];
}>();

// ── Dark mode ────────────────────────────────────────────────────────────────
const isDark    = useDark();
const toggleDark = useToggle(isDark);
const setLight  = () => { isDark.value = false; };
const setDark   = () => { isDark.value = true; };

// ── Navigation ───────────────────────────────────────────────────────────────
const { navItems, currentMenu, currentSubItem, user } = useNavigation();
const page = usePage<PageProps>();

const schoolName = computed(() => page.props.settings?.school_name ?? 'School MS');
const logoUrl    = computed(() => page.props.settings?.logo_url    ?? null);
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

// ── Recherche ────────────────────────────────────────────────────────────────
const searchQuery = ref('');

const filteredNav = computed<NavItem[]>(() => {
    const q = searchQuery.value.trim().toLowerCase();
    if (!q) return navItems.value;

    return navItems.value.filter(item => {
        if (item.label.toLowerCase().includes(q)) return true;
        if (item.children?.some(c => c.label.toLowerCase().includes(q))) return true;
        return false;
    });
});

// ── Menus ouverts (accordéon) ────────────────────────────────────────────────
const openMenus = ref<Set<string>>(new Set());
const profileOpen = ref(false);

// Ouvrir automatiquement le menu actif au chargement
watch(currentMenu, (menu) => {
    if (menu?.id && menu.children?.length) {
        openMenus.value.add(menu.id);
    }
}, { immediate: true });

// Fermer les sous-menus quand on collapse
watch(() => props.collapsed, (val) => {
    if (val) openMenus.value.clear();
});

const toggleMenu = (id: string) => {
    if (openMenus.value.has(id)) {
        openMenus.value.delete(id);
    } else {
        openMenus.value.add(id);
    }
};

// ── Active states ────────────────────────────────────────────────────────────
const isActive = (item: NavItem) => {
    if (!item.href) return false;
    return currentMenu.value?.id === item.id;
};

const isParentActive = (item: NavItem) => currentMenu.value?.id === item.id;

const isActiveChild = (item: NavItem) => currentSubItem.value?.id === item.id;
</script>

<style scoped>
.scrollbar-thin::-webkit-scrollbar { width: 4px; }
.scrollbar-thin::-webkit-scrollbar-track { background: transparent; }
.scrollbar-thin::-webkit-scrollbar-thumb { @apply bg-gray-200 dark:bg-gray-700 rounded-full; }
</style>
