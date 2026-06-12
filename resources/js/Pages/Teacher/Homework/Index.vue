<template>
    <div class="space-y-6">
        <!-- En-tête -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Mes travaux de maison</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ works.total }} travail(aux)</p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <AppButton variant="ghost" size="sm" href="/teacher/practicalworks/homework/trash">
                    <template #icon>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </template>
                    Corbeille
                </AppButton>
                <AppButton @click="openCreate">
                    <template #icon>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </template>
                    Nouveau travail
                </AppButton>
            </div>
        </div>

        <!-- Table -->
        <DataTable
            :columns="columns"
            :rows="tableRows"
            row-key="id"
            export-filename="mes_devoirs"
            :selectable="false"
        >
            <template #cell-work_date="{ row }">
                <span class="text-sm text-gray-600 dark:text-gray-400">{{ formatDate(row.work_date as string) }}</span>
            </template>
            <template #cell-submission_date="{ row }">
                <span class="text-sm text-gray-600 dark:text-gray-400">{{ formatDate(row.submission_date as string) }}</span>
            </template>
            <template #cell-description="{ row }">
                <span class="line-clamp-2 text-sm text-gray-600 dark:text-gray-400">{{ row.description }}</span>
            </template>
            <template #actions="{ row }">
                <div class="flex items-center justify-end gap-1">
                    <!-- Voir soumissions -->
                    <a
                        :href="`/teacher/practicalworks/homework/submission/${row.id}`"
                        class="p-1.5 rounded-lg text-gray-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors inline-flex"
                        title="Voir les soumissions"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </a>
                    <!-- Modifier -->
                    <button
                        class="p-1.5 rounded-lg text-gray-400 hover:text-warning-600 hover:bg-warning-50 dark:hover:bg-warning-900/20 transition-colors"
                        title="Modifier"
                        @click="openEdit(row.id as number)"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </button>
                    <!-- Supprimer -->
                    <button
                        class="p-1.5 rounded-lg text-gray-400 hover:text-danger-600 hover:bg-danger-50 dark:hover:bg-danger-900/20 transition-colors"
                        title="Mettre à la corbeille"
                        @click="confirmDelete(row.id as number, row.class_name as string)"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            </template>
        </DataTable>

        <!-- Modal Créer -->
        <AppModal v-model="showCreateForm" title="Nouveau travail de maison" size="xl">
            <form :id="createFormId" @submit.prevent="submitCreate" class="space-y-4">
                <AppSelect
                    v-model="createForm.classSubject"
                    label="Classe / Matière"
                    :options="classOptions"
                    required
                />
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <AppInput v-model="createForm.work_date" label="Date du travail" type="date" required />
                    <AppInput v-model="createForm.submission_date" label="Date de remise" type="date" required />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Description</label>
                    <textarea
                        v-model="createForm.description"
                        rows="3"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"
                    />
                </div>
                <!-- Pièces jointes multiples -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Pièces jointes <span class="text-gray-400 font-normal">(optionnel)</span>
                    </label>
                    <div
                        class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4 text-center cursor-pointer hover:border-primary-400 transition-colors"
                        @dragover.prevent
                        @drop.prevent="onDropCreate"
                        @click="createFileInput?.click()"
                    >
                        <svg class="w-8 h-8 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        <p class="text-sm text-gray-500">Glissez vos fichiers ici ou <span class="text-primary-600 font-medium">cliquez pour parcourir</span></p>
                        <p class="text-xs text-gray-400 mt-1">PDF, DOCX, PPTX, images… max 20 Mo par fichier</p>
                        <input ref="createFileInput" type="file" multiple class="hidden" @change="onCreateFileChange" />
                    </div>
                    <div v-if="createFiles.length" class="mt-3 space-y-2">
                        <div
                            v-for="(f, idx) in createFiles"
                            :key="idx"
                            class="flex items-center justify-between bg-gray-50 dark:bg-gray-700/50 rounded-lg px-3 py-2"
                        >
                            <div class="flex items-center gap-2 min-w-0">
                                <FileTypeIcon :filename="f.name" size="sm" />
                                <span class="text-sm text-gray-700 dark:text-gray-300 truncate">{{ f.name }}</span>
                                <span class="text-xs text-gray-400 shrink-0">{{ formatFileSize(f.size) }}</span>
                            </div>
                            <button type="button" @click="removeCreateFile(idx)" class="ml-2 text-gray-400 hover:text-danger-500 transition-colors shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            <template #footer>
                <AppButton variant="ghost" @click="showCreateForm = false">Annuler</AppButton>
                <AppButton type="submit" :form="createFormId" :loading="submitting">Créer</AppButton>
            </template>
        </AppModal>

        <!-- Modal Modifier -->
        <AppModal v-model="showEditForm" title="Modifier le travail" size="xl">
            <div v-if="loadingEdit" class="flex items-center justify-center py-12">
                <svg class="animate-spin w-8 h-8 text-primary-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" />
                </svg>
            </div>
            <form v-else-if="editWork" :id="editFormId" @submit.prevent="submitEdit" class="space-y-4">
                <AppSelect
                    v-model="editForm.classSubject"
                    label="Classe / Matière"
                    :options="classOptions"
                    required
                />
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <AppInput v-model="editForm.work_date" label="Date du travail" type="date" required />
                    <AppInput v-model="editForm.submission_date" label="Date de remise" type="date" required />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Description</label>
                    <textarea
                        v-model="editForm.description"
                        rows="3"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"
                    />
                </div>
                <!-- Pièces jointes existantes -->
                <div v-if="editWork.attachments?.length">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Fichiers existants</label>
                    <div class="space-y-2">
                        <div
                            v-for="att in editWork.attachments"
                            :key="att.id"
                            class="flex items-center justify-between bg-gray-50 dark:bg-gray-700/50 rounded-lg px-3 py-2"
                            :class="{ 'opacity-40 line-through': attachmentsToRemove.includes(att.id) }"
                        >
                            <div class="flex items-center gap-2 min-w-0">
                                <FileTypeIcon :filename="att.file_name" size="sm" />
                                <a :href="att.url" target="_blank" class="text-sm text-primary-600 hover:underline truncate">{{ att.file_name }}</a>
                                <span class="text-xs text-gray-400 shrink-0">{{ att.readable_size }}</span>
                            </div>
                            <button
                                type="button"
                                @click="toggleRemoveAttachment(att.id)"
                                class="ml-2 shrink-0 transition-colors"
                                :class="attachmentsToRemove.includes(att.id)
                                    ? 'text-success-500 hover:text-success-600'
                                    : 'text-gray-400 hover:text-danger-500'"
                                :title="attachmentsToRemove.includes(att.id) ? 'Annuler la suppression' : 'Supprimer ce fichier'"
                            >
                                <svg v-if="attachmentsToRemove.includes(att.id)" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                </svg>
                                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Nouveaux fichiers -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Ajouter des fichiers</label>
                    <div
                        class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4 text-center cursor-pointer hover:border-primary-400 transition-colors"
                        @dragover.prevent
                        @drop.prevent="onDropEdit"
                        @click="editFileInput?.click()"
                    >
                        <p class="text-sm text-gray-500">Glissez ou <span class="text-primary-600 font-medium">cliquez pour parcourir</span></p>
                        <input ref="editFileInput" type="file" multiple class="hidden" @change="onEditFileChange" />
                    </div>
                    <div v-if="editFiles.length" class="mt-3 space-y-2">
                        <div
                            v-for="(f, idx) in editFiles"
                            :key="idx"
                            class="flex items-center justify-between bg-gray-50 dark:bg-gray-700/50 rounded-lg px-3 py-2"
                        >
                            <div class="flex items-center gap-2 min-w-0">
                                <FileTypeIcon :filename="f.name" size="sm" />
                                <span class="text-sm text-gray-700 dark:text-gray-300 truncate">{{ f.name }}</span>
                                <span class="text-xs text-gray-400 shrink-0">{{ formatFileSize(f.size) }}</span>
                            </div>
                            <button type="button" @click="removeEditFile(idx)" class="ml-2 text-gray-400 hover:text-danger-500 transition-colors shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            <template #footer>
                <AppButton variant="ghost" @click="showEditForm = false">Annuler</AppButton>
                <AppButton type="submit" :form="editFormId" :loading="submitting">Enregistrer</AppButton>
            </template>
        </AppModal>

        <!-- Confirm Delete -->
        <ConfirmDialog
            v-model="showConfirmDelete"
            title="Mettre à la corbeille"
            :message="`Le travail « ${deleteTarget.label} » sera déplacé dans la corbeille. Vous pourrez le restaurer.`"
            confirm-label="Mettre à la corbeille"
            confirm-variant="danger"
            @confirm="doDelete"
        />
    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { AppButton, AppInput, AppSelect, AppModal, DataTable, ConfirmDialog, FileTypeIcon } from '@/Components/UI';
