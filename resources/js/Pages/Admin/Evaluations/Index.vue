<template>
    <div class="space-y-6">

        <!-- En-tête -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Évaluations</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    Gestion des évaluations selon le système béninois
                </p>
            </div>
            <AppButton v-if="can('action.exams.create')" @click="openCreate">
                <template #icon>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </template>
                Nouvelle évaluation
            </AppButton>
        </div>

        <!-- Légende des types -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div v-for="(label, key) in typeLabels" :key="key"
                class="flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800">
                <span class="w-3 h-3 rounded-full flex-shrink-0" :style="{ background: typeColors[key] }"/>
                <div>
                    <p class="text-xs font-semibold text-gray-900 dark:text-white">{{ label }}</p>
                    <p class="text-[10px] text-gray-400">Coeff. {{ typeCoeffs[key] }}</p>
                </div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            <AppSelect v-model="filters.class_id" :options="classOptions" placeholder="Toutes les classes" :block="true" @change="applyFilters" />
            <AppSelect v-model="filters.period_id" :options="periodOptions" placeholder="Toutes les périodes" :block="true" @change="applyFilters" />
            <AppSelect v-model="filters.type" :options="typeOptions" placeholder="Tous les types" :block="true" @change="applyFilters" />
            <AppSelect v-model="filters.status" :options="statusOptions" placeholder="Tous les statuts" :block="true" @change="applyFilters" />
        </div>

        <!-- Table -->
        <DataTable
            ref="tableRef"
            :columns="columns"
            :rows="evaluations.data"
            row-key="id"
            :pagination="evaluations"
            @delete="handleDelete"
        >
            <template #cell-type="{ row }">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold"
                    :style="{ background: typeColors[row.type] + '20', color: typeColors[row.type] }">
                    <span class="w-1.5 h-1.5 rounded-full" :style="{ background: typeColors[row.type] }"/>
                    {{ typeLabels[row.type] ?? row.type }}
                </span>
            </template>

            <template #cell-coefficient="{ row }">
                <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-bold bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400">
                    ×{{ row.coefficient }}
                </span>
            </template>

            <template #cell-status="{ row }">
                <AppBadge :variant="statusVariant(row.status)" dot>{{ statusLabel(row.status) }}</AppBadge>
            </template>

            <template #cell-max_score="{ row }">
                <span class="text-sm text-gray-600 dark:text-gray-400">/{{ row.max_score }}</span>
            </template>

            <template #actions="{ row }">
                <div class="flex items-center justify-end gap-1">
                    <!-- Saisie des notes -->
                    <a :href="`/admin/evaluations/grade-entry?evaluation_id=${row.id}`"
                        class="p-1.5 rounded-lg text-gray-400 hover:text-success-600 hover:bg-success-50 dark:hover:bg-success-900/20 transition-colors"
                        title="Saisir les notes">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </a>
                    <!-- Modifier statut -->
                    <button v-if="can('action.exams.edit') && row.status !== 'validated'"
                        class="p-1.5 rounded-lg text-gray-400 hover:text-warning-600 hover:bg-warning-50 dark:hover:bg-warning-900/20 transition-colors"
                        title="Valider l'évaluation"
                        @click="validateEval(row as any)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </button>
                    <!-- Éditer -->
                    <button v-if="can('action.exams.edit')"
                        class="p-1.5 rounded-lg text-gray-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors"
                        @click="openEdit(row as any)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </button>
                    <!-- Supprimer -->
                    <button v-if="can('action.exams.delete')"
                        class="p-1.5 rounded-lg text-gray-400 hover:text-danger-600 hover:bg-danger-50 dark:hover:bg-danger-900/20 transition-colors"
                        @click="tableRef?.confirmDelete(row.id as number, row.title || (typeLabels[row.type] ?? row.type) as string)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </template>
        </DataTable>

        <!-- Modal Créer/Modifier -->
        <AppModal v-model="showForm" :title="editTarget ? 'Modifier l\'évaluation' : 'Nouvelle évaluation'" size="lg">
            <form :id="formId" @submit.prevent="submitForm" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <AppSelect v-model="form.class_id" label="Classe" :options="classOptions" required :error="form.errors.class_id" @change="onClassChange" />
                    <AppSelect v-model="form.subject_id" label="Matière" :options="subjectOptions" required :error="form.errors.subject_id" />
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <AppSelect v-model="form.period_id" label="Période" :options="periodOptions" required :error="form.errors.period_id" />
                    <AppInput v-model="form.eval_date" label="Date" type="date" required :error="form.errors.eval_date" />
                </div>

                <!-- Type d'évaluation avec aide visuelle -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type d'évaluation</label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        <button v-for="(label, key) in typeLabels" :key="key" type="button"
                            :class="[
                                'flex flex-col items-center gap-1.5 p-3 rounded-xl border-2 transition-all text-center',
                                form.type === key
                                    ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20'
                                    : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600',
                            ]"
                            @click="selectType(key)">
                            <span class="w-3 h-3 rounded-full" :style="{ background: typeColors[key] }"/>
                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ label }}</span>
                            <span class="text-[10px] text-gray-400">Coeff. {{ typeCoeffs[key] }}</span>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <AppInput v-model="form.coefficient" label="Coefficient" type="number" min="1" max="10" required />
                    <AppInput v-model="form.max_score" label="Note max" type="number" min="1" max="100" />
                    <AppInput v-model="form.title" label="Titre (optionnel)" placeholder="ex: Interrogation N°1" />
                </div>
            </form>

            <!-- Aide contextuelle -->
            <div class="mt-4 p-3 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800">
                <p class="text-xs text-blue-700 dark:text-blue-300 font-medium">
                    💡 Les champs de saisie de notes (interrogation, devoir surveillé, etc.) seront disponibles lors de la saisie des notes.
                    Le coefficient peut être ajusté manuellement même si le type est déjà sélectionné.
                </p>
            </div>

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
import { ref, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { AppButton, AppInput, AppSelect, AppModal, DataTable, AppBadge } from '@/Components/UI';
import { useCan } from '@/Composables/useCan';
import { useToast } from '@/Composables/useToast';
import axios from 'axios';

const { can } = useCan();
const toast   = useToast();

interface Evaluation {
    id: number;
    class_id: number;
    class_name: string;
    subject_id: number;
    subject_name: string;
    period_id: number;
    period_name: string;
    type: string;
    coefficient: number;
    max_score: number;
    eval_date: string;
    title: string;
    status: string;
}

const props = defineProps<{
    evaluations: { data: Evaluation[]; total: number; from: number; to: number; links: any[] };
    classes:     { id: number; name: string }[];
    subjects:    { id: number; name: string }[];
    periods:     { id: number; name: string }[];
    typeLabels:  Record<string, string>;
    typeCoeffs:  Record<string, number>;
}>();

// Couleurs fixes par type
const typeColors: Record<string, string> = {
    interrogation:    '#3b82f6',
    devoir_surveille: '#f59e0b',
    travail_maison:   '#10b981',
    examen_blanc:     '#ef4444',
};

const formId     = 'eval-form';
const showForm   = ref(false);
const editTarget = ref<Evaluation | null>(null);
const tableRef   = ref<any>(null);
const dynamicSubjects = ref<{ id: number; name: string }[]>([]);

// Filtres
const filters = ref({ class_id: '', period_id: '', type: '', status: '' });

const classOptions  = computed(() => props.classes.map(c => ({ value: String(c.id), label: c.name })));
const periodOptions = computed(() => props.periods.map(p => ({ value: String(p.id), label: p.name })));
const typeOptions   = computed(() => Object.entries(props.typeLabels).map(([k, v]) => ({ value: k, label: v })));
const statusOptions = [
    { value: 'draft',     label: 'Brouillon' },
    { value: 'open',      label: 'Ouverte' },
    { value: 'closed',    label: 'Fermée' },
    { value: 'validated', label: 'Validée' },
];

const subjectOptions = computed(() => {
    const list = dynamicSubjects.value.length ? dynamicSubjects.value : props.subjects;
    return list.map(s => ({ value: String(s.id), label: s.name }));
});

const form = useForm({
    class_id:    '',
    subject_id:  '',
    period_id:   '',
    teacher_id:  '',
    type:        'interrogation',
    coefficient: '1',
    max_score:   '20',
    eval_date:   '',
    title:       '',
});

const selectType = (key: string) => {
    form.type        = key;
    form.coefficient = String(props.typeCoeffs[key] ?? 1);
};

const onClassChange = async () => {
    if (!form.class_id) return;
    try {
        const res = await axios.get(`/admin/evaluations/subjects-by-class/${form.class_id}`);
        dynamicSubjects.value = res.data;
    } catch {
        dynamicSubjects.value = [];
    }
};

const openCreate = () => {
    editTarget.value = null;
    form.reset();
    form.type        = 'interrogation';
    form.coefficient = '1';
    form.max_score   = '20';
    showForm.value   = true;
};

const openEdit = (eval_: Evaluation) => {
    editTarget.value = eval_;
    form.class_id    = String(eval_.class_id);
    form.subject_id  = String(eval_.subject_id);
    form.period_id   = String(eval_.period_id);
    form.type        = eval_.type;
    form.coefficient = String(eval_.coefficient);
    form.max_score   = String(eval_.max_score);
    form.eval_date   = eval_.eval_date;
    form.title       = eval_.title ?? '';
    showForm.value   = true;
};

const submitForm = () => {
    const url = editTarget.value
        ? `/admin/evaluations/edit/${editTarget.value.id}`
        : '/admin/evaluations/add';
    form.post(url, {
        onSuccess: () => { showForm.value = false; toast.success('Évaluation enregistrée avec succès.'); },
        onError:   () => toast.error('Veuillez vérifier les informations.'),
    });
};

const validateEval = async (eval_: Evaluation) => {
    try {
        await axios.post(`/admin/evaluations/status/${eval_.id}`, { status: 'validated' });
        router.reload();
        toast.success('Évaluation validée.');
    } catch {
        toast.error('Erreur lors de la validation.');
    }
};

const handleDelete = (ids: (string | number)[]) => {
    ids.forEach(id => router.get(`/admin/evaluations/delete/${id}`, {}, {
        onSuccess: () => toast.success('Évaluation supprimée.'),
        onError:   () => toast.error('Erreur lors de la suppression.'),
    }));
};

const applyFilters = () => {
    router.get('/admin/evaluations/list', {
        class_id:  filters.value.class_id  || undefined,
        period_id: filters.value.period_id || undefined,
        type:      filters.value.type      || undefined,
        status:    filters.value.status    || undefined,
    }, { preserveState: true });
};

const statusVariant = (s: string) => ({ draft: 'secondary', open: 'info', closed: 'warning', validated: 'success' }[s] ?? 'secondary') as any;
const statusLabel   = (s: string) => ({ draft: 'Brouillon', open: 'Ouverte', closed: 'Fermée', validated: 'Validée' }[s] ?? s);

const columns = [
    { key: 'class_name',   label: 'Classe' },
    { key: 'subject_name', label: 'Matière' },
    { key: 'type',         label: 'Type' },
    { key: 'coefficient',  label: 'Coeff.' },
    { key: 'max_score',    label: 'Sur' },
    { key: 'eval_date',    label: 'Date' },
    { key: 'period_name',  label: 'Période' },
    { key: 'status',       label: 'Statut' },
];
</script>
