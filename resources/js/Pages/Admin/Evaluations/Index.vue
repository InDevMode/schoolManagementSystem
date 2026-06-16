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
            <AppButton v-if="can('action.exams.create')" :disabled="!currentPeriod" @click="openCreate">
                <template #icon>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </template>
                Nouvelle évaluation
            </AppButton>
        </div>

        <!-- Bandeau : aucune période courante -->
        <div v-if="!currentPeriod"
            class="flex items-center gap-3 px-4 py-3 rounded-lg bg-warning-50 dark:bg-warning-900/20 border border-warning-200 dark:border-warning-700">
            <svg class="w-5 h-5 text-warning-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            <p class="text-sm text-warning-700 dark:text-warning-300">
                <span class="font-semibold">Aucune période courante définie.</span>
                La création d'évaluations est bloquée. Allez dans
                <Link href="/admin/examinations/period/list" class="underline font-semibold hover:text-warning-900 dark:hover:text-warning-100">Périodes</Link>
                et cliquez sur "Définir comme courante".
            </p>
        </div>

        <!-- Bandeau : période courante active -->
        <div v-else
            class="flex items-center gap-3 px-4 py-3 rounded-lg bg-success-50 dark:bg-success-900/20 border border-success-200 dark:border-success-700">
            <svg class="w-5 h-5 text-success-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p class="text-sm text-success-700 dark:text-success-300">
                Période courante : <span class="font-semibold">{{ currentPeriod.name }}</span>
            </p>
        </div>

        <!-- Filtres types — boutons actifs/inactifs -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <button v-for="(label, key) in typeLabels" :key="key"
                type="button"
                :class="[
                    'flex items-center gap-3 px-4 py-3 rounded-lg border-2 transition-all text-left',
                    filters.type === key
                        ? 'border-transparent shadow-sm'
                        : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-gray-300 dark:hover:border-gray-600',
                ]"
                :style="filters.type === key ? { background: typeColors[key] + '18', borderColor: typeColors[key] } : {}"
                @click="toggleTypeFilter(key)">
                <span class="w-3 h-3 rounded-full flex-shrink-0 ring-2 ring-offset-1 transition-all"
                    :style="{ background: typeColors[key], ringColor: typeColors[key] }"/>
                <p class="text-xs font-semibold text-gray-900 dark:text-white">{{ label }}</p>
                <span v-if="filters.type === key"
                    class="ml-auto w-4 h-4 rounded-full flex items-center justify-center"
                    :style="{ background: typeColors[key] }">
                    <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                    </svg>
                </span>
            </button>
        </div>

        <!-- Filtres secondaires -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            <AppSelect v-model="filters.class_id" :options="classOptions" placeholder="Toutes les classes" :block="true" @change="applyFilters" />
            <AppSelect v-model="filters.period_id" :options="periodOptions" placeholder="Toutes les périodes" :block="true" @change="applyFilters" />
            <AppSelect v-model="filters.status" :options="statusOptions" placeholder="Tous les statuts" :block="true" @change="applyFilters" />
            <button v-if="filters.class_id || filters.period_id || filters.type || filters.status"
                type="button"
                @click="filters = { class_id: '', period_id: '', type: '', status: '' }; applyFilters()"
                class="flex items-center justify-center gap-2 px-3 py-2 rounded-lg text-sm font-medium
                       text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200
                       bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600
                       border border-gray-200 dark:border-gray-600 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Réinitialiser
            </button>
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
                <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ row.max_score }}</span>
            </template>

            <template #actions="{ row }">
                <div class="flex items-center justify-end gap-1.5">
                    <!-- Saisie des notes — uniquement si l'éval n'est pas validée ni annulée -->
                    <Link v-if="row.status !== 'validated' && row.status !== 'cancelled'"
                        :href="`/admin/evaluations/grade-entry?evaluation_id=${row.id}`"
                        class="p-1.5 rounded-lg transition-all duration-150
                               text-white bg-violet-500 hover:bg-violet-600 active:bg-violet-700
                               shadow-sm shadow-violet-200 dark:shadow-violet-900/40"
                        title="Saisir les notes">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2
                                   M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2
                                   m-6 9l2 2 4-4"/>
                        </svg>
                    </Link>
                    <!-- Voir les notes (lecture seule) si validée ou annulée -->
                    <Link v-else
                        :href="`/admin/evaluations/grade-entry?evaluation_id=${row.id}`"
                        :class="[
                            'p-1.5 rounded-lg transition-all duration-150 shadow-sm',
                            row.status === 'cancelled'
                                ? 'text-white bg-amber-500 hover:bg-amber-600 active:bg-amber-700 shadow-amber-200 dark:shadow-amber-900/40'
                                : 'text-white bg-indigo-500 hover:bg-indigo-600 active:bg-indigo-700 shadow-indigo-200 dark:shadow-indigo-900/40'
                        ]"
                        :title="row.status === 'cancelled' ? 'Évaluation annulée — lecture seule' : 'Voir les notes (lecture seule — évaluation validée)'">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7
                                   -1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </Link>
                    <!-- Éditer — masqué si l'éval est validée ou annulée -->
                    <button v-if="can('action.exams.edit') && row.status !== 'validated' && row.status !== 'cancelled'"
                        class="p-1.5 rounded-lg transition-all duration-150
                               text-white bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700
                               shadow-sm shadow-emerald-200 dark:shadow-emerald-900/40"
                        title="Modifier l'évaluation"
                        @click="openEdit(row as any)">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </button>
                    <!-- Supprimer -->
                    <button
                        v-if="canDeleteEval(row)"
                        class="p-1.5 rounded-lg transition-all duration-150
                               text-white bg-red-500 hover:bg-red-600 active:bg-red-700
                               shadow-sm shadow-red-200 dark:shadow-red-900/40"
                        :title="deleteTitle(row)"
                        @click="tableRef?.confirmDelete(row.id as number, row.title || (typeLabels[row.type] ?? row.type) as string)">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                    <!-- Cadenas si validée et non supprimable -->
                    <span v-else-if="row.status === 'validated'"
                        class="p-1.5 rounded-lg text-white bg-success-500 dark:bg-success-600 cursor-not-allowed shadow-sm shadow-success-200 dark:shadow-success-900/40"
                        title="Évaluation validée — suppression impossible">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </span>
                </div>
            </template>
        </DataTable>

        <!-- Modal Créer/Modifier -->
        <AppModal v-model="showForm" :title="editTarget ? 'Modifier l\'évaluation' : 'Nouvelle évaluation'" size="xl">
            <form :id="formId" @submit.prevent="submitForm" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <AppSelect
                        v-model="form.class_id"
                        label="Classe"
                        :options="classOptions"
                        required
                        :error="form.errors.class_id"
                    />
                    <div>
                        <AppSelect
                            v-model="form.subject_id"
                            label="Matière"
                            :options="subjectOptions"
                            required
                            :disabled="!form.class_id || loadingSubjects"
                            :error="form.errors.subject_id"
                            :placeholder="loadingSubjects ? 'Chargement…' : (form.class_id ? 'Sélectionner une matière' : 'Choisir une classe d\'abord')"
                        />
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Période verrouillée sur la période courante -->
                    <AppSelect v-model="form.period_id" label="Période" :options="periodCurrentOptions" required :disabled="!!currentPeriod" :error="form.errors.period_id" />
                    <AppInput v-model="form.eval_date" label="Date" type="date" required :error="form.errors.eval_date" />
                </div>

                <!-- Type d'évaluation -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type d'évaluation</label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        <button v-for="(label, key) in typeLabels" :key="key" type="button"
                            :class="[
                                'flex flex-col items-center gap-1.5 p-3 rounded-lg border-2 transition-all text-center',
                                form.type === key
                                    ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20'
                                    : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600',
                            ]"
                            @click="selectType(key)">
                            <span class="w-3 h-3 rounded-full" :style="{ background: typeColors[key] }"/>
                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ label }}</span>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Coefficient en lecture seule : rempli automatiquement depuis la matière assignée -->
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
                    <AppInput v-model="form.max_score" label="Note max" type="number" min="1" max="100" />
                    <AppInput v-model="form.title" label="Titre (optionnel)" placeholder="ex: Interrogation N°1" />
                </div>
            </form>

            <!-- Note d'info -->
            <div class="mt-4 p-3 rounded-lg bg-violet-50 dark:bg-violet-900/20 border border-violet-100 dark:border-violet-800">
                <p class="text-xs text-violet-700 dark:text-violet-300 font-medium">
                    💡 Le coefficient est celui défini lors de l'assignation de la matière à la classe — il ne peut pas être modifié ici.
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
import { ref, computed, watch, nextTick } from 'vue';
import { useForm, router, Link } from '@inertiajs/vue3';
import { AppButton, AppInput, AppSelect, AppModal, DataTable, AppBadge } from '@/Components/UI';
import { useCan } from '@/Composables/useCan';
import { useToast } from '@/Composables/useToast';
import axios from 'axios';

