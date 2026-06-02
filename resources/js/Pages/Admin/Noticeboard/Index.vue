<template>
    <div class="space-y-6">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tableau d'affichage</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    {{ notices.total }} notification{{ notices.total > 1 ? 's' : '' }}
                </p>
            </div>
            <AppButton @click="openCreate">
                <template #icon>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </template>
                Nouvelle notification
            </AppButton>
        </div>

        <!-- Empty state -->
        <div v-if="!notices.data.length" class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
            </div>
            <p class="text-sm font-medium text-gray-900 dark:text-white">Aucune notification</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Créez votre première notification pour commencer.</p>
        </div>

        <!-- Cards grid -->
        <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
            <article
                v-for="notice in notices.data"
                :key="notice.id"
                class="card flex flex-col gap-0 overflow-hidden hover:shadow-card-md transition-shadow duration-200"
            >
                <!-- Card header -->
                <div class="flex items-start justify-between gap-3 px-5 pt-5 pb-3">
                    <div class="flex items-start gap-3 min-w-0">
                        <!-- Icon -->
                        <div class="w-9 h-9 rounded-xl bg-primary-50 dark:bg-primary-900/30 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white leading-snug line-clamp-2">
                                {{ notice.title }}
                            </h3>
                            <div class="flex items-center gap-1.5 mt-1">
                                <span class="inline-flex items-center gap-1 text-xs text-gray-400 dark:text-gray-500">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ formatDate(notice.publish_date) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <!-- Actions -->
                    <div class="flex items-center gap-0.5 flex-shrink-0">
                        <button
                            class="p-1.5 rounded-lg text-gray-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors"
                            title="Modifier"
                            @click="openEdit(notice)"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>
                        <button
                            class="p-1.5 rounded-lg text-gray-400 hover:text-danger-600 hover:bg-danger-50 dark:hover:bg-danger-900/20 transition-colors"
                            title="Supprimer"
                            @click="openDelete(notice)"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Message preview -->
                <div class="px-5 pb-4 flex-1">
                    <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-3 leading-relaxed">
                        {{ stripHtml(notice.message, 160) }}
                    </p>
                </div>

                <!-- Card footer -->
                <div class="px-5 py-3 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between gap-2">
                    <div class="flex items-center gap-1 text-xs text-gray-400 dark:text-gray-500">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Notice : {{ formatDate(notice.notice_date) }}</span>
                    </div>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        Publié
                    </span>
                </div>
            </article>
        </div>

        <!-- Pagination -->
        <div v-if="notices.links?.length > 3" class="flex flex-col sm:flex-row items-center justify-between gap-3 text-sm text-gray-600 dark:text-gray-400">
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

        <!-- ── Modal Créer / Modifier ─────────────────────────────────────── -->
        <AppModal
            v-model="showForm"
            :title="editTarget ? 'Modifier la notification' : 'Nouvelle notification'"
            size="lg"
        >
            <form :id="formId" @submit.prevent="submitForm" class="space-y-4">

                <AppInput v-model="form.title" label="Titre" required :error="form.errors.title" />

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <AppInput v-model="form.notice_date"  label="Date de la notice"   type="date" required :error="form.errors.notice_date" />
                    <AppInput v-model="form.publish_date" label="Date de publication" type="date" required :error="form.errors.publish_date" />
                </div>

                <!-- Rich editor -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Message <span class="text-danger-500 ml-0.5">*</span>
                    </label>
                    <AppRichEditor
                        v-model="form.message"
                        :error="form.errors.message"
                    />
                </div>

                <!-- Destinataires -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Destinataires
                    </label>
                    <div class="flex flex-wrap gap-3">
                        <label
                            v-for="opt in recipientOptions"
                            :key="opt.value"
                            class="flex items-center gap-2 cursor-pointer select-none"
                            @click="toggleRecipient(opt.value)"
                        >
                            <div
                                :class="[
                                    'w-4 h-4 rounded border-2 flex items-center justify-center flex-shrink-0 transition-colors',
                                    form.message_to.includes(opt.value)
                                        ? 'bg-primary-600 border-primary-600'
                                        : 'border-gray-300 dark:border-gray-500 bg-white dark:bg-gray-800',
                                ]"
                            >
                                <svg
                                    v-if="form.message_to.includes(opt.value)"
                                    class="w-2.5 h-2.5 text-white"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
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

        <!-- ── Modal Supprimer ───────────────────────────────────────────── -->
        <AppModal v-model="showDelete" title="Supprimer la notification" size="sm" persistent>
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-danger-50 dark:bg-danger-900/20 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-danger-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Confirmer la suppression</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Voulez-vous vraiment supprimer
                        <strong class="text-gray-900 dark:text-white">« {{ deleteTarget?.title }} »</strong> ?
                        Cette action est irréversible.
                    </p>
                </div>
            </div>
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
import { AppButton, AppInput, AppModal, AppRichEditor } from '@/Components/UI';
import { stripHtml } from '@/Utils/html';

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

// ── Actions ──────────────────────────────────────────────────────────────────

const openCreate = () => {
    editTarget.value = null;
    form.reset();
    showForm.value = true;
};

const openEdit = (notice: Notice) => {
    editTarget.value  = notice;
    form.title        = notice.title;
    form.notice_date  = notice.notice_date;
    form.publish_date = notice.publish_date;
    form.message      = notice.message;   // HTML brut → AppRichEditor l'interprète correctement
    form.message_to   = [];
    showForm.value    = true;
};

const openDelete = (notice: Notice) => {
    deleteTarget.value = notice;
    showDelete.value   = true;
};

const toggleRecipient = (value: string) => {
    const idx = form.message_to.indexOf(value);
    if (idx === -1) form.message_to.push(value);
    else form.message_to.splice(idx, 1);
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

// ── Helpers ───────────────────────────────────────────────────────────────────

const formatDate = (d: string) =>
    d ? new Date(d).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' }) : '—';
</script>
