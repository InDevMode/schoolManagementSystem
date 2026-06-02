<template>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Collecter les contributions</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ feesCollections.total }} apprenant(s)</p>
            </div>
            <AppSelect v-model="filters.class_id" :options="classOptions" placeholder="Filtrer par classe" class="w-48" @change="applyFilters" />
        </div>

        <div class="card overflow-hidden">
            <DataTable
                ref="tableRef"
                :columns="columns"
                :rows="feesCollections.data"
                row-key="id"
                export-filename="contributions"
            >

                <!-- Apprenant -->
                <template #cell-student="{ row }">
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ row.student_last_name }} {{ row.student_name }}</p>
                        <p class="text-xs text-gray-500">{{ row.student_admission_number ?? '—' }}</p>
                    </div>
                </template>

                <!-- Montant total -->
                <template #cell-class_amount="{ row }">
                    <span class="font-medium text-gray-900 dark:text-white">{{ formatAmount(row.class_amount as number) }}</span>
                </template>

                <!-- Montant payé -->
                <template #cell-paid_amount="{ row }">
                    <span :class="(row.paid_amount as number) > 0 ? 'text-success-600 dark:text-success-400 font-medium' : 'text-gray-400'">
                        {{ formatAmount((row.paid_amount as number) ?? 0) }}
                    </span>
                </template>

                <!-- Reste -->
                <template #cell-remaining="{ row }">
                    <span :class="((row.remaning_amount as number) ?? (row.class_amount as number)) > 0 ? 'text-danger-600 dark:text-danger-400 font-medium' : 'text-success-600 dark:text-success-400'">
                        {{ formatAmount((row.remaning_amount as number) ?? (row.class_amount as number)) }}
                    </span>
                </template>

                <!-- Progression -->
                <template #cell-progress="{ row }">
                    <div class="w-24">
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="text-gray-500">{{ progressPercent(row as any) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                            <div
                                :class="['h-1.5 rounded-full transition-all', progressPercent(row as any) >= 100 ? 'bg-success-500' : 'bg-primary-500']"
                                :style="{ width: progressPercent(row as any) + '%' }"
                            />
                        </div>
                    </div>
                </template>

                <!-- Actions -->
                <template #actions="{ row }">
                    <button
                        v-if="((row.remaning_amount as number) ?? (row.class_amount as number)) > 0"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 hover:bg-primary-100 dark:hover:bg-primary-900/40 transition-colors"
                        @click="openPayment(row as any)"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Collecter
                    </button>
                    <AppBadge v-else variant="success" dot>Soldé</AppBadge>
                </template>
            </DataTable>
        </div>

        <!-- Totaux -->
        <div v-if="feesCollections.data.length > 0" class="flex flex-wrap items-center gap-6 px-4 py-3 bg-gray-50 dark:bg-gray-800/60 rounded-xl border border-gray-200 dark:border-gray-700 text-sm">
            <span class="text-gray-500 font-medium">Totaux :</span>
            <span class="text-gray-700 dark:text-gray-300">Total : <strong>{{ formatAmount(totalClassAmount) }}</strong></span>
            <span class="text-success-600 dark:text-success-400">Payé : <strong>{{ formatAmount(totalPaidAmount) }}</strong></span>
            <span class="text-danger-600 dark:text-danger-400">Reste : <strong>{{ formatAmount(totalRemainingAmount) }}</strong></span>
        </div>

        <!-- Modal Paiement -->
        <AppModal v-model="showPayment" title="Enregistrer un paiement" size="md" persistent>
            <div v-if="selectedStudent" class="mb-5 p-4 bg-gray-50 dark:bg-gray-700/40 rounded-xl">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ selectedStudent.student_last_name }} {{ selectedStudent.student_name }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ selectedStudent.class_name }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-500">Reste à payer</p>
                        <p class="text-lg font-bold text-danger-600 dark:text-danger-400">
                            {{ formatAmount(selectedStudent.remaning_amount ?? selectedStudent.class_amount) }}
                        </p>
                    </div>
                </div>
            </div>

            <form :id="payFormId" @submit.prevent="submitPayment" class="space-y-4">
                <AppInput
                    v-model="payForm.amount"
                    label="Montant à collecter"
                    type="number"
                    :placeholder="`Max: ${formatAmount(selectedStudent?.remaning_amount ?? 0)}`"
                    required
                    :error="payForm.errors?.amount"
                />
                <AppSelect
                    v-model="payForm.payment_type"
                    label="Mode de paiement"
                    :options="paymentTypes"
                    required
                />
                <AppInput
                    v-model="payForm.remark"
                    label="Remarque (optionnel)"
                    placeholder="Ex: Paiement partiel..."
                />

                <!-- Info Kkiapay -->
                <div v-if="payForm.payment_type === 'kkiapay'" class="p-3 bg-amber-50 dark:bg-amber-900/20 rounded-xl text-xs text-amber-700 dark:text-amber-300">
                    <p class="font-medium mb-1">Paiement via Kkiapay</p>
                    <p>Cliquez sur "Payer avec Kkiapay" pour ouvrir le widget de paiement.</p>
                </div>

                <!-- Info FedaPay -->
                <div v-if="payForm.payment_type === 'fedapay'" class="p-3 bg-orange-50 dark:bg-orange-900/20 rounded-xl text-xs text-orange-700 dark:text-orange-300">
                    <p class="font-medium mb-1">Paiement via FedaPay</p>
                    <p>Vous serez redirigé vers la page de paiement FedaPay (Mobile Money, carte...).</p>
                </div>

                <!-- Info Stripe -->
                <div v-if="payForm.payment_type === 'stripe'" class="p-3 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl text-xs text-indigo-700 dark:text-indigo-300">
                    <p class="font-medium mb-1">Paiement via Stripe</p>
                    <p>Vous serez redirigé vers la page de paiement sécurisée Stripe.</p>
                </div>
            </form>

            <template #footer>
                <AppButton variant="ghost" @click="showPayment = false">Annuler</AppButton>

                <!-- Bouton Kkiapay -->
                <AppButton
                    v-if="payForm.payment_type === 'kkiapay'"
                    variant="warning"
                    :loading="paying"
                    :disabled="!payForm.amount"
                    @click="payWithKkiapay"
                >
                    Payer avec Kkiapay
                </AppButton>

                <!-- Bouton FedaPay -->
                <AppButton
                    v-else-if="payForm.payment_type === 'fedapay'"
                    :loading="paying"
                    :disabled="!payForm.amount"
                    class="bg-orange-500 hover:bg-orange-600 text-white"
                    @click="payWithFedapay"
                >
                    Payer avec FedaPay
                </AppButton>

                <!-- Bouton Stripe -->
                <AppButton
                    v-else-if="payForm.payment_type === 'stripe'"
                    variant="secondary"
                    :loading="paying"
                    :disabled="!payForm.amount"
                    @click="payWithStripe"
                >
                    Payer avec Stripe
                </AppButton>

                <!-- Bouton standard (cash, chèque, virement) -->
                <AppButton
                    v-else
                    type="submit"
                    :form="payFormId"
                    :loading="paying"
                >
                    Enregistrer le paiement
                </AppButton>
            </template>
        </AppModal>
    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { AppSelect, DataTable, AppBadge, AppModal, AppButton, AppInput } from '@/Components/UI';
