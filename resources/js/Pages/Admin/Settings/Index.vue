<template>
    <div class="space-y-5 max-w-5xl mx-auto">
        <PageHeader title="Paramètres" subtitle="Configuration de l'établissement" color="violet">
            <template #icon>
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </template>
        </PageHeader>

        <!-- Bandeau école multi-tenant -->
        <div v-if="isSchool"
             class="flex items-center gap-2.5 px-4 py-2.5 rounded-lg
                    bg-violet-50 dark:bg-violet-900/20 border border-violet-200 dark:border-violet-700 text-sm">
            <svg class="w-4 h-4 text-violet-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <span class="text-violet-700 dark:text-violet-300 font-medium">Paramètres de votre école</span>
            <span class="text-violet-600 dark:text-violet-400">— Ces paramètres s'appliquent uniquement à votre établissement.</span>
        </div>

        <AppAlert v-if="successMsg" variant="success" :message="successMsg" dismissible />
        <AppAlert v-if="errorMsg"   variant="danger"  :message="errorMsg"   dismissible />

        <form @submit.prevent="can('action.settings.manage') && submitForm()" enctype="multipart/form-data" class="space-y-5">

            <!-- Ligne 1 : Informations générales + Logo & Favicon côte à côte -->
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

                <!-- Informations générales (prend 2/3 de la largeur) -->
                <div class="card p-5 space-y-4 xl:col-span-2">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-3">
                        Informations générales
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <AppInput v-model="form.school_name" label="Nom de l'établissement" />
                        <AppInput v-model="form.school_type" label="Type d'établissement" placeholder="Ex: Lycée, Collège..." />
                        <AppInput v-model="form.phone"       label="Téléphone" />
                        <AppInput v-model="form.email"       label="Email" type="email" />
                        <AppInput v-model="form.uai_number"  label="Numéro UAI / Identifiant" />
                        <AppSelect v-model="form.status" label="Statut" :options="[{value:'1',label:'Actif'},{value:'0',label:'Inactif'}]" />
                        <div class="sm:col-span-2">
                            <AppInput v-model="form.address" label="Adresse complète" />
                        </div>
                    </div>
                </div>

                <!-- Logo & Favicon (prend 1/3 de la largeur) -->
                <div class="card p-5 space-y-4 xl:col-span-1">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-3">
                        Logo & Favicon
                    </h2>
                    <!-- Logo -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Logo</label>
                        <div class="flex items-center gap-3">
                            <div class="w-20 h-14 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 flex items-center justify-center overflow-hidden p-1 flex-shrink-0">
                                <img :src="logoPreview || logoUrl" alt="Logo" class="max-h-full max-w-full object-contain" />
                            </div>
                            <label class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                Changer
                                <input type="file" accept="image/*" class="hidden" @change="onLogoChange" />
                            </label>
                        </div>
                        <p class="text-xs text-gray-400">PNG, JPG recommandé. Fond transparent idéal.</p>
                    </div>
                    <!-- Favicon -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Favicon</label>
                        <div class="flex items-center gap-3">
                            <div class="w-14 h-14 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 flex items-center justify-center overflow-hidden p-1 flex-shrink-0">
                                <img :src="faviconPreview || faviconUrl" alt="Favicon" class="max-h-full max-w-full object-contain" />
                            </div>
                            <label class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                Changer
                                <input type="file" accept="image/*" class="hidden" @change="onFaviconChange" />
                            </label>
                        </div>
                        <p class="text-xs text-gray-400">ICO ou PNG 32×32 recommandé.</p>
                    </div>
                </div>
            </div>

            <!-- Ligne 2 : Passerelles de paiement en grille 2x2 -->
            <div>
                <h2 class="text-base font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    Passerelles de paiement
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                    <!-- PayPal -->
                    <div class="card p-5 space-y-3">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2.5 flex items-center gap-2">
                            <svg class="w-4 h-4 text-violet-500" viewBox="0 0 24 24" fill="currentColor"><path d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944.901C5.026.382 5.474 0 5.998 0h7.46c2.57 0 4.578.543 5.69 1.81 1.01 1.15 1.304 2.42 1.012 4.287-.023.143-.047.288-.077.437-.983 5.05-4.349 6.797-8.647 6.797h-2.19c-.524 0-.968.382-1.05.9l-1.12 7.106zm14.146-14.42a3.35 3.35 0 0 0-.607-.541c-.013.076-.026.175-.041.254-.93 4.778-4.005 7.201-9.138 7.201h-2.19a.563.563 0 0 0-.556.479l-1.187 7.527h-.506l-.24 1.516a.56.56 0 0 0 .554.647h3.882c.46 0 .85-.334.922-.788.06-.26.76-4.852.816-5.09a.932.932 0 0 1 .923-.788h.58c3.76 0 6.705-1.528 7.565-5.946.36-1.847.174-3.388-.777-4.471z"/></svg>
                            PayPal
                        </h3>
                        <AppInput v-model="form.paypal_email" label="Email PayPal" type="email" placeholder="votre@paypal.com" />
                    </div>

                    <!-- Stripe -->
                    <div class="card p-5 space-y-3">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2.5 flex items-center gap-2">
                            <svg class="w-4 h-4 text-primary-500" viewBox="0 0 24 24" fill="currentColor"><path d="M13.976 9.15c-2.172-.806-3.356-1.426-3.356-2.409 0-.831.683-1.305 1.901-1.305 2.227 0 4.515.858 6.09 1.631l.89-5.494C18.252.975 15.697 0 12.165 0 9.667 0 7.589.654 6.104 1.872 4.56 3.147 3.757 4.992 3.757 7.218c0 4.039 2.467 5.76 6.476 7.219 2.585.92 3.445 1.574 3.445 2.583 0 .98-.84 1.545-2.354 1.545-1.875 0-4.965-.921-6.99-2.109l-.9 5.555C5.175 22.99 8.385 24 11.714 24c2.641 0 4.843-.624 6.328-1.813 1.664-1.305 2.525-3.236 2.525-5.732 0-4.128-2.524-5.851-6.591-7.305z"/></svg>
                            Stripe
                        </h3>
                        <div class="grid grid-cols-2 gap-3">
                            <AppInput v-model="form.stripe_public_key" label="Clé publique" placeholder="pk_..." />
                            <AppInput v-model="form.stripe_secret_key" label="Clé secrète"  placeholder="sk_..." />
                        </div>
                    </div>

                    <!-- Kkiapay -->
                    <div class="card p-5 space-y-3">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2.5">
                            Kkiapay
                        </h3>
                        <div class="grid grid-cols-2 gap-3">
                            <AppInput v-model="form.kkiapay_public_key"  label="Clé publique" placeholder="pk_..." />
                            <AppInput v-model="form.kkiapay_private_key" label="Clé privée"   placeholder="sk_..." />
                            <div class="col-span-2">
                                <AppInput v-model="form.kkiapay_secret_key" label="Clé secrète" placeholder="secret_..." />
                            </div>
                        </div>
                    </div>

                    <!-- FedaPay -->
                    <div class="card p-5 space-y-3">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2.5 flex items-center gap-2">
                            <svg class="w-4 h-4 text-orange-500" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.41 16.09V20h-2.67v-1.93c-1.71-.36-3.16-1.46-3.27-3.4h1.96c.1 1.05.82 1.87 2.65 1.87 1.96 0 2.4-.98 2.4-1.59 0-.83-.44-1.61-2.67-2.14-2.48-.6-4.18-1.62-4.18-3.67 0-1.72 1.39-2.84 3.11-3.21V4h2.67v1.95c1.86.45 2.79 1.86 2.85 3.39H14.3c-.05-1.11-.64-1.87-2.22-1.87-1.5 0-2.4.68-2.4 1.64 0 .84.65 1.39 2.67 1.91s4.18 1.39 4.18 3.91c-.01 1.83-1.38 2.83-3.12 3.16z"/>
                            </svg>
                            FedaPay
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Mobile money pour l'Afrique de l'Ouest (Bénin, Togo, Côte d'Ivoire...).
                            Clés sur <a href="https://fedapay.com" target="_blank" class="text-primary-600 hover:underline">fedapay.com</a>
                        </p>
                        <div class="grid grid-cols-2 gap-3">
                            <AppInput v-model="form.fedapay_public_key" label="Clé publique" placeholder="pk_sandbox_..." />
                            <AppInput v-model="form.fedapay_secret_key" label="Clé secrète"  placeholder="sk_sandbox_..." />
                        </div>
                    </div>

                </div>
            </div>

            <div class="flex justify-end">
                <AppButton v-if="can('action.settings.manage')" type="submit" :loading="submitting" size="lg">
                    Enregistrer les paramètres
                </AppButton>
            </div>
        </form>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { PageHeader, AppButton, AppInput, AppSelect, AppAlert } from '@/Components/UI';
import { useCan } from '@/Composables/useCan';

const { can } = useCan();

interface Setting {
    school_name?:        string;
    school_type?:        string;
    address?:            string;
    phone?:              string;
    email?:              string;
    uai_number?:         string;
    status?:             string;
    paypal_email?:       string;
    kkiapay_public_key?: string;
    kkiapay_private_key?:string;
    kkiapay_secret_key?: string;
    stripe_public_key?:  string;
    stripe_secret_key?:  string;
    fedapay_public_key?: string;
    fedapay_secret_key?: string;
}

const props = defineProps<{
    setting:    Setting | null;
    faviconUrl: string;
    logoUrl:    string;
    isSchool?:  boolean;
}>();

const submitting = ref(false);

const successMsg = ref('');
const errorMsg   = ref('');

const logoPreview    = ref<string | null>(null);
const faviconPreview = ref<string | null>(null);
const logoFile       = ref<File | null>(null);
const faviconFile    = ref<File | null>(null);

const form = ref<Setting>({
    school_name:         props.setting?.school_name         ?? '',
    school_type:         props.setting?.school_type         ?? '',
    address:             props.setting?.address             ?? '',
    phone:               props.setting?.phone               ?? '',
    email:               props.setting?.email               ?? '',
    uai_number:          props.setting?.uai_number          ?? '',
    status:              props.setting?.status              ?? '1',
    paypal_email:        props.setting?.paypal_email        ?? '',
    kkiapay_public_key:  props.setting?.kkiapay_public_key  ?? '',
    kkiapay_private_key: props.setting?.kkiapay_private_key ?? '',
    kkiapay_secret_key:  props.setting?.kkiapay_secret_key  ?? '',
    stripe_public_key:   props.setting?.stripe_public_key   ?? '',
    stripe_secret_key:   props.setting?.stripe_secret_key   ?? '',
    fedapay_public_key:  props.setting?.fedapay_public_key  ?? '',
    fedapay_secret_key:  props.setting?.fedapay_secret_key  ?? '',
});

const onLogoChange = (e: Event) => {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (file) { logoFile.value = file; logoPreview.value = URL.createObjectURL(file); }
};

const onFaviconChange = (e: Event) => {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (file) { faviconFile.value = file; faviconPreview.value = URL.createObjectURL(file); }
};

const submitForm = () => {
    const data = new FormData();
    Object.entries(form.value).forEach(([k, v]) => {
        if (v !== null && v !== undefined) data.append(k, String(v));
    });
    if (logoFile.value)    data.append('logo',    logoFile.value);
    if (faviconFile.value) data.append('favicon', faviconFile.value);

    successMsg.value = '';
    errorMsg.value   = '';
    submitting.value = true;

    router.post('/admin/settings/setting_data', data, {
        preserveScroll: true,
        preserveState:  true,
        onSuccess: () => {
            successMsg.value = 'Paramètres enregistrés avec succès.';
            setTimeout(() => { successMsg.value = ''; }, 4000);
        },
        onError: () => {
            errorMsg.value = 'Une erreur est survenue. Veuillez réessayer.';
        },
        onFinish: () => { submitting.value = false; },
    });
};
</script>
