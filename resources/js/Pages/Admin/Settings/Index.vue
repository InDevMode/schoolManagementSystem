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
             class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl
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
                            <div class="w-20 h-14 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 flex items-center justify-center overflow-hidden p-1 flex-shrink-0">
                                <img :src="logoPreview || logoUrl" alt="Logo" class="max-h-full max-w-full object-contain" />
                            </div>
                            <label class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors">
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
                            <div class="w-14 h-14 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 flex items-center justify-center overflow-hidden p-1 flex-shrink-0">
                                <img :src="faviconPreview || faviconUrl" alt="Favicon" class="max-h-full max-w-full object-contain" />
                            </div>
                            <label class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors">
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
                            <span :class="form.paypal_email ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300'"
                                  class="ml-auto inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium">
                                <span :class="form.paypal_email ? 'bg-emerald-500' : 'bg-amber-500'" class="w-1.5 h-1.5 rounded-full"></span>
                                {{ form.paypal_email ? 'Configuré' : 'Non configuré' }}
                            </span>
                        </h3>
                        <AppInput v-model="form.paypal_email" label="Email PayPal" type="email" placeholder="votre@paypal.com" />
                        <p v-if="!form.paypal_email" class="text-xs text-amber-600 dark:text-amber-400 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Renseignez votre email PayPal pour activer ce mode de paiement.
                        </p>
                    </div>

                    <!-- Stripe -->
                    <div class="card p-5 space-y-3">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2.5 flex items-center gap-2">
                            <svg class="w-4 h-4 text-primary-500" viewBox="0 0 24 24" fill="currentColor"><path d="M13.976 9.15c-2.172-.806-3.356-1.426-3.356-2.409 0-.831.683-1.305 1.901-1.305 2.227 0 4.515.858 6.09 1.631l.89-5.494C18.252.975 15.697 0 12.165 0 9.667 0 7.589.654 6.104 1.872 4.56 3.147 3.757 4.992 3.757 7.218c0 4.039 2.467 5.76 6.476 7.219 2.585.92 3.445 1.574 3.445 2.583 0 .98-.84 1.545-2.354 1.545-1.875 0-4.965-.921-6.99-2.109l-.9 5.555C5.175 22.99 8.385 24 11.714 24c2.641 0 4.843-.624 6.328-1.813 1.664-1.305 2.525-3.236 2.525-5.732 0-4.128-2.524-5.851-6.591-7.305z"/></svg>
                            Stripe
                            <span :class="(form.stripe_public_key && form.stripe_secret_key) ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300'"
                                  class="ml-auto inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium">
                                <span :class="(form.stripe_public_key && form.stripe_secret_key) ? 'bg-emerald-500' : 'bg-amber-500'" class="w-1.5 h-1.5 rounded-full"></span>
                                {{ (form.stripe_public_key && form.stripe_secret_key) ? 'Configuré' : 'Non configuré' }}
                            </span>
                        </h3>
                        <div class="grid grid-cols-2 gap-3">
                            <AppInput v-model="form.stripe_public_key" label="Clé publique" placeholder="pk_..." />
                            <AppInput v-model="form.stripe_secret_key" label="Clé secrète"  placeholder="sk_..." />
                        </div>
                        <p v-if="!form.stripe_public_key || !form.stripe_secret_key" class="text-xs text-amber-600 dark:text-amber-400 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Les deux clés (publique et secrète) sont requises pour Stripe.
                        </p>
                    </div>

                    <!-- Kkiapay -->
                    <div class="card p-5 space-y-3">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2.5 flex items-center gap-2">
                            Kkiapay
                            <span :class="(form.kkiapay_public_key && form.kkiapay_secret_key) ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300'"
                                  class="ml-auto inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium">
                                <span :class="(form.kkiapay_public_key && form.kkiapay_secret_key) ? 'bg-emerald-500' : 'bg-amber-500'" class="w-1.5 h-1.5 rounded-full"></span>
                                {{ (form.kkiapay_public_key && form.kkiapay_secret_key) ? 'Configuré' : 'Non configuré' }}
                            </span>
                        </h3>
                        <div class="grid grid-cols-2 gap-3">
                            <AppInput v-model="form.kkiapay_public_key"  label="Clé publique" placeholder="pk_..." />
                            <AppInput v-model="form.kkiapay_private_key" label="Clé privée"   placeholder="sk_..." />
                            <div class="col-span-2">
                                <AppInput v-model="form.kkiapay_secret_key" label="Clé secrète" placeholder="secret_..." />
                            </div>
                        </div>
                        <p v-if="!form.kkiapay_public_key || !form.kkiapay_secret_key" class="text-xs text-amber-600 dark:text-amber-400 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            La clé publique et la clé secrète sont requises pour Kkiapay.
                        </p>
                    </div>

                    <!-- FedaPay -->
                    <div class="card p-5 space-y-3">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2.5 flex items-center gap-2">
                            <svg class="w-4 h-4 text-orange-500" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.41 16.09V20h-2.67v-1.93c-1.71-.36-3.16-1.46-3.27-3.4h1.96c.1 1.05.82 1.87 2.65 1.87 1.96 0 2.4-.98 2.4-1.59 0-.83-.44-1.61-2.67-2.14-2.48-.6-4.18-1.62-4.18-3.67 0-1.72 1.39-2.84 3.11-3.21V4h2.67v1.95c1.86.45 2.79 1.86 2.85 3.39H14.3c-.05-1.11-.64-1.87-2.22-1.87-1.5 0-2.4.68-2.4 1.64 0 .84.65 1.39 2.67 1.91s4.18 1.39 4.18 3.91c-.01 1.83-1.38 2.83-3.12 3.16z"/>
                            </svg>
                            FedaPay
                            <span :class="(form.fedapay_public_key && form.fedapay_secret_key) ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300'"
                                  class="ml-auto inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium">
                                <span :class="(form.fedapay_public_key && form.fedapay_secret_key) ? 'bg-emerald-500' : 'bg-amber-500'" class="w-1.5 h-1.5 rounded-full"></span>
                                {{ (form.fedapay_public_key && form.fedapay_secret_key) ? 'Configuré' : 'Non configuré' }}
                            </span>
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Mobile money pour l'Afrique de l'Ouest (Bénin, Togo, Côte d'Ivoire...).
                            Clés sur <a href="https://fedapay.com" target="_blank" class="text-primary-600 hover:underline">fedapay.com</a>
                        </p>
                        <div class="grid grid-cols-2 gap-3">
                            <AppInput v-model="form.fedapay_public_key" label="Clé publique" placeholder="pk_sandbox_..." />
                            <AppInput v-model="form.fedapay_secret_key" label="Clé secrète"  placeholder="sk_sandbox_..." />
                        </div>
                        <p v-if="!form.fedapay_public_key || !form.fedapay_secret_key" class="text-xs text-amber-600 dark:text-amber-400 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Les deux clés (publique et secrète) sont requises pour FedaPay.
                        </p>
                    </div>

                </div>
            </div>

            <!-- ── Section Background Auth — Super Admin uniquement ── -->
            <div v-if="!isSchool" class="card p-5 space-y-5">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Fond de la page de connexion
                    <span class="ml-auto inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-violet-100 dark:bg-violet-900/40 text-violet-700 dark:text-violet-300">
                        Super Admin
                    </span>
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Personnalisez le panneau gauche de la page de connexion. Changez le fond pour les fêtes (Noël 🎄, Nouvel An 🎉, Pâques 🐣...) ou pour des événements spéciaux.
                </p>

                <!-- Présets thématiques -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Thèmes prédéfinis</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                        <button v-for="preset in bgPresets" :key="preset.id"
                                type="button"
                                @click="applyPreset(preset)"
                                class="group relative flex flex-col items-center gap-2 p-3 rounded-2xl border-2 transition-all duration-200 hover:scale-105"
                                :class="form.auth_bg_value === preset.value
                                    ? 'border-violet-500 shadow-lg shadow-violet-200 dark:shadow-violet-900/30'
                                    : 'border-gray-200 dark:border-gray-700 hover:border-violet-300'">
                            <!-- Miniature du fond -->
                            <div class="w-full h-16 rounded-xl overflow-hidden flex-shrink-0"
                                 :style="{ background: preset.type === 'gradient' ? preset.value : undefined,
                                           backgroundImage: preset.type === 'image' ? `url(${preset.value})` : undefined,
                                           backgroundSize: 'cover', backgroundPosition: 'center' }">
                                <div v-if="preset.type !== 'gradient'" class="w-full h-full"
                                     :style="{ background: preset.overlay ?? 'rgba(0,0,0,0.3)' }"/>
                            </div>
                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300 text-center leading-tight">{{ preset.label }}</span>
                            <!-- Coche active -->
                            <div v-if="form.auth_bg_value === preset.value"
                                 class="absolute top-1.5 right-1.5 w-5 h-5 rounded-full bg-violet-500 flex items-center justify-center">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Type de fond + Upload ou URL selon le type -->
                <div class="space-y-4">
                    <AppSelect v-model="form.auth_bg_type" label="Type de fond"
                               :options="[
                                   { value: 'gradient', label: 'Dégradé CSS' },
                                   { value: 'image',    label: 'Image (upload ou URL)' },
                                   { value: 'video',    label: 'Vidéo (upload ou URL)' },
                               ]"/>

                    <!-- GRADIENT : champ texte CSS -->
                    <div v-if="form.auth_bg_type === 'gradient'">
                        <AppInput v-model="form.auth_bg_value" label="Valeur CSS du dégradé"
                                  placeholder="linear-gradient(145deg, #4f46e5 0%, #7c3aed 50%, #6d28d9 100%)" />
                    </div>

                    <!-- IMAGE : upload fichier OU URL -->
                    <div v-else-if="form.auth_bg_type === 'image'" class="space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <!-- Upload fichier -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    Uploader une image
                                </label>
                                <label class="flex items-center justify-center gap-2 w-full py-3 px-4 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600 cursor-pointer hover:border-violet-400 transition-colors group">
                                    <svg class="w-5 h-5 text-gray-400 group-hover:text-violet-500 transition-colors"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-sm text-gray-500 dark:text-gray-400 group-hover:text-violet-500 transition-colors">
                                        {{ bgImageFile ? bgImageFile.name : 'Choisir une image...' }}
                                    </span>
                                    <input type="file" accept="image/jpeg,image/png,image/webp,image/gif" class="hidden"
                                           @change="onBgImageChange"/>
                                </label>
                                <p class="mt-1 text-xs text-gray-400">JPG, PNG, WebP — max 5 Mo</p>
                            </div>
                            <!-- OU URL -->
                            <div>
                                <AppInput v-model="form.auth_bg_value" label="Ou coller une URL"
                                          placeholder="https://exemple.com/fond.jpg"/>
                                <p class="mt-1 text-xs text-gray-400">L'upload prend la priorité sur l'URL.</p>
                            </div>
                        </div>
                        <!-- Aperçu miniature de l'image choisie -->
                        <div v-if="bgImagePreview || form.auth_bg_value"
                             class="relative w-full h-28 rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700">
                            <img :src="bgImagePreview || form.auth_bg_value" alt="Aperçu"
                                 class="w-full h-full object-cover"/>
                            <button v-if="bgImageFile" type="button"
                                    @click="bgImageFile = null; bgImagePreview = null"
                                    class="absolute top-2 right-2 w-6 h-6 rounded-full bg-red-500 text-white flex items-center justify-center hover:bg-red-600">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        <AppInput v-model="form.auth_bg_overlay" label="Overlay (transparence)"
                                  placeholder="rgba(0,0,0,0.4)" />
                    </div>

                    <!-- VIDÉO : upload fichier OU URL -->
                    <div v-else-if="form.auth_bg_type === 'video'" class="space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <!-- Upload fichier vidéo -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    Uploader une vidéo
                                </label>
                                <label class="flex items-center justify-center gap-2 w-full py-3 px-4 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600 cursor-pointer hover:border-violet-400 transition-colors group">
                                    <svg class="w-5 h-5 text-gray-400 group-hover:text-violet-500 transition-colors"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15 10l4.553-2.276A1 1 0 0121 8.723v6.554a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-sm text-gray-500 dark:text-gray-400 group-hover:text-violet-500 transition-colors">
                                        {{ bgVideoFile ? bgVideoFile.name : 'Choisir une vidéo...' }}
                                    </span>
                                    <input type="file" accept="video/mp4,video/webm,video/ogg" class="hidden"
                                           @change="onBgVideoChange"/>
                                </label>
                                <p class="mt-1 text-xs text-gray-400">MP4, WebM — max 50 Mo. Fond en boucle silencieux.</p>
                            </div>
                            <!-- OU URL -->
                            <div>
                                <AppInput v-model="form.auth_bg_value" label="Ou coller une URL vidéo"
                                          placeholder="https://exemple.com/fond.mp4"/>
                                <p class="mt-1 text-xs text-gray-400">L'upload prend la priorité sur l'URL.</p>
                            </div>
                        </div>
                        <!-- Aperçu vidéo -->
                        <div v-if="bgVideoPreview || form.auth_bg_value"
                             class="relative w-full h-28 rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 bg-black">
                            <video :src="bgVideoPreview || form.auth_bg_value"
                                   class="w-full h-full object-cover" muted loop autoplay playsinline/>
                            <button v-if="bgVideoFile" type="button"
                                    @click="bgVideoFile = null; bgVideoPreview = null"
                                    class="absolute top-2 right-2 w-6 h-6 rounded-full bg-red-500 text-white flex items-center justify-center hover:bg-red-600">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        <AppInput v-model="form.auth_bg_overlay" label="Overlay (transparence)"
                                  placeholder="rgba(0,0,0,0.4)" />
                    </div>
                </div>

                <!-- Étiquette saisonnière — commun à tous les types -->
                <div>
                    <AppInput v-model="form.auth_bg_label" label="Étiquette saisonnière (optionnel)"
                              placeholder="🎄 Joyeux Noël à tous !" />
                    <p class="mt-1 text-xs text-gray-400">Affiché en bandeau sur le panneau gauche de la page de connexion.</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Aperçu live</label>
                    <div class="relative w-full h-40 rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-700">
                        <!-- Fond gradient -->
                        <div v-if="form.auth_bg_type === 'gradient' || !form.auth_bg_value"
                             class="absolute inset-0"
                             :style="{ background: form.auth_bg_value || defaultGradient }"/>
                        <!-- Fond image -->
                        <div v-else-if="form.auth_bg_type === 'image'"
                             class="absolute inset-0 bg-cover bg-center"
                             :style="{ backgroundImage: `url(${form.auth_bg_value})` }">
                            <div class="absolute inset-0" :style="{ background: form.auth_bg_overlay || 'rgba(0,0,0,0.35)' }"/>
                        </div>
                        <!-- Décors blancs -->
                        <div class="absolute top-4 left-4 w-20 h-20 rounded-full opacity-15 blur-xl" style="background: white"/>
                        <div class="absolute bottom-3 right-4 w-24 h-24 rounded-full opacity-10 blur-xl" style="background: white"/>
                        <!-- Étiquette -->
                        <div v-if="form.auth_bg_label"
                             class="absolute top-3 left-1/2 -translate-x-1/2 px-3 py-1 rounded-full text-xs font-semibold text-white"
                             style="background: rgba(255,255,255,0.18); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.35);">
                            {{ form.auth_bg_label }}
                        </div>
                        <!-- Texte sample -->
                        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 text-white text-center">
                            <div class="text-sm font-bold drop-shadow">Gérez votre école</div>
                            <div class="text-xs opacity-75 drop-shadow">intelligemment</div>
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
    // Fond Auth
    auth_bg_type?:       string;
    auth_bg_value?:      string;
    auth_bg_label?:      string;
    auth_bg_overlay?:    string;
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

