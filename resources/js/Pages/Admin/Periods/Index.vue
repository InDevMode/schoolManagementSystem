<template>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Périodes académiques</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    {{ periods.total }} période(s) — Semestres (publiques) ou Trimestres (privées)
                </p>
            </div>
            <AppButton v-if="can('view.exams.periods')" @click="openCreate">
                <template #icon>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </template>
                Nouvelle période
            </AppButton>
        </div>

        <!-- Infos types -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div class="flex items-start gap-3 p-4 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800">
                <svg class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="text-xs font-semibold text-blue-700 dark:text-blue-300">Écoles publiques</p>
                    <p class="text-xs text-blue-600 dark:text-blue-400">2 semestres · Semestre 1 (Oct–Jan), Semestre 2 (Fév–Juin)</p>
                </div>
            </div>
            <div class="flex items-start gap-3 p-4 rounded-xl bg-purple-50 dark:bg-purple-900/20 border border-purple-100 dark:border-purple-800">
                <svg class="w-5 h-5 text-purple-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="text-xs font-semibold text-purple-700 dark:text-purple-300">Écoles privées</p>
                    <p class="text-xs text-purple-600 dark:text-purple-400">3 trimestres · T1 (Oct–Déc), T2 (Jan–Mar), T3 (Avr–Juin)</p>
                </div>
            </div>
        </div>

        <!-- Table -->
        <DataTable
            ref="tableRef"
            :columns="columns"
            :rows="periods.data"
            row-key="id"
            :pagination="periods"
            @delete="handleDelete"
        >
            <template #cell-type="{ row }">
                <span :class="[
                    'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold',
                    row.type === 'semestre'
                        ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400'
                        : 'bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-400',
                ]">
                    {{ row.type === 'semestre' ? 'Semestre' : 'Trimestre' }}
                </span>
            </template>
            <template #cell-order_number="{ row }">
                <span class="text-sm font-bold text-gray-600 dark:text-gray-400">{{ row.order_number }}</span>
            </template>
            <template #cell-is_current="{ row }">
                <AppBadge v-if="row.is_current" variant="success" dot>Courante</AppBadge>
                <span v-else class="text-xs text-gray-300 dark:text-gray-600">—</span>
            </template>
            <template #cell-status="{ row }">
                <AppBadge :variant="row.status == 1 ? 'success' : 'danger'" dot>
                    {{ row.status == 1 ? 'Active' : 'Inactive' }}
                </AppBadge>
            </template>
            <template #actions="{ row }">
                <div class="flex items-center justify-end gap-1">
                    <!-- Définir comme courante -->
                    <button v-if="!row.is_current"
                        class="p-1.5 rounded-lg text-gray-400 hover:text-success-600 hover:bg-success-50 dark:hover:bg-success-900/20 transition-colors"
                        title="Définir comme période courante"
                        @click="setCurrent(row.id)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </button>
                    <button class="p-1.5 rounded-lg text-gray-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors"
                        @click="openEdit(row as any)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </button>
                    <button class="p-1.5 rounded-lg text-gray-400 hover:text-danger-600 hover:bg-danger-50 dark:hover:bg-danger-900/20 transition-colors"
                        @click="tableRef?.confirmDelete(row.id as number, row.name as string)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </template>
        </DataTable>

        <!-- Modal créer/modifier -->
        <AppModal v-model="showForm" :title="editTarget ? 'Modifier la période' : 'Nouvelle période'" size="md">
            <form :id="formId" @submit.prevent="submitForm" class="space-y-4">
                <AppInput v-model="form.name" label="Nom" required :error="form.errors.name"
                    placeholder="ex: 1er Trimestre 2025-2026"/>

                <div class="grid grid-cols-2 gap-4">
                    <!-- Type avec choix visuel -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type</label>
                        <div class="grid grid-cols-2 gap-2">
                            <button v-for="opt in typeOpts" :key="opt.value" type="button"
                                :class="[
                                    'py-2 px-3 rounded-xl border-2 text-xs font-semibold transition-all',
                                    form.type === opt.value
                                        ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400'
                                        : 'border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:border-gray-300',
                                ]"
                                @click="form.type = opt.value">
                                {{ opt.label }}
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Numéro d'ordre</label>
                        <div class="flex gap-2">
                            <button v-for="n in orderNumbers" :key="n" type="button"
                                :class="[
                                    'w-10 h-10 rounded-xl border-2 text-sm font-bold transition-all',
                                    form.order_number === String(n)
                                        ? 'border-primary-500 bg-primary-600 text-white'
                                        : 'border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:border-primary-300',
                                ]"
                                @click="form.order_number = String(n)">
                                {{ n }}
                            </button>
                        </div>
                    </div>
                </div>

                <AppInput v-model="form.school_year" label="Année scolaire" placeholder="ex: 2025-2026"/>

                <div class="grid grid-cols-2 gap-4">
                    <AppInput v-model="form.start_date" label="Date de début" type="date" required/>
                    <AppInput v-model="form.end_date"   label="Date de fin"   type="date" required/>
                </div>

                <AppSelect v-model="form.status" label="Statut" :options="statusOptions" required/>
            </form>
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
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { AppButton, AppInput, AppSelect, AppModal, DataTable, AppBadge } from '@/Components/UI';
import { useCan } from '@/Composables/useCan';
import { useToast } from '@/Composables/useToast';

