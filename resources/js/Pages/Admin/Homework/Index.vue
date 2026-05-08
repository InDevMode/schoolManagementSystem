<template>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Travaux de maison</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ works.total }} travail(aux) enregistré(s)</p>
            </div>
            <AppButton @click="openCreate">
                <template #icon>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                </template>
                Nouveau travail
            </AppButton>
        </div>

        <div class="card overflow-hidden">
            <AppTable :columns="columns" :rows="works.data" :pagination="works" row-key="id">
                <template #cell-description="{ row }">
                    <span class="line-clamp-2 text-sm text-gray-600 dark:text-gray-400">{{ stripHtml(row.description) }}</span>
                </template>
                <template #actions="{ row }">
                    <div class="flex items-center justify-end gap-1">
                        <a :href="`/admin/practicalworks/homework/details/${row.id}`" class="p-1.5 rounded-lg text-gray-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        </a>
                        <button class="p-1.5 rounded-lg text-gray-400 hover:text-danger-600 hover:bg-danger-50 dark:hover:bg-danger-900/20 transition-colors" @click="deleteWork(row.id)">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </div>
                </template>
            </AppTable>
        </div>

        <AppModal v-model="showForm" title="Nouveau travail de maison" size="lg">
            <form :id="formId" @submit.prevent="submitForm" class="space-y-4">
                <AppSelect v-model="form.class_id" label="Classe" :options="classOptions" required :error="form.errors.class_id" @change="loadSubjects" />
                <AppSelect v-model="form.subject_id" label="Matière" :options="subjectOptions" required :error="form.errors.subject_id" />
                <AppInput v-model="form.work_date" label="Date du travail" type="date" required />
                <AppInput v-model="form.submission_date" label="Date de remise" type="date" required />
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                    <textarea v-model="form.description" rows="3" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Document (optionnel)</label>
                    <input type="file" @change="onFileChange" class="text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-primary-50 file:text-primary-700" />
                </div>
            </form>
            <template #footer>
                <AppButton variant="ghost" @click="showForm = false">Annuler</AppButton>
                <AppButton type="submit" :form="formId" :loading="submitting">Créer</AppButton>
            </template>
        </AppModal>
    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import { AppButton, AppInput, AppSelect, AppModal, AppTable } from '@/Components/UI';

interface Work {
    id: number;
    class_name: string;
    subject_name: string;
    work_date: string;
    submission_date: string;
    description: string;
}

const props = defineProps<{
    works: {
        data: Work[];
        total: number;
        from: number;
        to: number;
        links: { url: string | null; label: string; active: boolean }[];
    };
    classes: { id: number; name: string }[];
}>();

const formId = 'homework-form';
const showForm = ref(false);
const submitting = ref(false);
const subjects = ref<{ id: number; name: string }[]>([]);
const docFile = ref<File | null>(null);

const classOptions = computed(() =>
    props.classes.map(c => ({ value: String(c.id), label: c.name }))
);

const subjectOptions = computed(() =>
    subjects.value.map(s => ({ value: String(s.id), label: s.name }))
);

const columns = [
    { key: 'class_name', label: 'Classe' },
    { key: 'subject_name', label: 'Matière' },
    { key: 'work_date', label: 'Date' },
    { key: 'submission_date', label: 'Remise' },
    { key: 'description', label: 'Description' },
];

const form = useForm({
    class_id: '',
    subject_id: '',
    work_date: '',
    submission_date: '',
    description: '',
});

const openCreate = () => {
    form.reset();
    subjects.value = [];
    showForm.value = true;
};

const loadSubjects = async () => {
    if (!form.class_id) return;
    const res = await fetch(`/admin/practicalworks/get_subject/${form.class_id}`);
    const data = await res.json();
    subjects.value = (data.getSubject ?? []).map((s: { subject_id: number; subject_name: string }) => ({
        id: s.subject_id,
        name: s.subject_name,
    }));
};

const onFileChange = (e: Event) => {
    docFile.value = (e.target as HTMLInputElement).files?.[0] ?? null;
};

const submitForm = () => {
    submitting.value = true;
    const data = new FormData();
    data.append('class_id', form.class_id);
    data.append('subject_id', form.subject_id);
    data.append('work_date', form.work_date);
    data.append('submission_date', form.submission_date);
    data.append('description', form.description);
    if (docFile.value) data.append('document_file', docFile.value);

    router.post('/admin/practicalworks/homework/create', data, {
        onFinish: () => { submitting.value = false; showForm.value = false; },
    });
};

const deleteWork = (id: number) => {
    if (confirm('Supprimer ce travail ?')) {
        router.get(`/admin/practicalworks/homework/delete/${id}`);
    }
};

const stripHtml = (html: string) =>
    html?.replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim() ?? '';
</script>
