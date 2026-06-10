<template>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Mes évaluations</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ evaluations.total }} évaluation(s)</p>
            </div>
            <AppButton :disabled="!currentPeriod" @click="openCreate">
                <template #icon>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </template>
                Nouvelle évaluation
            </AppButton>
        </div>

        <!-- Bandeau période courante -->
        <div v-if="!currentPeriod"
            class="flex items-center gap-3 px-4 py-3 rounded-lg bg-warning-50 dark:bg-warning-900/20 border border-warning-200 dark:border-warning-700">
            <svg class="w-5 h-5 text-warning-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            <p class="text-sm text-warning-700 dark:text-warning-300">
                <span class="font-semibold">Aucune période courante définie.</span>
                La création d'évaluations est suspendue en attendant qu'un administrateur définisse la période en cours.
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
                <AppBadge :variant="statusVariant(row.status)" dot>{{ statusLabel(row.status) }}</AppBadge>
            </template>
            <template #actions="{ row }">
                <div class="flex items-center justify-end gap-1">
                    <Link :href="`/teacher/evaluations/grade-entry?evaluation_id=${row.id}`"
                        class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-success-50 dark:bg-success-900/20 text-success-700 dark:text-success-400 hover:bg-success-100 transition-colors">
                        Saisir les notes
                    </Link>
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
                    <AppInput  v-model="form.eval_date" label="Date"    type="date"              required/>
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
                    <!-- Coefficient en lecture seule -->
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
                    <AppInput v-model="form.max_score"   label="Note max"    type="number" min="1" max="100"/>
                    <AppInput v-model="form.title"       label="Titre (optionnel)" placeholder="Interrogation N°1"/>
                </div>
            </form>
            <template #footer>
                <AppButton variant="ghost" @click="showForm = false">Annuler</AppButton>
                <AppButton type="submit" form="eval-form" :loading="form.processing">Créer</AppButton>
            </template>
        </AppModal>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { useForm, router, Link } from '@inertiajs/vue3';
import { AppButton, AppInput, AppSelect, AppModal, DataTable, AppBadge } from '@/Components/UI';
import { useToast } from '@/Composables/useToast';
import axios from 'axios';

const toast = useToast();

const props = defineProps<{
    evaluations: { data: any[]; total: number; from: number; to: number; links: any[] };
    classes:     { id: number; name: string }[];
    currentPeriod?: { id: number; name: string } | null;
    typeLabels:  Record<string, string>;
}>();

const typeColors: Record<string, string> = {
    interrogation:    '#3b82f6',
    devoir_surveille: '#f59e0b',
    travail_maison:   '#10b981',
    examen_blanc:     '#ef4444',
};

const showForm        = ref(false);
const filters         = ref({ class_id: '', period_id: '', type: '', status: '' });
const dynamicSubjects = ref<{ subject_id: number; subject_name: string; coefficient: number }[]>([]);
const loadingSubjects = ref(false);

const classOptions   = computed(() => props.classes.map(c => ({ value: String(c.id), label: c.name })));
// Le prof ne voit que la période courante dans son formulaire
const periodCurrentOptions = computed(() =>
    props.currentPeriod
        ? [{ value: String(props.currentPeriod.id), label: props.currentPeriod.name }]
        : []
);
const typeOptions    = computed(() => Object.entries(props.typeLabels).map(([k, v]) => ({ value: k, label: v })));
// dynamicSubjects contient les matières actives assignées à la classe choisie
// Structure : { subject_id, subject_name, coefficient }
const subjectOptions = computed(() =>
    dynamicSubjects.value.map(s => ({ value: String(s.subject_id), label: s.subject_name }))
);
const statusOptions  = [
    { value: 'draft',     label: 'Brouillon' },
    { value: 'open',      label: 'Ouverte' },
    { value: 'closed',    label: 'Fermée' },
    { value: 'validated', label: 'Validée' },
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
    form.type        = 'interrogation';
    form.coefficient = '1';
    form.max_score   = '20';
    // Pré-sélectionner la période courante
    form.period_id   = props.currentPeriod ? String(props.currentPeriod.id) : '';
    showForm.value   = true;
};

const selectType = (key: string) => {
    form.type = key;
    // Le coefficient vient de la matière, pas du type
};

// ── Watch sur class_id : charger les matières dès que la valeur change ───
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

// ── Watch sur subject_id : remplir le coefficient ────────────────────────
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

const statusVariant = (s: string): any => ({ draft: 'secondary', open: 'info', closed: 'warning', validated: 'success' }[s] ?? 'secondary');
const statusLabel   = (s: string) => ({ draft: 'Brouillon', open: 'Ouverte', closed: 'Fermée', validated: 'Validée' }[s] ?? s);
</script>
