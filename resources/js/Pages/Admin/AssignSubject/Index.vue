<template>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Assignation Matières-Classes</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ classSubjects.total }} assignation(s)</p>
            </div>
            <AppButton @click="openCreate">
                <template #icon>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                </template>
                Nouvelle assignation
            </AppButton>
        </div>

        <!-- Table -->
        <div class="card overflow-hidden">
            <AppTable :columns="columns" :rows="classSubjects.data" :pagination="classSubjects" row-key="id">
                <template #cell-status="{ row }">
                    <AppBadge :variant="row.status == 1 ? 'success' : 'danger'" dot>
                        {{ row.status == 1 ? 'Actif' : 'Inactif' }}
                    </AppBadge>
                </template>
                <template #actions="{ row }">
                    <div class="flex items-center justify-end gap-1">
                        <button class="p-1.5 rounded-lg text-gray-400 hover:text-danger-600 hover:bg-danger-50 dark:hover:bg-danger-900/20 transition-colors" title="Supprimer" @click="openDelete(row)">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </div>
                </template>
            </AppTable>
        </div>

        <!-- Modal Créer -->
        <AppModal v-model="showForm" title="Nouvelle assignation" size="md">
            <form :id="formId" @submit.prevent="submitForm" class="space-y-4">
                <AppSelect
                    v-model="form.class_id"
                    label="Classe"
                    :options="classOptions"
                    placeholder="Sélectionner une classe"
                    required
                    :error="form.errors.class_id"
                />
                <AppMultiSelect
                    v-model="form.subject_ids"
                    label="Matières"
                    :options="subjectOptions"
                    placeholder="Sélectionner des matières"
                    required
                    :error="form.errors.subject_id"
                />
                <AppInput v-model="form.coefficient" label="Coefficient" type="number" required :error="form.errors.coefficient" />
                <AppSelect v-model="form.status" label="Statut" :options="statusOptions" required :error="form.errors.status" />
            </form>
            <template #footer>
                <AppButton variant="ghost" @click="showForm = false">Annuler</AppButton>
                <AppButton type="submit" :form="formId" :loading="form.processing">Assigner</AppButton>
            </template>
        </AppModal>

        <!-- Modal Supprimer -->
        <AppModal v-model="showDelete" title="Supprimer l'assignation" size="sm" persistent>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Voulez-vous vraiment supprimer cette assignation ? Cette action est irréversible.
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
import { AppButton, AppInput, AppSelect, AppModal, AppTable, AppBadge } from '@/Components/UI';
import AppMultiSelect from '@/Components/UI/AppMultiSelect.vue';

interface ClassSubject {
    id: number;
    class_name: string;
    subject_name: string;
    status: number;
    coefficient: number;
}

interface ClassItem   { id: number; name: string; }
interface SubjectItem { id: number; name: string; }

const props = defineProps<{
    classSubjects: {
        data: ClassSubject[];
        total: number;
        from: number;
        to: number;
        links: { url: string | null; label: string; active: boolean }[];
    };
    classes:  ClassItem[];
    subjects: SubjectItem[];
}>();

const formId     = 'assign-subject-form';
const showForm   = ref(false);
const showDelete = ref(false);
const deleteTarget = ref<ClassSubject | null>(null);
const deleting     = ref(false);

const statusOptions = [
    { value: '1', label: 'Actif' },
    { value: '0', label: 'Inactif' },
];

const classOptions = computed(() =>
    props.classes.map(c => ({ value: String(c.id), label: c.name }))
);

const subjectOptions = computed(() =>
    props.subjects.map(s => ({ value: String(s.id), label: s.name }))
);

const columns = [
    { key: 'class_name',   label: 'Classe' },
    { key: 'subject_name', label: 'Matière' },
    { key: 'coefficient',  label: 'Coefficient' },
    { key: 'status',       label: 'Statut' },
];

const form = useForm({ class_id: '', subject_ids: [] as string[], coefficient: '1', status: '1' });

const openCreate = () => {
    form.reset();
    form.coefficient = '1';
    form.status = '1';
    showForm.value = true;
};

const openDelete = (item: ClassSubject) => {
    deleteTarget.value = item;
    showDelete.value = true;
};

const submitForm = () => {
    const data = new FormData();
    data.append('class_id', form.class_id);
    data.append('coefficient', form.coefficient);
    data.append('status', form.status);
    form.subject_ids.forEach(id => data.append('subject_id[]', id));

    router.post('/admin/assign_subject/add', data, {
        onSuccess: () => { showForm.value = false; form.reset(); form.coefficient = '1'; form.status = '1'; },
    });
};

const confirmDelete = () => {
    if (!deleteTarget.value) return;
    deleting.value = true;
    router.get(`/admin/assign_subject/delete/${deleteTarget.value.id}`, {}, {
        onFinish: () => { deleting.value = false; showDelete.value = false; },
    });
};
</script>
