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
        <DataTable
            ref="tableRef"
            :columns="columns"
            :rows="classSubjects.data"
            row-key="id"
            export-filename="assignations-matieres"
            :context-menu="true"
            @delete="handleDelete"
        >
            <template #cell-status="{ row }">
                <AppBadge :variant="row.status == 1 ? 'success' : 'danger'" dot>
                    {{ row.status == 1 ? 'Actif' : 'Inactif' }}
                </AppBadge>
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
                    <button title="Modifier" @click="openEdit(row as any)"
                            class="p-1.5 rounded-lg transition-all duration-150
                                   text-white bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700
                                   shadow-sm shadow-emerald-200 dark:shadow-emerald-900/40">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </button>
                    <button title="Supprimer" @click="tableRef?.confirmDelete(row.id as number, `${row.class_name} - ${row.subject_name}`)"
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
                <button @click="openEdit(row as any)"
                        class="flex w-full items-center gap-2.5 px-3.5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-emerald-50 dark:hover:bg-gray-700/60 hover:text-emerald-700 transition-colors">
                    <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Modifier
                </button>
                <div class="my-1 border-t border-gray-100 dark:border-gray-700"/>
                <button @click="tableRef?.confirmDelete(row.id as number, `${row.class_name} - ${row.subject_name}`)"
                        class="flex w-full items-center gap-2.5 px-3.5 py-2.5 text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Supprimer
                </button>
            </template>
        </DataTable>

        <!-- Modal Créer -->
        <AppModal v-model="showCreateForm" title="Nouvelle assignation" size="md">
            <form :id="createFormId" @submit.prevent="submitCreate" class="space-y-4">
                <AppSelect
                    v-model="createForm.class_id"
                    label="Classe"
                    :options="classOptions"
                    placeholder="Sélectionner une classe"
                    required
                    :error="createForm.errors.class_id"
                />
                <AppMultiSelect
                    v-model="createForm.subject_ids"
                    label="Matières"
                    :options="subjectOptions"
                    placeholder="Sélectionner des matières"
                    required
                    :error="createForm.errors.subject_ids"
                />
                <AppInput v-model="createForm.coefficient" label="Coefficient" type="number" min="1" required :error="createForm.errors.coefficient" />
                <AppSelect v-model="createForm.status" label="Statut" :options="statusOptions" required :error="createForm.errors.status" />
            </form>
            <template #footer>
                <AppButton variant="ghost" @click="showCreateForm = false">Annuler</AppButton>
                <AppButton type="submit" :form="createFormId" :loading="createForm.processing">Assigner</AppButton>
            </template>
        </AppModal>

        <!-- Modal Modifier (single) -->
        <AppModal v-model="showEditForm" title="Modifier l'assignation" size="md">
            <form :id="editFormId" @submit.prevent="submitEdit" class="space-y-4">
                <AppSelect
                    v-model="editForm.class_id"
                    label="Classe"
                    :options="classOptions"
                    placeholder="Sélectionner une classe"
                    required
                    :error="editForm.errors.class_id"
                />
                <AppSelect
                    v-model="editForm.subject_id"
                    label="Matière"
                    :options="subjectOptions"
                    placeholder="Sélectionner une matière"
                    required
                    :error="editForm.errors.subject_id"
                />
                <AppInput v-model="editForm.coefficient" label="Coefficient" type="number" min="1" required :error="editForm.errors.coefficient" />
                <AppSelect v-model="editForm.status" label="Statut" :options="statusOptions" required :error="editForm.errors.status" />
            </form>
            <template #footer>
                <AppButton variant="ghost" @click="showEditForm = false">Annuler</AppButton>
                <AppButton type="submit" :form="editFormId" :loading="editForm.processing">Enregistrer</AppButton>
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
                    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="showDetails = false" />

                    <Transition
                        enter-active-class="transition-transform duration-300 ease-out"
                        leave-active-class="transition-transform duration-200 ease-in"
                        enter-from-class="translate-x-full"
                        enter-to-class="translate-x-0"
                        leave-from-class="translate-x-0"
                        leave-to-class="translate-x-full"
                    >
                        <div v-if="showDetails" class="relative w-full max-w-sm bg-white dark:bg-gray-900 h-full shadow-2xl flex flex-col">
                            <!-- Header -->
                            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Détails de l'assignation</h2>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Matière — Classe</p>
                                    </div>
                                </div>
                                <button @click="showDetails = false" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </div>

                            <!-- Body -->
                            <div v-if="detailsTarget" class="flex-1 overflow-y-auto px-6 py-5 space-y-5">
                                <!-- Infos principales -->
                                <div class="rounded-xl bg-indigo-50 dark:bg-indigo-900/20 p-4 space-y-3">
                                    <div>
                                        <p class="text-xs font-medium text-indigo-400 uppercase tracking-wide mb-1">Classe</p>
                                        <p class="text-base font-semibold text-gray-900 dark:text-white">{{ detailsTarget.class_name }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-medium text-indigo-400 uppercase tracking-wide mb-1">Matière</p>
                                        <p class="text-base font-semibold text-gray-900 dark:text-white">{{ detailsTarget.subject_name }}</p>
                                    </div>
                                </div>

                                <!-- Coefficient -->
                                <div class="flex items-center justify-between rounded-xl bg-gray-50 dark:bg-gray-800 p-4">
                                    <div>
                                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Coefficient</p>
                                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ detailsTarget.coefficient ?? 1 }}</p>
                                    </div>
                                    <div class="w-12 h-12 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                    </div>
                                </div>

                                <!-- Statut -->
                                <div>
                                    <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-2">Statut</p>
                                    <AppBadge :variant="detailsTarget.status == 1 ? 'success' : 'danger'" dot>
                                        {{ detailsTarget.status == 1 ? 'Actif' : 'Inactif' }}
                                    </AppBadge>
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

                            <!-- Footer -->
                            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex gap-2">
                                <AppButton variant="ghost" class="flex-1" @click="showDetails = false">Fermer</AppButton>
                                <AppButton class="flex-1" @click="() => { showDetails = false; openEdit(detailsTarget!) }">
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
import { AppButton, AppInput, AppSelect, AppModal, DataTable, AppBadge } from '@/Components/UI';
import AppMultiSelect from '@/Components/UI/AppMultiSelect.vue';
import { useToast } from '@/Composables/useToast';

