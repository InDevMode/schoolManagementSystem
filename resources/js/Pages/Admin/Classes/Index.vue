<template>
    <div class="space-y-6">
        <!-- Header -->
        <PageHeader title="Classes" :subtitle="`${classes.total} classe(s) enregistrée(s)`" color="primary">
            <template #icon>
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </template>
            <template #actions>
                <AppButton v-if="canCreate" @click="openCreate">
                    <template #icon>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    </template>
                    Nouvelle classe
                </AppButton>
            </template>
        </PageHeader>

        <!-- Table -->
        <DataTable
            ref="tableRef"
            :columns="columns"
            :rows="classes.data"
            row-key="id"
            export-filename="classes"
            :context-menu="true"
            @delete="handleDelete"
        >
            <template #cell-status="{ row }">
                <AppBadge :variant="row.status == 1 ? 'success' : 'danger'" dot>
                    {{ row.status == 1 ? 'Actif' : 'Inactif' }}
                </AppBadge>
            </template>
            <template #cell-amount="{ row }">
                <span class="font-medium text-gray-700 dark:text-gray-300">
                    {{ Number(row.amount).toLocaleString('fr-FR') }} FCFA
                </span>
            </template>
            <template #actions="{ row }">
                <div class="flex items-center justify-end gap-1.5">
                    <button title="Voir les détails" @click="openDetails(row as any)"
                            class="p-1.5 rounded-lg transition-all duration-150
                                   text-white bg-violet-500 hover:bg-violet-600 active:bg-violet-700
                                   shadow-sm shadow-violet-200 dark:shadow-violet-900/40">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                    <button v-if="canEdit" title="Modifier" @click="openEdit(row as any)"
                            class="p-1.5 rounded-lg transition-all duration-150
                                   text-white bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700
                                   shadow-sm shadow-emerald-200 dark:shadow-emerald-900/40">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </button>
                    <button v-if="canDelete" title="Supprimer" @click="tableRef?.confirmDelete(row.id as number, row.name as string)"
                            class="p-1.5 rounded-lg transition-all duration-150
                                   text-white bg-red-500 hover:bg-red-600 active:bg-red-700
                                   shadow-sm shadow-red-200 dark:shadow-red-900/40">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </template>
            <template #context-menu="{ row }">
                <button @click="openDetails(row as any)"
                        class="flex w-full items-center gap-2.5 px-3.5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-violet-50 dark:hover:bg-gray-700/60 hover:text-violet-700 transition-colors">
                    <svg class="w-4 h-4 text-violet-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Voir les détails
                </button>
                <button v-if="canEdit" @click="openEdit(row as any)"
                        class="flex w-full items-center gap-2.5 px-3.5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-emerald-50 dark:hover:bg-gray-700/60 hover:text-emerald-700 transition-colors">
                    <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Modifier
                </button>
                <template v-if="canDelete">
                    <div class="my-1 border-t border-gray-100 dark:border-gray-700"/>
                    <button @click="tableRef?.confirmDelete(row.id as number, row.name as string)"
                            class="flex w-full items-center gap-2.5 px-3.5 py-2.5 text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Supprimer
                    </button>
                </template>
            </template>
        </DataTable>

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
                Elle sera masquée de l'affichage. Le super administrateur peut la retrouver dans l'historique.
            </p>
            <template #footer>
                <AppButton variant="ghost" @click="showDelete = false">Annuler</AppButton>
                <AppButton variant="danger" :loading="deleting" @click="confirmDelete">Supprimer</AppButton>
            </template>
        </AppModal>

        <!-- Drawer Voir détails -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition-all duration-300 ease-out"
                leave-active-class="transition-all duration-200 ease-in"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div v-if="showDetails" class="fixed inset-0 z-50 flex justify-end" @click.self="showDetails = false">
                    <!-- Overlay -->
                    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="showDetails = false" />

                    <!-- Panel -->
                    <Transition
                        enter-active-class="transition-transform duration-300 ease-out"
                        leave-active-class="transition-transform duration-200 ease-in"
                        enter-from-class="translate-x-full"
                        enter-to-class="translate-x-0"
                        leave-from-class="translate-x-0"
                        leave-to-class="translate-x-full"
                    >
                        <div v-if="showDetails" class="relative w-full max-w-sm bg-white dark:bg-gray-900 h-full shadow-2xl flex flex-col">
                            <!-- Header drawer -->
                            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Détails de la classe</h2>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Informations complètes</p>
                                    </div>
                                </div>
                                <button @click="showDetails = false" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </div>

                            <!-- Body drawer -->
                            <div v-if="detailsTarget" class="flex-1 overflow-y-auto px-6 py-5 space-y-5">
                                <!-- Nom -->
                                <div>
                                    <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-1">Nom de la classe</p>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ detailsTarget.name }}</p>
                                </div>

                                <!-- Statut -->
                                <div>
                                    <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-2">Statut</p>
                                    <AppBadge :variant="detailsTarget.status == 1 ? 'success' : 'danger'" dot>
                                        {{ detailsTarget.status == 1 ? 'Actif' : 'Inactif' }}
                                    </AppBadge>
                                </div>

                                <!-- Frais de scolarité -->
                                <div class="rounded-lg bg-gray-50 dark:bg-gray-800 p-4">
                                    <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-1">Frais de scolarité</p>
                                    <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                                        {{ Number(detailsTarget.amount).toLocaleString('fr-FR') }}
                                        <span class="text-sm font-medium text-gray-500"> FCFA</span>
                                    </p>
                                </div>

                                <!-- Métadonnées -->
                                <div class="space-y-3 pt-2 border-t border-gray-100 dark:border-gray-700">
                                    <div v-if="detailsTarget.created_by_name">
                                        <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-1">Créé par</p>
                                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ detailsTarget.created_by_name }}</p>
                                    </div>
                                    <div v-if="detailsTarget.created_at">
                                        <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-1">Date de création</p>
                                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ formatDate(detailsTarget.created_at) }}</p>
                                    </div>
                                    <div v-if="detailsTarget.updated_at">
                                        <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-1">Dernière modification</p>
                                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ formatDate(detailsTarget.updated_at) }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Footer drawer -->
                            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex gap-2">
                                <AppButton variant="ghost" class="flex-1" @click="showDetails = false">Fermer</AppButton>
                                <AppButton v-if="canEdit" class="flex-1" @click="() => { showDetails = false; openEdit(detailsTarget!) }">
                                    <template #icon>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </template>
                                    Modifier
                                </AppButton>
                            </div>
                        </div>
                    </Transition>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { PageHeader, AppButton, AppInput, AppSelect, AppModal, DataTable, AppBadge } from '@/Components/UI';
