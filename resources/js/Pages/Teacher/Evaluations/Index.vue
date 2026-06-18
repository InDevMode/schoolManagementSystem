<template>
    <div class="space-y-6">
        <PageHeader title="Mes évaluations" :subtitle="`${evaluations.total} évaluation(s)`" color="amber">
            <template #icon>
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
            </template>
            <template #actions>
                <AppButton :disabled="!currentPeriod" @click="openCreate">
                    <template #icon>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </template>
                    Nouvelle évaluation
                </AppButton>
            </template>
        </PageHeader>

        <!-- Bandeau période courante -->
        <div v-if="!currentPeriod"
            class="flex items-center gap-3 px-4 py-3 rounded-lg bg-warning-50 dark:bg-warning-900/20 border border-warning-200 dark:border-warning-700">
            <svg class="w-5 h-5 text-warning-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            <p class="text-sm text-warning-700 dark:text-warning-300">
                <span class="font-semibold">Aucune période courante définie.</span>
                La création d'évaluations est suspendue.
            </p>
        </div>
        <div v-else
            class="flex items-center gap-3 px-4 py-3 rounded-lg bg-success-50 dark:bg-success-900/20 border border-success-200 dark:border-success-700">
            <svg class="w-5 h-5 text-success-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p class="text-sm text-success-700 dark:text-success-300">
                Période courante : <span class="font-semibold">{{ currentPeriod.name }}</span>
            </p>
        </div>

        <!-- Filtres -->
        <div class="card p-4">
            <div class="flex flex-row flex-wrap items-center gap-3">
                <div class="flex-1 min-w-[150px]">
                    <AppSelect v-model="filters.class_id" :options="classOptions" placeholder="Toutes les classes" @change="applyFilters"/>
                </div>
                <div class="flex-1 min-w-[150px]">
                    <AppSelect v-model="filters.period_id" :options="periodCurrentOptions" placeholder="Période courante" @change="applyFilters"/>
                </div>
                <div class="flex-1 min-w-[160px]">
                    <AppSelect v-model="filters.type" :options="typeOptions" placeholder="Tous les types" @change="applyFilters"/>
                </div>
                <div class="flex-1 min-w-[150px]">
                    <AppSelect v-model="filters.status" :options="statusOptions" placeholder="Tous les statuts" @change="applyFilters"/>
                </div>
                <button v-if="filters.class_id || filters.period_id || filters.type || filters.status"
                    @click="filters = { class_id: '', period_id: '', type: '', status: '' }; applyFilters()"
                    class="flex-shrink-0 px-3 py-2 rounded-lg text-xs font-medium
                           text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200
                           bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600
                           transition-colors whitespace-nowrap">
                    Réinitialiser
                </button>
            </div>
        </div>

        <!-- Table -->
        <DataTable :columns="columns" :rows="evaluations.data" row-key="id" :pagination="evaluations">
            <template #cell-type="{ row }">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold"
                    :style="{ background: typeColors[row.type] + '20', color: typeColors[row.type] }">
                    {{ typeLabels[row.type] ?? row.type }}
                </span>
            </template>

            <template #cell-coefficient="{ row }">
                <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-bold bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400">
                    ×{{ row.coefficient }}
                </span>
            </template>

            <template #cell-status="{ row }">
                <div class="flex items-center gap-2 flex-wrap">
                    <AppBadge :variant="statusVariant(row.status)" dot>{{ statusLabel(row.status) }}</AppBadge>
                    <!-- Alerte notes rejetées -->
                    <span v-if="row.rejected_count > 0 && row.status === 'open'"
                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold
                               bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400
                               border border-orange-300 dark:border-orange-600 animate-pulse">
                        <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        </svg>
                        {{ row.rejected_count }} note{{ row.rejected_count > 1 ? 's' : '' }} rejetée{{ row.rejected_count > 1 ? 's' : '' }}
                    </span>
                </div>
            </template>

            <template #actions="{ row }">
                <div class="flex items-center justify-end gap-1.5">
                    <!-- Bouton contextuel selon statut -->
                    <Link v-if="row.status === 'open'"
                        :href="`/teacher/evaluations/grade-entry?evaluation_id=${row.id}`"
                        class="px-2.5 py-1 rounded-lg text-xs font-semibold
                               bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400
                               hover:bg-primary-100 transition-colors whitespace-nowrap">
                        Saisir les notes
                    </Link>
                    <Link v-else
                        :href="`/teacher/evaluations/grade-entry?evaluation_id=${row.id}`"
                        class="px-2.5 py-1 rounded-lg text-xs font-semibold
                               bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300
                               hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors whitespace-nowrap">
                        Voir les notes
                    </Link>

                    <!-- Bouton annuler (uniquement si pas validée) -->
                    <button v-if="row.status !== 'validated' && row.status !== 'cancelled'"
                        @click="confirmCancel(row)"
                        title="Annuler cette évaluation"
                        class="p-1.5 rounded-lg text-xs font-semibold
                               bg-danger-50 dark:bg-danger-900/20 text-danger-600 dark:text-danger-400
                               hover:bg-danger-100 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </template>
        </DataTable>

        <!-- Modal Créer -->
        <AppModal v-model="showForm" title="Nouvelle évaluation" size="xl">
            <form id="eval-form" @submit.prevent="submitForm" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <AppSelect v-model="form.class_id" label="Classe" :options="classOptions" required/>
                    <div>
                        <AppSelect
                            v-model="form.subject_id"
                            label="Matière"
                            :options="subjectOptions"
                            required
                            :disabled="!form.class_id || loadingSubjects"
                            :placeholder="loadingSubjects ? 'Chargement…' : (form.class_id ? 'Sélectionner une matière' : 'Choisir une classe d\'abord')"
                        />
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <AppSelect v-model="form.period_id" label="Période" :options="periodCurrentOptions" required :disabled="!!currentPeriod"/>
                    <AppInput  v-model="form.eval_date" label="Date"    type="date" required/>
                </div>

                <!-- Types d'évaluation -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type d'évaluation</label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        <button v-for="(label, key) in typeLabels" :key="key" type="button"
                            :class="[
                                'flex flex-col items-center gap-1.5 p-3 rounded-lg border-2 transition-all',
                                form.type === key
                                    ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20'
                                    : 'border-gray-200 dark:border-gray-700 hover:border-gray-300',
                            ]"
                            @click="selectType(key)">
                            <span class="w-3 h-3 rounded-full" :style="{ background: typeColors[key] }"/>
                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300 text-center">{{ label }}</span>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Coefficient
                            <span class="text-[10px] text-gray-400 font-normal ml-1">(matière assignée)</span>
                        </label>
                        <div class="flex items-center gap-2 px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50">
                            <span class="text-lg font-bold text-primary-600 dark:text-primary-400">
                                {{ form.coefficient || '—' }}
                            </span>
                            <span class="text-xs text-gray-400">
                                {{ form.subject_id ? 'lu depuis la matière' : 'sélectionnez une matière' }}
                            </span>
                        </div>
                        <input type="hidden" :value="form.coefficient" name="coefficient"/>
                    </div>
                    <AppInput v-model="form.max_score" label="Note max"       type="number" min="1" max="100"/>
                    <AppInput v-model="form.title"     label="Titre (optionnel)" placeholder="Interrogation N°1"/>
                </div>
            </form>
            <template #footer>
                <AppButton variant="ghost" @click="showForm = false">Annuler</AppButton>
                <AppButton type="submit" form="eval-form" :loading="form.processing">Créer</AppButton>
            </template>
        </AppModal>

        <!-- Modal confirmation annulation -->
        <AppModal v-model="showCancelModal" title="Annuler l'évaluation" size="sm">
            <div class="space-y-3">
                <div class="flex items-start gap-3 p-3 rounded-lg bg-danger-50 dark:bg-danger-900/20 border border-danger-200 dark:border-danger-700">
                    <svg class="w-5 h-5 text-danger-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-danger-700 dark:text-danger-300">
                            Confirmer l'annulation
                        </p>
                        <p class="text-xs text-danger-600 dark:text-danger-400 mt-1">
                            L'évaluation <span class="font-bold">« {{ cancelTarget?.type ? (typeLabels[cancelTarget.type] ?? cancelTarget.type) : '' }}
                            {{ cancelTarget?.class_name ? '— ' + cancelTarget.class_name : '' }} »</span>
                            sera annulée et exclue du calcul des moyennes.
                            Cette action est irréversible sans l'aide de l'administration.
                        </p>
                    </div>
                </div>
            </div>
            <template #footer>
                <AppButton variant="ghost" @click="showCancelModal = false">Annuler</AppButton>
                <AppButton variant="danger" :loading="cancelling" @click="doCancel">Confirmer l'annulation</AppButton>
            </template>
        </AppModal>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { useForm, router, Link } from '@inertiajs/vue3';
