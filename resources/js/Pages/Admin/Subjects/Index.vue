<template>
    <div class="space-y-6">
        <!-- Header -->
        <PageHeader title="Matières" :subtitle="`${subjects.total} matière(s) enregistrée(s)`" color="violet">
            <template #icon>
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </template>
            <template #actions>
                <AppButton v-if="canCreate" @click="openCreate">
                    <template #icon>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    </template>
                    Nouvelle matière
                </AppButton>
            </template>
        </PageHeader>

        <!-- Table -->
        <DataTable
            ref="tableRef"
            :columns="columns"
            :rows="subjects.data"
            row-key="id"
            export-filename="matieres"
            :context-menu="true"
            @delete="handleDelete"
        >
            <template #cell-type="{ row }">
                <AppBadge :variant="typeVariant(row.type)">
                    {{ typeLabel(row.type) }}
                </AppBadge>
            </template>
            <template #cell-status="{ row }">
                <AppBadge :variant="row.status == 1 ? 'success' : 'danger'" dot>
                    {{ row.status == 1 ? 'Actif' : 'Inactif' }}
                </AppBadge>
            </template>
            <template #actions="{ row }">
                <div class="flex items-center justify-end gap-1.5">
                    <button title="Voir les détails" @click="openDetails(row as any)"
                            class="p-1.5 rounded-xl transition-all duration-150
                                   text-white bg-violet-500 hover:bg-violet-600 active:bg-violet-700
                                   shadow-sm shadow-violet-200 dark:shadow-violet-900/40">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                    <button v-if="canEdit" title="Modifier" @click="openEdit(row as any)"
                            class="p-1.5 rounded-xl transition-all duration-150
                                   text-white bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700
                                   shadow-sm shadow-emerald-200 dark:shadow-emerald-900/40">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </button>
                    <button v-if="canDelete" title="Supprimer" @click="tableRef?.confirmDelete(row.id as number, row.name as string)"
                            class="p-1.5 rounded-xl transition-all duration-150
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
        <AppModal v-model="showForm" :title="editTarget ? 'Modifier la matière' : 'Nouvelle matière'" size="md">
            <form :id="formId" @submit.prevent="submitForm" class="space-y-4">
                <AppInput v-model="form.name" label="Nom de la matière" required :error="form.errors.name" />
                <AppSelect v-model="form.type" label="Type" :options="typeOptions" required :error="form.errors.type" />
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
        <AppModal v-model="showDelete" title="Supprimer la matière" size="sm" persistent>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Voulez-vous vraiment supprimer la matière <strong class="text-gray-900 dark:text-white">{{ deleteTarget?.name }}</strong> ?
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
                                    <div class="w-9 h-9 rounded-xl bg-violet-100 dark:bg-violet-900/30 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Détails de la matière</h2>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Informations complètes</p>
                                    </div>
                                </div>
                                <button @click="showDetails = false" class="p-1.5 rounded-xl bg-red-500 hover:bg-red-600 text-white transition-colors flex-shrink-0 shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </div>

                            <!-- Body -->
                            <div v-if="detailsTarget" class="flex-1 overflow-y-auto px-6 py-5 space-y-5">
                                <div>
                                    <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-1">Nom de la matière</p>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ detailsTarget.name }}</p>
                                </div>

                                <div>
                                    <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-2">Type</p>
                                    <AppBadge :variant="typeVariant(detailsTarget.type)">
                                        {{ typeLabel(detailsTarget.type) }}
                                    </AppBadge>
                                </div>

                                <div>
                                    <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-2">Statut</p>
                                    <AppBadge :variant="detailsTarget.status == 1 ? 'success' : 'danger'" dot>
                                        {{ detailsTarget.status == 1 ? 'Actif' : 'Inactif' }}
                                    </AppBadge>
                                </div>

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
import { fmtDate } from '@/Utils/dateFormat';

const { can } = useCan();
const canCreate = computed(() => can('action.subjects.create'));
const canEdit   = computed(() => can('action.subjects.edit'));
const canDelete = computed(() => can('action.subjects.delete'));

