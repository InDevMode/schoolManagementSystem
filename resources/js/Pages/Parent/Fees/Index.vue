<template>
    <div class="space-y-6">
        <PageHeader title="Frais de scolarité" :subtitle="student ? `${student.last_name} ${student.name}` : 'Suivi des paiements'" color="emerald">
            <template #icon>
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </template>
        </PageHeader>

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
import { PageHeader } from '@/Components/UI';

const props = defineProps<{
    student: { id: number; name: string; last_name: string } | null;
    classAmount: number;
    totalPaid: number;
}>();

const remaining = computed(() => (props.classAmount ?? 0) - (props.totalPaid ?? 0));
</script>
