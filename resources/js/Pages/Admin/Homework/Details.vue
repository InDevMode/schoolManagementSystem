<template>
    <div class="space-y-6">
        <div class="flex items-center gap-3">
            <a href="/admin/practicalworks/homework/list" class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Détails du travail</h1>
            </div>
        </div>

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
                                <a v-if="hw.document_file" :href="`/upload/homeworks/${hw.document_file}`" target="_blank" class="text-primary-600 hover:underline text-xs">Voir</a>
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
