<template>
    <div :class="['flex h-screen overflow-hidden transition-colors duration-300', isDark ? 'dark' : '', 'bg-gray-100 dark:bg-gray-900']">

        <!-- ── App Sidebar (navigation principale) ── -->
        <AppSidebar :collapsed="sidebarCollapsed" :mobile-open="mobileSidebarOpen"
            @toggle="sidebarCollapsed = !sidebarCollapsed" @close="mobileSidebarOpen = false" />

        <!-- ── Contenu principal décalé selon sidebar ── -->
        <div :class="['flex flex-col flex-1 min-w-0 transition-all duration-300 overflow-hidden', sidebarCollapsed ? 'lg:ml-[72px]' : 'lg:ml-64']">
            <AppTopbar @open-mobile="mobileSidebarOpen = true" />

            <!-- ── Corps chat (3 colonnes) ── -->
            <div class="flex flex-1 overflow-hidden">

                <!-- ══════════════════════════════════════════
                     COLONNE GAUCHE — Liste des conversations
                ══════════════════════════════════════════ -->
                <aside class="w-[340px] flex-shrink-0 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 flex flex-col shadow-sm">

                    <!-- En-tête sidebar -->
                    <div class="px-5 pt-5 pb-4 border-b border-gray-100 dark:border-gray-700">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white">Messages</h2>
                            <div class="flex items-center gap-1">
                                <!-- Bouton nouveau message — ouvre le modal contacts -->
                                <button
                                    @click="showNewMessageModal = true"
                                    class="p-2 rounded-xl text-gray-500 dark:text-gray-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-600 dark:hover:text-primary-400 transition-colors"
                                    title="Nouveau message">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <!-- Barre de recherche -->
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input v-model="search" type="text" placeholder="Rechercher..."
                                class="w-full pl-9 pr-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-700/60 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all" />
                        </div>
                    </div>

                    <!-- Onglets Inbox / Contacts -->
                    <div class="flex px-4 pt-3 pb-0 gap-1 border-b border-gray-100 dark:border-gray-700">
                        <button
                            @click="activeTab = 'inbox'"
                            :class="[
                                'flex items-center gap-1.5 px-3 py-2 rounded-t-lg text-xs font-semibold border-b-2 transition-colors',
                                activeTab === 'inbox'
                                    ? 'border-primary-500 text-primary-700 dark:text-primary-300 bg-primary-50/60 dark:bg-primary-900/20'
                                    : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50'
                            ]">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0H4"/>
                            </svg>
                            Boîte de réception
                            <span v-if="totalUnread > 0" class="ml-1 min-w-[18px] h-[18px] px-1 bg-primary-600 text-white text-[10px] font-bold rounded-full flex items-center justify-center">
                                {{ totalUnread > 9 ? '9+' : totalUnread }}
                            </span>
                        </button>
                        <button
                            @click="activeTab = 'contacts'"
                            :class="[
                                'flex items-center gap-1.5 px-3 py-2 rounded-t-lg text-xs font-semibold border-b-2 transition-colors',
                                activeTab === 'contacts'
                                    ? 'border-primary-500 text-primary-700 dark:text-primary-300 bg-primary-50/60 dark:bg-primary-900/20'
                                    : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50'
                            ]">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Contacts
                            <span v-if="localChatContacts.length > 0" class="ml-1 min-w-[18px] h-[18px] px-1 bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-300 text-[10px] font-bold rounded-full flex items-center justify-center">
                                {{ localChatContacts.length }}
                            </span>
                        </button>
                    </div>

                    <!-- ── TAB INBOX — Liste des conversations ── -->
                    <div v-show="activeTab === 'inbox'" class="flex-1 overflow-y-auto">
                        <template v-if="filteredContacts.length">
                            <a v-for="contact in filteredContacts" :key="contact.user_id"
                                :href="`/chat?receiver_id=${encodeId(contact.user_id)}`"
                                :class="[
                                    'flex items-center gap-3 px-4 py-3.5 cursor-pointer transition-all border-b border-gray-50 dark:border-gray-700/50 group relative',
                                    isActiveContact(contact)
                                        ? 'bg-primary-50 dark:bg-primary-900/20 border-l-[3px] border-l-primary-500'
                                        : 'hover:bg-gray-50 dark:hover:bg-gray-700/50 border-l-[3px] border-l-transparent'
                                ]">
                                <div class="relative flex-shrink-0">
                                    <img :src="avatarUrl(contact.sender_profile_picture)" :alt="contact.name"
                                        class="w-11 h-11 rounded-full object-cover bg-gray-200 ring-2 ring-white dark:ring-gray-800" @error="onImgError" />
                                    <span :class="[
                                        'absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full border-2 border-white dark:border-gray-800',
                                        isOnline(contact) ? 'bg-emerald-500' : 'bg-gray-300 dark:bg-gray-500'
                                    ]" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-0.5">
                                        <p :class="[
                                            'text-sm font-semibold truncate',
                                            isActiveContact(contact) ? 'text-primary-700 dark:text-primary-300' : 'text-gray-900 dark:text-white'
                                        ]">{{ contact.name }}</p>
                                        <span class="text-[10px] text-gray-400 dark:text-gray-500 flex-shrink-0 ml-1">
                                            {{ contactTime(contact) }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between gap-1">
                                        <p :class="[
                                            'text-xs truncate flex-1',
                                            contact.countMessage > 0 ? 'text-gray-700 dark:text-gray-300 font-medium' : 'text-gray-400 dark:text-gray-500'
                                        ]">
                                            <span v-if="contact.is_delete" class="italic">Message supprimé</span>
                                            <span v-else>{{ contact.message || 'Aucun message' }}</span>
                                        </p>
                                        <span v-if="contact.countMessage > 0"
                                            class="flex-shrink-0 min-w-[20px] h-5 px-1.5 bg-primary-600 text-white text-[10px] font-bold rounded-full flex items-center justify-center">
                                            {{ contact.countMessage > 9 ? '9+' : contact.countMessage }}
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </template>
                        <div v-else class="flex flex-col items-center justify-center py-16 text-center px-4">
                            <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-3">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
                                </svg>
                            </div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Aucune conversation</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Cliquez sur "Contacts" pour démarrer</p>
                        </div>
                    </div>

                    <!-- ── TAB CONTACTS — Contacts suggérés ── -->
                    <div v-show="activeTab === 'contacts'" class="flex-1 overflow-y-auto">
                        <template v-if="filteredChatContacts.length">
                            <!-- Groupement optionnel par classe (si les contacts ont class_name) -->
                            <template v-if="hasClassGroups">
                                <template v-for="group in contactGroups" :key="group.label">
                                    <div class="px-4 py-2 bg-gray-50/80 dark:bg-gray-700/40 border-b border-gray-100 dark:border-gray-700/50">
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                                            {{ group.label }}
                                        </p>
                                    </div>
                                    <a v-for="contact in group.contacts" :key="contact.id"
                                        :href="`/chat?receiver_id=${contact.id_encoded}`"
                                        class="flex items-center gap-3 px-4 py-3.5 cursor-pointer transition-all border-b border-gray-50 dark:border-gray-700/30 hover:bg-gray-50 dark:hover:bg-gray-700/50 border-l-[3px] border-l-transparent hover:border-l-primary-400">
                                        <div class="relative flex-shrink-0">
                                            <img :src="contact.profile_picture" :alt="contact.name"
                                                class="w-10 h-10 rounded-full object-cover bg-gray-200 ring-2 ring-white dark:ring-gray-800" @error="onImgError" />
                                            <span :class="[
                                                'absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 rounded-full border-2 border-white dark:border-gray-800',
                                                contact.is_online ? 'bg-emerald-500' : 'bg-gray-300 dark:bg-gray-500'
                                            ]" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ contact.name }}</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500 truncate">
                                                {{ contact.role }}
                                                <span v-if="contact.is_online" class="text-emerald-500"> · En ligne</span>
                                            </p>
                                        </div>
                                        <svg class="w-4 h-4 text-gray-300 dark:text-gray-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                        </svg>
                                    </a>
                                </template>
                            </template>
                            <template v-else>
                                <a v-for="contact in filteredChatContacts" :key="contact.id"
                                    :href="`/chat?receiver_id=${contact.id_encoded}`"
                                    class="flex items-center gap-3 px-4 py-3.5 cursor-pointer transition-all border-b border-gray-50 dark:border-gray-700/30 hover:bg-gray-50 dark:hover:bg-gray-700/50 border-l-[3px] border-l-transparent hover:border-l-primary-400">
                                    <div class="relative flex-shrink-0">
                                        <img :src="contact.profile_picture" :alt="contact.name"
                                            class="w-10 h-10 rounded-full object-cover bg-gray-200 ring-2 ring-white dark:ring-gray-800" @error="onImgError" />
                                        <span :class="[
                                            'absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 rounded-full border-2 border-white dark:border-gray-800',
                                            contact.is_online ? 'bg-emerald-500' : 'bg-gray-300 dark:bg-gray-500'
                                        ]" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ contact.name }}</p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500">
                                            {{ contact.role }}
                                            <span v-if="contact.is_online" class="text-emerald-500"> · En ligne</span>
                                        </p>
                                    </div>
                                    <svg class="w-4 h-4 text-gray-300 dark:text-gray-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                    </svg>
                                </a>
                            </template>
                        </template>
                        <div v-else class="flex flex-col items-center justify-center py-16 text-center px-4">
                            <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-3">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Aucun contact disponible</p>
                        </div>
                    </div>

                    <!-- Footer polling -->
                    <div class="border-t border-gray-100 dark:border-gray-700 px-4 py-2.5 bg-gray-50/50 dark:bg-gray-800/50">
                        <div class="flex items-center justify-between mb-1.5">
                            <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Actualisation auto</p>
                            <span :class="['inline-flex items-center gap-1 text-[10px] font-medium', pollingActive ? 'text-emerald-500' : 'text-gray-400']">
                                <span :class="['w-1.5 h-1.5 rounded-full', pollingActive ? 'bg-emerald-500 animate-pulse' : 'bg-gray-300']" />
                                {{ pollingActive ? 'En direct' : 'Inactif' }}
                            </span>
                        </div>
                        <div class="flex flex-wrap gap-1">
                            <button v-for="opt in pollOptions" :key="opt.value"
                                :class="[
                                    'px-2 py-0.5 text-[10px] rounded-md font-medium transition-colors',
                                    pollInterval_ === opt.value
                                        ? 'bg-primary-600 text-white'
                                        : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600'
                                ]"
                                @click="changePollInterval(opt.value)">{{ opt.label }}</button>
                        </div>
                    </div>
                </aside>

                <!-- ══ MODAL NOUVEAU MESSAGE ══════════════════════════════════════ -->
                <Teleport to="body">
                    <Transition
                        enter-active-class="transition duration-200 ease-out"
                        enter-from-class="opacity-0"
                        enter-to-class="opacity-100"
                        leave-active-class="transition duration-150 ease-in"
                        leave-to-class="opacity-0">
                        <div v-if="showNewMessageModal"
                            class="fixed inset-0 z-50 flex items-center justify-center p-4"
                            @click.self="showNewMessageModal = false">
                            <!-- Overlay -->
                            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="showNewMessageModal = false"/>

                            <!-- Panneau modal -->
                            <div class="relative z-10 w-full max-w-md rounded-2xl bg-white dark:bg-gray-800 shadow-2xl flex flex-col overflow-hidden"
                                style="max-height: 75vh">

                                <!-- En-tête -->
                                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                                    <div>
                                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Nouveau message</h3>
                                        <p class="text-xs text-gray-400 mt-0.5">{{ localChatContacts.length }} contact{{ localChatContacts.length > 1 ? 's' : '' }} disponible{{ localChatContacts.length > 1 ? 's' : '' }}</p>
                                    </div>
                                    <button @click="showNewMessageModal = false"
                                        class="p-1.5 rounded-xl text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>

                                <!-- Barre de recherche -->
                                <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                                    <div class="relative">
                                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                        <input
                                            v-model="modalContactSearch"
                                            type="text"
                                            placeholder="Rechercher un contact…"
                                            class="w-full pl-9 pr-4 py-2 text-sm bg-gray-50 dark:bg-gray-700/60 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                                        />
                                    </div>
                                </div>

                                <!-- Liste des contacts -->
                                <div class="flex-1 overflow-y-auto">
                                    <template v-if="filteredModalContacts.length">
                                        <!-- Groupement par classe si disponible -->
                                        <template v-if="hasClassGroups">
                                            <template v-for="group in modalContactGroups" :key="group.label">
                                                <div class="px-4 py-2 bg-gray-50/80 dark:bg-gray-700/40 sticky top-0 z-10">
                                                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                                                        {{ group.label }}
                                                    </p>
                                                </div>
                                                <a v-for="contact in group.contacts" :key="contact.id"
                                                    :href="`/chat?receiver_id=${contact.id_encoded}`"
                                                    @click="showNewMessageModal = false"
                                                    class="flex items-center gap-3 px-4 py-3 cursor-pointer transition-all border-b border-gray-50 dark:border-gray-700/30 hover:bg-primary-50 dark:hover:bg-primary-900/20 group/item">
                                                    <div class="relative flex-shrink-0">
                                                        <img :src="contact.profile_picture" :alt="contact.name"
                                                            class="w-10 h-10 rounded-full object-cover bg-gray-200 ring-2 ring-white dark:ring-gray-800" @error="onImgError"/>
                                                        <span :class="[
                                                            'absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 rounded-full border-2 border-white dark:border-gray-800',
                                                            contact.is_online ? 'bg-emerald-500' : 'bg-gray-300 dark:bg-gray-500'
                                                        ]"/>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate group-hover/item:text-primary-700 dark:group-hover/item:text-primary-300 transition-colors">
                                                            {{ contact.name }}
                                                        </p>
                                                        <p class="text-xs text-gray-400 truncate">
                                                            {{ contact.role }}
                                                            <span v-if="contact.is_online" class="text-emerald-500"> · En ligne</span>
                                                        </p>
                                                    </div>
                                                    <svg class="w-4 h-4 text-gray-300 dark:text-gray-600 group-hover/item:text-primary-400 flex-shrink-0 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                                    </svg>
                                                </a>
                                            </template>
                                        </template>
                                        <template v-else>
                                            <a v-for="contact in filteredModalContacts" :key="contact.id"
                                                :href="`/chat?receiver_id=${contact.id_encoded}`"
                                                @click="showNewMessageModal = false"
                                                class="flex items-center gap-3 px-4 py-3 cursor-pointer transition-all border-b border-gray-50 dark:border-gray-700/30 hover:bg-primary-50 dark:hover:bg-primary-900/20 group/item">
                                                <div class="relative flex-shrink-0">
                                                    <img :src="contact.profile_picture" :alt="contact.name"
                                                        class="w-10 h-10 rounded-full object-cover bg-gray-200 ring-2 ring-white dark:ring-gray-800" @error="onImgError"/>
                                                    <span :class="[
                                                        'absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 rounded-full border-2 border-white dark:border-gray-800',
                                                        contact.is_online ? 'bg-emerald-500' : 'bg-gray-300 dark:bg-gray-500'
                                                    ]"/>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate group-hover/item:text-primary-700 dark:group-hover/item:text-primary-300 transition-colors">
                                                        {{ contact.name }}
                                                    </p>
                                                    <p class="text-xs text-gray-400">
                                                        {{ contact.role }}
                                                        <span v-if="contact.is_online" class="text-emerald-500"> · En ligne</span>
                                                    </p>
                                                </div>
                                                <svg class="w-4 h-4 text-gray-300 dark:text-gray-600 group-hover/item:text-primary-400 flex-shrink-0 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                                </svg>
                                            </a>
                                        </template>
                                    </template>
                                    <div v-else class="flex flex-col items-center justify-center py-12 text-center px-4">
                                        <svg class="w-10 h-10 text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <p class="text-sm text-gray-400 dark:text-gray-500">Aucun contact trouvé</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </Transition>
                </Teleport>

                <!-- ══════════════════════════════════════════
                     COLONNE CENTRALE — Zone de messages
                ══════════════════════════════════════════ -->
                <div class="flex-1 flex flex-col overflow-hidden bg-white dark:bg-gray-800">

                    <!-- État vide — aucune conversation sélectionnée -->
                    <div v-if="!receiver" class="flex-1 flex flex-col items-center justify-center gap-5 bg-gray-50 dark:bg-gray-900">
                        <div class="w-24 h-24 rounded-3xl bg-gradient-to-br from-primary-100 to-primary-200 dark:from-primary-900/40 dark:to-primary-800/30 flex items-center justify-center shadow-lg shadow-primary-100 dark:shadow-primary-900/30">
                            <svg class="w-12 h-12 text-primary-500 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </div>
                        <div class="text-center">
                            <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">Sélectionnez une conversation</p>
                            <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Choisissez un contact pour commencer à discuter</p>
                        </div>
                    </div>

                    <template v-else>
                        <!-- ── En-tête conversation ── -->
                        <div class="flex items-center gap-3 px-5 py-3.5 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 flex-shrink-0 shadow-sm">
                            <!-- Avatar + infos -->
                            <div class="relative flex-shrink-0">
                                <img :src="avatarUrl(receiver.profile_picture)" :alt="receiver.name"
                                    class="w-10 h-10 rounded-full object-cover bg-gray-200 ring-2 ring-white dark:ring-gray-700" @error="onImgError" />
                                <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-emerald-500 rounded-full border-2 border-white dark:border-gray-800" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-gray-900 dark:text-white text-sm leading-tight">
                                    {{ receiver.last_name }} {{ receiver.name }}
                                </p>
                                <Transition enter-active-class="transition duration-200" enter-from-class="opacity-0" leave-active-class="transition duration-150" leave-to-class="opacity-0">
                                    <p v-if="peerTyping" class="text-xs text-primary-500 flex items-center gap-1 mt-0.5">
                                        <span class="flex gap-0.5 items-center">
                                            <span v-for="d in [0,150,300]" :key="d" class="w-1 h-1 bg-primary-500 rounded-full animate-bounce" :style="`animation-delay:${d}ms`"/>
                                        </span>
                                        en train d'écrire…
                                    </p>
                                    <p v-else class="text-xs text-emerald-500 mt-0.5">En ligne</p>
                                </Transition>
                            </div>
                            <!-- Boutons d'action header -->
                            <div class="flex items-center gap-1 ml-auto">
                                <!-- Recherche dans la conv -->
                                <button
                                    @click="showSearchBar = !showSearchBar"
                                    :class="[
                                        'p-2 rounded-xl transition-colors',
                                        showSearchBar
                                            ? 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'
                                            : 'text-gray-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20'
                                    ]"
                                    title="Rechercher dans la conversation">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </button>
                                <!-- Panneau info toggle -->
                                <button
                                    @click="showInfoPanel = !showInfoPanel"
                                    :class="[
                                        'p-2 rounded-xl transition-colors',
                                        showInfoPanel
                                            ? 'bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400'
                                            : 'text-gray-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20'
                                    ]"
                                    title="Infos du contact">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Barre de recherche dans la conv -->
                        <Transition
                            enter-active-class="transition-all duration-200 ease-out"
                            enter-from-class="opacity-0 -translate-y-2"
                            enter-to-class="opacity-100 translate-y-0"
                            leave-active-class="transition-all duration-150 ease-in"
                            leave-to-class="opacity-0 -translate-y-2">
                            <div v-if="showSearchBar" class="px-4 py-2.5 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
                                <div class="relative">
                                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                    <input
                                        v-model="msgSearch"
                                        ref="msgSearchInput"
                                        type="text"
                                        placeholder="Rechercher dans la conversation..."
                                        class="w-full pl-9 pr-4 py-2 text-sm bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500"
                                    />
                                    <span v-if="msgSearch" class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">
                                        {{ msgSearchResults.length }} résultat(s)
                                    </span>
                                </div>
                            </div>
                        </Transition>

                        <!-- ── Zone messages ── -->
                        <div ref="messagesContainer"
                            class="flex-1 overflow-y-auto px-5 py-5 space-y-1 bg-gray-50 dark:bg-gray-900/50"
                            style="background-image: radial-gradient(circle at 1px 1px, rgba(0,0,0,0.03) 1px, transparent 0); background-size: 24px 24px;">
                            <template v-for="(chat, index) in displayedChats" :key="chat.id">

                                <!-- Séparateur date -->
                                <div v-if="showDateSeparator(index)" class="flex items-center gap-3 my-5">
                                    <div class="flex-1 h-px bg-gray-200 dark:bg-gray-700" />
                                    <span class="text-[11px] text-gray-400 dark:text-gray-500 font-medium px-3 py-1 bg-white dark:bg-gray-800 rounded-full border border-gray-200 dark:border-gray-700 shadow-sm">
                                        {{ formatDate(chat.created_date) }}
                                    </span>
                                    <div class="flex-1 h-px bg-gray-200 dark:bg-gray-700" />
                                </div>

                                <!-- ── Message envoyé (moi) ── -->
                                <div v-if="authUser && chat.sender_id === authUser.id" class="group flex justify-end items-end gap-2 mb-1">
                                    <div class="flex flex-col items-end max-w-[68%]">
                                        <!-- Actions hover -->
                                        <div v-if="!chat.is_delete" class="flex items-center gap-1.5 mb-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <button v-if="!chat.file"
                                                class="text-[11px] px-2.5 py-1 rounded-xl text-gray-500 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 font-medium transition-colors"
                                                @click="startEdit(chat)">
                                                Modifier
                                            </button>
                                            <button
                                                class="text-[11px] px-2.5 py-1 rounded-xl text-gray-500 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 font-medium transition-colors"
                                                @click="deleteMsg(chat.id)">
                                                Supprimer
                                            </button>
                                        </div>
                                        <!-- Bulle message envoyé -->
                                        <div :class="[
                                            'rounded-2xl rounded-br-md overflow-hidden max-w-full shadow-sm',
                                            chat.is_delete ? 'bg-gray-200 dark:bg-gray-700' : 'bg-gradient-to-br from-primary-500 to-primary-600'
                                        ]">
                                            <!-- Image -->
                                            <template v-if="!chat.is_delete && chat.file && isImage(chat.file)">
                                                <div class="relative group/img">
                                                    <img :src="chat.file_url" @click="openLightbox(chat.file_url, chat.file)"
                                                        class="max-w-[260px] max-h-[200px] w-full object-cover cursor-zoom-in block" />
                                                    <a :href="chat.file_url" :download="fileBaseName(chat.file)" target="_blank"
                                                        class="absolute top-2 right-2 p-1.5 rounded-xl bg-black/50 text-white opacity-0 group-hover/img:opacity-100 transition-opacity"
                                                        title="Télécharger" @click.stop>
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                        </svg>
                                                    </a>
                                                </div>
                                            </template>
                                            <!-- Fichier -->
                                            <template v-else-if="!chat.is_delete && chat.file">
                                                <a :href="chat.file_url" :download="fileBaseName(chat.file)" target="_blank"
                                                    class="flex items-center gap-3 px-4 py-3 text-white hover:bg-white/10 transition-colors no-underline" @click.stop>
                                                    <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center flex-shrink-0">
                                                        <component :is="fileIcon(chat.file)" class="w-5 h-5 text-white" />
                                                    </div>
                                                    <div class="min-w-0">
                                                        <p class="text-sm font-semibold truncate max-w-[160px]">{{ fileBaseName(chat.file) }}</p>
                                                        <p class="text-[11px] opacity-70 mt-0.5">Télécharger</p>
                                                    </div>
                                                </a>
                                            </template>
                                            <!-- Texte -->
                                            <p v-if="!chat.is_delete && chat.message"
                                                :class="['px-4 py-2.5 text-sm text-white whitespace-pre-wrap break-words leading-relaxed', chat.file ? 'border-t border-white/20' : '']">
                                                {{ chat.message }}
                                            </p>
                                            <p v-if="chat.is_delete" class="px-4 py-2.5 text-sm text-gray-500 italic">
                                                Message supprimé
                                            </p>
                                        </div>
                                        <!-- Horodatage + statut lu -->
                                        <div class="flex items-center gap-1 mt-1.5 pr-1">
                                            <span class="text-[10px] text-gray-400">{{ timeAgo(chat.created_date) }}</span>
                                            <template v-if="!chat.is_delete && isLastSentMsg(chat)">
                                                <svg v-if="chat.status >= 1" class="w-3.5 h-3.5 text-primary-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2 12l5 5L17 6"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 12l5 5L22 6" opacity="0.5"/>
                                                </svg>
                                                <svg v-else class="w-3.5 h-3.5 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2 12l5 5L17 6"/>
                                                </svg>
                                            </template>
                                        </div>
                                    </div>
                                    <img :src="avatarUrl(authUser?.profile_picture)"
                                        class="w-7 h-7 rounded-full object-cover flex-shrink-0 bg-gray-200 ring-1 ring-white dark:ring-gray-800" @error="onImgError" />
                                </div>

                                <!-- ── Message reçu ── -->
                                <div v-else class="flex items-end gap-2 mb-1">
                                    <img :src="avatarUrl(receiver.profile_picture)"
                                        class="w-7 h-7 rounded-full object-cover flex-shrink-0 bg-gray-200 ring-1 ring-white dark:ring-gray-800" @error="onImgError" />
                                    <div class="flex flex-col items-start max-w-[68%]">
                                        <div :class="[
                                            'rounded-2xl rounded-bl-md overflow-hidden max-w-full shadow-sm',
                                            chat.is_delete ? 'bg-gray-200 dark:bg-gray-700' : 'bg-white dark:bg-gray-700'
                                        ]">
                                            <!-- Image reçue -->
                                            <template v-if="!chat.is_delete && chat.file && isImage(chat.file)">
                                                <div class="relative group/img">
                                                    <img :src="chat.file_url" @click="openLightbox(chat.file_url, chat.file)"
                                                        class="max-w-[260px] max-h-[200px] w-full object-cover cursor-zoom-in block" />
                                                    <a :href="chat.file_url" :download="fileBaseName(chat.file)" target="_blank"
                                                        class="absolute top-2 right-2 p-1.5 rounded-xl bg-black/50 text-white opacity-0 group-hover/img:opacity-100 transition-opacity"
                                                        title="Télécharger" @click.stop>
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                        </svg>
                                                    </a>
                                                </div>
                                            </template>
                                            <!-- Fichier reçu -->
                                            <template v-else-if="!chat.is_delete && chat.file">
                                                <a :href="chat.file_url" :download="fileBaseName(chat.file)" target="_blank"
                                                    class="flex items-center gap-3 px-4 py-3 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors no-underline" @click.stop>
                                                    <div class="w-9 h-9 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center flex-shrink-0">
                                                        <component :is="fileIcon(chat.file)" class="w-5 h-5 text-primary-600 dark:text-primary-400" />
                                                    </div>
                                                    <div class="min-w-0">
                                                        <p class="text-sm font-semibold truncate max-w-[160px]">{{ fileBaseName(chat.file) }}</p>
                                                        <p class="text-[11px] text-gray-400 mt-0.5">Télécharger</p>
                                                    </div>
                                                </a>
                                            </template>
                                            <!-- Texte reçu -->
                                            <p v-if="!chat.is_delete && chat.message"
                                                :class="['px-4 py-2.5 text-sm text-gray-800 dark:text-gray-100 whitespace-pre-wrap break-words leading-relaxed', chat.file ? 'border-t border-gray-100 dark:border-gray-600' : '']">
                                                {{ chat.message }}
                                            </p>
                                            <p v-if="chat.is_delete" class="px-4 py-2.5 text-sm text-gray-400 italic">Message supprimé</p>
                                        </div>
                                        <span class="text-[10px] text-gray-400 mt-1.5 pl-1">{{ timeAgo(chat.created_date) }}</span>
                                    </div>
                                </div>

                            </template>

                            <!-- Indicateur "en train d'écrire" -->
                            <Transition enter-active-class="transition duration-300 ease-out" enter-from-class="opacity-0 translate-y-2" enter-to-class="opacity-100 translate-y-0" leave-active-class="transition duration-200 ease-in" leave-to-class="opacity-0">
                                <div v-if="peerTyping" class="flex items-end gap-2 mb-1">
                                    <img :src="avatarUrl(receiver.profile_picture)"
                                        class="w-7 h-7 rounded-full object-cover flex-shrink-0 bg-gray-200" @error="onImgError" />
                                    <div class="px-4 py-3.5 rounded-2xl rounded-bl-md bg-white dark:bg-gray-700 shadow-sm">
                                        <span class="flex gap-1.5 items-center">
                                            <span v-for="d in [0,175,350]" :key="d"
                                                class="w-2 h-2 bg-gray-300 dark:bg-gray-400 rounded-full animate-bounce"
                                                :style="`animation-delay:${d}ms`"/>
                                        </span>
                                    </div>
                                </div>
                            </Transition>
                        </div>

                        <!-- ── Zone de saisie ── -->
                        <div class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 px-4 py-3 flex-shrink-0">
                            <!-- Bandeau édition -->
                            <div v-if="editing" class="flex items-center gap-2 mb-2.5 px-3 py-2 bg-primary-50 dark:bg-primary-900/20 rounded-xl text-xs text-primary-700 dark:text-primary-300 border border-primary-200 dark:border-primary-800/50">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                <span class="font-medium">Modification du message</span>
                                <button class="ml-auto text-primary-400 hover:text-primary-600 transition-colors" @click="cancelEdit">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>

                            <!-- Prévisualisation fichier -->
                            <div v-if="pendingFile && !editing"
                                class="flex items-center gap-3 mb-2.5 px-3 py-2.5 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600">
                                <img v-if="pendingFileIsImage" :src="pendingFilePreview ?? ''"
                                    class="w-10 h-10 rounded-xl object-cover flex-shrink-0" />
                                <div v-else class="w-10 h-10 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center flex-shrink-0">
                                    <component :is="fileIcon(pendingFile.name)" class="w-5 h-5 text-primary-600 dark:text-primary-400" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate">{{ pendingFile.name }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ formatFileSize(pendingFile.size) }}</p>
                                </div>
                                <button class="p-1.5 rounded-xl text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors" @click="clearFile">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>

                            <!-- Barre de saisie -->
                            <div class="flex items-end gap-2 bg-gray-50 dark:bg-gray-700/50 rounded-2xl border border-gray-200 dark:border-gray-600 px-2 py-2">
                                <!-- Pièce jointe -->
                                <label class="p-1.5 rounded-xl text-gray-400 hover:text-primary-600 hover:bg-white dark:hover:bg-gray-600 cursor-pointer transition-all flex-shrink-0"
                                    title="Joindre un fichier">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                    </svg>
                                    <input ref="fileInput" type="file" class="hidden"
                                        accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.csv,.zip,.rar,.7z,.txt,.mp4,.mov,.avi,.mkv"
                                        @change="onFileSelected" />
                                </label>

                                <!-- Emoji -->
                                <div class="relative flex-shrink-0" ref="emojiWrapper">
                                    <button class="p-1.5 rounded-xl text-gray-400 hover:text-amber-500 hover:bg-white dark:hover:bg-gray-600 transition-all"
                                        title="Emojis" type="button" @click.stop="toggleEmojiPicker">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </button>
                                    <div v-show="showEmojiPicker" ref="emojiContainer"
                                        class="absolute bottom-12 left-0 z-50 shadow-2xl rounded-2xl overflow-hidden"
                                        @click.stop />
                                </div>

                                <!-- Textarea -->
                                <div class="flex-1">
                                    <textarea
                                        ref="textarea"
                                        v-model="message"
                                        rows="1"
                                        placeholder="Aa"
                                        class="w-full resize-none bg-transparent border-0 px-2 py-1.5 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none max-h-32 overflow-y-auto"
                                        @keydown.enter.exact.prevent="submitMessage"
                                        @input="onTyping(); autoResize()"
                                    />
                                </div>

                                <!-- Bouton envoyer -->
                                <button
                                    type="button"
                                    :class="[
                                        'p-2 rounded-xl transition-all flex-shrink-0',
                                        canSend
                                            ? 'bg-primary-600 hover:bg-primary-700 text-white shadow-sm'
                                            : 'text-gray-300 dark:text-gray-600 cursor-not-allowed'
                                    ]"
                                    :disabled="!canSend || sending"
                                    @click="submitMessage">
                                    <svg v-if="!sending" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                    </svg>
                                    <svg v-else class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- ══════════════════════════════════════════
                     COLONNE DROITE — Panneau d'informations
                ══════════════════════════════════════════ -->
                <Transition
                    enter-active-class="transition duration-300 ease-out"
                    enter-from-class="opacity-0 translate-x-4"
                    enter-to-class="opacity-100 translate-x-0"
                    leave-active-class="transition duration-200 ease-in"
                    leave-to-class="opacity-0 translate-x-4">
                    <aside v-if="showInfoPanel && receiver"
                        class="w-72 flex-shrink-0 bg-white dark:bg-gray-800 border-l border-gray-200 dark:border-gray-700 flex flex-col overflow-y-auto shadow-sm">

                        <!-- Profil contact -->
                        <div class="flex flex-col items-center px-5 pt-7 pb-5 border-b border-gray-100 dark:border-gray-700">
                            <div class="relative mb-3">
                                <img :src="avatarUrl(receiver.profile_picture)"
                                    class="w-20 h-20 rounded-2xl object-cover bg-gray-200 shadow-md" @error="onImgError" />
                                <span class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 rounded-full border-2 border-white dark:border-gray-800" />
                            </div>
                            <h3 class="font-bold text-gray-900 dark:text-white text-base">{{ receiver.last_name }} {{ receiver.name }}</h3>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ receiver.user_type_label || 'Utilisateur' }}</p>
                            <!-- Actions rapides -->
                            <div class="flex items-center gap-3 mt-4">
                                <div class="flex flex-col items-center gap-1">
                                    <button class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-500 dark:text-gray-400 hover:bg-primary-100 dark:hover:bg-primary-900/30 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                        </svg>
                                    </button>
                                    <span class="text-[10px] text-gray-400">Notif.</span>
                                </div>
                                <div class="flex flex-col items-center gap-1">
                                    <button class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-500 dark:text-gray-400 hover:bg-primary-100 dark:hover:bg-primary-900/30 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                                        </svg>
                                    </button>
                                    <span class="text-[10px] text-gray-400">Épingler</span>
                                </div>
                                <div class="flex flex-col items-center gap-1">
                                    <button class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-500 dark:text-gray-400 hover:bg-primary-100 dark:hover:bg-primary-900/30 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3"/>
                                        </svg>
                                    </button>
                                    <span class="text-[10px] text-gray-400">Paramètres</span>
                                </div>
                            </div>
                        </div>

                        <!-- Stats de la conversation -->
                        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                            <div class="grid grid-cols-2 gap-3">
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-3 text-center">
                                    <p class="text-lg font-bold text-gray-900 dark:text-white">{{ localChats.filter(c => !c.is_delete).length }}</p>
                                    <p class="text-[10px] text-gray-400 mt-0.5">Messages</p>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-3 text-center">
                                    <p class="text-lg font-bold text-gray-900 dark:text-white">{{ sharedImages.length }}</p>
                                    <p class="text-[10px] text-gray-400 mt-0.5">Photos</p>
                                </div>
                            </div>
                        </div>

                        <!-- Images partagées -->
                        <div v-if="sharedImages.length > 0" class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-xs font-semibold text-gray-700 dark:text-gray-300">Photos partagées</h4>
                                <span class="text-[10px] text-primary-600 dark:text-primary-400 font-medium cursor-pointer hover:underline">
                                    Voir tout
                                </span>
                            </div>
                            <div class="grid grid-cols-3 gap-1.5">
                                <div v-for="(img, i) in sharedImages.slice(0, 6)" :key="i"
                                    class="aspect-square rounded-xl overflow-hidden cursor-zoom-in bg-gray-100 dark:bg-gray-700"
                                    @click="openLightbox(img.file_url, img.file)">
                                    <img :src="img.file_url" class="w-full h-full object-cover hover:scale-110 transition-transform duration-300" />
                                </div>
                            </div>
                        </div>

                        <!-- Fichiers partagés -->
                        <div v-if="sharedFiles.length > 0" class="px-5 py-4">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-xs font-semibold text-gray-700 dark:text-gray-300">Fichiers partagés</h4>
                                <span class="text-[10px] text-primary-600 dark:text-primary-400 font-medium cursor-pointer hover:underline">
                                    Voir tout
                                </span>
                            </div>
                            <div class="space-y-2">
                                <a v-for="(file, i) in sharedFiles.slice(0, 4)" :key="i"
                                    :href="file.file_url" :download="fileBaseName(file.file)" target="_blank"
                                    class="flex items-center gap-2.5 p-2.5 rounded-xl bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors no-underline group">
                                    <div class="w-8 h-8 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center flex-shrink-0">
                                        <component :is="fileIcon(file.file)" class="w-4 h-4 text-primary-600 dark:text-primary-400" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-semibold text-gray-800 dark:text-gray-200 truncate">{{ fileBaseName(file.file) }}</p>
                                        <p class="text-[10px] text-gray-400 mt-0.5">{{ formatDate(file.created_date) }}</p>
                                    </div>
                                    <svg class="w-3.5 h-3.5 text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                </a>
                            </div>
                        </div>

                        <!-- Placeholder si aucune image/fichier -->
                        <div v-if="sharedImages.length === 0 && sharedFiles.length === 0" class="flex flex-col items-center justify-center py-10 px-5 text-center">
                            <div class="w-12 h-12 rounded-2xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <p class="text-xs text-gray-400 dark:text-gray-500">Aucun fichier partagé</p>
                        </div>
                    </aside>
                </Transition>

            </div>
        </div>
    </div>

    <!-- ── Lightbox image ── -->
    <Transition enter-active-class="transition duration-200" enter-from-class="opacity-0" leave-to-class="opacity-0">
        <div v-if="lightboxSrc"
            class="fixed inset-0 z-[9999] bg-black/90 flex items-center justify-center p-4"
            @click="lightboxSrc = null; lightboxFile = null">
            <img :src="lightboxSrc" class="max-w-full max-h-full rounded-xl shadow-2xl object-contain" @click.stop />
            <a :href="lightboxSrc" :download="lightboxFile ? fileBaseName(lightboxFile) : 'image'"
                target="_blank"
                class="absolute top-4 right-16 p-2.5 rounded-xl bg-white/10 text-white hover:bg-white/20 transition-colors"
                title="Télécharger" @click.stop>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
            </a>
            <button class="absolute top-4 right-4 p-2.5 rounded-xl bg-white/10 text-white hover:bg-white/20 transition-colors"
                @click="lightboxSrc = null; lightboxFile = null">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </Transition>
