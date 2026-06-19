<template>
    <div class="space-y-6">
        <PageHeader title="Mes contributions" subtitle="Suivi de vos paiements scolaires" color="emerald">
            <template #icon>
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </template>
        </PageHeader>

        <!-- Summary cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="card p-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">Montant total</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ formatAmount(classAmount) }}</p>
            </div>
            <div class="card p-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">Montant payé</p>
                <p class="text-2xl font-bold text-success-600 mt-1">{{ formatAmount(totalPaid) }}</p>
            </div>
            <div class="card p-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">Reste à payer</p>
                <p class="text-2xl font-bold text-danger-600 mt-1">{{ formatAmount(remaining) }}</p>
            </div>
        </div>

        <!-- Barre de progression -->
        <div class="card p-4">
            <div class="flex items-center justify-between text-sm mb-2">
                <span class="text-gray-600 dark:text-gray-400">Progression du paiement</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ progressPercent }}%</span>
            </div>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                <div
                    :class="['h-2.5 rounded-full transition-all duration-500', progressPercent >= 100 ? 'bg-success-500' : 'bg-primary-500']"
                    :style="{ width: progressPercent + '%' }"
                />
            </div>
        </div>

        <!-- Historique des paiements -->
        <div>
            <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-3">Historique des paiements</h2>
            <DataTable
                :columns="columns"
                :rows="tableRows"
                row-key="id"
                export-filename="mes_contributions"
                :selectable="false"
            >
                <template #cell-paid_amount="{ row }">
                    <span class="font-medium text-success-600 dark:text-success-400">{{ formatAmount(row.paid_amount as number) }}</span>
                </template>
                <template #cell-remaning_amount="{ row }">
                    <span :class="(row.remaning_amount as number) > 0 ? 'text-danger-600 dark:text-danger-400' : 'text-success-600 dark:text-success-400'">
                        {{ formatAmount(row.remaning_amount as number) }}
                    </span>
                </template>
                <template #cell-payment_type="{ row }">
                    <AppBadge variant="gray">{{ paymentLabel(row.payment_type as string) }}</AppBadge>
                </template>
                <template #cell-payment_status="{ row }">
                    <AppBadge :variant="row.payment_status === 'Paid' ? 'success' : 'warning'" dot>
                        {{ row.payment_status === 'Paid' ? 'Payé' : 'En attente' }}
                    </AppBadge>
                </template>
                <template #cell-created_at="{ row }">
                    <span class="text-xs text-gray-500">{{ formatDate(row.created_at as string) }}</span>
                </template>
            </DataTable>

            <!-- Totaux -->
            <div v-if="feesCollections.data.length" class="flex flex-wrap items-center gap-6 px-4 py-3 mt-2 bg-gray-50 dark:bg-gray-800/60 rounded-xl border border-gray-200 dark:border-gray-700 text-sm">
                <span class="text-gray-500 font-medium">Totaux :</span>
                <span class="text-success-600 dark:text-success-400 font-semibold">
                    Total payé : <strong>{{ formatAmount(sumPaid) }}</strong>
                </span>
                <span class="text-danger-600 dark:text-danger-400 font-semibold">
                    Total restant : <strong>{{ formatAmount(sumRemaining) }}</strong>
                </span>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { PageHeader, DataTable, AppBadge } from '@/Components/UI';

interface FeesRecord {
    [key: string]: unknown;
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
    feesCollections: { data: FeesRecord[]; total: number; from: number; to: number; links: any[] };
}>();

const remaining      = computed(() => (props.classAmount ?? 0) - (props.totalPaid ?? 0));
const progressPercent = computed(() => props.classAmount ? Math.min(Math.round((props.totalPaid / props.classAmount) * 100), 100) : 0);
const sumPaid        = computed(() => props.feesCollections.data.reduce((s, r) => s + (r.paid_amount ?? 0), 0));
const sumRemaining   = computed(() => props.feesCollections.data.reduce((s, r) => s + (r.remaning_amount ?? 0), 0));

const tableRows = computed(() => props.feesCollections.data);

const columns = [
    { key: 'paid_amount',     label: 'Montant payé' },
    { key: 'remaning_amount', label: 'Reste' },
    { key: 'payment_type',    label: 'Mode' },
    { key: 'payment_status',  label: 'Statut' },
    { key: 'remark',          label: 'Remarque' },
    { key: 'created_at',      label: 'Date' },
];

const formatAmount = (n: number) =>
    new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF', maximumFractionDigits: 0 }).format(n ?? 0);

const formatDate = (d: string) => {
    if (!d) return '—';
    try { return new Date(d).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' }); }
    catch { return d; }
};

const paymentLabel = (type: string) => ({
    cash: 'Espèces', check: 'Chèque', transfer: 'Virement',
    kkiapay: 'Kkiapay', fedapay: 'FedaPay', stripe: 'Stripe', paypal: 'PayPal',
}[type] ?? type ?? '—');
</script>
