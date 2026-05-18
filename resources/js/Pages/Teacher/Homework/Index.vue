<template>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Travaux de maison</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ works.total }} travail(aux)</p>
            </div>
            <AppButton @click="openCreate">
                <template #icon>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                </template>
                Nouveau travail
            </AppButton>
        </div>

        <DataTable
            ref="tableRef"
            :columns="columns"
            :rows="works.data"
            row-key="id"
            export-filename="devoirs"
            @delete="handleDelete"
        >
            <template #cell-description="{ row }">
                <span class="line-clamp-2 text-sm text-gray-600 dark:text-gray-400">{{ stripHtml(row.description as string, 100) }}</span>
            </template>
            <template #actions="{ row }">
                <a :href="`/teacher/practicalworks/homework/submission/${row.id}`" class="p-1.5 rounded-lg text-gray-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors inline-flex">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                </a>
            </template>
        </DataTable>

        <AppModal v-model="showForm" title="Nouveau travail de maison" size="lg">
            <form :id="formId" @submit.prevent="submitForm" class="space-y-4">
                <AppSelect v-model="form.class_id" label="Classe / Matière" :options="classOptions" required />
                <AppInput v-model="form.work_date" label="Date du travail" type="date" required />
                <AppInput v-model="form.submission_date" label="Date de remise" type="date" required />
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                    <textarea v-model="form.description" rows="3" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></textarea>
                </div>
            </form>
            <template #footer>
                <AppButton variant="ghost" @click="showForm = false">Annuler</AppButton>
                <AppButton type="submit" :form="formId" :loading="form.processing">Créer</AppButton>
            </template>
        </AppModal>
    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { AppButton, AppInput, AppSelect, AppModal, DataTable } from '@/Components/UI';
import { stripHtml } from '@/Utils/html';
import { useToast } from '@/Composables/useToast';

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
    classes: { class_id: number; class_name: string; subject_id: number; subject_name: string }[];
}>();

const formId = 'teacher-homework-form';
const showForm = ref(false);
const toast = useToast();
const tableRef = ref<InstanceType<typeof DataTable> | null>(null);

const classOptions = computed(() =>
    props.classes.map(c => ({ value: `${c.class_id}:${c.subject_id}`, label: `${c.class_name} — ${c.subject_name}` }))
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
    showForm.value = true;
};

const submitForm = () => {
    form.post('/teacher/practicalworks/homework/create', {
        onSuccess: () => { showForm.value = false; },
    });
};

const handleDelete = (ids: (string | number)[]) => {
    ids.forEach(id => {
        router.get(`/teacher/practicalworks/homework/delete/${id}`, {}, {
            onSuccess: () => toast.success('Travail supprimé avec succès.'),
            onError: () => toast.error('Erreur lors de la suppression.'),
        });
    });
};
</script>
