<template>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Travaux de maison</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ works.total }} travail(aux) enregistré(s)</p>
            </div>
            <AppButton @click="openCreate">
                <template #icon>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                </template>
                Nouveau travail
            </AppButton>
        </div>

        <DataTable
            ref="tableRef"
            :columns="columns"
            :rows="tableRows"
            row-key="id"
            export-filename="travaux_maison"
            @delete="handleDelete"
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
                    <button
                        class="p-1.5 rounded-lg text-gray-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors"
                        title="Voir les détails"
                        @click="openDetails(row.id as number)"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                    </button>
                    <button
                        class="p-1.5 rounded-lg text-gray-400 hover:text-danger-600 hover:bg-danger-50 dark:hover:bg-danger-900/20 transition-colors"
                        title="Supprimer"
                        @click="tableRef?.confirmDelete(row.id as number, `le travail de ${row.class_name}`)"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>
                </div>
            </template>
        </DataTable>

        <!-- Modal Créer -->
        <AppModal v-model="showForm" title="Nouveau travail de maison" size="lg">
            <form :id="formId" @submit.prevent="submitForm" class="space-y-4">
                <AppSelect v-model="form.class_id" label="Classe" :options="classOptions" required :error="form.errors.class_id" @change="loadSubjects" />
                <AppSelect v-model="form.subject_id" label="Matière" :options="subjectOptions" required :error="form.errors.subject_id" />
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <AppInput v-model="form.work_date" label="Date du travail" type="date" required />
                    <AppInput v-model="form.submission_date" label="Date de remise" type="date" required />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Description</label>
                    <textarea v-model="form.description" rows="3" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Document (optionnel)</label>
                    <input type="file" @change="onFileChange" class="text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-primary-50 file:text-primary-700" />
                </div>
            </form>
            <template #footer>
                <AppButton variant="ghost" @click="showForm = false">Annuler</AppButton>
                <AppButton type="submit" :form="formId" :loading="submitting">Créer</AppButton>
            </template>
        </AppModal>

        <!-- Modal Détails -->
        <AppModal v-model="showDetails" title="Détails du travail" size="xl">
            <div v-if="loadingDetails" class="flex items-center justify-center py-12">
                <svg class="animate-spin w-8 h-8 text-primary-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" />
                </svg>
            </div>
            <div v-else-if="detailWork" class="space-y-5">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div><p class="text-xs text-gray-500 uppercase font-semibold">Classe</p><p class="text-sm font-medium text-gray-900 dark:text-white mt-1">{{ detailWork.class_name }}</p></div>
                    <div><p class="text-xs text-gray-500 uppercase font-semibold">Matière</p><p class="text-sm font-medium text-gray-900 dark:text-white mt-1">{{ detailWork.subject_name }}</p></div>
                    <div><p class="text-xs text-gray-500 uppercase font-semibold">Date travail</p><p class="text-sm font-medium text-gray-900 dark:text-white mt-1">{{ formatDate(detailWork.work_date) }}</p></div>
                    <div><p class="text-xs text-gray-500 uppercase font-semibold">Date remise</p><p class="text-sm font-medium text-gray-900 dark:text-white mt-1">{{ formatDate(detailWork.submission_date) }}</p></div>
                </div>
                <div v-if="detailWork.description" class="bg-gray-50 dark:bg-gray-700/40 rounded-xl p-4">
                    <p class="text-xs text-gray-500 uppercase font-semibold mb-2">Description</p>
                    <div class="text-sm text-gray-700 dark:text-gray-300 prose prose-sm dark:prose-invert max-w-none" v-html="detailWork.description" />
                </div>
                <div v-if="detailWork.document_file">
                    <a :href="`/upload/practicalworks/${detailWork.document_file}`" target="_blank" class="inline-flex items-center gap-2 text-sm text-primary-600 hover:text-primary-700 font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                        Télécharger le document
                    </a>
                </div>
                <div v-if="detailWork.homeworks?.length">
                    <p class="text-xs text-gray-500 uppercase font-semibold mb-3">Soumissions ({{ detailWork.homeworks.length }})</p>
                    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-800/60">
                                <tr>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Apprenant</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Statut</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Document</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                <tr v-for="hw in detailWork.homeworks" :key="hw.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ hw.student_last_name }} {{ hw.student_name }}</td>
                                    <td class="px-4 py-3"><AppBadge variant="success" dot>{{ hw.status }}</AppBadge></td>
                                    <td class="px-4 py-3 text-gray-500 text-xs">{{ formatDate(hw.created_at) }}</td>
                                    <td class="px-4 py-3">
                                        <a v-if="hw.document_file" :href="`/upload/homeworks/${hw.document_file}`" target="_blank" class="text-primary-600 hover:underline text-xs">Voir</a>
                                        <span v-else class="text-gray-400 text-xs">—</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <p v-else class="text-sm text-gray-400 text-center py-4">Aucune soumission pour ce travail.</p>
            </div>
        </AppModal>
    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import { AppButton, AppInput, AppSelect, AppModal, AppBadge, DataTable } from '@/Components/UI';
