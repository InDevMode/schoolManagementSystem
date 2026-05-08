<template>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Mes contributions</h1>
        </div>

        <!-- Summary cards -->
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

        <!-- Fees history -->
        <div class="card overflow-hidden">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white">Historique des paiements</h2>
            </div>
            <AppTable :columns="columns" :rows="feesCollections.data" :pagination="feesCollections" row-key="id">
                <template #cell-payment_type="{ row }">
                    <span class="capitalize">{{ row.payment_type }}</span>
                </template>
                <template #cell-payment_status="{ row }">
                    <AppBadge :variant="row.payment_status === 'Paid' ? 'success' : 'warning'" dot>
                        {{ row.payment_status ?? 'En attente' }}
                    </AppBadge>
                </template>
            </AppTable>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { AppTable, AppBadge } from '@/Components/UI';

interface FeesRecord {
    id: number;
    paid_amount: number;
    remaning_amount: number;
    payment_type: string;
    payment_status: string;
    remark: string;
    created_at: string;
}

const props = defineProps<{
    student: Record<string, unknown> | null;
    classAmount: number;
    totalPaid: number;
    feesCollections: {
        data: FeesRecord[];
        total: number;
        from: number;
        to: number;
        links: { url: string | null; label: string; active: boolean }[];
    };
}>();

const remaining = computed(() => (props.classAmount ?? 0) - (props.totalPaid ?? 0));

const columns = [
    { key: 'paid_amount', label: 'Montant payé' },
    { key: 'remaning_amount', label: 'Reste' },
    { key: 'payment_type', label: 'Mode' },
    { key: 'payment_status', label: 'Statut' },
    { key: 'remark', label: 'Remarque' },
    { key: 'created_at', label: 'Date' },
];
</script>
