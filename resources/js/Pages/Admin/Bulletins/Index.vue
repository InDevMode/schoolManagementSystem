<template>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Bulletins scolaires</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Génération et publication des bulletins</p>
            </div>
        </div>

        <!-- Génération en masse -->
        <div class="card p-5">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Générer les bulletins d'une classe</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                <AppSelect v-model="genForm.class_id"  label="Classe"   :options="classOptions"  :block="true" />
                <AppSelect v-model="genForm.period_id" label="Période"  :options="periodOptions" :block="true" />
                <div class="flex gap-2 flex-wrap">
                    <AppButton variant="secondary" :loading="previewing" @click="previewAverages">
                        Aperçu moyennes
                    </AppButton>
                    <AppButton :loading="generating" @click="generateAll">
                        <template #icon>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 7h16a1 1 0 011 1v10a1 1 0 01-1 1H4a1 1 0 01-1-1V8a1 1 0 011-1z"/>
                            </svg>
                        </template>
                        Générer tous les bulletins
                    </AppButton>
                    <AppButton v-if="hasUnpublished" variant="success" :loading="publishing" @click="publishAll">
                        Publier tous
                    </AppButton>
                </div>
            </div>
        </div>

        <!-- Aperçu des moyennes -->
        <div v-if="previewData.length" class="card overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h3 class="font-semibold text-gray-900 dark:text-white">Aperçu des moyennes</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800/60">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Rang</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Élève</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Moyenne</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Appréciation</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                        <tr v-for="(s, i) in previewData" :key="s.student_id">
                            <td class="px-4 py-3 text-sm font-bold text-gray-500">{{ i + 1 }}ᵉ</td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">
                                {{ s.last_name }} {{ s.name }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="text-base font-bold" :class="avgClass(s.average)">{{ s.average }}/20</span>
                            </td>
                            <td class="px-4 py-3">
                                <AppBadge :variant="appreciationVariant(s.appreciation)">{{ s.appreciation }}</AppBadge>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Filtres table -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <AppSelect v-model="filters.period_id" :options="periodOptions" placeholder="Toutes les périodes" :block="true" @change="applyFilters" />
            <AppSelect v-model="filters.class_id"  :options="classOptions"  placeholder="Toutes les classes"  :block="true" @change="applyFilters" />
            <AppSelect v-model="filters.status"    :options="statusOpts"    placeholder="Tous les statuts"    :block="true" @change="applyFilters" />
        </div>

        <!-- Table des bulletins -->
        <DataTable
            :columns="columns"
            :rows="bulletins.data"
            row-key="id"
            :pagination="bulletins"
        >
            <template #cell-average="{ row }">
                <span class="font-bold text-sm" :class="avgClass(Number(row.average))">
                    {{ row.average ? Number(row.average).toFixed(2) + '/20' : '—' }}
                </span>
            </template>
            <template #cell-rank="{ row }">
                <span v-if="row.rank" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ row.rank }}ᵉ/{{ row.total_students }}
                </span>
                <span v-else class="text-gray-400">—</span>
            </template>
            <template #cell-status="{ row }">
                <AppBadge :variant="row.status === 'published' ? 'success' : 'secondary'" dot>
                    {{ row.status === 'published' ? 'Publié' : 'Brouillon' }}
                </AppBadge>
            </template>
            <template #actions="{ row }">
                <div class="flex items-center justify-end gap-1.5">
                    <!-- Voir le bulletin -->
                    <Link :href="`/admin/bulletins/show/${row.id}`"
                        class="p-1.5 rounded-lg transition-all duration-150
                               text-white bg-violet-500 hover:bg-violet-600 active:bg-violet-700
                               shadow-sm shadow-violet-200 dark:shadow-violet-900/40"
                        title="Voir le bulletin">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </Link>
                    <!-- Imprimer -->
                    <a :href="`/admin/bulletins/print/${row.id}`" target="_blank"
                        class="p-1.5 rounded-lg transition-all duration-150
                               text-white bg-blue-500 hover:bg-blue-600 active:bg-blue-700
                               shadow-sm shadow-blue-200 dark:shadow-blue-900/40"
                        title="Imprimer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                    </a>
                    <!-- Publier (brouillon uniquement) -->
                    <button v-if="row.status === 'draft'"
                        class="p-1.5 rounded-lg transition-all duration-150
                               text-white bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700
                               shadow-sm shadow-emerald-200 dark:shadow-emerald-900/40"
                        title="Publier le bulletin"
                        @click="publishOne(row.id as number)">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </button>
                    <!-- Déjà publié — indicateur -->
                    <span v-else
                        class="p-1.5 rounded-lg
                               text-white bg-emerald-400/60 dark:bg-emerald-700/40 cursor-default"
                        title="Déjà publié">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </span>
                </div>
            </template>
        </DataTable>
    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import { AppButton, AppSelect, AppBadge, DataTable } from '@/Components/UI';
