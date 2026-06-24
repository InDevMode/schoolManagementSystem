<template>
    <div class="space-y-6">
        <PageHeader title="Collecter les contributions" :subtitle="`${feesCollections.total} apprenant(s)`" color="emerald">
            <template #icon>
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </template>
            <template #actions>
                <div class="flex items-center gap-2">
                    <label class="text-sm font-medium text-gray-600 dark:text-gray-400 whitespace-nowrap">Filtrer par classe</label>
                    <AppSelect
                        v-model="filters.class_id"
                        :options="classOptions"
                        placeholder="Toutes les classes"
                        class="min-w-[200px]"
                    />
                </div>
            </template>
        </PageHeader>

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
                        v-if="can('action.fees.collect') && ((row.remaning_amount as number) ?? (row.class_amount as number)) > 0"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-xl
                               transition-all duration-150 text-white
                               bg-primary-600 hover:bg-primary-700 active:bg-primary-800
                               shadow-sm shadow-primary-300 dark:shadow-primary-900/50"
                        @click="openPayment(row as any)"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                        Collecter
                    </button>
                    <AppBadge v-else-if="((row.remaning_amount as number) ?? (row.class_amount as number)) <= 0" variant="success" dot>Soldé</AppBadge>
                    <span v-else class="text-xs text-gray-400 italic">—</span>
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
                    @change="onPaymentTypeChange"
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
                <div v-if="payForm.payment_type === 'stripe'" class="p-3 bg-primary-50 dark:bg-primary-900/20 rounded-xl text-xs text-primary-700 dark:text-primary-300">
                    <p class="font-medium mb-1">Paiement via Stripe</p>
                    <p>Vous serez redirigé vers la page de paiement sécurisée Stripe.</p>
                </div>

                <!-- Info PayPal -->
                <div v-if="payForm.payment_type === 'paypal'" class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-xl text-xs text-blue-700 dark:text-blue-300">
                    <p class="font-medium mb-1">Paiement via PayPal</p>
                    <p>Vous serez redirigé vers PayPal pour finaliser le paiement.</p>
                </div>
            </form>

            <template #footer>
                <AppButton variant="ghost" @click="showPayment = false; paying = false">Annuler</AppButton>

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

                <!-- Bouton PayPal -->
                <AppButton
                    v-else-if="payForm.payment_type === 'paypal'"
                    :loading="paying"
                    :disabled="!payForm.amount"
                    class="bg-[#0070ba] hover:bg-[#005ea6] text-white"
                    @click="payWithPaypal"
                >
                    <svg class="w-4 h-4 mr-1 inline" viewBox="0 0 24 24" fill="currentColor"><path d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944.901C5.026.382 5.474 0 5.998 0h7.46c2.57 0 4.578.543 5.69 1.81 1.01 1.15 1.304 2.42 1.012 4.287-.023.143-.047.288-.077.437-.983 5.05-4.349 6.797-8.647 6.797h-2.19c-.524 0-.968.382-1.05.9l-1.12 7.106zm14.146-14.42a3.35 3.35 0 0 0-.607-.541c-.013.076-.026.175-.041.254-.93 4.778-4.005 7.201-9.138 7.201h-2.19a.563.563 0 0 0-.556.479l-1.187 7.527h-.506l-.24 1.516a.56.56 0 0 0 .554.647h3.882c.46 0 .85-.334.922-.788.06-.26.76-4.852.816-5.09a.932.932 0 0 1 .923-.788h.58c3.76 0 6.705-1.528 7.565-5.946.36-1.847.174-3.388-.777-4.471z"/></svg>
                    Payer avec PayPal
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
import { ref, computed, watch } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { PageHeader, AppSelect, DataTable, AppBadge, AppModal, AppButton, AppInput } from '@/Components/UI';
import { useCan } from '@/Composables/useCan';
import { useToast } from '@/Composables/useToast';
import type { PageProps } from '@/types';

const { can } = useCan();
const toast = useToast();

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

const page           = usePage<PageProps>();
const kkiapayKey     = computed(() => page.props.settings?.kkiapay_public_key ?? '');
const fedapayKey     = computed(() => page.props.settings?.fedapay_public_key ?? '');
const paypalClientId = computed(() => page.props.settings?.paypal_client_id ?? '');

// Initialiser le filtre depuis l'URL courante (pour conserver le filtre après reload)
const urlParams   = new URLSearchParams(window.location.search);
const filters     = ref({ class_id: urlParams.get('class_id') ?? '' });
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
    { value: 'paypal',   label: 'PayPal' },
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
    paying.value = false;
    showPayment.value = true;
};

