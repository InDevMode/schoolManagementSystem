<template>
    <GuestLayout>
        <!-- Icône + Titre -->
        <div class="mb-8">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-5 transition-colors duration-300"
                 :style="iconBoxStyle">
                <svg class="w-7 h-7" style="color: #2563eb;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
            </div>
            <h2 class="text-2xl font-bold mb-1 transition-colors duration-300"
                :style="{ color: isDark ? '#f9fafb' : '#111827' }">
                Nouveau mot de passe
            </h2>
            <p class="text-sm transition-colors duration-300"
               :style="{ color: isDark ? '#9ca3af' : '#6b7280' }">
                Choisissez un mot de passe fort d'au moins 6 caractères.
            </p>
        </div>

        <!-- Alertes -->
        <AppAlert v-if="flash.error" variant="danger" :message="flash.error" class="mb-5" />

        <!-- Formulaire -->
        <form @submit.prevent="submit" class="space-y-4">
            <!-- Nouveau mot de passe -->
            <div>
                <label class="block text-xs font-semibold mb-1.5 uppercase tracking-wider transition-colors duration-300"
                       :style="{ color: isDark ? '#9ca3af' : '#6b7280' }">
                    Nouveau mot de passe
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 transition-colors duration-300"
                             :style="{ color: isDark ? '#6b7280' : '#9ca3af' }"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <input
                        v-model="form.password"
                        :type="showPassword ? 'text' : 'password'"
                        placeholder="••••••••••"
                        required
                        class="w-full pl-10 pr-12 py-3 rounded-xl text-sm outline-none transition-all duration-200 focus:ring-2 focus:ring-violet-500"
                        :class="form.errors.password ? 'ring-2 ring-red-500' : ''"
                        :style="inputStyle"
                    />
                    <button type="button"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center transition-colors"
                            :style="{ color: isDark ? '#6b7280' : '#9ca3af' }"
                            @click="showPassword = !showPassword">
                        <svg v-if="showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
                <p v-if="form.errors.password" class="mt-1 text-xs text-red-500">{{ form.errors.password }}</p>

                <!-- Indicateur de force -->
                <div class="mt-2 flex gap-1">
                    <div v-for="i in 4" :key="i"
                         class="h-1 flex-1 rounded-full transition-all duration-300"
                         :style="{ background: passwordStrength >= i ? strengthColor : (isDark ? '#374151' : '#e5e7eb') }">
                    </div>
                </div>
                <p v-if="form.password" class="mt-1 text-xs font-medium" :style="{ color: strengthColor }">
                    {{ strengthLabel }}
                </p>
            </div>

            <!-- Confirmer mot de passe -->
            <div>
                <label class="block text-xs font-semibold mb-1.5 uppercase tracking-wider transition-colors duration-300"
                       :style="{ color: isDark ? '#9ca3af' : '#6b7280' }">
                    Confirmer le mot de passe
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 transition-colors duration-300"
                             :style="{ color: isDark ? '#6b7280' : '#9ca3af' }"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <input
                        v-model="form.confPassword"
                        :type="showConfirm ? 'text' : 'password'"
                        placeholder="••••••••••"
                        required
                        class="w-full pl-10 pr-12 py-3 rounded-xl text-sm outline-none transition-all duration-200 focus:ring-2 focus:ring-violet-500"
                        :class="confirmRingClass"
                        :style="inputStyle"
                    />
                    <button type="button"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center transition-colors"
                            :style="{ color: isDark ? '#6b7280' : '#9ca3af' }"
                            @click="showConfirm = !showConfirm">
                        <svg v-if="showConfirm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
                <p v-if="form.errors.confPassword" class="mt-1 text-xs text-red-500">{{ form.errors.confPassword }}</p>
                <p v-else-if="form.confPassword && form.confPassword !== form.password" class="mt-1 text-xs text-red-500">
                    Les mots de passe ne correspondent pas.
                </p>
                <p v-else-if="form.confPassword && form.confPassword === form.password" class="mt-1 text-xs text-green-500">
                    ✓ Les mots de passe correspondent.
                </p>
            </div>

            <!-- Bouton -->
            <button
                type="submit"
                :disabled="form.processing || (!!form.confPassword && form.confPassword !== form.password)"
                class="w-full py-3.5 px-4 rounded-xl font-semibold text-sm text-white transition-all duration-200 flex items-center justify-center gap-2 hover:opacity-90 active:scale-[0.98] disabled:opacity-60 disabled:cursor-not-allowed"
                style="background: #7B74F0; box-shadow: 0 4px 20px rgba(123,116,240,0.45);"
            >
                <svg v-if="form.processing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ form.processing ? 'Réinitialisation...' : 'Réinitialiser le mot de passe' }}</span>
            </button>
        </form>

        <!-- Retour connexion -->
        <div class="mt-6 text-center">
            <a href="/login"
               class="inline-flex items-center gap-2 text-sm font-medium transition-colors hover:opacity-80"
               style="color: #2563eb;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour à la connexion
            </a>
        </div>
    </GuestLayout>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { useDark } from '@vueuse/core';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { AppAlert } from '@/Components/UI';

defineOptions({ layout: null });

const isDark = useDark();
const props  = defineProps<{ token: string }>();
const page   = usePage();
const flash  = computed(() => page.props.flash as any);

const showPassword = ref(false);
const showConfirm  = ref(false);

const form = useForm({ password: '', confPassword: '' });

// Force du mot de passe
const passwordStrength = computed(() => {
    const p = form.password;
    if (!p) return 0;
    let s = 0;
    if (p.length >= 6)  s++;
    if (p.length >= 10) s++;
    if (/[A-Z]/.test(p) && /[0-9]/.test(p)) s++;
    if (/[^A-Za-z0-9]/.test(p)) s++;
    return s;
});
const strengthColor = computed(() => ['', '#ef4444', '#f59e0b', '#3b82f6', '#22c55e'][passwordStrength.value] || '#ef4444');
const strengthLabel = computed(() => ['', 'Très faible', 'Faible', 'Moyen', 'Fort'][passwordStrength.value] || '');

const confirmRingClass = computed(() => {
    if (form.errors.confPassword) return 'ring-2 ring-red-500';
    if (form.confPassword && form.confPassword !== form.password) return 'ring-2 ring-red-500';
    if (form.confPassword && form.confPassword === form.password) return 'ring-2 ring-green-500';
    return '';
});

const inputStyle = computed(() => ({
    background: isDark.value ? 'rgba(123,116,240,0.35)' : '#f5f3ff',
    color:      isDark.value ? '#f5f3ff'                : '#3b2fa0',
    border:     isDark.value ? '1px solid rgba(123,116,240,0.25)' : '1px solid #ddd6fe',
}));

const iconBoxStyle = computed(() => ({
    background: isDark.value ? 'rgba(123,116,240,0.25)' : 'rgba(123,116,240,0.25)',
    border:     isDark.value ? '1px solid rgba(123,116,240,0.25)' : '1px solid rgba(123,116,240,0.25)',
}));

const submit = () => form.post(`/reset/${props.token}`);
</script>