</template>

import { fmtDate } from '@/utils/dateFormat';
<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, nextTick, watch, h, defineComponent } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useDark } from '@vueuse/core';
import type { PageProps } from '@/types';
import AppSidebar from '@/Components/Layout/AppSidebar.vue';
import AppTopbar  from '@/Components/Layout/AppTopbar.vue';
import { Picker }  from 'emoji-mart';
import dataEmoji   from '@emoji-mart/data';

defineOptions({ layout: null });

const isDark = useDark();
const STORAGE_KEY       = 'sidebar_collapsed';
const sidebarCollapsed  = ref<boolean>(localStorage.getItem(STORAGE_KEY) === 'true');
const mobileSidebarOpen = ref(false);
watch(sidebarCollapsed, v => localStorage.setItem(STORAGE_KEY, String(v)));

const page     = usePage<PageProps>();
const props    = defineProps<{ contacts: any[]; chatContacts: any[]; receiver: any | null; chats: any[]; receiver_id: string | null }>();
const authUser = computed(() => page.props.auth?.user as any);
const csrf     = computed(() => (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? '');
const encodeId = (id: number | string) => btoa(String(id));

// ── Panneau info ───────────────────────────────────────────────────────────────
const showInfoPanel  = ref(false);
const activeTab      = ref<'inbox' | 'contacts'>('inbox');
const showSearchBar  = ref(false);
const msgSearch      = ref('');
const msgSearchInput = ref<HTMLInputElement>();

// ── Modal nouveau message ─────────────────────────────────────────────────────
const showNewMessageModal = ref(false);
const modalContactSearch  = ref('');

// ── Refs DOM ──────────────────────────────────────────────────────────────────
const messagesContainer = ref<HTMLElement>();
const textarea          = ref<HTMLTextAreaElement>();
const fileInput         = ref<HTMLInputElement>();
const emojiWrapper      = ref<HTMLElement>();
const emojiContainer    = ref<HTMLElement>();

// ── State messages ────────────────────────────────────────────────────────────
const localChats        = ref<any[]>([...props.chats]);
const localContacts     = ref<any[]>([...props.contacts]);
const localChatContacts = ref<any[]>([...props.chatContacts]);
const search        = ref('');
const message       = ref('');
const editing       = ref(false);
const editId        = ref<number | null>(null);
const sending       = ref(false);
const pollingActive = ref(false);
const peerTyping    = ref(false);
const lastMsgId     = ref<number>(props.chats.length ? (props.chats.at(-1)?.id ?? 0) : 0);
const lightboxSrc   = ref<string | null>(null);
const lightboxFile  = ref<string | null>(null);

// ── State fichier ─────────────────────────────────────────────────────────────
const pendingFile        = ref<File | null>(null);
const pendingFilePreview = ref<string | null>(null);
const pendingFileIsImage = ref(false);

// ── Emoji picker ──────────────────────────────────────────────────────────────
const showEmojiPicker = ref(false);
let   pickerInstance:  InstanceType<typeof Picker> | null = null;

// ── Timers polling ────────────────────────────────────────────────────────────
const pollOptions  = [
    { label: '3s',  value: 3000  },
    { label: '5s',  value: 5000  },
    { label: '10s', value: 10000 },
    { label: '20s', value: 20000 },
    { label: '30s', value: 30000 },
];
const pollInterval_  = ref(3000);
let msgTimer:         ReturnType<typeof setInterval> | null = null;
let contactsTimer:    ReturnType<typeof setInterval> | null = null;
let typingTimer:      ReturnType<typeof setInterval> | null = null;
let typingStopTimer:  ReturnType<typeof setTimeout>  | null = null;
let isTypingNow = false;

// ── Computed ──────────────────────────────────────────────────────────────────
const canSend = computed(() => (message.value.trim() !== '' || pendingFile.value !== null) && !sending.value);

const totalUnread = computed(() => localContacts.value.reduce((sum, c) => sum + (c.countMessage || 0), 0));

const sharedImages = computed(() =>
    localChats.value.filter(c => !c.is_delete && c.file && isImage(c.file))
);
const sharedFiles = computed(() =>
    localChats.value.filter(c => !c.is_delete && c.file && !isImage(c.file))
);

// Recherche dans la conversation
const msgSearchResults = computed(() => {
    if (!msgSearch.value.trim()) return [];
    const q = msgSearch.value.toLowerCase();
    return localChats.value.filter(c => !c.is_delete && c.message?.toLowerCase().includes(q));
});

// Messages affichés (filtrés par recherche si active)
const displayedChats = computed(() => {
    if (msgSearch.value.trim() && msgSearchResults.value.length > 0) return msgSearchResults.value;
    return localChats.value;
});

// Contacts suggérés filtrés par la barre de recherche
const filteredChatContacts = computed(() => {
    if (!search.value) return localChatContacts.value;
    const q = search.value.toLowerCase();
    return localChatContacts.value.filter(c =>
        c.name?.toLowerCase().includes(q) ||
        c.role?.toLowerCase().includes(q) ||
        c.class_name?.toLowerCase().includes(q)
    );
});

// Regroupement par classe si les contacts ont un class_name
const hasClassGroups = computed(() =>
    filteredChatContacts.value.some(c => c.class_name)
);

const contactGroups = computed(() => {
    if (!hasClassGroups.value) return [];
    const groups: Record<string, any[]> = {};
    for (const c of filteredChatContacts.value) {
        const key = c.class_name ?? 'Autres';
        if (!groups[key]) groups[key] = [];
        groups[key].push(c);
    }
    return Object.entries(groups).map(([label, contacts]) => ({ label, contacts }));
});

// ── Filtre modal nouveau message ──────────────────────────────────────────────
const filteredModalContacts = computed(() => {
    if (!modalContactSearch.value) return localChatContacts.value;
    const q = modalContactSearch.value.toLowerCase();
    return localChatContacts.value.filter(c =>
        c.name?.toLowerCase().includes(q) ||
        c.role?.toLowerCase().includes(q) ||
        c.class_name?.toLowerCase().includes(q)
    );
});

const modalContactGroups = computed(() => {
    if (!filteredModalContacts.value.some((c: any) => c.class_name)) return [];
    const groups: Record<string, any[]> = {};
    for (const c of filteredModalContacts.value) {
        const key = (c as any).class_name ?? 'Autres';
        if (!groups[key]) groups[key] = [];
        groups[key].push(c);
    }
    return Object.entries(groups).map(([label, contacts]) => ({ label, contacts }));
});

// ── Avatar ────────────────────────────────────────────────────────────────────
const DEFAULT_AVATAR = '/upload/default.jpg';
const avatarUrl = (pic?: string | null) => {
    if (!pic) return DEFAULT_AVATAR;
    if (pic.startsWith('http') || pic.startsWith('/')) return pic;
    return `/upload/profile/${pic}`;
};
const onImgError = (e: Event) => {
    const img = e.target as HTMLImageElement;
    if (!img.src.endsWith(DEFAULT_AVATAR)) img.src = DEFAULT_AVATAR;
};

// ── Détection type de fichier ─────────────────────────────────────────────────
const IMAGE_EXTS = new Set(['jpg','jpeg','png','gif','webp','bmp','svg','avif']);

const isImage = (filename: string) =>
    IMAGE_EXTS.has((filename.split('.').pop() ?? '').toLowerCase());

const fileIcon = (filename: string) => {
    const ext = (filename.split('.').pop() ?? '').toLowerCase();
    if (ext === 'pdf') return defineComponent({
        render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '1.5', d: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' })
        ])
    });
    if (['zip','rar','7z','tar','gz'].includes(ext)) return defineComponent({
        render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '1.5', d: 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4' })
        ])
    });
    if (['xls','xlsx','csv'].includes(ext)) return defineComponent({
        render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '1.5', d: 'M3 10h18M3 14h18M10 3v18M14 3v18M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z' })
        ])
    });
    if (['mp4','mov','avi','mkv','webm'].includes(ext)) return defineComponent({
        render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '1.5', d: 'M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z' })
        ])
    });
    return defineComponent({
        render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '1.5', d: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' })
        ])
    });
};

