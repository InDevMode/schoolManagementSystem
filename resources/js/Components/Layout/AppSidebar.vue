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

    <!-- Modal confirmation déconnexion -->
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="showLogoutConfirm" class="fixed inset-0 z-[9998] flex items-center justify-center p-4">
                <!-- Overlay -->
                <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showLogoutConfirm = false" />
                <!-- Boîte de dialogue -->
                <Transition
                    enter-active-class="transition duration-200 ease-out"
                    enter-from-class="opacity-0 scale-95"
                    enter-to-class="opacity-100 scale-100"
                    leave-active-class="transition duration-150 ease-in"
                    leave-from-class="opacity-100 scale-100"
                    leave-to-class="opacity-0 scale-95"
                >
                    <div v-if="showLogoutConfirm" class="relative w-full max-w-sm bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden">
                        <!-- Icône en haut -->
                        <div class="flex flex-col items-center px-6 pt-8 pb-4 text-center">
                            <div class="w-14 h-14 rounded-full bg-danger-50 dark:bg-danger-900/30 flex items-center justify-center mb-4">
                                <svg class="w-7 h-7 text-danger-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Déconnexion</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Êtes-vous sûr de vouloir vous déconnecter ?
                            </p>
                        </div>
                        <!-- Actions -->
                        <div class="px-6 pb-6 flex gap-3 mt-2">
                            <button
                                @click="showLogoutConfirm = false"
                                class="flex-1 px-4 py-2.5 rounded-xl text-sm font-medium
                                       border border-gray-200 dark:border-gray-600
                                       text-gray-700 dark:text-gray-300
                                       hover:bg-gray-50 dark:hover:bg-gray-700
                                       transition-colors"
                            >
                                Annuler
                            </button>
                            <a
                                href="/logout"
                                class="flex-1 px-4 py-2.5 rounded-xl text-sm font-medium text-center
                                       bg-danger-600 hover:bg-danger-700 text-white
                                       transition-colors"
                            >
                                Se déconnecter
                            </a>
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>

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
                     style="background: linear-gradient(135deg, #9189f5, #7B74F0);">
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
        <nav class="flex-1 overflow-y-auto overflow-x-hidden px-3 pb-3 space-y-0.5
                    [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">

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
                            ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300'
                            : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white',
                    ]"
                    :title="collapsed ? item.label : undefined"
                >
                    <!-- Icône avec fond coloré style action-button -->
                    <span :class="[
                        'flex-shrink-0 w-7 h-7 rounded-lg flex items-center justify-center transition-all duration-150',
                        isActive(item)
                            ? 'bg-primary-600 text-white shadow-md shadow-primary-500/40'
                            : 'bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 group-hover:bg-primary-600 group-hover:text-white group-hover:shadow-md group-hover:shadow-primary-500/40',
                    ]">
                        <NavIcon :name="item.icon" class="w-4 h-4" />
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
                        <!-- Icône avec fond coloré style action-button -->
                        <span :class="[
                            'flex-shrink-0 w-7 h-7 rounded-lg flex items-center justify-center transition-all duration-150',
                            isParentActive(item)
                                ? 'bg-primary-600 text-white shadow-md shadow-primary-500/40'
                                : 'bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 group-hover/parent:bg-primary-600 group-hover/parent:text-white group-hover/parent:shadow-md group-hover/parent:shadow-primary-500/40',
                        ]">
                            <NavIcon :name="item.icon" class="w-4 h-4" />
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
                        <div v-if="!collapsed && openMenus.has(item.id)" class="mt-1 ml-3 space-y-0.5">
                            <a
                                v-for="(child, idx) in item.children"
                                :key="child.id"
                                :href="child.href"
                                :class="[
                                    'group/child relative flex items-center gap-2.5 pl-6 pr-3 py-2 rounded-lg text-sm transition-all duration-150',
                                    isActiveChild(child)
                                        ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 font-medium'
                                        : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-800 dark:hover:text-gray-200 font-normal',
                                ]"
                            >
                                <!-- Connecteur en L arrondi -->
                                <span class="pointer-events-none absolute left-0 top-0 flex h-full w-6 flex-col items-center" aria-hidden="true">
                                    <span
                                        :class="[
                                            'w-px flex-1',
                                            idx === item.children.length - 1 ? 'h-1/2 flex-none' : 'flex-1',
                                            isActiveChild(child) ? 'bg-primary-400/60' : 'bg-gray-300 dark:bg-gray-600',
                                        ]"
                                        style="margin-top: 0;"
                                    />
                                    <svg viewBox="0 0 12 12" class="w-3 h-3 flex-shrink-0 -mt-px" fill="none" stroke="currentColor"
                                        :class="isActiveChild(child) ? 'text-primary-400/60' : 'text-gray-300 dark:text-gray-600'"
                                        stroke-width="1.5" stroke-linecap="round">
                                        <path d="M1 0 V7 Q1 11 5 11 H12" />
                                    </svg>
                                    <span
                                        v-if="idx < item.children.length - 1"
                                        :class="['w-px flex-1', isActiveChild(child) ? 'bg-primary-400/60' : 'bg-gray-300 dark:bg-gray-600']"
                                    />
                                </span>

                                <!-- Icône avec fond coloré style action-button -->
                                <span :class="[
                                    'flex-shrink-0 w-6 h-6 rounded-md flex items-center justify-center transition-all duration-150',
                                    isActiveChild(child)
                                        ? 'bg-primary-600 text-white shadow-sm shadow-primary-500/40'
                                        : 'bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500 group-hover/child:bg-primary-600 group-hover/child:text-white group-hover/child:shadow-sm group-hover/child:shadow-primary-500/40',
                                ]">
                                    <NavIcon :name="child.icon" class="w-3.5 h-3.5" />
                                </span>
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
                        Clair
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
                        Sombre
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
        <div ref="sidebarProfileRef" class="px-3 pb-4 flex-shrink-0 relative">

            <!-- Dropdown profil — s'ouvre vers le haut -->
            <Transition
                enter-active-class="transition duration-150 ease-out"
                enter-from-class="opacity-0 translate-y-2"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition duration-100 ease-in"
                leave-from-class="opacity-100 translate-y-0"
                leave-to-class="opacity-0 translate-y-2"
            >
                <div v-if="profileOpen && !collapsed"
                     class="absolute bottom-full left-3 right-3 mb-2
                            bg-white dark:bg-gray-800 rounded-2xl
                            border border-gray-100 dark:border-gray-700
                            shadow-card-lg overflow-hidden z-50">
                    <!-- En-tête dégradé -->
                    <div class="px-4 py-3.5 bg-gradient-to-br from-primary-50 to-secondary-50
                                dark:from-primary-900/20 dark:to-secondary-900/20
                                border-b border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            <img :src="avatarUrl" :alt="user?.name"
                                 class="w-10 h-10 rounded-full object-cover ring-2 ring-primary-300 flex-shrink-0"/>
                            <div class="min-w-0">
                                <p class="font-bold text-sm text-gray-900 dark:text-white truncate">
                                    {{ user?.last_name }} {{ user?.name }}
                                </p>
                                <p class="text-xs text-primary-600 dark:text-primary-400 font-medium mt-0.5">{{ roleLabel }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ user?.email }}</p>
                            </div>
                        </div>
                    </div>
                    <!-- Liens -->
                    <div class="py-1.5">
                        <a v-for="link in profileLinks" :key="link.href" :href="link.href"
                           class="flex items-center gap-3 px-4 py-2.5 text-sm
                                  text-gray-700 dark:text-gray-300
                                  hover:bg-gray-50 dark:hover:bg-gray-700/60
                                  hover:text-primary-600 dark:hover:text-primary-400
                                  transition-colors">
                            <span class="w-7 h-7 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                                <NavIcon :name="link.icon" class="w-3.5 h-3.5 text-gray-500"/>
                            </span>
                            {{ link.label }}
                        </a>
                    </div>
                    <!-- Déconnexion -->
                    <div class="border-t border-gray-100 dark:border-gray-700 py-1.5">
                        <button
                           @click="profileOpen = false; showLogoutConfirm = true"
                           class="w-full flex items-center gap-3 px-4 py-2.5 text-sm
                                  text-danger-600 dark:text-danger-400
                                  hover:bg-danger-50 dark:hover:bg-danger-900/20 transition-colors">
                            <span class="w-7 h-7 rounded-lg bg-danger-50 dark:bg-danger-900/20 flex items-center justify-center flex-shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                            </span>
                            Déconnexion
                        </button>
                    </div>
                </div>
            </Transition>

        <!-- Bouton profil -->
        <button :class="[
                'w-full flex items-center gap-3 p-2.5 rounded-xl transition-colors',
                'hover:bg-gray-100 dark:hover:bg-gray-800',
                collapsed ? 'justify-center' : '',
                profileOpen ? 'bg-gray-100 dark:bg-gray-800' : '',
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
                    <div v-if="!collapsed" class="flex-1 min-w-0 text-left">
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
                    <svg v-if="!collapsed"
                         :class="['w-3.5 h-3.5 text-gray-400 transition-transform duration-200 flex-shrink-0', profileOpen ? 'rotate-180' : '']"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                    </svg>
                </Transition>
            </button>
        </div>
    </aside>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
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
    0: 'Super Admin',
    1: 'Administrateur',
    2: 'Professeur',
    3: 'Apprenant',
    4: 'Parent',
};
// Pour les rôles custom (user_type >= 5), on utilise role_label partagé par Inertia
const roleLabel = computed(() => {
    const ut = user.value?.user_type ?? -1;
    if (roleLabelMap[ut]) return roleLabelMap[ut];
    // Rôle custom : utiliser le role_label du backend ou le nom du rôle Spatie
    return user.value?.role_label ?? user.value?.roles?.[0] ?? 'Utilisateur';
});

