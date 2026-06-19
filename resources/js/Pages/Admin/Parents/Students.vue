<template>
    <div class="space-y-6">

        <!-- En-tête -->
        <PageHeader :title="`Enfants de ${parent?.last_name ?? ''} ${parent?.name ?? ''}`" :subtitle="`${myStudents.data.length} enfant(s) assigné(s)`" color="violet">
            <template #icon>
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </template>
            <template #actions>
                <Link href="/admin/parent/list"
                   class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700
                          text-sm font-medium text-gray-500 hover:text-primary-600 hover:border-primary-400
                          dark:text-gray-400 dark:hover:text-primary-400 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Retour
                </Link>
            </template>
        </PageHeader>

        <!-- Carte parent -->
        <div class="card p-4 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full overflow-hidden bg-primary-100 dark:bg-primary-900/30 flex-shrink-0 flex items-center justify-center">
                <img v-if="parent?.profile_picture"
                     :src="`/upload/profile/${parent.profile_picture}`"
                     :alt="`${parent.last_name} ${parent.name}`"
                     class="w-full h-full object-cover"/>
                <span v-else class="text-lg font-bold text-primary-700 dark:text-primary-300">
                    {{ (parent?.last_name?.[0] ?? '') }}{{ (parent?.name?.[0] ?? '') }}
                </span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-900 dark:text-white">
                    {{ parent?.last_name }} {{ parent?.name }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ parent?.email }}</p>
            </div>
            <AppBadge :variant="parent?.status == 1 ? 'success' : 'danger'" dot>
                {{ parent?.status == 1 ? 'Actif' : 'Inactif' }}
            </AppBadge>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- ── Enfants assignés ── -->
            <div class="card overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="w-7 h-7 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </span>
                        Enfants assignés
                        <span class="ml-1 px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                            {{ myStudents.data.length }}
                        </span>
                    </h2>
                </div>

                <div class="divide-y divide-gray-50 dark:divide-gray-700/50">
                    <div v-if="myStudents.data.length === 0"
                         class="py-10 text-center text-sm text-gray-400 dark:text-gray-500">
                        <svg class="w-8 h-8 mx-auto mb-2 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Aucun enfant assigné à ce parent
                    </div>

                    <div v-for="student in myStudents.data" :key="student.id"
                         class="flex items-center gap-3 px-5 py-3.5 hover:bg-gray-50 dark:hover:bg-gray-800/60 transition-colors">
                        <!-- Avatar -->
                        <div class="w-9 h-9 rounded-full overflow-hidden flex-shrink-0 bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                            <img v-if="student.profile_picture"
                                 :src="`/upload/profile/${student.profile_picture}`"
                                 class="w-full h-full object-cover"/>
                            <span v-else class="text-xs font-bold text-primary-700 dark:text-primary-300">
                                {{ (student.last_name?.[0] ?? '') }}{{ (student.name?.[0] ?? '') }}
                            </span>
                        </div>

                        <!-- Infos -->
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                {{ student.last_name }} {{ student.name }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                {{ student.class_name ?? '—' }}
                                <span v-if="student.admission_number" class="ml-1 text-gray-400">· {{ student.admission_number }}</span>
                            </p>
                        </div>

                        <!-- Désassigner -->
                        <button
                            title="Retirer cet enfant"
                            @click="confirmDeassign(student)"
                            class="p-1.5 rounded-xl transition-all duration-150 flex-shrink-0
                                   text-white bg-red-500 hover:bg-red-600 active:bg-red-700
                                   shadow-sm shadow-red-200 dark:shadow-red-900/40">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- ── Apprenants disponibles ── -->
            <div class="card overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="w-7 h-7 rounded-xl bg-violet-100 dark:bg-violet-900/30 flex items-center justify-center">
                            <svg class="w-4 h-4 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </span>
                        Apprenants sans parent
                        <span class="ml-1 px-2 py-0.5 rounded-full text-xs font-bold bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-400">
                            {{ filteredStudentList.length }}
                        </span>
                    </h2>

                    <!-- Recherche -->
                    <div class="mt-3 relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input v-model="search" type="text" placeholder="Rechercher un apprenant..."
                               class="w-full pl-9 pr-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600
                                      bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300
                                      placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500/40 transition-all"/>
                    </div>
                </div>

                <div class="divide-y divide-gray-50 dark:divide-gray-700/50 max-h-[420px] overflow-y-auto">
                    <div v-if="filteredStudentList.length === 0"
                         class="py-10 text-center text-sm text-gray-400 dark:text-gray-500">
                        <svg class="w-8 h-8 mx-auto mb-2 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Aucun apprenant disponible
                    </div>

                    <div v-for="student in filteredStudentList" :key="student.id"
                         class="flex items-center gap-3 px-5 py-3.5 hover:bg-gray-50 dark:hover:bg-gray-800/60 transition-colors">
                        <!-- Avatar -->
                        <div class="w-9 h-9 rounded-full overflow-hidden flex-shrink-0 bg-violet-100 dark:bg-violet-900/30 flex items-center justify-center">
                            <img v-if="student.profile_picture"
                                 :src="`/upload/profile/${student.profile_picture}`"
                                 class="w-full h-full object-cover"/>
                            <span v-else class="text-xs font-bold text-violet-700 dark:text-violet-300">
                                {{ (student.last_name?.[0] ?? '') }}{{ (student.name?.[0] ?? '') }}
                            </span>
                        </div>

                        <!-- Infos -->
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                {{ student.last_name }} {{ student.name }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                {{ student.class_name ?? '—' }}
                                <span v-if="student.admission_number" class="ml-1 text-gray-400">· {{ student.admission_number }}</span>
                            </p>
                        </div>

                        <!-- Assigner -->
                        <button
                            title="Assigner à ce parent"
                            @click="assignStudent(student.id)"
                            class="p-1.5 rounded-xl transition-all duration-150 flex-shrink-0
                                   text-white bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700
                                   shadow-sm shadow-emerald-200 dark:shadow-emerald-900/40">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Confirm désassigner -->
        <ConfirmDialog
            v-model="showConfirmDeassign"
            title="Retirer l'enfant"
            :message="deassignTarget ? `Voulez-vous retirer ${deassignTarget.last_name} ${deassignTarget.name} de ce parent ?` : ''"
            confirm-label="Retirer"
            confirm-variant="danger"
            @confirm="doDeassign"
        />
    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import { PageHeader, AppBadge, ConfirmDialog } from '@/Components/UI';
import { useToast } from '@/Composables/useToast';

// ── Types ─────────────────────────────────────────────────────────────────────
interface ParentUser {
    id: number;
    name: string;
    last_name: string;
    email: string;
    status: number;
    profile_picture: string | null;
}

interface Student {
    id: number;
    name: string;
    last_name: string;
    email: string;
    class_name: string | null;
    admission_number: string | null;
    profile_picture: string | null;
    parent_id: number | null;
}

// ── Props ─────────────────────────────────────────────────────────────────────
const props = defineProps<{
    parent: ParentUser;
    /** Apprenants déjà assignés à ce parent */
    myStudents: { data: Student[]; total?: number };
    /** Tous les apprenants sans parent assigné */
    studentList: { data: Student[] } | Student[];
    parentId: number;
}>();

// ── État ──────────────────────────────────────────────────────────────────────
const toast                = useToast();
const search               = ref('');
const showConfirmDeassign  = ref(false);
const deassignTarget       = ref<Student | null>(null);

// ── Computed ──────────────────────────────────────────────────────────────────
/** Normalise studentList peu importe si c'est paginé ou un tableau brut */
const rawStudentList = computed<Student[]>(() => {
    if (Array.isArray(props.studentList)) return props.studentList;
    return (props.studentList as any).data ?? [];
});

/** Exclut les apprenants déjà assignés au parent courant */
const assignedIds = computed(() => new Set(props.myStudents.data.map(s => s.id)));

const filteredStudentList = computed(() => {
    const q = search.value.trim().toLowerCase();
    return rawStudentList.value.filter(s => {
        if (assignedIds.value.has(s.id)) return false;
        if (!q) return true;
        return (
            s.name.toLowerCase().includes(q) ||
            s.last_name.toLowerCase().includes(q) ||
            (s.class_name ?? '').toLowerCase().includes(q) ||
            (s.admission_number ?? '').toLowerCase().includes(q)
        );
    });
});

// ── Actions ───────────────────────────────────────────────────────────────────
const assignStudent = (studentId: number) => {
    router.get(`/admin/parent/${props.parentId}/assign_student_parent/${studentId}`, {}, {
        onSuccess: () => toast.success('Apprenant assigné avec succès.'),
        onError:   () => toast.error('Erreur lors de l\'assignation.'),
        preserveScroll: true,
    });
};

const confirmDeassign = (student: Student) => {
    deassignTarget.value = student;
    showConfirmDeassign.value = true;
};

const doDeassign = () => {
    if (!deassignTarget.value) return;
    router.get(`/admin/parent/des_assign_student_parent/${deassignTarget.value.id}`, {}, {
        onSuccess: () => toast.success('Apprenant retiré du parent.'),
        onError:   () => toast.error('Erreur lors de la désassignation.'),
        preserveScroll: true,
    });
};
</script>
