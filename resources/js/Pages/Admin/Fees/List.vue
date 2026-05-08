<template>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Liste des contributions reçues</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ feesCollections.total }} contribution(s)</p>
        </div>

        <div class="card overflow-hidden">
            <AppTable :columns="columns" :rows="feesCollections.data" :pagination="feesCollections" row-key="id">
                <template #cell-student="{ row }">
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ row.student_last_name }} {{ row.student_name }}</p>
                        <p class="text-xs text-gray-500">{{ row.student_admission_number }}</p>
                    </div>
                </template>
                <template #cell-payment_type="{ row }">
                    <span class="capitalize">{{ row.payment_type }}</span>
                </template>
                <template #cell-payment_status="{ row }">
                    <AppBadge :variant="row.payment_status === 'Paid' ? 'success' : 'warning'" dot>
                        {{ row.payment_status ?? 'En attente' }}
                    </AppBadge>
                </template>
                <template #actions="{ row }">
                    <button class="p-1.5 rounded-lg text-gray-400 hover:text-danger-600 hover:bg-danger-50 dark:hover:bg-danger-900/20 transition-colors" @click="deleteFees(row.id)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>
                </template>
            </AppTable>
        </div>
    </div>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { AppTable, AppBadge } from '@/Components/UI';

interface FeesCollection {
    id: number;
    student_name: string;
    student_last_name: string;
    student_admission_number: string;
    class_name: string;
    paid_amount: number;
    remaning_amount: number;
    payment_type: string;
    payment_status: string;
    created_at: string;
}

defineProps<{
    feesCollections: {
        data: FeesCollection[];
        total: number;
        from: number;
        to: number;
        links: { url: string | null; label: string; active: boolean }[];
    };
}>();

const columns = [
    { key: 'student', label: 'Apprenant' },
    { key: 'class_name', label: 'Classe' },
    { key: 'paid_amount', label: 'Montant payé' },
    { key: 'remaning_amount', label: 'Reste' },
    { key: 'payment_type', label: 'Mode' },
    { key: 'payment_status', label: 'Statut' },
    { key: 'created_at', label: 'Date' },
];

const deleteFees = (id: number) => {
    if (confirm('Supprimer cette contribution ?')) {
        router.get(`/admin/feescollections/delete/${id}`);
    }
};
</script>