import { stripHtml } from '@/Utils/html';
import { useToast } from '@/Composables/useToast';

interface Work {
    [key: string]: unknown;
    id: number; class_name: string; subject_name: string;
    work_date: string; submission_date: string; description: string;
}

interface WorkDetail extends Work {
    document_file: string | null;
    homeworks?: { id: number; student_name: string; student_last_name: string; status: string; document_file: string | null; created_at: string }[];
}

const props = defineProps<{
    works: { data: Work[]; total: number; from: number; to: number; links: any[] };
    classes: { id: number; name: string }[];
}>();

const toast    = useToast();
const tableRef = ref<InstanceType<typeof DataTable> | null>(null);
const formId   = 'homework-form';
const showForm = ref(false); const showDetails = ref(false);
const submitting = ref(false); const loadingDetails = ref(false);
const detailWork = ref<WorkDetail | null>(null);
const subjects   = ref<{ id: number; name: string }[]>([]);
const docFile    = ref<File | null>(null);

const classOptions   = computed(() => props.classes.map(c => ({ value: String(c.id), label: c.name })));
const subjectOptions = computed(() => subjects.value.map(s => ({ value: String(s.id), label: s.name })));

const tableRows = computed(() => props.works.data.map(w => ({ ...w, description: stripHtml(w.description as string, 80) })));

const columns = [
    { key: 'class_name',      label: 'Classe' },
    { key: 'subject_name',    label: 'Matière' },
    { key: 'work_date',       label: 'Date' },
    { key: 'submission_date', label: 'Remise' },
    { key: 'description',     label: 'Description' },
];

const form = useForm({ class_id: '', subject_id: '', work_date: '', submission_date: '', description: '' });

const formatDate = (d: string) => {
    if (!d) return '—';
    try { return new Date(d).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' }); }
    catch { return d; }
};

const openCreate = () => { form.reset(); subjects.value = []; showForm.value = true; };

const loadSubjects = async () => {
    if (!form.class_id) return;
    const res = await fetch(`/admin/practicalworks/get_subject/${form.class_id}`);
    const data = await res.json();
    subjects.value = (data.getSubject ?? []).map((s: any) => ({ id: s.subject_id, name: s.subject_name }));
};

const onFileChange = (e: Event) => { docFile.value = (e.target as HTMLInputElement).files?.[0] ?? null; };

const submitForm = () => {
    submitting.value = true;
    const data = new FormData();
    data.append('class_id', form.class_id); data.append('subject_id', form.subject_id);
    data.append('work_date', form.work_date); data.append('submission_date', form.submission_date);
    data.append('description', form.description);
    if (docFile.value) data.append('document_file', docFile.value);
    router.post('/admin/practicalworks/homework/create', data, {
        onSuccess: () => { showForm.value = false; toast.success('Travail créé avec succès.'); },
        onFinish:  () => { submitting.value = false; },
    });
};

const openDetails = async (id: number) => {
    showDetails.value = true; loadingDetails.value = true; detailWork.value = null;
    try {
        const res  = await fetch(`/admin/practicalworks/homework/details-json/${id}`, { headers: { 'Accept': 'application/json' } });
        const json = await res.json();
        detailWork.value = json.work ?? null;
    } catch { detailWork.value = null; }
    finally { loadingDetails.value = false; }
};

const handleDelete = (ids: (string | number)[]) => {
    ids.forEach(id => {
        router.get(`/admin/practicalworks/homework/delete/${id}`, {}, {
            onSuccess: () => toast.success('Travail supprimé.'),
            onError:   () => toast.error('Erreur lors de la suppression.'),
        });
    });
};
</script>