interface Subject {
    id: number;
    name: string;
    type: string;
    status: number;
    created_by_name?: string;
    created_at?: string;
    updated_at?: string;
}

const props = defineProps<{
    subjects: {
        data: Subject[];
        total: number;
        from: number;
        to: number;
        links: { url: string | null; label: string; active: boolean }[];
    };
}>();

const formId        = 'subject-form';
const showForm      = ref(false);
const showDelete    = ref(false);
const showDetails   = ref(false);
const editTarget    = ref<Subject | null>(null);
const deleteTarget  = ref<Subject | null>(null);
const detailsTarget = ref<Subject | null>(null);
const deleting      = ref(false);
const toast         = useToast();
const tableRef      = ref<InstanceType<typeof DataTable> | null>(null);

const statusOptions = [
    { value: '1', label: 'Actif' },
    { value: '0', label: 'Inactif' },
];

const typeOptions = [
    { value: 'theoretical', label: 'Théorique' },
    { value: 'practical',   label: 'Pratique' },
    { value: 'technical',   label: 'Technique' },
    { value: 'sport',       label: 'Sport / EPS' },
    { value: 'artistic',    label: 'Artistique' },
    { value: 'language',    label: 'Langue' },
];

const typeMap: Record<string, { label: string; variant: string }> = {
    theoretical: { label: 'Théorique',   variant: 'info'    },
    practical:   { label: 'Pratique',    variant: 'purple'  },
    technical:   { label: 'Technique',   variant: 'cyan'    },
    sport:       { label: 'Sport / EPS', variant: 'success' },
    artistic:    { label: 'Artistique',  variant: 'warning' },
    language:    { label: 'Langue',      variant: 'amber'   },
};

const typeLabel   = (type: string) => typeMap[type]?.label   ?? type;
const typeVariant = (type: string) => typeMap[type]?.variant ?? 'info';

const columns = [
    { key: 'name',            label: 'Nom de la matière', sortable: true  },
    { key: 'type',            label: 'Type',              sortable: true  },
    { key: 'status',          label: 'Statut',            sortable: true, exportFormat: (v: unknown) => (v == 1 ? 'Actif' : 'Inactif')  },
    { key: 'created_by_name', label: 'Créé par',          sortable: false },
    { key: 'created_at',      label: 'Date création',     sortable: true,
      format: (v: unknown) => fmtDate(v as string) },
];

const form = useForm({ name: '', type: 'theoretical', status: '1' });

const formatDate = fmtDate;

const openCreate = () => {
    editTarget.value = null;
    form.reset();
    form.type   = 'theoretical';
    form.status = '1';
    showForm.value = true;
};

const openEdit = (subject: Subject) => {
    editTarget.value = subject;
    form.name   = subject.name;
    form.type   = subject.type;
    form.status = String(subject.status);
    showForm.value = true;
};

const openDetails = (subject: Subject) => {
    detailsTarget.value = subject;
    showDetails.value = true;
};

const openDelete = (subject: Subject) => {
    deleteTarget.value = subject;
    showDelete.value = true;
};

const submitForm = () => {
    if (editTarget.value) {
        form.post(`/admin/subject/edit/${editTarget.value.id}`, {
            onSuccess: () => { showForm.value = false; },
        });
    } else {
        form.post('/admin/subject/add', {
            onSuccess: () => { showForm.value = false; },
        });
    }
};

const confirmDelete = () => {
    if (!deleteTarget.value) return;
    deleting.value = true;
    router.get(`/admin/subject/delete/${deleteTarget.value.id}`, {}, {
        onFinish: () => { deleting.value = false; showDelete.value = false; },
    });
};

const handleDelete = (ids: (string | number)[]) => {
    ids.forEach(id => {
        router.get(`/admin/subject/delete/${id}`, {}, {
            onSuccess: () => toast.success('Matière supprimée avec succès.'),
            onError: () => toast.error('Erreur lors de la suppression.'),
        });
    });
};
</script>
