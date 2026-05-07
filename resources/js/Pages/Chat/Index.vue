<template>
    <!-- Layout plein écran avec header en haut -->
    <div class="flex flex-col h-screen bg-gray-50 dark:bg-gray-900">

        <!-- Header Inertia réutilisé -->
        <AppHeader />

        <!-- Corps du chat : sidebar + zone messages -->
        <div class="flex flex-1 overflow-hidden">

            <!-- ── Sidebar contacts ── -->
            <aside class="w-80 flex-shrink-0 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 flex flex-col">

                <!-- Titre + recherche -->
                <div class="px-4 pt-5 pb-3 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Messages</h2>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Rechercher..."
                            class="w-full pl-9 pr-4 py-2 text-sm bg-gray-100 dark:bg-gray-700 border-0 rounded-full text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500"
                        />
                    </div>
                </div>

                <!-- Liste des contacts -->
                <div class="flex-1 overflow-y-auto py-2">
                    <template v-if="filteredContacts.length">
                        <a
                            v-for="contact in filteredContacts"
                            :key="contact.user_id"
                            :href="`/chat?receiver_id=${encodeId(contact.user_id)}`"
                            :class="[
                                'flex items-center gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors cursor-pointer',
                                isActiveContact(contact) ? 'bg-primary-50 dark:bg-primary-900/20' : ''
                            ]"
                        >
                            <!-- Avatar + indicateur en ligne -->
                            <div class="relative flex-shrink-0">
                                <img
                                    :src="contact.sender_profile_picture ? `/upload/profile/${contact.sender_profile_picture}` : '/upload/default.jpg'"
                                    :alt="contact.name"
                                    class="w-12 h-12 rounded-full object-cover"
                                />
                                <span :class="['absolute bottom-0 right-0 w-3 h-3 rounded-full border-2 border-white dark:border-gray-800', isOnline(contact) ? 'bg-green-500' : 'bg-gray-300']" />
                            </div>

                            <!-- Infos contact -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <p :class="['text-sm font-semibold truncate', isActiveContact(contact) ? 'text-primary-700 dark:text-primary-300' : 'text-gray-900 dark:text-white']">
                                        {{ contact.name }}
                                    </p>
                                    <span v-if="contact.countMessage > 0" class="ml-2 flex-shrink-0 w-5 h-5 bg-primary-600 text-white text-[10px] font-bold rounded-full flex items-center justify-center">
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
            </aside>

            <!-- ── Zone principale ── -->
            <div class="flex-1 flex flex-col overflow-hidden">

                <!-- Pas de conversation sélectionnée -->
                <div v-if="!receiver" class="flex-1 flex flex-col items-center justify-center text-gray-400 dark:text-gray-500 gap-4">
                    <svg class="w-20 h-20 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <p class="text-lg font-medium">Sélectionnez une conversation</p>
                    <p class="text-sm">Choisissez un contact pour commencer à discuter</p>
                </div>

                <!-- Conversation active -->
                <template v-else>

                    <!-- Header conversation -->
                    <div class="flex items-center gap-3 px-5 py-3 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
                        <div class="relative">
                            <img
                                :src="receiver.profile_picture ? `/upload/profile/${receiver.profile_picture}` : '/upload/default.jpg'"
                                :alt="receiver.name"
                                class="w-10 h-10 rounded-full object-cover"
                            />
                            <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 rounded-full border-2 border-white dark:border-gray-800" />
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ receiver.last_name }} {{ receiver.name }}</p>
                            <p class="text-xs text-green-500">En ligne</p>
                        </div>
                    </div>

                    <!-- Messages -->
                    <div ref="messagesContainer" class="flex-1 overflow-y-auto px-5 py-4 space-y-1 bg-gray-50 dark:bg-gray-900">
                        <template v-for="(chat, index) in chats" :key="chat.id">

                            <!-- Séparateur de date -->
                            <div v-if="showDateSeparator(index)" class="flex items-center gap-3 my-4">
                                <div class="flex-1 h-px bg-gray-200 dark:bg-gray-700" />
                                <span class="text-xs text-gray-400 dark:text-gray-500 font-medium px-2">{{ formatDate(chat.created_date) }}</span>
                                <div class="flex-1 h-px bg-gray-200 dark:bg-gray-700" />
                            </div>

                            <!-- Message envoyé (moi) -->
                            <div v-if="chat.sender_id === authUser.id" class="flex justify-end items-end gap-2 mb-1">
                                <div class="flex flex-col items-end max-w-[65%]">
                                    <!-- Actions -->
                                    <div v-if="!chat.is_delete" class="flex items-center gap-2 mb-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button class="text-xs text-gray-400 hover:text-primary-600" @click="startEdit(chat)">Modifier</button>
                                        <a :href="`/chat/${chat.id}`" class="text-xs text-gray-400 hover:text-danger-600">Supprimer</a>
                                    </div>
                                    <!-- Bulle -->
                                    <div :class="['px-4 py-2.5 rounded-2xl rounded-br-sm text-sm', chat.is_delete ? 'bg-gray-200 dark:bg-gray-700 text-gray-500 italic' : 'bg-primary-600 text-white']">
                                        {{ chat.is_delete ? 'Message supprimé' : chat.message }}
                                    </div>
                                    <span class="text-[10px] text-gray-400 mt-1 pr-1">{{ timeAgo(chat.created_date) }}</span>
                                </div>
                                <img
                                    :src="authUser.profile_picture ? `/upload/profile/${authUser.profile_picture}` : '/upload/default.jpg'"
                                    class="w-7 h-7 rounded-full object-cover flex-shrink-0"
                                />
                            </div>

                            <!-- Message reçu -->
                            <div v-else class="flex items-end gap-2 mb-1">
                                <img
                                    :src="receiver.profile_picture ? `/upload/profile/${receiver.profile_picture}` : '/upload/default.jpg'"
                                    class="w-7 h-7 rounded-full object-cover flex-shrink-0"
                                />
                                <div class="flex flex-col items-start max-w-[65%]">
                                    <div :class="['px-4 py-2.5 rounded-2xl rounded-bl-sm text-sm', chat.is_delete ? 'bg-gray-200 dark:bg-gray-700 text-gray-500 italic' : 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm']">
                                        {{ chat.is_delete ? 'Message supprimé' : chat.message }}
                                    </div>
                                    <span class="text-[10px] text-gray-400 mt-1 pl-1">{{ timeAgo(chat.created_date) }}</span>
                                </div>
                            </div>

                        </template>
                    </div>

                    <!-- Zone de saisie -->
                    <div class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 px-4 py-3 flex-shrink-0">
                        <!-- Barre d'édition -->
                        <div v-if="editing" class="flex items-center gap-2 mb-2 px-3 py-1.5 bg-primary-50 dark:bg-primary-900/20 rounded-lg text-xs text-primary-700 dark:text-primary-300">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            <span>Modification du message</span>
                            <button class="ml-auto text-gray-400 hover:text-gray-600" @click="cancelEdit">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>

                        <form :action="editing ? `/chat/${editId}` : '/chat'" method="POST" @submit="onSubmit">
                            <input type="hidden" name="_token" :value="csrfToken" />
                            <input v-if="editing" type="hidden" name="_method" value="PUT" />
                            <input type="hidden" name="receiver_id" :value="receiver.id" />

                            <div class="flex items-center gap-3">
                                <!-- Pièce jointe -->
                                <label class="p-2 rounded-full text-gray-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 cursor-pointer transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                    </svg>
                                    <input type="file" name="file" class="hidden" />
                                </label>

                                <!-- Textarea -->
                                <div class="flex-1 relative">
                                    <textarea
                                        v-model="message"
                                        name="message"
                                        rows="1"
                                        placeholder="Écrivez un message..."
                                        class="w-full resize-none bg-gray-100 dark:bg-gray-700 border-0 rounded-2xl px-4 py-2.5 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 max-h-32 overflow-y-auto"
                                        @keydown.enter.exact.prevent="submitForm"
                                        @input="autoResize"
                                        ref="textarea"
                                    />
                                </div>

                                <!-- Envoyer -->
                                <button
                                    type="submit"
                                    :class="['p-2.5 rounded-full transition-colors', message.trim() ? 'bg-primary-600 hover:bg-primary-700 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-400 cursor-not-allowed']"
                                    :disabled="!message.trim()"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>

                </template>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, nextTick } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AppHeader from '@/Components/Layout/AppHeader.vue';

