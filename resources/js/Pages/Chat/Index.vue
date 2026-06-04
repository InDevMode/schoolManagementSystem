<template>
    <div :class="['flex h-screen overflow-hidden transition-colors duration-300', isDark ? 'dark' : '', 'bg-gray-50 dark:bg-gray-900']">

        <AppSidebar :collapsed="sidebarCollapsed" :mobile-open="mobileSidebarOpen"
            @toggle="sidebarCollapsed = !sidebarCollapsed" @close="mobileSidebarOpen = false" />

        <div :class="['flex flex-col flex-1 min-w-0 transition-all duration-300 overflow-hidden', sidebarCollapsed ? 'lg:ml-[72px]' : 'lg:ml-64']">
            <AppTopbar @open-mobile="mobileSidebarOpen = true" />

            <div class="flex flex-1 overflow-hidden">

                <!-- ── Sidebar contacts ── -->
                <aside class="w-80 flex-shrink-0 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 flex flex-col">
                    <div class="px-4 pt-5 pb-3 border-b border-gray-100 dark:border-gray-700">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Messages</h2>
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input v-model="search" type="text" placeholder="Rechercher..."
                                class="w-full pl-9 pr-4 py-2 text-sm bg-gray-100 dark:bg-gray-700 border-0 rounded-full text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                        </div>
                    </div>
                    <div class="flex-1 overflow-y-auto py-2">
                        <template v-if="filteredContacts.length">
                            <a v-for="contact in filteredContacts" :key="contact.user_id"
                                :href="`/chat?receiver_id=${encodeId(contact.user_id)}`"
                                :class="['flex items-center gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors cursor-pointer', isActiveContact(contact) ? 'bg-primary-50 dark:bg-primary-900/20' : '']">
                                <div class="relative flex-shrink-0">
                                    <img :src="avatarUrl(contact.sender_profile_picture)" :alt="contact.name"
                                        class="w-12 h-12 rounded-full object-cover bg-gray-200" @error="onImgError" />
                                    <span :class="['absolute bottom-0 right-0 w-3 h-3 rounded-full border-2 border-white dark:border-gray-800', isOnline(contact) ? 'bg-green-500' : 'bg-gray-300']" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <p :class="['text-sm font-semibold truncate', isActiveContact(contact) ? 'text-primary-700 dark:text-primary-300' : 'text-gray-900 dark:text-white']">
                                            {{ contact.name }}
                                        </p>
                                        <span v-if="contact.countMessage > 0"
                                            class="ml-2 flex-shrink-0 min-w-[20px] h-5 px-1 bg-primary-600 text-white text-[10px] font-bold rounded-full flex items-center justify-center">
                                            {{ contact.countMessage > 9 ? '9+' : contact.countMessage }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5">
                                        {{ contact.is_delete ? 'Message supprimé' : contact.message }}
                                    </p>
                                </div>
                            </a>
                        </template>
                        <p v-else class="text-center text-sm text-gray-400 dark:text-gray-500 mt-8">Aucun contact</p>
                    </div>
                    <!-- Panneau intervalle polling -->
                    <div class="border-t border-gray-100 dark:border-gray-700 px-4 py-3">
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-2">Actualisation auto</p>
                        <div class="flex flex-wrap gap-1.5">
                            <button v-for="opt in pollOptions" :key="opt.value"
                                :class="['px-2.5 py-1 text-xs rounded-full font-medium transition-colors',
                                    pollInterval_ === opt.value ? 'bg-primary-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600']"
                                @click="changePollInterval(opt.value)">{{ opt.label }}</button>
                        </div>
                    </div>
                </aside>

                <!-- ── Zone messages ── -->
                <div class="flex-1 flex flex-col overflow-hidden">

                    <!-- Aucune conversation -->
                    <div v-if="!receiver" class="flex-1 flex flex-col items-center justify-center text-gray-400 dark:text-gray-500 gap-4">
                        <svg class="w-20 h-20 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <p class="text-lg font-medium">Sélectionnez une conversation</p>
                        <p class="text-sm">Choisissez un contact pour commencer à discuter</p>
                    </div>

                    <template v-else>

                        <!-- Header -->
                        <div class="flex items-center gap-3 px-5 py-3 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
                            <div class="relative">
                                <img :src="avatarUrl(receiver.profile_picture)" :alt="receiver.name"
                                    class="w-10 h-10 rounded-full object-cover bg-gray-200" @error="onImgError" />
                                <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 rounded-full border-2 border-white dark:border-gray-800" />
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white">{{ receiver.last_name }} {{ receiver.name }}</p>
                                <Transition enter-active-class="transition duration-200" enter-from-class="opacity-0" leave-active-class="transition duration-200" leave-to-class="opacity-0">
                                    <p v-if="peerTyping" class="text-xs text-primary-500 flex items-center gap-1">
                                        <span class="flex gap-0.5">
                                            <span v-for="d in [0,150,300]" :key="d" class="w-1 h-1 bg-primary-500 rounded-full animate-bounce" :style="`animation-delay:${d}ms`"/>
                                        </span>
                                        en train d'écrire…
                                    </p>
                                    <p v-else class="text-xs text-green-500">En ligne</p>
                                </Transition>
                            </div>
                            <div class="ml-auto flex items-center gap-1.5 text-xs text-gray-400">
                                <span :class="['w-1.5 h-1.5 rounded-full', pollingActive ? 'bg-green-400 animate-pulse' : 'bg-gray-300']" />
                                <span>{{ pollingActive ? 'En direct' : 'Hors ligne' }}</span>
                            </div>
                        </div>

                        <!-- Zone messages -->
                        <div ref="messagesContainer" class="flex-1 overflow-y-auto px-5 py-4 space-y-1 bg-gray-50 dark:bg-gray-900">
                            <template v-for="(chat, index) in localChats" :key="chat.id">

                                <!-- Séparateur date -->
                                <div v-if="showDateSeparator(index)" class="flex items-center gap-3 my-4">
                                    <div class="flex-1 h-px bg-gray-200 dark:bg-gray-700" />
                                    <span class="text-xs text-gray-400 dark:text-gray-500 font-medium px-2">{{ formatDate(chat.created_date) }}</span>
                                    <div class="flex-1 h-px bg-gray-200 dark:bg-gray-700" />
                                </div>

                                <!-- Message envoyé (moi) -->
                                <div v-if="authUser && chat.sender_id === authUser.id" class="group flex justify-end items-end gap-2 mb-1">
                                    <div class="flex flex-col items-end max-w-[65%]">
                                        <!-- Actions hover -->
                                        <div v-if="!chat.is_delete" class="flex items-center gap-2 mb-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <button v-if="!chat.file" class="text-xs text-gray-400 hover:text-primary-600" @click="startEdit(chat)">Modifier</button>
                                            <button class="text-xs text-gray-400 hover:text-red-500" @click="deleteMsg(chat.id)">Supprimer</button>
                                        </div>
                                        <!-- Bulle envoyée -->
                                        <div :class="['rounded-2xl rounded-br-sm overflow-hidden max-w-full',
                                            chat.is_delete ? 'bg-gray-200 dark:bg-gray-700' : 'bg-primary-600']">
                                            <!-- Image envoyée -->
                                            <template v-if="!chat.is_delete && chat.file && isImage(chat.file)">
                                                <div class="relative group/img">
                                                    <img :src="chat.file_url" @click="openLightbox(chat.file_url, chat.file)"
                                                        class="max-w-[240px] max-h-[200px] w-full object-cover cursor-zoom-in block" />
                                                    <!-- Bouton télécharger sur hover -->
                                                    <a :href="chat.file_url" :download="fileBaseName(chat.file)" target="_blank"
                                                        class="absolute top-2 right-2 p-1.5 rounded-lg bg-black/50 text-white opacity-0 group-hover/img:opacity-100 transition-opacity"
                                                        title="Télécharger" @click.stop>
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                        </svg>
                                                    </a>
                                                </div>
                                            </template>
                                            <!-- Fichier envoyé -->
                                            <template v-else-if="!chat.is_delete && chat.file">
                                                <a :href="chat.file_url" :download="fileBaseName(chat.file)" target="_blank"
                                                    class="flex items-center gap-3 px-4 py-3 text-white hover:bg-primary-700 transition-colors no-underline"
                                                    @click.stop>
                                                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center flex-shrink-0">
                                                        <component :is="fileIcon(chat.file)" class="w-5 h-5 text-white" />
                                                    </div>
                                                    <div class="min-w-0 flex-1">
                                                        <p class="text-sm font-semibold truncate max-w-[160px]">{{ fileBaseName(chat.file) }}</p>
                                                        <p class="text-[11px] opacity-70 mt-0.5 flex items-center gap-1">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                            Télécharger
                                                        </p>
                                                    </div>
                                                </a>
                                            </template>
                                            <!-- Texte -->
                                            <p v-if="!chat.is_delete && chat.message"
                                                :class="['px-4 py-2.5 text-sm text-white whitespace-pre-wrap break-words',
                                                    chat.file ? 'border-t border-white/20 pt-2' : '']"
                                                style="font-family: ui-sans-serif, system-ui, -apple-system, 'Segoe UI Emoji', 'Apple Color Emoji', 'Noto Color Emoji', sans-serif;">
                                                {{ chat.message }}
                                            </p>
                                            <p v-if="chat.is_delete" class="px-4 py-2.5 text-sm text-gray-500 italic">
                                                Message supprimé
                                            </p>
                                        </div>
                                        <!-- Heure + check lu -->
                                        <div class="flex items-center gap-1 mt-1 pr-1">
                                            <span class="text-[10px] text-gray-400">{{ timeAgo(chat.created_date) }}</span>
                                            <template v-if="!chat.is_delete && isLastSentMsg(chat)">
                                                <!-- Double check bleu = lu -->
                                                <svg v-if="chat.status >= 1" class="w-4 h-4 text-blue-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2 12l5 5L17 6"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 12l5 5L22 6" opacity="0.5"/>
                                                </svg>
                                                <!-- Simple check gris = envoyé -->
                                                <svg v-else class="w-4 h-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2 12l5 5L17 6"/>
                                                </svg>
                                            </template>
                                        </div>
                                    </div>
                                    <img :src="avatarUrl(authUser?.profile_picture)"
                                        class="w-7 h-7 rounded-full object-cover flex-shrink-0 bg-gray-200" @error="onImgError" />
                                </div>

                                <!-- Message reçu -->
                                <div v-else class="flex items-end gap-2 mb-1">
                                    <img :src="avatarUrl(receiver.profile_picture)"
                                        class="w-7 h-7 rounded-full object-cover flex-shrink-0 bg-gray-200" @error="onImgError" />
                                    <div class="flex flex-col items-start max-w-[65%]">
                                        <div :class="['rounded-2xl rounded-bl-sm overflow-hidden max-w-full',
                                            chat.is_delete ? 'bg-gray-200 dark:bg-gray-700' : 'bg-white dark:bg-gray-700 shadow-sm']">
                                            <!-- Image reçue -->
                                            <template v-if="!chat.is_delete && chat.file && isImage(chat.file)">
                                                <div class="relative group/img">
                                                    <img :src="chat.file_url" @click="openLightbox(chat.file_url, chat.file)"
                                                        class="max-w-[240px] max-h-[200px] w-full object-cover cursor-zoom-in block" />
                                                    <a :href="chat.file_url" :download="fileBaseName(chat.file)" target="_blank"
                                                        class="absolute top-2 right-2 p-1.5 rounded-lg bg-black/50 text-white opacity-0 group-hover/img:opacity-100 transition-opacity"
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
                                                    class="flex items-center gap-3 px-4 py-3 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors no-underline"
                                                    @click.stop>
                                                    <div class="w-10 h-10 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center flex-shrink-0">
                                                        <component :is="fileIcon(chat.file)" class="w-5 h-5 text-primary-600 dark:text-primary-400" />
                                                    </div>
                                                    <div class="min-w-0 flex-1">
                                                        <p class="text-sm font-semibold truncate max-w-[160px]">{{ fileBaseName(chat.file) }}</p>
                                                        <p class="text-[11px] text-gray-400 mt-0.5 flex items-center gap-1">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                            Télécharger
                                                        </p>
                                                    </div>
                                                </a>
                                            </template>
                                            <!-- Texte reçu -->
                                            <p v-if="!chat.is_delete && chat.message"
                                                :class="['px-4 py-2.5 text-sm text-gray-900 dark:text-white whitespace-pre-wrap break-words',
                                                    chat.file ? 'border-t border-gray-100 dark:border-gray-600 pt-2' : '']"
                                                style="font-family: ui-sans-serif, system-ui, -apple-system, 'Segoe UI Emoji', 'Apple Color Emoji', 'Noto Color Emoji', sans-serif;">
                                                {{ chat.message }}
                                            </p>
                                            <p v-if="chat.is_delete" class="px-4 py-2.5 text-sm text-gray-500 italic">Message supprimé</p>
                                        </div>
                                        <span class="text-[10px] text-gray-400 mt-1 pl-1">{{ timeAgo(chat.created_date) }}</span>
                                    </div>
                                </div>

                            </template>

                            <!-- Bulles "en train d'écrire" -->
                            <Transition enter-active-class="transition duration-300 ease-out" enter-from-class="opacity-0 translate-y-2"
                                enter-to-class="opacity-100 translate-y-0" leave-active-class="transition duration-200 ease-in" leave-to-class="opacity-0">
                                <div v-if="peerTyping" class="flex items-end gap-2 mb-1">
                                    <img :src="avatarUrl(receiver.profile_picture)"
                                        class="w-7 h-7 rounded-full object-cover flex-shrink-0 bg-gray-200" @error="onImgError" />
                                    <div class="px-4 py-3 rounded-2xl rounded-bl-sm bg-white dark:bg-gray-700 shadow-sm">
                                        <span class="flex gap-1.5 items-center">
                                            <span v-for="d in [0,175,350]" :key="d"
                                                class="w-2 h-2 bg-gray-400 dark:bg-gray-300 rounded-full animate-bounce"
                                                :style="`animation-delay:${d}ms`"/>
                                        </span>
                                    </div>
                                </div>
                            </Transition>
                        </div>

                        <!-- Zone de saisie -->
                        <div class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 px-4 py-3 flex-shrink-0">

                            <!-- Bandeau édition -->
                            <div v-if="editing" class="flex items-center gap-2 mb-2 px-3 py-1.5 bg-primary-50 dark:bg-primary-900/20 rounded-lg text-xs text-primary-700 dark:text-primary-300">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                <span>Modification du message</span>
                                <button class="ml-auto text-gray-400 hover:text-gray-600" @click="cancelEdit">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>

                            <!-- Prévisualisation fichier -->
                            <div v-if="pendingFile && !editing"
                                class="flex items-center gap-3 mb-2 px-3 py-2 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600">
                                <img v-if="pendingFileIsImage" :src="pendingFilePreview ?? ''"
                                    class="w-12 h-12 rounded-lg object-cover flex-shrink-0" />
                                <div v-else class="w-12 h-12 rounded-lg bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center flex-shrink-0">
                                    <component :is="fileIcon(pendingFile.name)" class="w-6 h-6 text-primary-600 dark:text-primary-400" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate">{{ pendingFile.name }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ formatFileSize(pendingFile.size) }}</p>
                                </div>
                                <button class="p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors flex-shrink-0" @click="clearFile">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>

                            <!-- Barre de saisie -->
                            <div class="flex items-end gap-1.5">

                                <!-- Bouton pièce jointe -->
                                <label class="p-2 rounded-full text-gray-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 cursor-pointer transition-colors flex-shrink-0"
                                    title="Joindre un fichier">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                    </svg>
                                    <input ref="fileInput" type="file" class="hidden"
                                        accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.csv,.zip,.rar,.7z,.txt,.mp4,.mov,.avi,.mkv"
                                        @change="onFileSelected" />
                                </label>

                                <!-- Bouton emoji -->
                                <div class="relative flex-shrink-0" ref="emojiWrapper">
                                    <button class="p-2 rounded-full text-gray-400 hover:text-yellow-500 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 transition-colors"
                                        title="Emojis" type="button" @click.stop="toggleEmojiPicker">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </button>
                                    <!-- Container du picker — monté programmatiquement -->
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
                                        placeholder="Écrivez un message..."
                                        class="w-full resize-none bg-gray-100 dark:bg-gray-700 border-0 rounded-2xl px-4 py-2.5 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 max-h-32 overflow-y-auto"
                                        style="font-family: ui-sans-serif, system-ui, -apple-system, 'Segoe UI Emoji', 'Apple Color Emoji', 'Noto Color Emoji', sans-serif;"
                                        @keydown.enter.exact.prevent="submitMessage"
                                        @input="onTyping(); autoResize()"
                                    />
                                </div>

                                <!-- Bouton envoyer -->
                                <button
                                    type="button"
                                    :class="['p-2.5 rounded-full transition-all flex-shrink-0',
                                        canSend ? 'bg-primary-600 hover:bg-primary-700 text-white shadow-sm shadow-primary-200 dark:shadow-primary-900/40' : 'bg-gray-100 dark:bg-gray-700 text-gray-400 cursor-not-allowed']"
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
            </div>
        </div>
    </div>

    <!-- Lightbox image -->
    <Transition enter-active-class="transition duration-200" enter-from-class="opacity-0" leave-to-class="opacity-0">
        <div v-if="lightboxSrc"
            class="fixed inset-0 z-[9999] bg-black/90 flex items-center justify-center p-4"
            @click="lightboxSrc = null; lightboxFile = null">
            <img :src="lightboxSrc" class="max-w-full max-h-full rounded-xl shadow-2xl object-contain" @click.stop />
            <!-- Télécharger -->
            <a :href="lightboxSrc" :download="lightboxFile ? fileBaseName(lightboxFile) : 'image'"
                target="_blank"
                class="absolute top-4 right-16 p-2 rounded-full bg-white/10 text-white hover:bg-white/20 transition-colors"
                title="Télécharger" @click.stop>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
            </a>
            <!-- Fermer -->
            <button class="absolute top-4 right-4 p-2 rounded-full bg-white/10 text-white hover:bg-white/20 transition-colors"
                @click="lightboxSrc = null; lightboxFile = null">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </Transition>