import { stripHtml } from '@/Utils/html';
import { useToast } from '@/Composables/useToast';

// ─── Types ────────────────────────────────────────────────────────────────────
interface Work {
    [key: string]: unknown;
    id: number;
    class_id: number;
    subject_id: number;
    class_name: string;
    subject_name: string;
    work_date: string;
    submission_date: string;
    description: string;
}

interface Attachment {
    id: number;
    file_name: string;
    url: string;
    readable_size: string;
}

interface WorkDetail extends Work {
    attachments?: Attachment[];
}

// ─── Props ─────────────────────────────────────────────────────────────────────
const props = defineProps<{
    works: { data: Work[]; total: number; from: number; to: number; links: any[] };
    classes: { class_id: number; class_name: string; subject_id: number; subject_name: string }[];
}>();

// ─── État ──────────────────────────────────────────────────────────────────────
const toast      = useToast();
const createFormId = 'teacher-hw-create';
const editFormId   = 'teacher-hw-edit';

const showCreateForm = ref(false);
const showEditForm   = ref(false);
const submitting     = ref(false);
const loadingEdit    = ref(false);
const editWork       = ref<WorkDetail | null>(null);
const editWorkId     = ref<number | null>(null);

const createFileInput = ref<HTMLInputElement | null>(null);
const editFileInput   = ref<HTMLInputElement | null>(null);
const createFiles     = ref<File[]>([]);
const editFiles       = ref<File[]>([]);
const attachmentsToRemove = ref<number[]>([]);