import { PageHeader, AppButton, AppInput, AppSelect, AppModal, DataTable, AppBadge } from '@/Components/UI';
import { useToast } from '@/Composables/useToast';
import axios from 'axios';

const toast = useToast();

interface EvalRow {
    id:             number;
    type:           string;
    class_name:     string;
    subject_name:   string;
    coefficient:    number;
    eval_date:      string;
    status:         string;
    rejected_count: number;
}

const props = defineProps<{
    evaluations:  { data: EvalRow[]; total: number; from: number; to: number; links: any[] };
    classes:      { id: number; class_id: number; class_name: string }[];
    currentPeriod?: { id: number; name: string } | null;
    typeLabels:   Record<string, string>;
}>();

const typeColors: Record<string, string> = {
    interrogation:    '#3b82f6',
    devoir_surveille: '#f59e0b',
    travail_maison:   '#10b981',
    examen_blanc:     '#ef4444',
};

const showForm        = ref(false);
const showCancelModal = ref(false);
const cancelTarget    = ref<EvalRow | null>(null);
const cancelling      = ref(false);
const filters         = ref({ class_id: '', period_id: '', type: '', status: '' });
const dynamicSubjects = ref<{ subject_id: number; subject_name: string; coefficient: number }[]>([]);
const loadingSubjects = ref(false);