</template>

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
const props    = defineProps<{ contacts: any[]; receiver: any | null; chats: any[]; receiver_id: string | null }>();
const authUser = computed(() => page.props.auth?.user as any);
const csrf     = computed(() => (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? '');
const encodeId = (id: number | string) => btoa(String(id));

// ── Refs DOM ──────────────────────────────────────────────────────────────────
const messagesContainer = ref<HTMLElement>();
const textarea          = ref<HTMLTextAreaElement>();
const fileInput         = ref<HTMLInputElement>();
const emojiWrapper      = ref<HTMLElement>();     // wrapper bouton + container
const emojiContainer    = ref<HTMLElement>();     // div où monter le Picker

// ── State messages ────────────────────────────────────────────────────────────
const localChats    = ref<any[]>([...props.chats]);
const localContacts = ref<any[]>([...props.contacts]);
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

// ── Emoji picker (instance Picker emoji-mart) ─────────────────────────────────
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

// ── canSend ───────────────────────────────────────────────────────────────────
const canSend = computed(() => (message.value.trim() !== '' || pendingFile.value !== null) && !sending.value);

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

// Retourne un composant SVG selon l'extension
const fileIcon = (filename: string) => {
    const ext = (filename.split('.').pop() ?? '').toLowerCase();
    // Icone PDF
    if (ext === 'pdf') return defineComponent({
        render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '1.5', d: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' })
        ])
    });
    // Icone ZIP/archive
    if (['zip','rar','7z','tar','gz'].includes(ext)) return defineComponent({
        render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '1.5', d: 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4' })
        ])
    });
    // Icone Excel
    if (['xls','xlsx','csv'].includes(ext)) return defineComponent({
        render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '1.5', d: 'M3 10h18M3 14h18M10 3v18M14 3v18M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z' })
        ])
    });
    // Icone vidéo
    if (['mp4','mov','avi','mkv','webm'].includes(ext)) return defineComponent({
        render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '1.5', d: 'M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z' })
        ])
    });
    // Icone document par défaut (Word, txt, etc.)
    return defineComponent({
        render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '1.5', d: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' })
        ])
    });
};

