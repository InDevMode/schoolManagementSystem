<template>
    <div class="space-y-6">
        <PageHeader title="Détails du travail" subtitle="Informations complètes sur le travail de maison" color="indigo">
            <template #icon>
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                </svg>
            </template>
            <template #actions>
                <Link href="/admin/practicalworks/homework/list" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 text-sm font-medium text-gray-500 hover:text-primary-600 hover:border-primary-400 dark:text-gray-400 dark:hover:text-primary-400 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                    Retour
                </Link>
            </template>
        </PageHeader>

        <div v-if="work" class="card p-6 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-gray-500 uppercase font-medium">Classe</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white mt-1">{{ work.class_name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-medium">Matière</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white mt-1">{{ work.subject_name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-medium">Date du travail</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white mt-1">{{ work.work_date }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-medium">Date de remise</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white mt-1">{{ work.submission_date }}</p>
                </div>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase font-medium">Description</p>
                <div class="text-sm text-gray-700 dark:text-gray-300 mt-1 prose prose-sm dark:prose-invert max-w-none" v-html="work.description" />
            </div>
            <div v-if="work.document_file">
                <p class="text-xs text-gray-500 uppercase font-medium">Document</p>
                <a :href="`/upload/practicalworks/${work.document_file}`" target="_blank" class="text-sm text-primary-600 hover:underline mt-1 inline-block">
                    Télécharger le document
                </a>
            </div>
        </div>

        <!-- Submissions -->
        <div v-if="work?.homeworks?.length" class="card overflow-hidden">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white">Soumissions ({{ work.homeworks.length }})</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Apprenant</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Document</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <tr v-for="hw in work.homeworks" :key="hw.id" class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ hw.student_last_name }} {{ hw.student_name }}</td>
                            <td class="px-4 py-3">
                                <AppBadge variant="success" dot>{{ hw.status }}</AppBadge>
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ hw.created_at }}</td>
                            <td class="px-4 py-3">
                                <div v-if="hw.document_file" class="flex items-center gap-1.5">
                                    <a
                                        :href="`/upload/homeworks/${hw.document_file}`"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        title="Voir le document"
                                        class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-xs font-medium transition-all duration-150
                                               text-white bg-violet-500 hover:bg-violet-600 active:bg-violet-700
                                               shadow-sm shadow-violet-200 dark:shadow-violet-900/40"
                                    >
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Voir
                                    </a>
                                    <a
                                        :href="`/upload/homeworks/${hw.document_file}`"
                                        :download="hw.document_file"
                                        title="Télécharger le document"
                                        class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-xs font-medium transition-all duration-150
                                               text-white bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700
                                               shadow-sm shadow-emerald-200 dark:shadow-emerald-900/40"
                                    >
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                        Télécharger
                                    </a>
                                </div>
                                <span v-else class="text-gray-400 text-xs">—</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { AppBadge } from '@/Components/UI';
import { Link } from '@inertiajs/vue3';

interface Homework {
    id: number;
    student_name: string;
    student_last_name: string;
    status: string;
    document_file: string | null;
    created_at: string;
}

interface WorkDetail {
    id: number;
    class_name: string;
    subject_name: string;
    work_date: string;
    submission_date: string;
    description: string;
    document_file: string | null;
    homeworks?: Homework[];
}

defineProps<{
    work: WorkDetail | null;
}>();
</script>