const createForm = ref({ classSubject: '', work_date: '', submission_date: '', description: '' });
const editForm   = ref({ classSubject: '', work_date: '', submission_date: '', description: '' });

const showConfirmDelete = ref(false);
const deleteTarget = ref({ id: 0, label: '' });

// ─── Computed ─────────────────────────────────────────────────────────────────
const classOptions = computed(() =>
    props.classes.map(c => ({ value: `${c.class_id}:${c.subject_id}`, label: `${c.class_name} — ${c.subject_name}` }))
);

const tableRows = computed(() =>
    props.works.data.map(w => ({ ...w, description: stripHtml(w.description as string, 80) }))
);

const columns = [
    { key: 'class_name',      label: 'Classe' },
    { key: 'subject_name',    label: 'Matière' },
    { key: 'work_date',       label: 'Date' },
    { key: 'submission_date', label: 'Remise' },
    { key: 'description',     label: 'Description' },
];

// ─── Utilitaires ──────────────────────────────────────────────────────────────
const formatDate = (d: string) => {
    if (!d) return '—';
    try { return new Date(d).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' }); }
    catch { return d; }
};

const formatFileSize = (bytes: number) => {
    if (bytes < 1024) return bytes + ' o';
    if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' Ko';
    return (bytes / 1048576).toFixed(1) + ' Mo';
};

const parseClassSubject = (val: string) => {
    const [classId, subjectId] = val.split(':');
    return { class_id: classId, subject_id: subjectId };
};

// ─── Gestion fichiers ─────────────────────────────────────────────────────────
const onCreateFileChange = (e: Event) => {
    const files = (e.target as HTMLInputElement).files;
    if (files) createFiles.value.push(...Array.from(files));
};
const onDropCreate = (e: DragEvent) => {
    if (e.dataTransfer?.files) createFiles.value.push(...Array.from(e.dataTransfer.files));
};
const removeCreateFile = (idx: number) => { createFiles.value.splice(idx, 1); };

const onEditFileChange = (e: Event) => {
    const files = (e.target as HTMLInputElement).files;
    if (files) editFiles.value.push(...Array.from(files));
};
const onDropEdit = (e: DragEvent) => {
    if (e.dataTransfer?.files) editFiles.value.push(...Array.from(e.dataTransfer.files));
};
const removeEditFile = (idx: number) => { editFiles.value.splice(idx, 1); };

const toggleRemoveAttachment = (id: number) => {
    const idx = attachmentsToRemove.value.indexOf(id);
    if (idx === -1) attachmentsToRemove.value.push(id);
    else attachmentsToRemove.value.splice(idx, 1);
};

// ─── Actions ──────────────────────────────────────────────────────────────────
const openCreate = () => {
    createForm.value = { classSubject: '', work_date: '', submission_date: '', description: '' };
    createFiles.value = [];
    showCreateForm.value = true;
};

const submitCreate = () => {
    const { class_id, subject_id } = parseClassSubject(createForm.value.classSubject);
    submitting.value = true;
    const data = new FormData();
    data.append('class_id',        class_id);
    data.append('subject_id',      subject_id);
    data.append('work_date',       createForm.value.work_date);
    data.append('submission_date', createForm.value.submission_date);
    data.append('description',     createForm.value.description);
    createFiles.value.forEach(f => data.append('attachments[]', f));

    router.post('/teacher/practicalworks/homework/add', data, {
        onSuccess: () => { showCreateForm.value = false; toast.success('Travail créé avec succès.'); },
        onError:   (errors) => { toast.error(Object.values(errors)[0] as string || 'Erreur.'); },
        onFinish:  () => { submitting.value = false; },
    });
};

const openEdit = async (id: number) => {
    editWorkId.value   = id;
    loadingEdit.value  = true;
    editWork.value     = null;
    editFiles.value    = [];
    attachmentsToRemove.value = [];
    showEditForm.value = true;

    try {
        const res  = await fetch(`/teacher/practicalworks/homework/edit-json/${id}`, { headers: { Accept: 'application/json' } });
        if (!res.ok) { toast.error('Travail introuvable.'); showEditForm.value = false; return; }
        const json = await res.json();
        editWork.value = json.work;
        editForm.value = {
            classSubject:    `${json.work.class_id}:${json.work.subject_id}`,
            work_date:       json.work.work_date,
            submission_date: json.work.submission_date,
            description:     stripHtml(json.work.description ?? ''),
        };
    } catch {
        toast.error('Erreur lors du chargement.');
        showEditForm.value = false;
    } finally {
        loadingEdit.value = false;
    }
};

const submitEdit = () => {
    if (!editWorkId.value) return;
    const { class_id, subject_id } = parseClassSubject(editForm.value.classSubject);
    submitting.value = true;
    const data = new FormData();
    data.append('class_id',        class_id);
    data.append('subject_id',      subject_id);
    data.append('work_date',       editForm.value.work_date);
    data.append('submission_date', editForm.value.submission_date);
    data.append('description',     editForm.value.description);
    attachmentsToRemove.value.forEach(id => data.append('remove_attachments[]', String(id)));
    editFiles.value.forEach(f => data.append('attachments[]', f));

    router.post(`/teacher/practicalworks/homework/edit/${editWorkId.value}`, data, {
        onSuccess: () => { showEditForm.value = false; toast.success('Travail modifié avec succès.'); },
        onError:   (errors) => { toast.error(Object.values(errors)[0] as string || 'Erreur.'); },
        onFinish:  () => { submitting.value = false; },
    });
};

const confirmDelete = (id: number, label: string) => {
    deleteTarget.value = { id, label };
    showConfirmDelete.value = true;
};

const doDelete = () => {
    router.get(`/teacher/practicalworks/homework/delete/${deleteTarget.value.id}`, {}, {
        onSuccess: () => toast.success('Travail déplacé dans la corbeille.'),
        onError:   () => toast.error('Erreur lors de la suppression.'),
    });
};
</script>
