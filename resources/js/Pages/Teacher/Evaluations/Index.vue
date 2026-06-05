<template>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Mes évaluations</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ evaluations.total }} évaluation(s)</p>
            </div>
            <AppButton @click="openCreate">
                <template #icon>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </template>
                Nouvelle évaluation
            </AppButton>
        </div>

        <!-- Filtres -->
        <div class="flex flex-wrap gap-3">
            <AppSelect v-model="filters.class_id"  :options="classOptions"  placeholder="Toutes les classes"  class="w-44" @change="applyFilters"/>
            <AppSelect v-model="filters.period_id" :options="periodOptions" placeholder="Toutes les périodes" class="w-44" @change="applyFilters"/>
            <AppSelect v-model="filters.type"      :options="typeOptions"   placeholder="Tous les types"      class="w-48" @change="applyFilters"/>
            <AppSelect v-model="filters.status"    :options="statusOptions" placeholder="Tous les statuts"    class="w-40" @change="applyFilters"/>
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
                    <a :href="`/teacher/evaluations/grade-entry?evaluation_id=${row.id}`"
                        class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-success-50 dark:bg-success-900/20 text-success-700 dark:text-success-400 hover:bg-success-100 transition-colors">
                        Saisir les notes
                    </a>
                </div>
            </template>
        </DataTable>

        <!-- Modal Créer -->
        <AppModal v-model="showForm" title="Nouvelle évaluation" size="lg">
            <form id="eval-form" @submit.prevent="submitForm" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <AppSelect v-model="form.class_id"   label="Classe"   :options="classOptions"   required @change="onClassChange"/>
                    <AppSelect v-model="form.subject_id" label="Matière"  :options="subjectOptions" required/>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <AppSelect v-model="form.period_id" label="Période" :options="periodOptions" required/>
                    <AppInput  v-model="form.eval_date" label="Date"    type="date"              required/>
                </div>

                <!-- Types d'évaluation -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type d'évaluation</label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        <button v-for="(label, key) in typeLabels" :key="key" type="button"
                            :class="[
                                'flex flex-col items-center gap-1.5 p-3 rounded-xl border-2 transition-all',
                                form.type === key
                                    ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20'
                                    : 'border-gray-200 dark:border-gray-700 hover:border-gray-300',
                            ]"
                            @click="selectType(key)">
                            <span class="w-3 h-3 rounded-full" :style="{ background: typeColors[key] }"/>
                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300 text-center">{{ label }}</span>
                            <span class="text-[10px] text-gray-400">Coeff. {{ typeCoeffs[key] }}</span>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <AppInput v-model="form.coefficient" label="Coefficient" type="number" min="1" max="10"/>
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
import { ref, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { AppButton, AppInput, AppSelect, AppModal, DataTable, AppBadge } from '@/Components/UI';
import { useToast } from '@/Composables/useToast';
import axios from 'axios';

const toast = useToast();

const props = defineProps<{
    evaluations: { data: any[]; total: number; from: number; to: number; links: any[] };
    classes:     { id: number; name: string }[];
    periods:     { id: number; name: string }[];
    typeLabels:  Record<string, string>;
    typeCoeffs:  Record<string, number>;
}>();

const typeColors: Record<string, string> = {
    interrogation:    '#3b82f6',
    devoir_surveille: '#f59e0b',
    travail_maison:   '#10b981',
    examen_blanc:     '#ef4444',
};

const showForm   = ref(false);
const filters    = ref({ class_id: '', period_id: '', type: '', status: '' });
const dynamicSubjects = ref<any[]>([]);

const classOptions   = computed(() => props.classes.map(c => ({ value: String(c.id), label: c.name })));
const periodOptions  = computed(() => props.periods.map(p => ({ value: String(p.id), label: p.name })));
const typeOptions    = computed(() => Object.entries(props.typeLabels).map(([k, v]) => ({ value: k, label: v })));
const subjectOptions = computed(() => dynamicSubjects.value.map(s => ({ value: String(s.id ?? s.subject_id), label: s.name ?? s.subject_name })));
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
    showForm.value   = true;
};

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