const fileBaseName = (filename: string) => {
    const match = filename.match(/chat_file[\w]+\.(.+)$/i);
    return match ? `fichier.${match[1]}` : filename;
};

const formatFileSize = (bytes: number) => {
    if (bytes < 1024) return `${bytes} o`;
    if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} Ko`;
    return `${(bytes / 1024 / 1024).toFixed(1)} Mo`;
};

// ── Gestion fichier ───────────────────────────────────────────────────────────
const onFileSelected = (e: Event) => {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (!file) return;
    pendingFile.value        = file;
    pendingFileIsImage.value = file.type.startsWith('image/');
    if (pendingFileIsImage.value) {
        const reader = new FileReader();
        reader.onload = ev => { pendingFilePreview.value = ev.target?.result as string; };
        reader.readAsDataURL(file);
    } else {
        pendingFilePreview.value = null;
    }
};
const clearFile = () => {
    pendingFile.value = null; pendingFilePreview.value = null; pendingFileIsImage.value = false;
    if (fileInput.value) fileInput.value.value = '';
};
const openLightbox = (src: string, file?: string) => {
    lightboxSrc.value  = src;
    lightboxFile.value = file ?? null;
};

// ── Emoji Picker ──────────────────────────────────────────────────────────────
const mountPicker = () => {
    if (!emojiContainer.value) return;
    if (pickerInstance) {
        emojiContainer.value.innerHTML = '';
        pickerInstance = null;
    }
    pickerInstance = new Picker({
        data: dataEmoji,
        theme: isDark.value ? 'dark' : 'light',
        locale: 'fr',
        onEmojiSelect: (emoji: any) => {
            const native = emoji.native ?? '';
            if (!native) return;
            if (textarea.value) {
                const start = textarea.value.selectionStart ?? message.value.length;
                const end   = textarea.value.selectionEnd   ?? message.value.length;
                message.value = message.value.slice(0, start) + native + message.value.slice(end);
                nextTick(() => {
                    const pos = start + [...native].length;
                    textarea.value?.setSelectionRange(pos, pos);
                    textarea.value?.focus();
                });
            } else {
                message.value += native;
            }
        },
    });
    emojiContainer.value.appendChild(pickerInstance as unknown as HTMLElement);
};

const toggleEmojiPicker = () => {
    showEmojiPicker.value = !showEmojiPicker.value;
    if (showEmojiPicker.value) nextTick(mountPicker);
};

const onDocClick = (e: MouseEvent) => {
    if (showEmojiPicker.value && emojiWrapper.value && !emojiWrapper.value.contains(e.target as Node)) {
        showEmojiPicker.value = false;
    }
};

// ── Contacts ──────────────────────────────────────────────────────────────────
const filteredContacts = computed(() =>
    localContacts.value.filter(c =>
        !search.value ||
        c.name?.toLowerCase().includes(search.value.toLowerCase()) ||
        c.message?.toLowerCase().includes(search.value.toLowerCase())
    )
);
const isActiveContact = (c: any) => {
    if (!props.receiver_id) return false;
    try { return atob(props.receiver_id) === String(c.user_id); } catch { return false; }
};
const isOnline = (c: any) =>
    c.last_login ? (Date.now() - new Date(c.last_login).getTime()) / 60000 <= 5 : false;

const isLastSentMsg = (chat: any) => {
    if (!authUser.value) return false;
    const sent = localChats.value.filter(c => c.sender_id === authUser.value.id && !c.is_delete);
    return sent.length > 0 && sent.at(-1)?.id === chat.id;
};

// Timestamp du dernier message d'un contact (formaté)
const contactTime = (contact: any) => {
    if (!contact.created_date) return '';
    const d    = new Date(contact.created_date);
    const now  = new Date();
    const diff = (now.getTime() - d.getTime()) / 1000;
    if (diff < 60)    return 'Maintenant';
    if (diff < 3600)  return `${Math.floor(diff / 60)} min`;
    if (diff < 86400) return d.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
    const yesterday = new Date(now); yesterday.setDate(now.getDate() - 1);
    if (d.toDateString() === yesterday.toDateString()) return 'Hier';
    return d.toLocaleDateString('fr-FR', { day: '2-digit', month: 'short' });
};

// ── Dates / heure ─────────────────────────────────────────────────────────────
const showDateSeparator = (i: number) => {
    if (i === 0) return true;
    return new Date(localChats.value[i - 1].created_date).toDateString()
        !== new Date(localChats.value[i].created_date).toDateString();
};
const formatDate = fmtDate;
const timeAgo = (date: string) => {
    const diff = (Date.now() - new Date(date).getTime()) / 1000;
    if (diff < 60)    return "À l'instant";
    if (diff < 3600)  return `Il y a ${Math.floor(diff / 60)} min`;
    if (diff < 86400) return `Il y a ${Math.floor(diff / 3600)} h`;
    return new Date(date).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short' });
};

// ── Scroll ────────────────────────────────────────────────────────────────────
const scrollToBottom = (smooth = false) => {
    nextTick(() => {
        if (!messagesContainer.value) return;
        messagesContainer.value.scrollTo({ top: messagesContainer.value.scrollHeight, behavior: smooth ? 'smooth' : 'instant' });
    });
};

// ── Polling contacts ──────────────────────────────────────────────────────────
const pollContacts = async () => {
    try {
        const res = await fetch('/chat/contacts/poll',
            { headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrf.value } });
        if (!res.ok) return;
        const data = await res.json();
        if (data.contacts)     localContacts.value     = data.contacts;
        if (data.chatContacts) localChatContacts.value = data.chatContacts;
    } catch { /* silencieux */ }
};

// ── Polling messages ──────────────────────────────────────────────────────────
const pollMessages = async () => {
    if (!props.receiver) return;
    try {
        const res = await fetch(`/chat/messages/poll?receiver_id=${props.receiver.id}&last_id=${lastMsgId.value}`,
            { headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrf.value } });
        if (!res.ok) { pollingActive.value = false; return; }
        pollingActive.value = true;
        const data = await res.json();

        if (data.messages?.length) {
            const atBottom = messagesContainer.value
                ? messagesContainer.value.scrollHeight - messagesContainer.value.scrollTop - messagesContainer.value.clientHeight < 80
                : true;
            data.messages.forEach((m: any) => {
                const existing = localChats.value.find(c => c.id === m.id);
                if (existing) existing.status = m.status;
                else { localChats.value.push(m); lastMsgId.value = m.id; }
            });
            if (atBottom) scrollToBottom(true);
        }
        if (data.updated_messages?.length) {
            data.updated_messages.forEach((u: any) => {
                const idx = localChats.value.findIndex(c => c.id === u.id);
                if (idx !== -1) localChats.value[idx].is_delete = 1;
            });
        }
        if (data.read_up_to) {
            localChats.value.forEach(c => { if (c.sender_id === authUser.value?.id && c.id <= data.read_up_to) c.status = 1; });
        }
        if (data.contacts) localContacts.value = data.contacts;
    } catch { pollingActive.value = false; }
};

const pollTyping = async () => {
    if (!props.receiver) return;
    try {
        const res = await fetch(`/chat/typing/check?receiver_id=${props.receiver.id}`,
            { headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrf.value } });
        if (!res.ok) return;
        const data = await res.json();
        peerTyping.value = data.is_typing ?? false;
        if (data.last_msg_id && data.last_msg_read) {
            const m = localChats.value.find(c => c.id === data.last_msg_id);
            if (m) m.status = 1;
        }
        if (peerTyping.value) scrollToBottom(true);
    } catch { /* silencieux */ }
};

const startPolling = () => {
    stopPolling();
    pollMessages(); pollTyping();
    msgTimer    = setInterval(pollMessages, pollInterval_.value);
    typingTimer = setInterval(pollTyping, 2000);
};
const stopPolling = () => {
    if (msgTimer)      { clearInterval(msgTimer);      msgTimer      = null; }
    if (typingTimer)   { clearInterval(typingTimer);   typingTimer   = null; }
    pollingActive.value = false;
};
const changePollInterval = (ms: number) => { pollInterval_.value = ms; if (props.receiver) startPolling(); };

// ── Signal typing ─────────────────────────────────────────────────────────────
const sendTyping = async (val: boolean) => {
    if (!props.receiver) return;
    try {
        await fetch('/chat/typing', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-CSRF-TOKEN': csrf.value, Accept: 'application/json' },
            body: new URLSearchParams({ receiver_id: String(props.receiver.id), is_typing: val ? '1' : '0' }).toString(),
        });
    } catch { /* silencieux */ }
};
const onTyping = () => {
    if (!isTypingNow) { isTypingNow = true; sendTyping(true); }
    if (typingStopTimer) clearTimeout(typingStopTimer);
    typingStopTimer = setTimeout(() => { isTypingNow = false; sendTyping(false); }, 2500);
};

// ── Envoi message ─────────────────────────────────────────────────────────────
const submitMessage = async () => {
    if (!canSend.value || !props.receiver) return;
    if (typingStopTimer) clearTimeout(typingStopTimer);
    isTypingNow = false;
    sendTyping(false);
    showEmojiPicker.value = false;
    sending.value = true;

    try {
        const url      = editing.value ? `/chat/update-ajax/${editId.value}` : '/chat/send-ajax';
        const formData = new FormData();
        formData.append('receiver_id', String(props.receiver.id));
        if (message.value.trim()) formData.append('message', message.value.trim());
        if (pendingFile.value && !editing.value) formData.append('file', pendingFile.value);

        const res  = await fetch(url, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf.value, Accept: 'application/json' },
            body: formData,
        });
        const data = await res.json();

        if (data.success) {
            if (editing.value) {
                const idx = localChats.value.findIndex(c => c.id === editId.value);
                if (idx !== -1) localChats.value[idx].message = message.value.trim();
                cancelEdit();
            } else {
                if (data.message) {
                    localChats.value.push(data.message);
                    lastMsgId.value = data.message.id;
                }
                message.value = '';
                clearFile();
                if (textarea.value) textarea.value.style.height = 'auto';
                scrollToBottom(true);
            }
        }
    } catch (e) { console.error(e); }
    finally { sending.value = false; }
};

const deleteMsg = async (id: number) => {
    try {
        const res  = await fetch(`/chat/delete-ajax/${id}`, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf.value, Accept: 'application/json' } });
        const data = await res.json();
        if (data.success) { const i = localChats.value.findIndex(c => c.id === id); if (i !== -1) localChats.value[i].is_delete = 1; }
    } catch { /* silencieux */ }
};

const startEdit = (chat: any) => {
    editing.value = true; editId.value = chat.id; message.value = chat.message;
    nextTick(() => textarea.value?.focus());
};
const cancelEdit = () => { editing.value = false; editId.value = null; message.value = ''; };
const autoResize = () => {
    if (!textarea.value) return;
    textarea.value.style.height = 'auto';
    textarea.value.style.height = Math.min(textarea.value.scrollHeight, 128) + 'px';
};

// ── Watch recherche ───────────────────────────────────────────────────────────
watch(showSearchBar, (val) => {
    if (val) nextTick(() => msgSearchInput.value?.focus());
    else msgSearch.value = '';
});

// ── Lifecycle ─────────────────────────────────────────────────────────────────
onMounted(() => {
    scrollToBottom();
    pollContacts();
    contactsTimer = setInterval(pollContacts, 5000);
    if (props.receiver) startPolling();
    document.addEventListener('click', onDocClick);
});
onUnmounted(() => {
    stopPolling();
    if (contactsTimer)   { clearInterval(contactsTimer); contactsTimer = null; }
    if (typingStopTimer) clearTimeout(typingStopTimer);
    if (isTypingNow) sendTyping(false);
    document.removeEventListener('click', onDocClick);
    if (emojiContainer.value) emojiContainer.value.innerHTML = '';
    pickerInstance = null;
});
</script>
