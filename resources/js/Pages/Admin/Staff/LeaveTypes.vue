<template>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Types de congés</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ leaveTypes.total }} type(s) configuré(s)</p>
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

        <!-- Grille de cartes -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div v-for="lt in leaveTypes.data" :key="lt.id"
                class="card p-5 flex items-start gap-4">
                <!-- Couleur -->
                <div class="w-10 h-10 rounded-xl flex-shrink-0 flex items-center justify-center"
                    :style="{ background: lt.color + '20' }">
                    <span class="w-4 h-4 rounded-full" :style="{ background: lt.color }"/>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ lt.name }}</p>
                    <p v-if="lt.description" class="text-xs text-gray-400 mt-0.5 truncate">{{ lt.description }}</p>
                </div>
                <div class="flex gap-1">
                    <button class="p-1.5 rounded-lg text-gray-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors"
                        @click="openEdit(lt)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </button>
                    <button class="p-1.5 rounded-lg text-gray-400 hover:text-danger-600 hover:bg-danger-50 dark:hover:bg-danger-900/20 transition-colors"
                        @click="deleteType(lt.id)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <AppModal v-model="showForm" :title="editTarget ? 'Modifier le type' : 'Nouveau type de congé'" size="sm">
            <form id="lt-form" @submit.prevent="submitForm" class="space-y-4">
                <AppInput v-model="form.name" label="Nom" required :error="form.errors.name" placeholder="ex: Congé annuel"/>
                <AppInput v-model="form.description" label="Description" placeholder="Courte description..."/>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Couleur</label>
                    <div class="flex items-center gap-3">
                        <input v-model="form.color" type="color" class="w-10 h-10 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer"/>
                        <div class="flex gap-2 flex-wrap">
                            <button v-for="c in presetColors" :key="c" type="button"
                                class="w-6 h-6 rounded-full border-2 transition-transform hover:scale-110"
                                :style="{ background: c, borderColor: form.color === c ? '#1a1a2e' : 'transparent' }"
                                @click="form.color = c"/>
                        </div>
                    </div>
                </div>
            </form>
            <template #footer>
                <AppButton variant="ghost" @click="showForm = false">Annuler</AppButton>
                <AppButton type="submit" form="lt-form" :loading="form.processing">
                    {{ editTarget ? 'Enregistrer' : 'Créer' }}
                </AppButton>
            </template>
        </AppModal>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { AppButton, AppInput, AppModal } from '@/Components/UI';
import { useToast } from '@/Composables/useToast';

const toast = useToast();

defineProps<{
    leaveTypes: { data: any[]; total: number; from: number; to: number; links: any[] };
}>();

const showForm   = ref(false);
const editTarget = ref<any>(null);
const presetColors = ['#10b981', '#ef4444', '#8b5cf6', '#f59e0b', '#3b82f6', '#ec4899', '#6366f1', '#14b8a6'];

const form = useForm({ name: '', description: '', color: '#6366f1' });

const openCreate = () => {
    editTarget.value = null;
    form.reset();
    form.color = '#6366f1';
    showForm.value = true;
};

const openEdit = (lt: any) => {
    editTarget.value = lt;
    form.name        = lt.name;
    form.description = lt.description ?? '';
    form.color       = lt.color ?? '#6366f1';
    showForm.value   = true;
};

const submitForm = () => {
    const url = editTarget.value
        ? `/admin/staff/leave-types/edit/${editTarget.value.id}`
        : '/admin/staff/leave-types/add';
    form.post(url, {
        onSuccess: () => { showForm.value = false; toast.success('Type de congé enregistré.'); },
    });
};

const deleteType = (id: number) => {
    if (!confirm('Supprimer ce type de congé ?')) return;
    router.get(`/admin/staff/leave-types/delete/${id}`, {}, {
        onSuccess: () => toast.success('Type supprimé.'),
    });
};
</script>
