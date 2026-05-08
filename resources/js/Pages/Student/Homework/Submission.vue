<template>
    <div class="space-y-6">
        <div class="flex items-center gap-3">
            <a href="/student/my_homework" class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Soumettre un travail</h1>
        </div>

        <div v-if="work" class="card p-6 space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-gray-500 uppercase font-medium">Matière</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white mt-1">{{ work.subject_name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-medium">Date de remise</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white mt-1">{{ work.submission_date }}</p>
                </div>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase font-medium">Description</p>
                <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">{{ work.description }}</p>
            </div>
            <div v-if="work.document_file">
                <a :href="`/upload/practicalworks/${work.document_file}`" target="_blank" class="text-sm text-primary-600 hover:underline">
                    Télécharger le document du professeur
                </a>
            </div>
        </div>

        <div class="card p-6">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Votre soumission</h2>
            <form @submit.prevent="submitHomework" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description / Commentaire</label>
                    <textarea v-model="form.description" rows="4" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Document (optionnel)</label>
                    <input type="file" @change="onFileChange" class="text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-primary-50 file:text-primary-700" />
                </div>
                <div class="flex justify-end">
                    <AppButton type="submit" :loading="submitting">Soumettre</AppButton>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { AppButton } from '@/Components/UI';

interface Work {
    id: number;
    subject_name: string;
    submission_date: string;
    description: string;
    document_file: string | null;
}

const props = defineProps<{
    work: Work | null;
}>();

const form = ref({ description: '' });
const docFile = ref<File | null>(null);
const submitting = ref(false);

const onFileChange = (e: Event) => {
    docFile.value = (e.target as HTMLInputElement).files?.[0] ?? null;
};

const submitHomework = () => {
    if (!props.work) return;
    submitting.value = true;
    const data = new FormData();
    data.append('description', form.value.description);
    if (docFile.value) data.append('document_file', docFile.value);

    router.post(`/student/my_homework/submission/create/${props.work.id}`, data, {
        onFinish: () => { submitting.value = false; },
    });
};
</script>
