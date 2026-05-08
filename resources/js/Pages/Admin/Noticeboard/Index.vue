<template>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tableau d'affichage</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ notices.total }} notification(s)</p>
            </div>
            <AppButton @click="openCreate">
                <template #icon>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                </template>
                Nouvelle notification
            </AppButton>
        </div>

        <!-- Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            <div
                v-for="notice in notices.data"
                :key="notice.id"
                class="card p-5 space-y-3 hover:shadow-md transition-shadow"
            >
                <div class="flex items-start justify-between gap-2">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white line-clamp-2">{{ notice.title }}</h3>
                    <div class="flex items-center gap-1 flex-shrink-0">
                        <button class="p-1.5 rounded-lg text-gray-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors" @click="openEdit(notice)">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                        </button>
                        <button class="p-1.5 rounded-lg text-gray-400 hover:text-danger-600 hover:bg-danger-50 dark:hover:bg-danger-900/20 transition-colors" @click="openDelete(notice)">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </div>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-3" v-html="truncateHtml(notice.message, 150)" />
                <div class="flex items-center gap-3 text-xs text-gray-400 dark:text-gray-500 pt-1 border-t border-gray-100 dark:border-gray-700">
                    <span>Publié: {{ formatDate(notice.publish_date) }}</span>
                    <span>Date: {{ formatDate(notice.notice_date) }}</span>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="notices.links?.length" class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400">
            <span>Affichage de <strong>{{ notices.from }}</strong> à <strong>{{ notices.to }}</strong> sur <strong>{{ notices.total }}</strong></span>
            <div class="flex items-center gap-1">
                <template v-for="link in notices.links" :key="link.label">
                    <component
                        :is="link.url ? 'a' : 'span'"
                        :href="link.url ?? undefined"
                        v-html="link.label"
                        :class="['px-3 py-1.5 rounded-lg text-xs font-medium transition-colors', link.active ? 'bg-primary-600 text-white' : link.url ? 'hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer' : 'opacity-40 cursor-not-allowed']"
                    />
                </template>
            </div>
        </div>

        <!-- Modal Créer / Modifier -->
        <AppModal v-model="showForm" :title="editTarget ? 'Modifier la notification' : 'Nouvelle notification'" size="lg">
            <form :id="formId" @submit.prevent="submitForm" class="space-y-4">
                <AppInput v-model="form.title" label="Titre" required :error="form.errors.title" />
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <AppInput v-model="form.notice_date"  label="Date de la notice"    type="date" required :error="form.errors.notice_date" />
                    <AppInput v-model="form.publish_date" label="Date de publication"  type="date" required :error="form.errors.publish_date" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Message</label>
                    <textarea
                        v-model="form.message"
                        rows="4"
                        required
                        class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                    />
                    <p v-if="form.errors.message" class="mt-1 text-xs text-danger-600">{{ form.errors.message }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Destinataires</label>
                    <div class="flex flex-wrap gap-3">
                        <label v-for="opt in recipientOptions" :key="opt.value" class="flex items-center gap-2 cursor-pointer">
                            <input
                                type="checkbox"
                                :value="opt.value"
                                v-model="form.message_to"
                                class="w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                            />
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ opt.label }}</span>
                        </label>
                    </div>
                </div>
            </form>
            <template #footer>
                <AppButton variant="ghost" @click="showForm = false">Annuler</AppButton>
                <AppButton type="submit" :form="formId" :loading="form.processing">
                    {{ editTarget ? 'Enregistrer' : 'Créer' }}
                </AppButton>
            </template>
        </AppModal>

        <!-- Modal Supprimer -->
        <AppModal v-model="showDelete" title="Supprimer la notification" size="sm" persistent>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Voulez-vous vraiment supprimer <strong class="text-gray-900 dark:text-white">{{ deleteTarget?.title }}</strong> ?
            </p>
            <template #footer>
                <AppButton variant="ghost" @click="showDelete = false">Annuler</AppButton>
                <AppButton variant="danger" :loading="deleting" @click="confirmDelete">Supprimer</AppButton>
            </template>
        </AppModal>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { AppButton, AppInput, AppModal } from '@/Components/UI';

interface Notice {
    id: number;
    title: string;
    message: string;
    notice_date: string;
    publish_date: string;
}

const props = defineProps<{
    notices: {
        data: Notice[];
        total: number;
        from: number;
        to: number;
        links: { url: string | null; label: string; active: boolean }[];
    };
    editNotice?: Notice | null;
}>();

const formId     = 'notice-form';
const showForm   = ref(!!props.editNotice);
const showDelete = ref(false);
const editTarget   = ref<Notice | null>(props.editNotice ?? null);
const deleteTarget = ref<Notice | null>(null);
const deleting     = ref(false);

const recipientOptions = [
    { value: '2', label: 'Professeurs' },
    { value: '3', label: 'Apprenants' },
    { value: '4', label: 'Parents' },
];

const form = useForm({
    title:        editTarget.value?.title        ?? '',
    notice_date:  editTarget.value?.notice_date  ?? '',
    publish_date: editTarget.value?.publish_date ?? '',
    message:      editTarget.value?.message      ?? '',
    message_to:   [] as string[],
});

const openCreate = () => {
    editTarget.value = null;
    form.reset();
    showForm.value = true;
};

const openEdit = (notice: Notice) => {
    editTarget.value = notice;
    form.title        = notice.title;
    form.notice_date  = notice.notice_date;
    form.publish_date = notice.publish_date;
    form.message      = notice.message;
    form.message_to   = [];
    showForm.value = true;
};

const openDelete = (notice: Notice) => {
    deleteTarget.value = notice;
    showDelete.value = true;
};

const submitForm = () => {
    if (editTarget.value) {
        form.post(`/admin/communicate/noticeboard/edit/${editTarget.value.id}`, {
            onSuccess: () => { showForm.value = false; },
        });
    } else {
        form.post('/admin/communicate/noticeboard/add', {
            onSuccess: () => { showForm.value = false; },
        });
    }
};

const confirmDelete = () => {
    if (!deleteTarget.value) return;
    deleting.value = true;
    router.post(`/admin/communicate/noticeboard/delete/${deleteTarget.value.id}`, {}, {
        onFinish: () => { deleting.value = false; showDelete.value = false; },
    });
};

const formatDate = (d: string) =>
    d ? new Date(d).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' }) : '—';

// Nettoie le HTML et tronque le texte
const truncateHtml = (html: string, maxLength = 150) => {
    const text = html?.replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim() ?? '';
    return text.length > maxLength ? text.slice(0, maxLength) + '…' : text;
};
</script>
