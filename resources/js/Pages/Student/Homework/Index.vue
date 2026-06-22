<template>
    <div class="space-y-6">
        <PageHeader title="Mes travaux de maison" :subtitle="`${works.total} travail(aux)`" color="indigo">
            <template #icon>
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                </svg>
            </template>
        </PageHeader>

        <DataTable
            :columns="columns"
            :rows="tableRows"
            row-key="id"
            export-filename="mes_devoirs"
            :selectable="false"
        >
            <template #cell-description="{ row }">
                <span class="line-clamp-2 text-sm text-gray-600 dark:text-gray-400">{{ stripHtml(row.description as string, 100) }}</span>
            </template>

            <template #cell-homework_status="{ row }">
                <AppBadge v-if="row.homework_status === 'submitted'" variant="success" dot>Soumis</AppBadge>
                <AppBadge v-else-if="row.homework_status === 'late'" variant="danger" dot>En retard</AppBadge>
                <AppBadge v-else-if="row.homework_status === 'graded'" variant="primary" dot>Noté</AppBadge>
                <AppBadge v-else variant="gray" dot>Non soumis</AppBadge>
            </template>

            <template #actions="{ row }">
                <div class="flex items-center gap-1.5 justify-end">
                    <!-- Bouton Voir (modal détail) — visible si déjà soumis -->
                    <button
                        v-if="row.homework_status === 'submitted' || row.homework_status === 'graded' || row.homework_status === 'late'"
                        class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl text-xs font-medium transition-all duration-150
                               text-white bg-emerald-500 hover:bg-emerald-600 shadow-sm shadow-emerald-200 dark:shadow-emerald-900/40"
                        title="Voir les détails et ma soumission"
                        @click="openDetail(row as Work)"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        Voir
                    </button>

                    <!-- Bouton Soumettre — désactivé si déjà soumis -->
                    <a
                        v-if="row.homework_status !== 'submitted' && row.homework_status !== 'graded'"
                        :href="`/student/my_homework/submission/${row.id}`"
                        class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl text-xs font-medium transition-all duration-150
                               text-white bg-primary-500 hover:bg-primary-600 shadow-sm shadow-primary-200 dark:shadow-primary-900/40"
                        title="Soumettre le travail"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        Soumettre
                    </a>
                    <!-- Indicateur désactivé si soumis -->
                    <span
                        v-else-if="row.homework_status === 'submitted'"
                        class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl text-xs font-medium
                               text-gray-400 bg-gray-100 dark:bg-gray-700/50 dark:text-gray-500 cursor-not-allowed"
                        title="Vous avez déjà soumis ce travail"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Soumis
                    </span>
                </div>
            </template>
        </DataTable>

        <!-- ── Modal Détail du travail ─────────────────────────────────── -->
        <AppModal v-model="showDetail" title="Détail du travail de maison" size="xl">
            <div v-if="detailWork" class="space-y-6">

                <!-- Section : Travail assigné par le professeur -->
                <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="flex items-center gap-2 px-4 py-3 bg-primary-50 dark:bg-primary-900/20 border-b border-primary-100 dark:border-primary-800">
                        <svg class="w-4 h-4 text-primary-600 dark:text-primary-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="text-sm font-semibold text-primary-700 dark:text-primary-300">Consignes du travail</h3>
                    </div>
                    <div class="p-4 space-y-4">
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                            <div>
                                <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-1">Matière</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ detailWork.subject_name }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-1">Classe</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ detailWork.class_name }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-1">Date du travail</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ formatDate(detailWork.work_date) }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-1">Date de remise</p>
                                <p class="text-sm font-semibold" :class="isPastDue(detailWork.submission_date) ? 'text-danger-600' : 'text-gray-900 dark:text-white'">
                                    {{ formatDate(detailWork.submission_date) }}
                                    <span v-if="isPastDue(detailWork.submission_date)" class="text-xs ml-1">(dépassée)</span>
                                </p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-1">Assigné par</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ detailWork.created_by_name ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-1">Statut</p>
                                <AppBadge v-if="detailWork.homework_status === 'submitted'" variant="success" dot>Soumis</AppBadge>
                                <AppBadge v-else-if="detailWork.homework_status === 'graded'" variant="primary" dot>Noté</AppBadge>
                                <AppBadge v-else-if="detailWork.homework_status === 'late'" variant="danger" dot>En retard</AppBadge>
                                <AppBadge v-else variant="gray" dot>Non soumis</AppBadge>
                            </div>
                        </div>

                        <!-- Description du travail -->
                        <div v-if="detailWork.description">
                            <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-2">Description</p>
                            <div
                                class="text-sm text-gray-700 dark:text-gray-300 prose prose-sm dark:prose-invert max-w-none bg-gray-50 dark:bg-gray-700/40 rounded-xl p-4"
                                v-html="detailWork.description"
                            />
                        </div>
                    </div>
                </div>

                <!-- Section : Soumission de l'apprenant -->
                <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="flex items-center gap-2 px-4 py-3 bg-emerald-50 dark:bg-emerald-900/20 border-b border-emerald-100 dark:border-emerald-800">
                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        <h3 class="text-sm font-semibold text-emerald-700 dark:text-emerald-300">Ma soumission</h3>
                    </div>
                    <div class="p-4 space-y-3">
                        <!-- Commentaire de l'apprenant -->
                        <div v-if="detailWork.homework_description">
                            <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-2">Mon commentaire</p>
                            <p class="text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700/40 rounded-xl p-3">
                                {{ detailWork.homework_description }}
                            </p>
                        </div>
                        <p v-else class="text-sm text-gray-400 italic">Aucun commentaire ajouté.</p>

                        <!-- Document soumis -->
                        <div v-if="detailWork.homework_document_file">
                            <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-2">Document soumis</p>
                            <a
                                :href="`/upload/homeworks/${detailWork.homework_document_file}`"
                                target="_blank"
                                class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 hover:bg-emerald-100 text-sm font-medium transition-colors"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Télécharger mon document
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <template #footer>
                <AppButton variant="ghost" @click="showDetail = false">Fermer</AppButton>
            </template>
        </AppModal>
    </div>