// Réinitialiser l'état paying quand on change de mode de paiement
const onPaymentTypeChange = () => {
    paying.value = false;
    payForm.value.errors = {};
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
        onError:  (e) => {
            payForm.value.errors = e;
            toast.error('Erreur lors de l\'enregistrement du paiement.');
        },
        onFinish: () => { paying.value = false; },
    });
};

// Paiement Kkiapay — ouvre le widget
const payWithKkiapay = () => {
    if (!payForm.value.amount || !selectedStudent.value) return;
    if (!kkiapayKey.value) {
        toast.error('Clé publique Kkiapay non configurée. Allez dans Paramètres pour la configurer.');
        return;
    }

    paying.value = true;

    // Charger le SDK Kkiapay si pas encore chargé
    const openKkiapay = (window as any).openKkiapayWidget;
    if (typeof openKkiapay !== 'function') {
        toast.error("Le widget Kkiapay n'est pas chargé. Vérifiez votre connexion internet.");
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
    const successHandler = (response: { transactionId: string }) => {
        router.post(`/admin/feescollections/collections/addFees/${selectedStudent.value!.id}`, {
            amount:              payForm.value.amount,
            payment_type:        'kkiapay',
            remark:              payForm.value.remark,
            kkiapay_payment_id:  response.transactionId,
        }, {
            preserveScroll: true,
            onSuccess: () => {
                showPayment.value = false;
                router.reload({ only: ['feesCollections'] });
            },
            onError: () => { toast.error('Erreur lors de l\'enregistrement du paiement Kkiapay.'); },
            onFinish:  () => { paying.value = false; },
        });
    };

    // Écouter la fermeture/annulation du widget Kkiapay → réactiver le bouton
    const closeHandler = () => {
        paying.value = false;
    };

    (window as any).addSuccessListener?.(successHandler);
    // KKiaPay émet un événement "close" quand l'utilisateur ferme le widget
    (window as any).addCloseListener?.(closeHandler);
};

// Paiement FedaPay — redirection vers FedaPay Checkout
const payWithFedapay = async () => {
    if (!payForm.value.amount || !selectedStudent.value) return;
    if (!fedapayKey.value) {
        toast.error('Clé publique FedaPay non configurée. Allez dans Paramètres pour la configurer.');
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
            toast.error(data.error);
            paying.value = false;
        }
    } catch {
        toast.error('Erreur lors de la connexion à FedaPay.');
        paying.value = false;
    }
};

// Paiement Stripe — le serveur crée la session et retourne l'URL en JSON
const payWithStripe = async () => {
    if (!payForm.value.amount || !selectedStudent.value) return;

    // Vérifier la clé publique Stripe côté frontend avant d'envoyer
    if (!page.props.settings?.stripe_public_key) {
        toast.error('Clé publique Stripe non configurée. Allez dans Paramètres pour la configurer.');
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
                payment_type: 'stripe',
                remark:       payForm.value.remark,
            }),
        });

        const data = await res.json();

        if (data.redirect_url) {
            // Redirection externe — window.location.href évite le problème CORS
            window.location.href = data.redirect_url;
        } else if (data.error) {
            toast.error(data.error);
            paying.value = false;
        }
    } catch {
        toast.error('Erreur lors de la connexion à Stripe.');
        paying.value = false;
    }
};

// Paiement PayPal — redirection vers PayPal REST v2
const payWithPaypal = async () => {
    if (!payForm.value.amount || !selectedStudent.value) return;

    if (!paypalClientId.value) {
        toast.error('Identifiants PayPal non configurés. Allez dans Paramètres pour les configurer.');
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
                payment_type: 'paypal',
                remark:       payForm.value.remark,
            }),
        });

        const data = await res.json();

        if (data.redirect_url) {
            window.location.href = data.redirect_url;
        } else if (data.error) {
            toast.error(data.error);
            paying.value = false;
        }
    } catch {
        toast.error('Erreur lors de la connexion à PayPal.');
        paying.value = false;
    }
};

// Réinitialiser paying quand le modal se ferme (ex: clic en dehors ou Échap)
watch(showPayment, (val) => {
    if (!val) paying.value = false;
});

// Filtrage par classe — watch car AppSelect n'émet que update:modelValue (pas @change)
watch(() => filters.value.class_id, () => {
    applyFilters();
});

const applyFilters = () => {
    router.get('/admin/feescollections/collections/list', { class_id: filters.value.class_id || undefined }, { preserveState: true, replace: true });
};
</script>
