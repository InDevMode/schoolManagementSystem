<template>
    <div class="space-y-6">

        <!-- Header -->
        <PageHeader title="Matières &amp; Classes" :subtitle="`${classSubjects.length} classe${classSubjects.length > 1 ? 's' : ''} assignée${classSubjects.length > 1 ? 's' : ''} · ${totalSubjects} matière${totalSubjects > 1 ? 's' : ''}`" color="violet">
            <template #icon>
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </template>
        </PageHeader>

        <!-- Cartes classes -->
        <div v-if="classSubjects.length" class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            <div
                v-for="item in classSubjects"
                :key="item.id"
                class="group rounded-2xl border border-gray-200 dark:border-gray-700/60
                       bg-white dark:bg-gray-800/60 overflow-hidden
                       hover:shadow-lg hover:border-primary-300 dark:hover:border-primary-600
                       transition-all duration-300"
            >
                <!-- En-tête de carte -->
                <div class="relative px-5 pt-5 pb-4 flex items-start gap-4"
                     :style="`background: linear-gradient(135deg, ${classColor(item.class_name)}18, transparent)`">
                    <!-- Icône classe -->
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 text-white font-bold text-lg shadow-md"
                         :style="{ backgroundColor: classColor(item.class_name) }">
                        {{ item.class_name?.[0]?.toUpperCase() }}
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h2 class="text-base font-bold text-gray-900 dark:text-white">{{ item.class_name }}</h2>
                            <span :class="[
                                'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold',
                                item.status == 1
                                    ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400'
                                    : 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400'
                            ]">
                                <span class="w-1.5 h-1.5 rounded-full"
                                      :class="item.status == 1 ? 'bg-emerald-500' : 'bg-red-400'"/>
                                {{ item.status == 1 ? 'Actif' : 'Inactif' }}
                            </span>
                        </div>

                        <!-- Stats rapides -->
                        <div class="flex items-center gap-4 mt-2 flex-wrap">
                            <div class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span>
                                    <span class="font-bold text-gray-700 dark:text-gray-200">{{ item.student_count }}</span>
                                    élève{{ item.student_count > 1 ? 's' : '' }}
                                </span>
                            </div>
                            <div class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                <span>
                                    <span class="font-bold text-gray-700 dark:text-gray-200">{{ item.subjects?.length ?? 0 }}</span>
                                    matière{{ (item.subjects?.length ?? 0) > 1 ? 's' : '' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Séparateur -->
                <div class="h-px bg-gray-100 dark:bg-gray-700/50 mx-5"/>

                <!-- Liste des matières -->
                <div class="px-5 py-4">
                    <div v-if="item.subjects && item.subjects.length" class="space-y-2">
                        <div
                            v-for="subject in item.subjects"
                            :key="subject.id"
                            class="flex items-center gap-3 p-2.5 rounded-xl
                                   bg-gray-50 dark:bg-gray-700/40
                                   hover:bg-primary-50 dark:hover:bg-primary-900/20
                                   border border-transparent hover:border-primary-200 dark:hover:border-primary-700/50
                                   transition-all duration-150 group/subject"
                        >
                            <!-- Point de type -->
                            <span class="w-2.5 h-2.5 rounded-full flex-shrink-0"
                                  :class="subjectTypeColor(subject.subject_type)"/>

                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate">
                                    {{ subject.subject_name }}
                                </p>
                                <p v-if="subject.subject_type" class="text-[10px] text-gray-400 dark:text-gray-500 capitalize">
                                    {{ subjectTypeLabel(subject.subject_type) }}
                                </p>
                            </div>

                            <!-- Coefficient -->
                            <div v-if="subject.coefficient"
                                 class="flex-shrink-0 px-2 py-0.5 rounded-md text-[11px] font-bold
                                        bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-300
                                        group-hover/subject:bg-primary-100 group-hover/subject:text-primary-700
                                        dark:group-hover/subject:bg-primary-900/40 dark:group-hover/subject:text-primary-400
                                        transition-colors">
                                coef. {{ subject.coefficient }}
                            </div>
                        </div>
                    </div>
                    <div v-else class="py-4 text-center text-xs text-gray-400 dark:text-gray-500">
                        <svg class="w-8 h-8 mx-auto mb-1.5 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        Aucune matière assignée
                    </div>
                </div>
            </div>
        </div>

        <!-- État vide -->
        <div v-else class="rounded-2xl border border-dashed border-gray-300 dark:border-gray-600 p-16 text-center">
            <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">Aucune classe assignée</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Contactez l'administration pour l'attribution des classes.</p>
        </div>

    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';

interface Subject {
    id:            number;
    subject_id:    number;
    subject_name:  string;
    subject_type?: string;
    coefficient?:  number | string | null;
}

interface ClassSubject {
    id:            number;
    class_id:      number;
    class_name:    string;
    status:        number;
    student_count: number;
    subjects?:     Subject[];
}

const props = defineProps<{
    classSubjects: ClassSubject[];
}>();

// ── Computed ─────────────────────────────────────────────────────────────────

const totalSubjects = computed(() =>
    props.classSubjects.reduce((acc, c) => acc + (c.subjects?.length ?? 0), 0)
);

// ── Helpers ───────────────────────────────────────────────────────────────────

const CLASS_COLORS = [
    '#6366f1', '#3b82f6', '#8b5cf6', '#06b6d4', '#10b981',
    '#f59e0b', '#ef4444', '#ec4899', '#84cc16', '#f97316',
];

const classColor = (name: string): string => {
    const code = [...(name ?? '')].reduce((acc, c) => acc + c.charCodeAt(0), 0);
    return CLASS_COLORS[code % CLASS_COLORS.length];
};

const subjectTypeColor = (type?: string): string => {
    if (!type) return 'bg-gray-400';
    const t = type.toLowerCase();
    if (t === 'obligatoire' || t === 'required' || t === 'mandatory') return 'bg-violet-500';
    if (t === 'optionnel' || t === 'optional' || t === 'elective')    return 'bg-violet-400';
    return 'bg-amber-400';
};

const subjectTypeLabel = (type?: string): string => {
    if (!type) return '';
    const t = type.toLowerCase();
    if (t === 'required' || t === 'mandatory') return 'Obligatoire';
    if (t === 'optional' || t === 'elective')  return 'Optionnel';
    return type;
};
</script>
