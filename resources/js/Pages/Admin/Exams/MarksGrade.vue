<template>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Barème des notes</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ marksGrades.total }} barème(s) enregistré(s)</p>
            </div>
            <AppButton @click="openCreate">
                <template #icon>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                </template>
                Nouveau barème
            </AppButton>
        </div>

        <div class="card overflow-hidden">
            <AppTable :columns="columns" :rows="marksGrades.data" :pagination="marksGrades" row-key="id">
                <template #actions="{ row }">
                    <div class="flex items-center justify-end gap-1">
                        <button class="p-1.5 rounded-lg text-gray-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors" @click="openEdit(row)">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                        </button>
                        <button class="p-1.5 rounded-lg text-gray-400 hover:text-danger-600 hover:bg-danger-50 dark:hover:bg-danger-900/20 transition-colors" @click="openDelete(row)">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </div>
                </template>
            </AppTable>
        </div>

        <AppModal v-model="showForm" :title="editTarget ? 'Modifier le barème' : 'Nouveau barème'" size="md">
            <form :id="formId" @submit.prevent="submitForm" class="space-y-4">
                <AppInput v-model="form.name" label="Mention (ex: A, B, C)" required :error="form.errors.name" />
                <AppInput v-model="form.percent_from" label="Pourcentage de" type="number" required />
                <AppInput v-model="form.percent_to" label="Pourcentage à" type="number" required />
            </form>
            <template #footer>
                <AppButton variant="ghost" @click="showForm = false">Annuler</AppButton>
                <AppButton type="submit" :form="formId" :loading="form.processing">
                    {{ editTarget ? 'Enregistrer' : 'Créer' }}
                </AppButton>
            </template>
        </AppModal>

        <AppModal v-model="showDelete" title="Supprimer le barème" size="sm" persistent>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Voulez-vous vraiment supprimer <strong>{{ deleteTarget?.name }}</strong> ?
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
import { AppButton, AppInput, AppModal, AppTable } from '@/Components/UI';

interface MarksGrade {
    id: number;
    name: string;
    percent_from: number;
    percent_to: number;
}

defineProps<{
    marksGrades: {
        data: MarksGrade[];
        total: number;
        from: number;
        to: number;
        links: { url: string | null; label: string; active: boolean }[];
    };
}>();

const formId = 'marks-grade-form';
const showForm = ref(false);
const showDelete = ref(false);
const editTarget = ref<MarksGrade | null>(null);
const deleteTarget = ref<MarksGrade | null>(null);
const deleting = ref(false);

const columns = [
    { key: 'name', label: 'Mention' },
    { key: 'percent_from', label: 'De (%)' },
    { key: 'percent_to', label: 'À (%)' },
];

const form = useForm({ name: '', percent_from: '', percent_to: '' });

const openCreate = () => {
    editTarget.value = null;
    form.reset();
    showForm.value = true;
};

const openEdit = (grade: MarksGrade) => {
    editTarget.value = grade;
    form.name = grade.name;
    form.percent_from = String(grade.percent_from);
    form.percent_to = String(grade.percent_to);
    showForm.value = true;
};

const openDelete = (grade: MarksGrade) => {
    deleteTarget.value = grade;
    showDelete.value = true;
};

const submitForm = () => {
    if (editTarget.value) {
        form.post(`/admin/examinations/marks_grade/edit/${editTarget.value.id}`, {
            onSuccess: () => { showForm.value = false; },
        });
    } else {
        form.post('/admin/examinations/marks_grade/add', {
            onSuccess: () => { showForm.value = false; },
        });
    }
};

const confirmDelete = () => {
    if (!deleteTarget.value) return;
    deleting.value = true;
    router.get(`/admin/examinations/marks_grade/delete/${deleteTarget.value.id}`, {}, {
        onFinish: () => { deleting.value = false; showDelete.value = false; },
    });
};
</script>