// Les classes sont celles du prof (format ClassTeacherModel: {class_id, class_name})
const classOptions = computed(() =>
    props.classes.map(c => ({ value: String(c.class_id), label: c.class_name }))
);
const periodCurrentOptions = computed(() =>
    props.currentPeriod
        ? [{ value: String(props.currentPeriod.id), label: props.currentPeriod.name }]
        : []
);
const typeOptions   = computed(() => Object.entries(props.typeLabels).map(([k, v]) => ({ value: k, label: v })));
const subjectOptions = computed(() =>
    dynamicSubjects.value.map(s => ({ value: String(s.subject_id), label: s.subject_name }))
);
const statusOptions = [
    { value: 'open',      label: 'Ouverte' },
    { value: 'closed',    label: 'Fermée' },
    { value: 'validated', label: 'Validée' },
    { value: 'cancelled', label: 'Annulée' },
];

const form = useForm({
    class_id:    '',
    subject_id:  '',
    period_id:   '',
    type:        'interrogation',
    coefficient: '1',
    max_score:   '20',
    eval_date:   '',
    title:       '',
});

const columns = [
    { key: 'class_name',   label: 'Classe'  },
    { key: 'subject_name', label: 'Matière' },
    { key: 'type',         label: 'Type'    },
    { key: 'coefficient',  label: 'Coeff.'  },
    { key: 'eval_date',    label: 'Date'    },
    { key: 'status',       label: 'Statut'  },
];

const openCreate = () => {
    form.reset();
    form.type      = 'interrogation';
    form.coefficient = '1';
    form.max_score = '20';
    form.period_id = props.currentPeriod ? String(props.currentPeriod.id) : '';
    showForm.value = true;
};

const selectType = (key: string) => { form.type = key; };

watch(() => form.class_id, async (newClassId) => {
    form.subject_id       = '';
    form.coefficient      = '';
    dynamicSubjects.value = [];
    if (!newClassId) return;
    loadingSubjects.value = true;
    try {
        const res = await axios.get(`/admin/evaluations/subjects-by-class/${newClassId}`);
        dynamicSubjects.value = res.data;
    } catch {
        dynamicSubjects.value = [];
    } finally {
        loadingSubjects.value = false;
    }
});

watch(() => form.subject_id, (newSubjectId) => {
    if (!newSubjectId) { form.coefficient = ''; return; }
    const found = dynamicSubjects.value.find(s => String(s.subject_id) === newSubjectId);
    form.coefficient = found ? String(found.coefficient) : '';
});

const submitForm = () => {
    form.post('/teacher/evaluations/add', {
        onSuccess: () => { showForm.value = false; toast.success('Évaluation créée.'); },
        onError:   () => toast.error('Veuillez vérifier les informations.'),
    });
};

const applyFilters = () => {
    router.get('/teacher/evaluations', {
        class_id:  filters.value.class_id  || undefined,
        period_id: filters.value.period_id || undefined,
        type:      filters.value.type      || undefined,
        status:    filters.value.status    || undefined,
    }, { preserveState: true });
};

const confirmCancel = (row: EvalRow) => {
    cancelTarget.value  = row;
    showCancelModal.value = true;
};

const doCancel = async () => {
    if (!cancelTarget.value) return;
    cancelling.value = true;
    try {
        const res = await axios.post(`/teacher/evaluations/${cancelTarget.value.id}/cancel`);
        if (res.data.success) {
            toast.success(res.data.message);
            showCancelModal.value = false;
            router.reload({ only: ['evaluations'] });
        } else {
            toast.error(res.data.message);
        }
    } catch {
        toast.error('Erreur lors de l\'annulation.');
    } finally {
        cancelling.value = false;
    }
};

const statusVariant = (s: string): any => ({
    draft:     'secondary',
    open:      'info',
    closed:    'warning',
    validated: 'success',
    cancelled: 'danger',
}[s] ?? 'secondary');

const statusLabel = (s: string) => ({
    draft:     'Brouillon',
    open:      'Ouverte',
    closed:    'Fermée',
    validated: 'Validée',
    cancelled: 'Annulée',
}[s] ?? s);
</script>
