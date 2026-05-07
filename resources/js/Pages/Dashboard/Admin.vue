<template>
    <div class="space-y-6">
        <!-- Page header -->
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tableau de bord</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Vue d'ensemble de l'établissement</p>
        </div>

        <!-- Stats grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            <StatCard label="Administrateurs"  :value="totalAdmin"    icon="shield"       color="primary" />
            <StatCard label="Professeurs"       :value="totalTeacher"  icon="academic-cap" color="secondary" />
            <StatCard label="Apprenants"        :value="totalStudent"  icon="user-group"   color="success" />
            <StatCard label="Parents"           :value="totalParent"   icon="users"        color="warning" />
            <StatCard label="Classes"           :value="totalClass"    icon="building-library" color="info" />
            <StatCard label="Matières"          :value="totalSubject"  icon="book-open"    color="primary" />
            <StatCard label="Examens"           :value="totalExam"     icon="clipboard-document-list" color="secondary" />
            <StatCard label="Contributions"     :value="totalFeesCollections" icon="banknotes" color="success" />
            <StatCard label="Présences"         :value="totalAttendance" icon="user-check" color="info" />
            <StatCard label="Devoirs"           :value="totalHomework" icon="pencil"       color="warning" />
        </div>

        <!-- Attendance breakdown -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="card p-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-success-100 dark:bg-success-900/30 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Présents</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ totalAttendanceStudentPresent }}</p>
                </div>
            </div>
            <div class="card p-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-warning-100 dark:bg-warning-900/30 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-warning-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">En retard</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ totalAttendanceStudentLate }}</p>
                </div>
            </div>
            <div class="card p-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-danger-100 dark:bg-danger-900/30 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-danger-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Absents</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ totalAttendanceStudentAbsent }}</p>
                </div>
            </div>
            <div class="card p-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-info-100 dark:bg-info-900/30 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-info-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Demi-journée</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ totalAttendanceStudentHalfDay }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import StatCard from '@/Components/Dashboard/StatCard.vue';

defineProps<{
    totalAdmin: number;
    totalTeacher: number;
    totalStudent: number;
    totalParent: number;
    totalClass: number;
    totalSubject: number;
    totalExam: number;
    totalFeesCollections: number;
    totalAttendance: number;
    totalHomework: number;
    totalAttendanceStudentPresent: number;
    totalAttendanceStudentLate: number;
    totalAttendanceStudentAbsent: number;
    totalAttendanceStudentHalfDay: number;
    [key: string]: unknown;
}>();
</script>