</template>

<script setup lang="ts">
import { fmtDate } from '@/utils/dateFormat';
import { ref, computed } from 'vue';
import { PageHeader, DataTable, AppBadge, AppModal, AppButton } from '@/Components/UI';
import { stripHtml } from '@/Utils/html';

interface Work {
    [key: string]: unknown;
    id: number;
    class_name: string;
    subject_name: string;
    work_date: string;
    submission_date: string;
    description: string;
    created_by_name?: string;
    homework_status?: string;
    homework_description?: string;
    homework_document_file?: string | null;
}

const props = defineProps<{
    works: { data: Work[]; total: number; from: number; to: number; links: any[] };
}>();

const showDetail = ref(false);
const detailWork = ref<Work | null>(null);

const openDetail = (row: Work) => {
    detailWork.value = row;
    showDetail.value = true;
};

const formatDate = fmtDate;

const isPastDue = (d: string) => {
    if (!d) return false;
    return new Date(d) < new Date();
};

const tableRows = computed(() => props.works.data.map(w => ({
    ...w,
    description: stripHtml(w.description, 100),
})));

const columns = [
    { key: 'subject_name',    label: 'Matière' },
    { key: 'work_date',       label: 'Date' },
    { key: 'submission_date', label: 'Remise' },
    { key: 'description',     label: 'Description' },
    { key: 'homework_status', label: 'Statut', exportFormat: (v: unknown) => ({ submitted: 'Soumis', graded: 'Noté', late: 'En retard', pending: 'En attente' }[v as string] ?? 'Non soumis') },
];
</script>