const { can, isSuperAdmin } = useCan();
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
    periods:     { id: number; name: string }[];
    currentPeriod?: { id: number; name: string } | null;
    typeLabels:  Record<string, string>;
    typeCoeffs:  Record<string, number>;
}>();

const typeColors: Record<string, string> = {
    interrogation:    '#3b82f6',
    devoir_surveille: '#f59e0b',
    travail_maison:   '#10b981',
    examen_blanc:     '#ef4444',
};

const formId          = 'eval-form';
const showForm        = ref(false);
const editTarget      = ref<Evaluation | null>(null);
const tableRef        = ref<any>(null);
const dynamicSubjects = ref<{ subject_id: number; subject_name: string; coefficient: number }[]>([]);
const loadingSubjects = ref(false);

const filters = ref({ class_id: '', period_id: '', type: '', status: '' });

const classOptions         = computed(() => props.classes.map(c => ({ value: String(c.id), label: c.name })));
const typeOptions          = computed(() => Object.entries(props.typeLabels).map(([k, v]) => ({ value: k, label: v })));
const periodOptions        = computed(() => props.periods.map(p => ({ value: String(p.id), label: p.name })));
const periodCurrentOptions = computed(() =>
    props.currentPeriod
        ? [{ value: String(props.currentPeriod.id), label: props.currentPeriod.name }]
        : []
);
const statusOptions = [
    { value: 'draft',     label: 'Brouillon' },
    { value: 'open',      label: 'Ouverte' },
    { value: 'closed',    label: 'Fermée' },
    { value: 'validated', label: 'Validée' },
    { value: 'cancelled', label: 'Annulée' },
];