defineOptions({ layout: null }); // Layout custom — on gère tout ici

const page = usePage();

const props = defineProps<{
    contacts:    any[];
    receiver:    any | null;
    chats:       any[];
    receiver_id: string | null;
}>();

const authUser = computed(() => page.props.auth?.user as any);
const csrfToken = computed(() => (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? '');

// btoa n'est pas accessible dans les templates Vue — on l'expose explicitement
const encodeId = (id: number | string) => btoa(String(id));

const search           = ref('');
const message          = ref('');
const editing          = ref(false);
const editId           = ref<number | null>(null);
const messagesContainer = ref<HTMLElement>();
const textarea          = ref<HTMLTextAreaElement>();

const filteredContacts = computed(() =>
    props.contacts.filter(c =>
        !search.value ||
        c.name?.toLowerCase().includes(search.value.toLowerCase()) ||
        c.message?.toLowerCase().includes(search.value.toLowerCase())
    )
);

const isActiveContact = (contact: any) => {
    if (!props.receiver_id) return false;
    return atob(props.receiver_id) === String(contact.user_id);
};

const isOnline = (contact: any) => {
    if (!contact.last_login) return false;
    const diff = (Date.now() - new Date(contact.last_login).getTime()) / 60000;
    return diff <= 5;
};

const showDateSeparator = (index: number) => {
    if (index === 0) return true;
    const prev = props.chats[index - 1];
    const curr = props.chats[index];
    return new Date(prev.created_date).toDateString() !== new Date(curr.created_date).toDateString();
};

const formatDate = (date: string) => {
    const d = new Date(date);
    const today = new Date();
    const yesterday = new Date(today);
    yesterday.setDate(today.getDate() - 1);
    if (d.toDateString() === today.toDateString()) return "Aujourd'hui";
    if (d.toDateString() === yesterday.toDateString()) return 'Hier';
    return d.toLocaleDateString('fr-FR', { day: '2-digit', month: 'long', year: 'numeric' });
};

const timeAgo = (date: string) => {
    const d = new Date(date);
    const diff = (Date.now() - d.getTime()) / 1000;
    if (diff < 60) return "À l'instant";
    if (diff < 3600) return `Il y a ${Math.floor(diff / 60)} min`;
    if (diff < 86400) return `Il y a ${Math.floor(diff / 3600)} h`;
    return d.toLocaleDateString('fr-FR', { day: '2-digit', month: 'short' });
};

const startEdit = (chat: any) => {
    editing.value = true;
    editId.value  = chat.id;
    message.value = chat.message;
    textarea.value?.focus();
};

const cancelEdit = () => {
    editing.value = false;
    editId.value  = null;
    message.value = '';
};

const autoResize = () => {
    if (!textarea.value) return;
    textarea.value.style.height = 'auto';
    textarea.value.style.height = Math.min(textarea.value.scrollHeight, 128) + 'px';
};

const submitForm = () => {
    if (!message.value.trim()) return;
    (textarea.value?.closest('form') as HTMLFormElement)?.submit();
};

const onSubmit = () => {
    // Laisser le form se soumettre normalement
};

// Scroll vers le bas à l'ouverture
onMounted(() => {
    nextTick(() => {
        if (messagesContainer.value) {
            messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
        }
    });
});
</script>
