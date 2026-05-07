<template>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Classes</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ classes.total }} classe(s) enregistrée(s)</p>
            </div>
            <AppButton @click="openCreate">
                <template #icon>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                </template>
                Nouvelle classe
            </AppButton>
        </div>

        <!-- Table -->
        <div class="card overflow-hidden">
            <AppTable :columns="columns" :rows="classes.data" :pagination="classes" row-key="id">
                <template #cell-status="{ row }">
                    <AppBadge :variant="row.status == 1 ? 'success' : 'danger'" dot>
                        {{ row.status == 1 ? 'Actif' : 'Inactif' }}
                    </AppBadge>
                </template>
                <template #actions="{ row }">
                    <div class="flex items-center justify-end gap-1">
                        <button class="p-1.5 rounded-lg text-gray-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors" title="Modifier" @click="openEdit(row)">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                        </button>
                        <button class="p-1.5 rounded-lg text-gray-400 hover:text-danger-600 hover:bg-danger-50 dark:hover:bg-danger-900/20 transition-colors" title="Supprimer" @click="openDelete(row)">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </div>
                </template>
            </AppTable>
        </div>

        <!-- Modal Créer / Modifier -->
        <AppModal v-model="showForm" :title="editTarget ? 'Modifier la classe' : 'Nouvelle classe'" size="md">
            <form :id="formId" @submit.prevent="submitForm" class="space-y-4">
                <AppInput v-model="form.name" label="Nom de la classe" required :error="form.errors.name" />
                <AppInput v-model="form.amount" label="Montant (frais)" type="number" required :error="form.errors.amount" />
                <AppSelect v-model="form.status" label="Statut" :options="statusOptions" required :error="form.errors.status" />
            </form>
            <template #footer>
                <AppButton variant="ghost" @click="showForm = false">Annuler</AppButton>
                <AppButton type="submit" :form="formId" :loading="form.processing">
                    {{ editTarget ? 'Enregistrer' : 'Créer' }}
                </AppButton>
            </template>
        </AppModal>

        <!-- Modal Supprimer -->
        <AppModal v-model="showDelete" title="Supprimer la classe" size="sm" persistent>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Voulez-vous vraiment supprimer la classe <strong class="text-gray-900 dark:text-white">{{ deleteTarget?.name }}</strong> ?
                Cette action est irréversible.
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
import { AppButton, AppInput, AppSelect, AppModal, AppTable, AppBadge } from '@/Components/UI';

interface ClassItem {
    id: number;
    name: string;
    amount: number;
    status: number;
}

const props = defineProps<{
    classes: {
        data: ClassItem[];
        total: number;
        from: number;
        to: number;
        links: { url: string | null; label: string; active: boolean }[];
    };
}>();

const formId     = 'class-form';
const showForm   = ref(false);
const showDelete = ref(false);
const editTarget   = ref<ClassItem | null>(null);
const deleteTarget = ref<ClassItem | null>(null);
const deleting     = ref(false);

const statusOptions = [
    { value: '1', label: 'Actif' },
    { value: '0', label: 'Inactif' },
];

const columns = [
    { key: 'name',   label: 'Nom' },
    { key: 'amount', label: 'Montant' },
    { key: 'status', label: 'Statut' },
];

const form = useForm({ name: '', amount: '', status: '1' });

const openCreate = () => {
    editTarget.value = null;
    form.reset();
    form.status = '1';
    showForm.value = true;
};

const openEdit = (cls: ClassItem) => {
    editTarget.value = cls;
    form.name   = cls.name;
    form.amount = String(cls.amount);
    form.status = String(cls.status);
    showForm.value = true;
};

const openDelete = (cls: ClassItem) => {
    deleteTarget.value = cls;
    showDelete.value = true;
};

const submitForm = () => {
    if (editTarget.value) {
        form.post(`/admin/class/edit/${editTarget.value.id}`, {
            onSuccess: () => { showForm.value = false; },
        });
    } else {
        form.post('/admin/class/add', {
            onSuccess: () => { showForm.value = false; },
        });
    }
};

const confirmDelete = () => {
    if (!deleteTarget.value) return;
    deleting.value = true;
    router.get(`/admin/class/delete/${deleteTarget.value.id}`, {}, {
        onFinish: () => { deleting.value = false; showDelete.value = false; },
    });
};
</script>
