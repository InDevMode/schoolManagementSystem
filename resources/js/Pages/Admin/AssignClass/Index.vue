<template>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Assignation Classes-Professeurs</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ classTeachers.total }} assignation(s)</p>
            </div>
            <AppButton @click="openCreate">
                <template #icon>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                </template>
                Nouvelle assignation
            </AppButton>
        </div>

        <!-- Table -->
        <DataTable
            ref="tableRef"
            :columns="columns"
            :rows="classTeachers.data"
            row-key="id"
            export-filename="assignations-classes"
            @delete="handleDelete"
        >
            <template #cell-teacher_name="{ row }">
                {{ row.teacher_last_name }} {{ row.teacher_name }}
            </template>
            <template #cell-status="{ row }">
                <AppBadge :variant="row.status == 1 ? 'success' : 'danger'" dot>
                    {{ row.status == 1 ? 'Actif' : 'Inactif' }}
                </AppBadge>
            </template>
            <template #actions="{ row }">
                <div class="flex items-center justify-end gap-1">
                    <button class="p-1.5 rounded-lg text-gray-400 hover:text-danger-600 hover:bg-danger-50 dark:hover:bg-danger-900/20 transition-colors" title="Supprimer" @click="tableRef?.confirmDelete(row.id as number, `${row.class_name}`)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>
                </div>
            </template>
        </DataTable>

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
                    v-model="form.teacher_ids"
                    label="Professeurs"
                    :options="teacherOptions"
                    placeholder="Sélectionner des professeurs"
                    required
                    :error="form.errors.teacher_ids"
                />
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
import { AppButton, AppSelect, AppModal, DataTable, AppBadge } from '@/Components/UI';
import AppMultiSelect from '@/Components/UI/AppMultiSelect.vue';
import { useToast } from '@/Composables/useToast';

interface ClassTeacher {
    id: number;
    class_name: string;
    teacher_name: string;
    teacher_last_name: string;
    status: number;
}

interface ClassItem { id: number; name: string; }
interface Teacher   { id: number; name: string; last_name: string; }

const props = defineProps<{
    classTeachers: {
        data: ClassTeacher[];
        total: number;
        from: number;
        to: number;
        links: { url: string | null; label: string; active: boolean }[];
    };
    classes:  ClassItem[];
    teachers: Teacher[];
}>();

const formId     = 'assign-class-form';
const showForm   = ref(false);
const showDelete = ref(false);
const deleteTarget = ref<ClassTeacher | null>(null);
const deleting     = ref(false);
const toast = useToast();
const tableRef = ref<InstanceType<typeof DataTable> | null>(null);

const statusOptions = [
    { value: '1', label: 'Actif' },
    { value: '0', label: 'Inactif' },
];

const classOptions = computed(() =>
    props.classes.map(c => ({ value: String(c.id), label: c.name }))
);

const teacherOptions = computed(() =>
    props.teachers.map(t => ({ value: String(t.id), label: `${t.last_name} ${t.name}` }))
);

const columns = [
    { key: 'class_name',   label: 'Classe' },
    { key: 'teacher_name', label: 'Professeur' },
    { key: 'status',       label: 'Statut' },
];

const form = useForm({ class_id: '', teacher_ids: [] as string[], status: '1' });

const openCreate = () => {
    form.reset();
    form.status = '1';
    showForm.value = true;
};

const openDelete = (item: ClassTeacher) => {
    deleteTarget.value = item;
    showDelete.value = true;
};

const submitForm = () => {
    const data = new FormData();
    data.append('class_id', form.class_id);
    data.append('status', form.status);
    form.teacher_ids.forEach(id => data.append('teacher_id[]', id));

    router.post('/admin/assign_class/add', data, {
        onSuccess: () => { showForm.value = false; form.reset(); form.status = '1'; },
    });
};

const confirmDelete = () => {
    if (!deleteTarget.value) return;
    deleting.value = true;
    router.get(`/admin/assign_class/delete/${deleteTarget.value.id}`, {}, {
        onFinish: () => { deleting.value = false; showDelete.value = false; },
    });
};

const handleDelete = (ids: (string | number)[]) => {
    ids.forEach(id => {
        router.get(`/admin/assign_class/delete/${id}`, {}, {
            onSuccess: () => toast.success('Assignation supprimée avec succès.'),
            onError: () => toast.error('Erreur lors de la suppression.'),
        });
    });
};
</script>