const fileBaseName = (filename: string) => {
    // "chat_file01062025abc123.pdf" → "fichier.pdf"
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

// ── Emoji Picker — montage programmatique ─────────────────────────────────────
const mountPicker = () => {
    if (!emojiContainer.value) return;
    // Détruire l'instance précédente si elle existe
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
            // NE PAS fermer le picker — laisser l'utilisateur choisir plusieurs emojis
        },
    });
    emojiContainer.value.appendChild(pickerInstance as unknown as HTMLElement);
};

const toggleEmojiPicker = () => {
    showEmojiPicker.value = !showEmojiPicker.value;
    if (showEmojiPicker.value) {
        nextTick(mountPicker);
    }
};

// Fermer picker au clic extérieur
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

// ── Dates / heure ─────────────────────────────────────────────────────────────
const showDateSeparator = (i: number) => {
    if (i === 0) return true;
    return new Date(localChats.value[i - 1].created_date).toDateString()
        !== new Date(localChats.value[i].created_date).toDateString();
};
const formatDate = (date: string) => {
    const d = new Date(date), today = new Date(), yesterday = new Date(today);
    yesterday.setDate(today.getDate() - 1);
    if (d.toDateString() === today.toDateString())     return "Aujourd'hui";
    if (d.toDateString() === yesterday.toDateString()) return 'Hier';
    return d.toLocaleDateString('fr-FR', { day: '2-digit', month: 'long', year: 'numeric' });
};
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

// ── Polling contacts (indépendant — tourne même sans conversation ouverte) ────
const pollContacts = async () => {
    try {
        const res = await fetch('/chat/contacts/poll',
            { headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrf.value } });
        if (!res.ok) return;
        const data = await res.json();
        if (data.contacts) localContacts.value = data.contacts;
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
        // Appliquer les suppressions survenues côté serveur
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

// ── Lifecycle ─────────────────────────────────────────────────────────────────
onMounted(() => {
    scrollToBottom();
    // Polling contacts — démarre toujours (sidebar refresh même sans conversation)
    pollContacts();
    contactsTimer = setInterval(pollContacts, 5000);
    // Polling messages + typing — seulement si une conversation est ouverte
    if (props.receiver) startPolling();
    document.addEventListener('click', onDocClick);
});
onUnmounted(() => {
    stopPolling();
    if (contactsTimer)   { clearInterval(contactsTimer); contactsTimer = null; }
    if (typingStopTimer) clearTimeout(typingStopTimer);
    if (isTypingNow) sendTyping(false);
    document.removeEventListener('click', onDocClick);
    // Nettoyer le picker
    if (emojiContainer.value) emojiContainer.value.innerHTML = '';
    pickerInstance = null;
});
</script>
