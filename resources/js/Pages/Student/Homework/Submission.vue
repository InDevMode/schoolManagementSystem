<template>
    <div class="space-y-6">
        <!-- Navigation -->
        <div class="flex items-center gap-3">
            <Link
                href="/student/my_homework"
                class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </Link>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Soumettre un travail</h1>
        </div>

        <!-- Détails du devoir -->
        <div v-if="work" class="card p-6 space-y-4">
            <h2 class="text-base font-semibold text-gray-700 dark:text-gray-300 mb-2">Consignes du travail</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <p class="text-xs text-gray-500 uppercase font-medium">Matière</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white mt-1">{{ work.subject_name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-medium">Classe</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white mt-1">{{ work.class_name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-medium">Date de remise</p>
                    <p class="text-sm font-semibold mt-1" :class="isPastDue ? 'text-danger-600' : 'text-gray-900 dark:text-white'">
                        {{ formatDate(work.submission_date) }}
                        <span v-if="isPastDue" class="text-xs ml-1">(dépassée)</span>
                    </p>
                </div>
            </div>

            <!-- Description -->
            <div v-if="work.description">
                <p class="text-xs text-gray-500 uppercase font-medium mb-1">Description</p>
                <div
                    class="text-sm text-gray-700 dark:text-gray-300 prose prose-sm dark:prose-invert max-w-none bg-gray-50 dark:bg-gray-700/40 rounded-lg p-4"
                    v-html="work.description"
                />
            </div>

            <!-- Pièces jointes du devoir (multiples) -->
            <div v-if="work.attachments?.length">
                <p class="text-xs text-gray-500 uppercase font-medium mb-2">Documents fournis ({{ work.attachments.length }})</p>
                <div class="space-y-2">
                    <a
                        v-for="att in work.attachments"
                        :key="att.id"
                        :href="att.url"
                        target="_blank"
                        class="flex items-center gap-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg px-3 py-2.5 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors group"
                    >
                        <FileTypeIcon :filename="att.file_name" size="sm" />
                        <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-primary-600 truncate flex-1">
                            {{ att.file_name }}
                        </span>
                        <span class="text-xs text-gray-400 shrink-0">{{ att.readable_size }}</span>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-primary-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Fichier legacy -->
            <div v-else-if="work.document_file">
                <a
                    :href="`/upload/practicalworks/${work.document_file}`"
                    target="_blank"
                    class="inline-flex items-center gap-2 text-sm text-primary-600 hover:text-primary-700 font-medium"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Télécharger le document du professeur
                </a>
            </div>
        </div>

        <!-- Formulaire de soumission -->
        <div class="card p-6">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Votre soumission</h2>
            <form @submit.prevent="submitHomework" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Description / Commentaire
                    </label>
                    <textarea
                        v-model="form.description"
                        rows="4"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"
                        placeholder="Décrivez votre travail ou ajoutez un commentaire…"
                    />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Document <span class="text-gray-400 font-normal">(optionnel)</span>
                    </label>
                    <div
                        class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4 text-center cursor-pointer hover:border-primary-400 transition-colors"
                        @dragover.prevent
                        @drop.prevent="onDrop"
                        @click="fileInput?.click()"
                    >
                        <svg class="w-8 h-8 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        <p class="text-sm text-gray-500">
                            Glissez votre fichier ici ou
                            <span class="text-primary-600 font-medium">cliquez pour parcourir</span>
                        </p>
                        <p class="text-xs text-gray-400 mt-1">PDF, DOCX, image… max 20 Mo</p>
                        <input ref="fileInput" type="file" class="hidden" @change="onFileChange" />
                    </div>
                    <!-- Fichier sélectionné -->
                    <div v-if="docFile" class="mt-3 flex items-center justify-between bg-gray-50 dark:bg-gray-700/50 rounded-lg px-3 py-2">
                        <div class="flex items-center gap-2 min-w-0">
                            <FileTypeIcon :filename="docFile.name" size="sm" />
                            <span class="text-sm text-gray-700 dark:text-gray-300 truncate">{{ docFile.name }}</span>
                            <span class="text-xs text-gray-400 shrink-0">{{ formatFileSize(docFile.size) }}</span>
                        </div>
                        <button
                            type="button"
                            @click="docFile = null"
                            class="ml-2 text-gray-400 hover:text-danger-500 transition-colors shrink-0"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="flex justify-end">
                    <AppButton type="submit" :loading="submitting">
                        <template #icon>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                        </template>
                        Soumettre
                    </AppButton>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import { AppButton, FileTypeIcon } from '@/Components/UI';
import { useToast } from '@/Composables/useToast';

interface Attachment {
    id: number;
    file_name: string;
    url: string;
    readable_size: string;
}

interface Work {
    id: number;
    class_name?: string;
    subject_name?: string;
    submission_date: string;
    description: string;
    document_file: string | null;
    attachments?: Attachment[];
}

const props = defineProps<{ work: Work | null }>();

const toast      = useToast();
const fileInput  = ref<HTMLInputElement | null>(null);
const form       = ref({ description: '' });
const docFile    = ref<File | null>(null);
const submitting = ref(false);

// Indique si la date de remise est dépassée
const isPastDue = computed(() => {
    if (!props.work?.submission_date) return false;
    return new Date(props.work.submission_date) < new Date();
});

const formatDate = (d: string) => {
    if (!d) return '—';
    try { return new Date(d).toLocaleDateString('fr-FR', { day: '2-digit', month: 'long', year: 'numeric' }); }
    catch { return d; }
};

const formatFileSize = (bytes: number) => {
    if (bytes < 1024) return bytes + ' o';
    if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' Ko';
    return (bytes / 1048576).toFixed(1) + ' Mo';
};

const onFileChange = (e: Event) => {
    docFile.value = (e.target as HTMLInputElement).files?.[0] ?? null;
};

const onDrop = (e: DragEvent) => {
    docFile.value = e.dataTransfer?.files?.[0] ?? null;
};

const submitHomework = () => {
    if (!props.work) return;
    submitting.value = true;
    const data = new FormData();
    data.append('description', form.value.description);
    if (docFile.value) data.append('document_file', docFile.value);

    // Route correcte : POST /student/my_homework/submission/{id}
    router.post(`/student/my_homework/submission/${props.work.id}`, data, {
        onSuccess: () => toast.success('Travail soumis avec succès.'),
        onError:   () => toast.error('Erreur lors de la soumission. Veuillez réessayer.'),
        onFinish:  () => { submitting.value = false; },
    });
};
</script>