import { useCan } from '@/Composables/useCan';
import { useToast } from '@/Composables/useToast';

const { can } = useCan();
const canCreate = computed(() => can('action.classes.create'));
const canEdit   = computed(() => can('action.classes.edit'));
const canDelete = computed(() => can('action.classes.delete'));

interface ClassItem {
    id: number;
    name: string;
    amount: number;
    status: number;
    created_by_name?: string;
    created_at?: string;
    updated_at?: string;
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

const formId       = 'class-form';
const showForm     = ref(false);
const showDelete   = ref(false);
const showDetails  = ref(false);
const editTarget    = ref<ClassItem | null>(null);
const deleteTarget  = ref<ClassItem | null>(null);
const detailsTarget = ref<ClassItem | null>(null);
const deleting      = ref(false);
const toast         = useToast();
const tableRef      = ref<InstanceType<typeof DataTable> | null>(null);

const statusOptions = [
    { value: '1', label: 'Actif' },
    { value: '0', label: 'Inactif' },
];

const columns = [
    { key: 'name',   label: 'Nom' },
    { key: 'amount', label: 'Montant (FCFA)' },
    { key: 'status', label: 'Statut' },
];

const form = useForm({ name: '', amount: '', status: '1' });

const formatDate = (dateStr: string) => {
    if (!dateStr) return '—';
    return new Date(dateStr).toLocaleDateString('fr-FR', {
        day: '2-digit', month: 'long', year: 'numeric',
        hour: '2-digit', minute: '2-digit',
    });
};

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

const openDetails = (cls: ClassItem) => {
    detailsTarget.value = cls;
    showDetails.value = true;
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

const handleDelete = (ids: (string | number)[]) => {
    ids.forEach(id => {
        router.get(`/admin/class/delete/${id}`, {}, {
            onSuccess: () => toast.success('Classe supprimée avec succès.'),
            onError: () => toast.error('Erreur lors de la suppression.'),
        });
    });
};
</script>
