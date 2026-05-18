<template>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Évaluations</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ exams.total }} évaluation(s) enregistrée(s)</p>
            </div>
            <AppButton @click="openCreate">
                <template #icon>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                </template>
                Nouvelle évaluation
            </AppButton>
        </div>

        <DataTable
            ref="tableRef"
            :columns="columns"
            :rows="exams.data"
            row-key="id"
            export-filename="evaluations"
            @delete="handleDelete"
        >
            <template #cell-status="{ row }">
                <AppBadge :variant="row.status == 1 ? 'success' : 'danger'" dot>
                    {{ row.status == 1 ? 'Actif' : 'Inactif' }}
                </AppBadge>
            </template>
            <template #actions="{ row }">
                <div class="flex items-center justify-end gap-1">
                    <button class="p-1.5 rounded-lg text-gray-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors" @click="openEdit(row as any)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    </button>
                    <button class="p-1.5 rounded-lg text-gray-400 hover:text-danger-600 hover:bg-danger-50 dark:hover:bg-danger-900/20 transition-colors" @click="tableRef?.confirmDelete(row.id as number, row.name as string)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>
                </div>
            </template>
        </DataTable>

        <AppModal v-model="showForm" :title="editTarget ? 'Modifier l\'évaluation' : 'Nouvelle évaluation'" size="md">
            <form :id="formId" @submit.prevent="submitForm" class="space-y-4">
                <AppInput v-model="form.name" label="Nom" required :error="form.errors.name" />
                <AppInput v-model="form.start_date" label="Date de début" type="date" required />
                <AppInput v-model="form.end_date" label="Date de fin" type="date" required />
                <AppSelect v-model="form.period_id" label="Période" :options="periodOptions" required />
                <AppSelect v-model="form.status" label="Statut" :options="statusOptions" required />
            </form>
            <template #footer>
                <AppButton variant="ghost" @click="showForm = false">Annuler</AppButton>
                <AppButton type="submit" :form="formId" :loading="form.processing">
                    {{ editTarget ? 'Enregistrer' : 'Créer' }}
                </AppButton>
            </template>
        </AppModal>

        <AppModal v-model="showDelete" title="Supprimer l'évaluation" size="sm" persistent>
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
import { ref, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { AppButton, AppInput, AppSelect, AppModal, DataTable, AppBadge } from '@/Components/UI';
import { useToast } from '@/Composables/useToast';

interface Exam {
    id: number;
    name: string;
    start_date: string;
    end_date: string;
    period_id: number;
    status: number;
}

interface Period {
    id: number;
    name: string;
}

const props = defineProps<{
    exams: {
        data: Exam[];
        total: number;
        from: number;
        to: number;
        links: { url: string | null; label: string; active: boolean }[];
    };
    periods: Period[];
}>();

const formId = 'exam-form';
const showForm = ref(false);
const showDelete = ref(false);
const editTarget = ref<Exam | null>(null);
const deleteTarget = ref<Exam | null>(null);
const deleting = ref(false);
const toast = useToast();
const tableRef = ref<InstanceType<typeof DataTable> | null>(null);

const statusOptions = [
    { value: '1', label: 'Actif' },
    { value: '0', label: 'Inactif' },
];

const periodOptions = computed(() =>
    props.periods.map(p => ({ value: String(p.id), label: p.name }))
);

const columns = [
    { key: 'name', label: 'Nom' },
    { key: 'start_date', label: 'Début' },
    { key: 'end_date', label: 'Fin' },
    { key: 'status', label: 'Statut' },
];

const form = useForm({ name: '', start_date: '', end_date: '', period_id: '', status: '1' });

const openCreate = () => {
    editTarget.value = null;
    form.reset();
    form.status = '1';
    showForm.value = true;
};

const openEdit = (exam: Exam) => {
    editTarget.value = exam;
    form.name = exam.name;
    form.start_date = exam.start_date;
    form.end_date = exam.end_date;
    form.period_id = String(exam.period_id);
    form.status = String(exam.status);
    showForm.value = true;
};

const openDelete = (exam: Exam) => {
    deleteTarget.value = exam;
    showDelete.value = true;
};

const submitForm = () => {
    if (editTarget.value) {
        form.post(`/admin/examinations/exam/edit/${editTarget.value.id}`, {
            onSuccess: () => { showForm.value = false; },
        });
    } else {
        form.post('/admin/examinations/exam/add', {
            onSuccess: () => { showForm.value = false; },
        });
    }
};

const confirmDelete = () => {
    if (!deleteTarget.value) return;
    deleting.value = true;
    router.get(`/admin/examinations/exam/delete/${deleteTarget.value.id}`, {}, {
        onFinish: () => { deleting.value = false; showDelete.value = false; },
    });
};

const handleDelete = (ids: (string | number)[]) => {
    ids.forEach(id => {
        router.get(`/admin/examinations/exam/delete/${id}`, {}, {
            onSuccess: () => toast.success('Évaluation supprimée avec succès.'),
            onError: () => toast.error('Erreur lors de la suppression.'),
        });
    });
};
</script>