import { useToast } from '@/Composables/useToast';
import axios from 'axios';

const toast = useToast();

const props = defineProps<{
    bulletins: { data: any[]; total: number; from: number; to: number; links: any[] };
    classes:   { id: number; name: string }[];
    periods:   { id: number; name: string }[];
}>();

const genForm    = ref({ class_id: '', period_id: '' });
const filters    = ref({ period_id: '', class_id: '', status: '' });
const generating = ref(false);
const publishing = ref(false);
const previewing = ref(false);
const previewData = ref<any[]>([]);

const classOptions  = computed(() => props.classes.map(c => ({ value: String(c.id), label: c.name })));
const periodOptions = computed(() => props.periods.map(p => ({ value: String(p.id), label: p.name })));
const statusOpts = [
    { value: 'draft',     label: 'Brouillon' },
    { value: 'published', label: 'Publié' },
];

const hasUnpublished = computed(() =>
    props.bulletins.data.some(b => b.status === 'draft')
);

const columns = [
    { key: 'student_last_name', label: 'Nom' },
    { key: 'student_name',      label: 'Prénom' },
    { key: 'class_name',        label: 'Classe' },
    { key: 'period_name',       label: 'Période' },
    { key: 'average',           label: 'Moyenne' },
    { key: 'rank',              label: 'Rang' },
    { key: 'appreciation',      label: 'Appréciation' },
    { key: 'status',            label: 'Statut' },
];

const previewAverages = async () => {
    if (!genForm.value.class_id || !genForm.value.period_id) {
        toast.error('Sélectionnez une classe et une période.'); return;
    }
    previewing.value = true;
    try {
        const res = await axios.get('/admin/bulletins/preview-averages', { params: genForm.value });
        previewData.value = res.data;
    } catch {
        toast.error('Erreur lors du calcul des moyennes.');
    } finally {
        previewing.value = false;
    }
};

const generateAll = () => {
    if (!genForm.value.class_id || !genForm.value.period_id) {
        toast.error('Sélectionnez une classe et une période.'); return;
    }
    generating.value = true;
    router.post('/admin/bulletins/generate-class', genForm.value, {
        onFinish: () => { generating.value = false; },
    });
};

const publishAll = () => {
    if (!genForm.value.class_id || !genForm.value.period_id) {
        toast.error('Sélectionnez une classe et une période.'); return;
    }
    publishing.value = true;
    router.post('/admin/bulletins/publish-all', genForm.value, {
        onFinish: () => { publishing.value = false; },
    });
};

const publishOne = (id: number) => {
    router.post(`/admin/bulletins/publish/${id}`, {}, {
        onSuccess: () => toast.success('Bulletin publié.'),
    });
};

const applyFilters = () => {
    router.get('/admin/bulletins/list', {
        period_id: filters.value.period_id || undefined,
        class_id:  filters.value.class_id  || undefined,
        status:    filters.value.status    || undefined,
    }, { preserveState: true });
};

const avgClass = (avg: number) => {
    if (!avg) return 'text-gray-400';
    if (avg >= 14) return 'text-success-600 dark:text-success-400';
    if (avg >= 10) return 'text-warning-600 dark:text-warning-400';
    return 'text-danger-600 dark:text-danger-400';
};

const appreciationVariant = (a: string): any => {
    if (['Excellent', 'Très Bien', 'Bien'].includes(a)) return 'success';
    if (['Assez Bien', 'Passable'].includes(a)) return 'warning';
    return 'danger';
};
</script>