// Fichiers background auth
const bgImageFile    = ref<File | null>(null);
const bgImagePreview = ref<string | null>(null);
const bgVideoFile    = ref<File | null>(null);
const bgVideoPreview = ref<string | null>(null);

const defaultGradient = 'linear-gradient(145deg, #0d3b3e 0%, #0e4d52 45%, #0a3336 100%)';

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
    // Fond Auth
    auth_bg_type:        props.setting?.auth_bg_type    ?? 'gradient',
    auth_bg_value:       props.setting?.auth_bg_value   ?? 'linear-gradient(145deg, #5b21b6 0%, #7c3aed 50%, #6d28d9 100%)',
    auth_bg_label:       props.setting?.auth_bg_label   ?? '',
    auth_bg_overlay:     props.setting?.auth_bg_overlay ?? 'rgba(0,0,0,0.35)',
});

// ── Presets thématiques ────────────────────────────────────────────────────
interface BgPreset {
    id:      string;
    label:   string;
    type:    'gradient' | 'image' | 'video';
    value:   string;
    overlay?: string;
}

const bgPresets: BgPreset[] = [
    {
        id: 'default',
        label: '🎓 Par défaut',
        type: 'gradient',
        value: 'linear-gradient(145deg, #5b21b6 0%, #7c3aed 50%, #6d28d9 100%)',
    },
    {
        id: 'noel',
        label: '🎄 Noël',
        type: 'gradient',
        value: 'linear-gradient(145deg, #1a472a 0%, #2d6a4f 35%, #c0392b 100%)',
    },
    {
        id: 'nouvel_an',
        label: '🎉 Nouvel An',
        type: 'gradient',
        value: 'linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%)',
    },
    {
        id: 'paques',
        label: '🐣 Pâques',
        type: 'gradient',
        value: 'linear-gradient(145deg, #f9ca24 0%, #f0932b 40%, #6ab04c 100%)',
    },
    {
        id: 'rentrée',
        label: '📚 Rentrée',
        type: 'gradient',
        value: 'linear-gradient(145deg, #2980b9 0%, #6dd5fa 50%, #ffffff 100%)',
    },
    {
        id: 'ramadan',
        label: '🌙 Ramadan',
        type: 'gradient',
        value: 'linear-gradient(145deg, #1a1a2e 0%, #16213e 45%, #0f3460 100%)',
    },
    {
        id: 'tabaski',
        label: '🐏 Aïd',
        type: 'gradient',
        value: 'linear-gradient(135deg, #134e5e 0%, #71b280 100%)',
    },
    {
        id: 'fete_nat',
        label: '🇧🇯 Fête Nat.',
        type: 'gradient',
        value: 'linear-gradient(145deg, #007a3d 0%, #fcd116 50%, #ce1126 100%)',
    },
    {
        id: 'ocean',
        label: '🌊 Océan',
        type: 'gradient',
        value: 'linear-gradient(145deg, #0575e6 0%, #021b79 100%)',
    },
    {
        id: 'sunset',
        label: '🌅 Coucher de soleil',
        type: 'gradient',
        value: 'linear-gradient(145deg, #f83600 0%, #f9d423 100%)',
    },
];

