<template>
    <div class="space-y-6">

        <!-- En-tête -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Types de congés</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ leaveTypes.total }} type(s) défini(s)</p>
            </div>
            <AppButton @click="openCreate">
                <template #icon>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </template>
                Nouveau type
            </AppButton>
        </div>

        <!-- Aperçu des types (cards couleurs) -->
        <div v-if="leaveTypes.data.length" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
            <div v-for="lt in leaveTypes.data" :key="lt.id"
                class="flex items-center gap-3 p-3 rounded-lg border border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800">
                <span class="w-4 h-4 rounded-full flex-shrink-0" :style="{ background: lt.color ?? '#6366f1' }"/>
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-gray-900 dark:text-white truncate">{{ lt.name }}</p>
                </div>
            </div>
        </div>

        <!-- Table -->
        <DataTable
            ref="tableRef"
            :columns="columns"
            :rows="leaveTypes.data"
            row-key="id"
            :pagination="leaveTypes"
            @delete="handleDelete"
        >
            <template #cell-color="{ row }">
                <div class="flex items-center gap-2">
                    <span class="w-5 h-5 rounded-lg border border-gray-200 dark:border-gray-600 flex-shrink-0"
                        :style="{ background: row.color ?? '#6366f1' }"/>
                    <span class="text-xs font-mono text-gray-500 dark:text-gray-400">{{ row.color ?? '#6366f1' }}</span>
                </div>
            </template>

            <template #actions="{ row }">
                <div class="flex items-center justify-end gap-1.5">
                    <!-- Modifier -->
                    <button class="p-1.5 rounded-lg transition-all duration-150
                                   text-white bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700
                                   shadow-sm shadow-emerald-200 dark:shadow-emerald-900/40"
                            title="Modifier"
                            @click="openEdit(row as any)">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </button>
                    <!-- Supprimer -->
                    <button class="p-1.5 rounded-lg transition-all duration-150
                                   text-white bg-red-500 hover:bg-red-600 active:bg-red-700
                                   shadow-sm shadow-red-200 dark:shadow-red-900/40"
                            title="Supprimer"
                            @click="tableRef?.confirmDelete(row.id as number, row.name as string)">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </template>
        </DataTable>

        <!-- Modal Créer/Modifier -->
        <AppModal v-model="showForm" :title="editTarget ? 'Modifier le type' : 'Nouveau type de congé'" size="md">
            <form :id="formId" @submit.prevent="submitForm" class="space-y-4">
                <AppInput v-model="form.name"        label="Nom du type"    required :error="form.errors.name" placeholder="ex : Congé annuel"/>
                <AppInput v-model="form.description" label="Description"    placeholder="Description optionnelle..."/>

                <!-- Sélecteur de couleur -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Couleur d'identification</label>
                    <div class="flex items-center gap-3">
                        <input
                            v-model="form.color"
                            type="color"
                            class="w-10 h-10 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer"
                        />
                        <span class="text-sm font-mono text-gray-500 dark:text-gray-400">{{ form.color }}</span>
                        <!-- Palette rapide -->
                        <div class="flex gap-2 flex-wrap">
                            <button v-for="c in palette" :key="c" type="button"
                                class="w-6 h-6 rounded-full border-2 transition-transform hover:scale-110"
                                :style="{ background: c, borderColor: form.color === c ? '#fff' : 'transparent' }"
                                @click="form.color = c"/>
                        </div>
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

    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { AppButton, AppInput, AppModal, DataTable } from '@/Components/UI';
import { useToast } from '@/Composables/useToast';

const toast   = useToast();

interface LeaveType {
    id: number;
    name: string;
    description: string | null;
    color: string | null;
}

defineProps<{
    leaveTypes: {
        data: LeaveType[];
        total: number;
        from: number;
        to: number;
        links: { url: string | null; label: string; active: boolean }[];
    };
}>();

const formId     = 'leave-type-form';
const showForm   = ref(false);
const editTarget = ref<LeaveType | null>(null);
const tableRef   = ref<any>(null);

// Palette de couleurs suggérées
const palette = [
    '#3b82f6', '#ef4444', '#8b5cf6', '#f59e0b', '#10b981',
    '#06b6d4', '#f97316', '#6b7280', '#ec4899', '#84cc16',
];

const form = useForm({
    name:        '',
    description: '',
    color:       '#3b82f6',
});

const columns = [
    { key: 'name',        label: 'Nom' },
    { key: 'description', label: 'Description' },
    { key: 'color',       label: 'Couleur' },
];

const openCreate = () => {
    editTarget.value = null;
    form.reset();
    form.color = '#3b82f6';
    showForm.value = true;
};

const openEdit = (lt: LeaveType) => {
    editTarget.value = lt;
    form.name        = lt.name;
    form.description = lt.description ?? '';
    form.color       = lt.color ?? '#3b82f6';
    showForm.value   = true;
};

const submitForm = () => {
    const url = editTarget.value
        ? `/admin/staff/leave-types/edit/${editTarget.value.id}`
        : '/admin/staff/leave-types/add';
    form.post(url, {
        onSuccess: () => { showForm.value = false; toast.success('Type de congé enregistré.'); },
        onError:   () => toast.error('Vérifiez les informations.'),
    });
};

const handleDelete = (ids: (string | number)[]) => {
    ids.forEach(id => router.get(`/admin/staff/leave-types/delete/${id}`, {}, {
        onSuccess: () => toast.success('Type supprimé.'),
        onError:   () => toast.error('Erreur lors de la suppression.'),
    }));
};
</script>