import type { PageProps } from '@/types';

interface FeesStudent {
    [key: string]: unknown;
    id: number;
    student_name: string;
    student_last_name: string;
    student_admission_number: string;
    class_name: string;
    class_amount: number;
    paid_amount?: number;
    remaning_amount?: number;
    payment_status?: string;
}

const props = defineProps<{
    classes: { id: number; name: string }[];
    feesCollections: {
        data: FeesStudent[];
        total: number;
        from: number;
        to: number;
        links: { url: string | null; label: string; active: boolean }[];
    };
}>();

const page            = usePage<PageProps>();
const kkiapayKey  = computed(() => page.props.settings?.kkiapay_public_key ?? '');
const fedapayKey  = computed(() => page.props.settings?.fedapay_public_key ?? '');

const filters         = ref({ class_id: '' });
const showPayment     = ref(false);
const paying          = ref(false);
const selectedStudent = ref<FeesStudent | null>(null);
const payFormId       = 'pay-form';
const tableRef        = ref<InstanceType<typeof DataTable> | null>(null);

// Totaux
const totalClassAmount    = computed(() => props.feesCollections.data.reduce((s, r) => s + (r.class_amount ?? 0), 0));
const totalPaidAmount     = computed(() => props.feesCollections.data.reduce((s, r) => s + (r.paid_amount ?? 0), 0));
const totalRemainingAmount = computed(() => props.feesCollections.data.reduce((s, r) => s + (r.remaning_amount ?? r.class_amount ?? 0), 0));

const payForm = ref({
    amount:       '',
    payment_type: 'cash',
    remark:       '',
    errors:       {} as Record<string, string>,
});

const classOptions = computed(() =>
    props.classes.map(c => ({ value: String(c.id), label: c.name }))
);

const paymentTypes = [
    { value: 'cash',     label: 'Espèces' },
    { value: 'check',    label: 'Chèque' },
    { value: 'transfer', label: 'Virement' },
    { value: 'kkiapay',  label: 'Kkiapay' },
    { value: 'fedapay',  label: 'FedaPay' },
    { value: 'stripe',   label: 'Stripe' },
];