const { can } = useCan();
const toast   = useToast();

interface Period {
    id:           number;
    name:         string;
    type:         string;
    order_number: number;
    school_year:  string;
    start_date:   string;
    end_date:     string;
    status:       number;
    is_current:   boolean;
}

defineProps<{
    periods: { data: Period[]; total: number; from: number; to: number; links: any[] };
    settings?: any;
}>();

const formId     = 'period-form';
const showForm   = ref(false);
const editTarget = ref<Period | null>(null);
const tableRef   = ref<any>(null);

const typeOpts     = [{ value: 'trimestre', label: 'Trimestre' }, { value: 'semestre', label: 'Semestre' }];
const orderNumbers = [1, 2, 3];
const statusOptions = [
    { value: '1', label: 'Active' },
    { value: '0', label: 'Inactive' },
];

const columns = [
    { key: 'name',         label: 'Nom' },
    { key: 'type',         label: 'Type' },
    { key: 'order_number', label: 'N°' },
    { key: 'school_year',  label: 'Année scolaire' },
    { key: 'start_date',   label: 'Début' },
    { key: 'end_date',     label: 'Fin' },
    { key: 'is_current',   label: 'Courante' },
    { key: 'status',       label: 'Statut' },
];

const form = useForm({
    name:         '',
    type:         'trimestre',
    order_number: '1',
    school_year:  '',
    start_date:   '',
    end_date:     '',
    status:       '1',
    settings_id:  '1',
});

const openCreate = () => {
    editTarget.value = null;
    form.reset();
    form.type         = 'trimestre';
    form.order_number = '1';
    form.status       = '1';
    showForm.value    = true;
};

const openEdit = (period: Period) => {
    editTarget.value  = period;
    form.name         = period.name;
    form.type         = period.type ?? 'trimestre';
    form.order_number = String(period.order_number ?? 1);
    form.school_year  = period.school_year ?? '';
    form.start_date   = period.start_date;
    form.end_date     = period.end_date;
    form.status       = String(period.status);
    showForm.value    = true;
};

const submitForm = () => {
    const url = editTarget.value
        ? `/admin/examinations/period/edit/${editTarget.value.id}`
        : '/admin/examinations/period/add';
    form.post(url, {
        onSuccess: () => {
            showForm.value   = false;
            editTarget.value = null;
            form.reset();
            form.type         = 'trimestre';
            form.order_number = '1';
            form.status       = '1';
            toast.success('Période enregistrée avec succès.');
        },
        onError: () => toast.error('Veuillez vérifier les informations.'),
    });
};

const setCurrent = (id: number) => {
    router.post(`/admin/examinations/period/set-current/${id}`, {}, {
        onSuccess: () => toast.success('Période définie comme courante.'),
        onError:   () => toast.error('Erreur lors de la mise à jour.'),
    });
};

const handleDelete = (ids: (string | number)[]) => {
    ids.forEach(id => router.get(`/admin/examinations/period/delete/${id}`, {}, {
        onSuccess: () => toast.success('Période supprimée.'),
    }));
};
</script>
