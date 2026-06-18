<template>
    <div class="space-y-6">

        <!-- Header avec retour -->
        <PageHeader :title="`Matières de ${student?.last_name ?? ''} ${student?.name ?? ''}`" :subtitle="`${subjects.data?.length ?? 0} matière${(subjects.data?.length ?? 0) > 1 ? 's' : ''} assignée${(subjects.data?.length ?? 0) > 1 ? 's' : ''}`" color="violet">
            <template #icon>
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </template>
            <template #actions>
                <Link href="/parent/my_student"
                      class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700
                             text-sm font-medium text-gray-500 hover:text-primary-600 hover:border-primary-400
                             dark:text-gray-400 dark:hover:text-primary-400 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Retour
                </Link>
            </template>
        </PageHeader>

        <!-- Grille de matières -->
        <div v-if="subjects.data?.length" class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            <div
                v-for="subject in subjects.data"
                :key="subject.id"
                class="group rounded-2xl border border-gray-200 dark:border-gray-700/60
                       bg-white dark:bg-gray-800/60 overflow-hidden
                       hover:shadow-lg hover:border-primary-300 dark:hover:border-primary-600
                       transition-all duration-300"
            >
                <!-- En-tête -->
                <div class="relative px-5 pt-5 pb-4 flex items-start gap-4"
                     :style="`background: linear-gradient(135deg, ${subjectColor(subject.subject_name)}18, transparent)`">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 text-white font-bold text-lg shadow-md"
                         :style="{ backgroundColor: subjectColor(subject.subject_name) }">
                        {{ subject.subject_name?.[0]?.toUpperCase() }}
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h2 class="text-base font-bold text-gray-900 dark:text-white">{{ subject.subject_name }}</h2>
                            <span v-if="subject.subject_type"
                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold"
                                :class="typeClass(subject.subject_type)">
                                <span class="w-1.5 h-1.5 rounded-full" :class="typeDot(subject.subject_type)"/>
                                {{ subjectTypeLabel(subject.subject_type) }}
                            </span>
                        </div>

                        <div class="flex items-center gap-4 mt-2 flex-wrap">
                            <div class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <span>Coefficient :
                                    <span class="font-bold text-primary-600 dark:text-primary-400">{{ subject.coefficient ?? 1 }}</span>
                                </span>
                            </div>
                            <div v-if="subject.class_name" class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                {{ subject.class_name }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="h-px bg-gray-100 dark:bg-gray-700/50 mx-5"/>

                <div class="px-5 py-3 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="flex gap-0.5">
                            <span v-for="n in maxCoeff" :key="n"
                                  class="w-3 h-2 rounded-full transition-colors"
                                  :class="n <= (subject.coefficient ?? 1)
                                      ? 'bg-primary-500'
                                      : 'bg-gray-200 dark:bg-gray-700'"/>
                        </div>
                        <span class="text-[11px] text-gray-400">coef. {{ subject.coefficient ?? 1 }}/{{ maxCoeff }}</span>
                    </div>
                    <span class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                        {{ subjectTypeLabel(subject.subject_type) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- État vide -->
        <div v-else class="rounded-2xl border border-dashed border-gray-300 dark:border-gray-600 p-16 text-center">
            <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">Aucune matière assignée</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Contactez l'administration pour plus d'informations.</p>
        </div>

    </div>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
defineProps<{ subjects: any; student: any }>();

const maxCoeff = 5;

const COLORS = [
    '#6366f1','#3b82f6','#8b5cf6','#06b6d4','#10b981',
    '#f59e0b','#ef4444','#ec4899','#84cc16','#f97316',
];

const subjectColor = (name: string): string => {
    const code = [...(name ?? '')].reduce((acc, c) => acc + c.charCodeAt(0), 0);
    return COLORS[code % COLORS.length];
};

const subjectTypeLabel = (type?: string): string => {
    if (!type) return '';
    const t = type.toLowerCase();
    if (t === 'required' || t === 'mandatory' || t === 'obligatoire') return 'Obligatoire';
    if (t === 'optional' || t === 'elective'  || t === 'optionnel')   return 'Optionnel';
    return type;
};

const typeClass = (type?: string): string => {
    if (!type) return 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400';
    const t = type.toLowerCase();
    if (t === 'required' || t === 'mandatory' || t === 'obligatoire')
        return 'bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-400';
    if (t === 'optional' || t === 'elective' || t === 'optionnel')
        return 'bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-400';
    return 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400';
};

const typeDot = (type?: string): string => {
    if (!type) return 'bg-gray-400';
    const t = type.toLowerCase();
    if (t === 'required' || t === 'mandatory' || t === 'obligatoire') return 'bg-violet-500';
    if (t === 'optional' || t === 'elective' || t === 'optionnel')    return 'bg-violet-400';
    return 'bg-amber-400';
};
</script>
