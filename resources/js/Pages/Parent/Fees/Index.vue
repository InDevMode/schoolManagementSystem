<template>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Frais de scolarité</h1>
            <p v-if="student" class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                {{ student.last_name }} {{ student.name }}
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="card p-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">Montant total</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ classAmount?.toLocaleString() }} FCFA</p>
            </div>
            <div class="card p-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">Montant payé</p>
                <p class="text-2xl font-bold text-success-600 mt-1">{{ totalPaid?.toLocaleString() }} FCFA</p>
            </div>
            <div class="card p-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">Reste à payer</p>
                <p class="text-2xl font-bold text-danger-600 mt-1">{{ remaining?.toLocaleString() }} FCFA</p>
            </div>
        </div>

        <div v-if="student" class="card p-4">
            <Link :href="`/parent/my_student/feescollections/${student.id}/create`" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Effectuer un paiement
            </Link>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps<{
    student: { id: number; name: string; last_name: string } | null;
    classAmount: number;
    totalPaid: number;
}>();

const remaining = computed(() => (props.classAmount ?? 0) - (props.totalPaid ?? 0));
</script>
