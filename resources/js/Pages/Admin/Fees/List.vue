<template>
    <div class="space-y-6">
        <PageHeader title="Rapports des contributions" :subtitle="`${feesCollections.total} contribution(s)`" color="emerald">
            <template #icon>
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </template>
            <template #actions>
                <a
                    v-if="can('view.fees.reports')"
                    href="/admin/feescollections/feescollects/export"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold
                           transition-all duration-150 text-white
                           bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800
                           shadow-sm shadow-emerald-200 dark:shadow-emerald-900/40"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Exporter Excel
                </a>
            </template>
        </PageHeader>

        <!-- Résumé -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="card p-4">
                <p class="text-xs text-gray-500 dark:text-gray-400">Total collecté</p>
                <p class="text-xl font-bold text-success-600 dark:text-success-400 mt-1">{{ formatAmount(totalPaid) }}</p>
            </div>
            <div class="card p-4">
                <p class="text-xs text-gray-500 dark:text-gray-400">Nombre de paiements</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white mt-1">{{ feesCollections.total }}</p>
            </div>
            <div class="card p-4">
                <p class="text-xs text-gray-500 dark:text-gray-400">Paiements validés</p>
                <p class="text-xl font-bold text-primary-600 dark:text-primary-400 mt-1">{{ paidCount }}</p>
            </div>
            <div class="card p-4">
                <p class="text-xs text-gray-500 dark:text-gray-400">En attente</p>
                <p class="text-xl font-bold text-warning-600 dark:text-warning-400 mt-1">{{ pendingCount }}</p>
            </div>
        </div>

        <div class="card overflow-hidden">
            <DataTable
                ref="tableRef"
                :columns="columns"
                :rows="feesCollections.data"
                row-key="id"
                export-filename="rapport-contributions"
                @delete="handleDelete"
            >

                <template #cell-student="{ row }">
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ row.student_last_name }} {{ row.student_name }}</p>
                        <p class="text-xs text-gray-500">{{ row.student_admission_number ?? '—' }}</p>
                    </div>
                </template>

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
                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ formatDate(row.created_at as string) }}</span>
                </template>

                <template #actions="{ row }">
                    <button
                        v-if="can('action.fees.delete')"
                        class="p-1.5 rounded-xl transition-all duration-150 text-white
                               bg-red-500 hover:bg-red-600 active:bg-red-700
                               shadow-sm shadow-red-200 dark:shadow-red-900/40"
                        title="Supprimer"
                        @click="tableRef?.confirmDelete(row.id as number, `paiement #${row.id}`)"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </template>
            </DataTable>
        </div>

        <!-- Totaux -->
        <div v-if="feesCollections.data.length > 0" class="flex flex-wrap items-center gap-6 px-4 py-3 bg-gray-50 dark:bg-gray-800/60 rounded-xl border border-gray-200 dark:border-gray-700 text-sm">
            <span class="text-gray-500 font-medium">Totaux :</span>
            <span class="text-success-600 dark:text-success-400">Payé : <strong>{{ formatAmount(totalPaid) }}</strong></span>
            <span class="text-danger-600 dark:text-danger-400">Reste : <strong>{{ formatAmount(totalRemaining) }}</strong></span>
        </div>
    </div>
</template>

<script setup lang="ts">
import { fmtDate } from '@/Utils/dateFormat';
import { computed, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { PageHeader, DataTable, AppBadge } from '@/Components/UI';
import { useCan } from '@/Composables/useCan';
import { useToast } from '@/Composables/useToast';

const { can } = useCan();

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

const props = defineProps<{
    feesCollections: {
        data: FeesCollection[];
        total: number;
        from: number;
        to: number;
        links: { url: string | null; label: string; active: boolean }[];
    };
}>();

const columns = [
    { key: 'student',          label: 'Apprenant' },
    { key: 'class_name',       label: 'Classe' },
    { key: 'paid_amount',      label: 'Montant payé' },
    { key: 'remaning_amount',  label: 'Reste' },
    { key: 'payment_type',     label: 'Mode', exportFormat: (v: unknown) => ({ cash: 'Espèces', check: 'Chèque', transfer: 'Virement', kkiapay: 'Kkiapay', fedapay: 'FedaPay', stripe: 'Stripe', paypal: 'PayPal' }[v as string] ?? String(v ?? '—')) },
    { key: 'payment_status',   label: 'Statut', exportFormat: (v: unknown) => (v === 'Paid' ? 'Payé' : 'En attente') },
    { key: 'created_at',       label: 'Date' },
];

// Résumé calculé côté client sur la page courante
const totalPaid    = computed(() => props.feesCollections.data.reduce((s, r) => s + (r.paid_amount ?? 0), 0));
const totalRemaining = computed(() => props.feesCollections.data.reduce((s, r) => s + (r.remaning_amount ?? 0), 0));
const paidCount    = computed(() => props.feesCollections.data.filter(r => r.payment_status === 'Paid').length);
const pendingCount = computed(() => props.feesCollections.data.filter(r => r.payment_status !== 'Paid').length);

const toast = useToast();
const tableRef = ref<InstanceType<typeof DataTable> | null>(null);

const formatAmount = (n: number) =>
    new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF', maximumFractionDigits: 0 }).format(n ?? 0);

const formatDate = fmtDate;

const paymentLabel = (type: string) => ({
    cash: 'Espèces', check: 'Chèque', transfer: 'Virement',
    kkiapay: 'Kkiapay', fedapay: 'FedaPay', stripe: 'Stripe', paypal: 'PayPal',
}[type] ?? type ?? '—');

const deleteFees = (id: number) => {
    router.get(`/admin/feescollections/collections/delete/${id}`, {}, {
        onSuccess: () => toast.success('Contribution masquée.'),
        onError:   () => toast.error('Erreur lors de la suppression.'),
    });
};

const handleDelete = (ids: (string | number)[]) => {
    ids.forEach(id => {
        router.get(`/admin/feescollections/collections/delete/${id}`, {}, {
            onSuccess: () => toast.success('Contribution supprimée avec succès.'),
            onError: () => toast.error('Erreur lors de la suppression.'),
        });
    });
};
</script>