const subjectOptions = computed(() =>
    dynamicSubjects.value.map(s => ({ value: String(s.subject_id), label: s.subject_name }))
);

const form = useForm({
    class_id:    '',
    subject_id:  '',
    period_id:   '',
    teacher_id:  '',
    type:        'interrogation',
    coefficient: '',
    max_score:   '20',
    eval_date:   '',
    title:       '',
});

// Flag pour bloquer le watch class_id pendant le chargement d'une édition
let skipClassWatch = false;

// ── Watch sur class_id : dès qu'il change, charger les matières ──────────
watch(() => form.class_id, async (newClassId) => {
    // Ne pas reset pendant openEdit (chargement pré-rempli)
    if (skipClassWatch) return;

    form.subject_id  = '';
    form.coefficient = '';
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

// ── Watch sur subject_id : remplir le coefficient dès la sélection ───────
watch(() => form.subject_id, (newSubjectId) => {
    // Ne pas écraser pendant openEdit (coefficient déjà assigné manuellement)
    if (skipClassWatch) return;
    if (!newSubjectId) { form.coefficient = ''; return; }
    const found = dynamicSubjects.value.find(s => String(s.subject_id) === newSubjectId);
    form.coefficient = found ? String(found.coefficient) : '';
});

const selectType = (key: string) => { form.type = key; };

/**
 * Règles de suppression :
 * - validated  → personne ne peut supprimer (même pas le super admin)
 * - open/closed → super admin uniquement
 * - draft       → admin + super admin
 */
const canDeleteEval = (row: Evaluation): boolean => {
    if (row.status === 'validated') return false;
    if (row.status === 'open' || row.status === 'closed') return isSuperAdmin.value;
    // draft
    return can('action.exams.delete');
};

const deleteTitle = (row: Evaluation): string => {
    if (row.status === 'open')   return 'Réservé au Super Admin (statut : ouverte)';
    if (row.status === 'closed') return 'Réservé au Super Admin (statut : fermée)';
    return 'Supprimer';
};

const toggleTypeFilter = (key: string) => {
    filters.value.type = filters.value.type === key ? '' : key;
    applyFilters();
};

const openCreate = () => {
    editTarget.value      = null;
    dynamicSubjects.value = [];
    form.reset();
    form.type      = 'interrogation';
    form.max_score = '20';
    form.period_id = props.currentPeriod ? String(props.currentPeriod.id) : '';
    showForm.value = true;
};

const openEdit = async (eval_: Evaluation) => {
    editTarget.value      = eval_;
    dynamicSubjects.value = [];
    form.reset();

    // Bloquer le watch class_id pour éviter qu'il reset subject_id/coefficient
    skipClassWatch = true;

    // Charger les matières de la classe
    loadingSubjects.value = true;
    try {
        const res = await axios.get(`/admin/evaluations/subjects-by-class/${eval_.class_id}`);
        dynamicSubjects.value = res.data;
    } catch {
        dynamicSubjects.value = [];
    } finally {
        loadingSubjects.value = false;
    }

    // Remplir le formulaire — le watch ne se déclenchera pas grâce au flag
    form.class_id    = String(eval_.class_id);
    form.subject_id  = String(eval_.subject_id);
    form.period_id   = String(eval_.period_id);
    form.type        = eval_.type;
    form.max_score   = String(eval_.max_score);
    form.eval_date   = eval_.eval_date;
    form.title       = eval_.title ?? '';

    const found      = dynamicSubjects.value.find(s => String(s.subject_id) === String(eval_.subject_id));
    form.coefficient = found ? String(found.coefficient) : String(eval_.coefficient);

    // Débloquer le watch après le prochain tick
    await nextTick();
    skipClassWatch = false;

    showForm.value = true;
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

const statusVariant = (s: string) => ({ draft: 'secondary', open: 'info', closed: 'warning', validated: 'success', cancelled: 'danger' }[s] ?? 'secondary') as any;
const statusLabel   = (s: string) => ({ draft: 'Brouillon', open: 'Ouverte', closed: 'Fermée', validated: 'Validée', cancelled: 'Annulée' }[s] ?? s);

const columns = [
    { key: 'class_name',   label: 'Classe'  },
    { key: 'subject_name', label: 'Matière' },
    { key: 'type',         label: 'Type'    },
    { key: 'coefficient',  label: 'Coeff.'  },
    { key: 'max_score',    label: 'Sur'     },
    { key: 'eval_date',    label: 'Date'    },
    { key: 'period_name',  label: 'Période' },
    { key: 'status',       label: 'Statut'  },
];
</script>