const homeLinks: Record<number, string> = {
    0: '/superadmin/dashboard',
    1: '/admin/dashboard',
    2: '/teacher/dashboard',
    3: '/student/dashboard',
    4: '/parent/dashboard',
};
// Rôles custom → /admin/dashboard
const homeLink = computed(() => homeLinks[user.value?.user_type ?? -1] ?? '/admin/dashboard');

// ── Liens profil (dropdown sidebar) ─────────────────────────────────────────
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
        { href: '/teacher/account',         icon: 'user', label: 'Mon profil' },
        { href: '/teacher/change_password', icon: 'lock', label: 'Mot de passe' },
    ],
    3: [
        { href: '/student/account',         icon: 'user', label: 'Mon profil' },
        { href: '/student/change_password', icon: 'lock', label: 'Mot de passe' },
    ],
    4: [
        { href: '/parent/account',         icon: 'user', label: 'Mon profil' },
        { href: '/parent/change_password', icon: 'lock', label: 'Mot de passe' },
    ],
};
const profileLinks = computed(() => {
    const ut = user.value?.user_type ?? 1;
    return profileLinksMap[ut] ?? profileLinksMap[1] ?? [];
});

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
const showLogoutConfirm = ref(false);
const sidebarProfileRef = ref<HTMLElement | null>(null);

// Fermer le dropdown profil si clic en dehors
const handleOutsideClick = (e: MouseEvent) => {
    if (sidebarProfileRef.value && !sidebarProfileRef.value.contains(e.target as Node)) {
        profileOpen.value = false;
    }
};
onMounted(()  => document.addEventListener('mousedown', handleOutsideClick));
onUnmounted(() => document.removeEventListener('mousedown', handleOutsideClick));

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