const applyPreset = (preset: BgPreset) => {
    form.value.auth_bg_type    = preset.type;
    form.value.auth_bg_value   = preset.value;
    form.value.auth_bg_overlay = preset.overlay ?? 'rgba(0,0,0,0.35)';
};

const onLogoChange = (e: Event) => {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (file) { logoFile.value = file; logoPreview.value = URL.createObjectURL(file); }
};

const onFaviconChange = (e: Event) => {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (file) { faviconFile.value = file; faviconPreview.value = URL.createObjectURL(file); }
};

const onBgImageChange = (e: Event) => {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (file) {
        bgImageFile.value    = file;
        bgImagePreview.value = URL.createObjectURL(file);
        // Effacer l'URL manuelle pour que l'upload prenne la priorité visuellement
        form.value.auth_bg_value = '';
    }
};

const onBgVideoChange = (e: Event) => {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (file) {
        bgVideoFile.value    = file;
        bgVideoPreview.value = URL.createObjectURL(file);
        form.value.auth_bg_value = '';
    }
};

const submitForm = () => {
    const data = new FormData();
    Object.entries(form.value).forEach(([k, v]) => {
        if (v !== null && v !== undefined) data.append(k, String(v));
    });
    if (logoFile.value)    data.append('logo',    logoFile.value);
    if (faviconFile.value) data.append('favicon', faviconFile.value);
    if (bgImageFile.value) data.append('auth_bg_image', bgImageFile.value);
    if (bgVideoFile.value) data.append('auth_bg_video', bgVideoFile.value);

    successMsg.value = '';
    errorMsg.value   = '';
    submitting.value = true;

    const url = props.isSchool ? '/admin/settings/setting_data' : '/superadmin/config/settings/save';
    router.post(url, data, {
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