interface ClassSubject {
    id: number;
    class_id: number;
    subject_id: number;
    class_name: string;
    subject_name: string;
    status: number;
    coefficient: number;
    created_by_name?: string;
    created_at?: string;
    updated_at?: string;
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

const createFormId = 'assign-subject-create-form';
const editFormId   = 'assign-subject-edit-form';

const showCreateForm = ref(false);
const showEditForm   = ref(false);
const showDetails    = ref(false);

const editTarget    = ref<ClassSubject | null>(null);
const detailsTarget = ref<ClassSubject | null>(null);

const deleting = ref(false);
const toast    = useToast();
const tableRef = ref<InstanceType<typeof DataTable> | null>(null);

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

const createForm = useForm({
    class_id:    '',
    subject_ids: [] as string[],
    coefficient: '1',
    status:      '1',
});

const editForm = useForm({
    class_id:    '',
    subject_id:  '',
    coefficient: '1',
    status:      '1',
});

const formatDate = (dateStr: string) => {
    if (!dateStr) return '—';
    return new Date(dateStr).toLocaleDateString('fr-FR', {
        day: '2-digit', month: 'long', year: 'numeric',
        hour: '2-digit', minute: '2-digit',
    });
};

// ── Ouvrir créer ─────────────────────────────────────────────────────────────
const openCreate = () => {
    createForm.reset();
    createForm.coefficient = '1';
    createForm.status = '1';
    showCreateForm.value = true;
};

// ── Ouvrir modifier ──────────────────────────────────────────────────────────
const openEdit = (item: ClassSubject) => {
    editTarget.value = item;
    editForm.class_id    = String(item.class_id);
    editForm.subject_id  = String(item.subject_id);
    editForm.coefficient = String(item.coefficient ?? 1);
    editForm.status      = String(item.status);
    showEditForm.value = true;
};

// ── Ouvrir détails ───────────────────────────────────────────────────────────
const openDetails = (item: ClassSubject) => {
    detailsTarget.value = item;
    showDetails.value = true;
};

// ── Soumettre créer ──────────────────────────────────────────────────────────
const submitCreate = () => {
    const data = new FormData();
    data.append('class_id', createForm.class_id);
    data.append('coefficient', createForm.coefficient);
    data.append('status', createForm.status);
    createForm.subject_ids.forEach(id => data.append('subject_id[]', id));

    router.post('/admin/assign_subject/add', data, {
        onSuccess: () => {
            showCreateForm.value = false;
            createForm.reset();
            createForm.coefficient = '1';
            createForm.status = '1';
        },
    });
};

// ── Soumettre modifier ───────────────────────────────────────────────────────
const submitEdit = () => {
    if (!editTarget.value) return;
    editForm.post(`/admin/assign_subject/edit_single/${editTarget.value.id}`, {
        onSuccess: () => { showEditForm.value = false; },
    });
};

// ── Supprimer (bulk via DataTable) ───────────────────────────────────────────
const handleDelete = (ids: (string | number)[]) => {
    ids.forEach(id => {
        router.get(`/admin/assign_subject/delete/${id}`, {}, {
            onSuccess: () => toast.success('Assignation supprimée avec succès.'),
            onError: () => toast.error('Erreur lors de la suppression.'),
        });
    });
};
</script>