const columns = [
    { key: 'student',      label: 'Apprenant' },
    { key: 'class_name',   label: 'Classe' },
    { key: 'class_amount', label: 'Total' },
    { key: 'paid_amount',  label: 'Payé' },
    { key: 'remaining',    label: 'Reste' },
    { key: 'progress',     label: 'Progression' },
];

const formatAmount = (n: number) =>
    new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF', maximumFractionDigits: 0 }).format(n ?? 0);

const progressPercent = (row: FeesStudent) => {
    if (!row.class_amount) return 0;
    return Math.min(Math.round(((row.paid_amount ?? 0) / row.class_amount) * 100), 100);
};

const openPayment = (student: FeesStudent) => {
    selectedStudent.value = student;
    payForm.value = { amount: '', payment_type: 'cash', remark: '', errors: {} };
    showPayment.value = true;
};

// Paiement standard (cash, chèque, virement)
const submitPayment = () => {
    if (!selectedStudent.value) return;
    paying.value = true;
    router.post(`/admin/feescollections/collections/addFees/${selectedStudent.value.id}`, {
        amount:       payForm.value.amount,
        payment_type: payForm.value.payment_type,
        remark:       payForm.value.remark,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            showPayment.value = false;
            router.reload({ only: ['feesCollections'] });
        },
        onError:  (e) => { payForm.value.errors = e; },
        onFinish: () => { paying.value = false; },
    });
};

// Paiement Kkiapay — ouvre le widget
const payWithKkiapay = () => {
    if (!payForm.value.amount || !selectedStudent.value) return;
    if (!kkiapayKey.value) {
        alert('Clé publique Kkiapay non configurée. Allez dans Paramètres pour la configurer.');
        return;
    }

    paying.value = true;

    // Charger le SDK Kkiapay si pas encore chargé
    const openKkiapay = (window as any).openKkiapayWidget;
    if (typeof openKkiapay !== 'function') {
        alert('Le widget Kkiapay n\'est pas chargé. Vérifiez votre connexion internet.');
        paying.value = false;
        return;
    }

    openKkiapay({
        amount:    parseInt(payForm.value.amount),
        key:       kkiapayKey.value,
        // Détecter sandbox selon le préfixe de la clé publique
        sandbox:   !kkiapayKey.value.startsWith('pk_live'),
        callback:  `${window.location.origin}/admin/feescollections/collections/list`,
    });

    // Écouter le succès du paiement Kkiapay
    (window as any).addSuccessListener?.((response: { transactionId: string }) => {
        router.post(`/admin/feescollections/collections/addFees/${selectedStudent.value!.id}`, {
            amount:              payForm.value.amount,
            payment_type:        'kkiapay',
            remark:              payForm.value.remark,
            kkiapay_payment_id:  response.transactionId,
        }, {
            preserveScroll: true,
            onSuccess: () => { showPayment.value = false; router.reload({ only: ['feesCollections'] }); },
            onFinish:  () => { paying.value = false; },
        });
    });
};

// Paiement FedaPay — redirection vers FedaPay Checkout
const payWithFedapay = async () => {
    if (!payForm.value.amount || !selectedStudent.value) return;
    if (!fedapayKey.value) {
        alert('Clé publique FedaPay non configurée. Allez dans Paramètres pour la configurer.');
        return;
    }
    paying.value = true;

    try {
        const csrfToken = (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? '';
        const res = await fetch(`/admin/feescollections/collections/addFees/${selectedStudent.value.id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                amount:       payForm.value.amount,
                payment_type: 'fedapay',
                remark:       payForm.value.remark,
            }),
        });

        const data = await res.json();

        if (data.redirect_url) {
            window.location.href = data.redirect_url;
        } else if (data.error) {
            payForm.value.errors = { amount: data.error };
            paying.value = false;
        }
    } catch {
        payForm.value.errors = { amount: 'Erreur lors de la connexion à FedaPay.' };
        paying.value = false;
    }
};

// Paiement Stripe — le serveur crée la session et retourne l'URL en JSON
const payWithStripe = async () => {
    if (!payForm.value.amount || !selectedStudent.value) return;
    paying.value = true;

    try {
        const csrfToken = (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? '';
        const res = await fetch(`/admin/feescollections/collections/addFees/${selectedStudent.value.id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                amount:       payForm.value.amount,
                payment_type: 'stripe',
                remark:       payForm.value.remark,
            }),
        });

        const data = await res.json();

        if (data.redirect_url) {
            // Redirection externe — window.location.href évite le problème CORS
            window.location.href = data.redirect_url;
        } else if (data.error) {
            payForm.value.errors = { amount: data.error };
            paying.value = false;
        }
    } catch (e) {
        payForm.value.errors = { amount: 'Erreur lors de la connexion à Stripe.' };
        paying.value = false;
    }
};

const applyFilters = () => {
    router.get('/admin/feescollections/collections/list', filters.value, { preserveState: true, replace: true });
};
</script>
