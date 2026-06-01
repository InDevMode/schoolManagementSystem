<template>
    <GuestLayout>
        <!-- Titre -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold mb-1 transition-colors duration-300"
                :style="{ color: isDark ? '#f9fafb' : '#111827' }">
                Connexion
            </h2>
            <p class="text-sm transition-colors duration-300"
               :style="{ color: isDark ? '#9ca3af' : '#6b7280' }">
                Bienvenue ! Connectez-vous à votre espace.
            </p>
        </div>

        <!-- Alerte erreur -->
        <AppAlert v-if="flash.error" variant="danger" :message="flash.error" class="mb-5" />

        <!-- Boutons OAuth -->
        <div class="space-y-3 mb-6">
            <a href="/auth/google"
               class="flex items-center justify-center gap-3 w-full py-3 px-4 rounded-xl font-medium text-sm transition-all duration-200 hover:scale-[1.02] active:scale-[0.98]"
               :style="oauthBtnStyle">
                <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                </svg>
                <span :style="{ color: isDark ? '#e5e7eb' : '#374151' }">Continuer avec Google</span>
            </a>

            <a href="/auth/facebook"
               class="flex items-center justify-center gap-3 w-full py-3 px-4 rounded-xl font-medium text-sm transition-all duration-200 hover:scale-[1.02] active:scale-[0.98]"
               :style="oauthBtnStyle">
                <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="#1877F2">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
                <span :style="{ color: isDark ? '#e5e7eb' : '#374151' }">Continuer avec Facebook</span>
            </a>
        </div>

        <!-- Séparateur -->
        <div class="flex items-center gap-3 mb-6">
            <div class="flex-1 h-px transition-colors duration-300"
                 :style="{ background: isDark ? '#374151' : '#e5e7eb' }"></div>
            <span class="text-xs font-medium px-2 transition-colors duration-300"
                  :style="{ color: isDark ? '#6b7280' : '#9ca3af' }">
                ou avec votre email
            </span>
            <div class="flex-1 h-px transition-colors duration-300"
                 :style="{ background: isDark ? '#374151' : '#e5e7eb' }"></div>
        </div>

        <!-- Formulaire -->
        <form @submit.prevent="submit" class="space-y-4">
            <!-- Email -->
            <div>
                <label class="block text-xs font-semibold mb-1.5 uppercase tracking-wider transition-colors duration-300"
                       :style="{ color: isDark ? '#9ca3af' : '#6b7280' }">
                    Adresse email
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 transition-colors duration-300"
                             :style="{ color: isDark ? '#6b7280' : '#9ca3af' }"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <input
                        v-model="form.email"
                        type="email"
                        placeholder="exemple@email.com"
                        required
                        class="w-full pl-10 pr-4 py-3 rounded-xl text-sm outline-none transition-all duration-200 focus:ring-2 focus:ring-violet-500"
                        :class="form.errors.email ? 'ring-2 ring-red-500' : ''"
                        :style="inputStyle"
                    />
                </div>
                <p v-if="form.errors.email" class="mt-1 text-xs text-red-500">{{ form.errors.email }}</p>
            </div>

            <!-- Mot de passe -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-xs font-semibold uppercase tracking-wider transition-colors duration-300"
                           :style="{ color: isDark ? '#9ca3af' : '#6b7280' }">
                        Mot de passe
                    </label>
                    <a href="/forgot_password"
                       class="text-xs font-medium transition-colors hover:opacity-80"
                       style="color: #7c3aed;">
                        Mot de passe oublié ?
                    </a>
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 transition-colors duration-300"
                             :style="{ color: isDark ? '#6b7280' : '#9ca3af' }"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
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
            </div>

            <!-- Bouton connexion -->
            <button
                type="submit"
                :disabled="form.processing"
                class="w-full py-3.5 px-4 rounded-xl font-semibold text-sm text-white transition-all duration-200 mt-2 flex items-center justify-center gap-2 hover:opacity-90 active:scale-[0.98] disabled:opacity-60 disabled:cursor-not-allowed"
                style="background: linear-gradient(135deg, #8b5cf6, #6d28d9); box-shadow: 0 4px 18px rgba(124,58,237,0.35);"
            >
                <svg v-if="form.processing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                <span>{{ form.processing ? 'Connexion...' : 'Se connecter' }}</span>
            </button>
        </form>
    </GuestLayout>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { useDark } from '@vueuse/core';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { AppAlert } from '@/Components/UI';
import type { PageProps } from '@/types';

defineOptions({ layout: null });

const isDark = useDark();
const page   = usePage<PageProps>();
const flash  = computed(() => page.props.flash);
const showPassword = ref(false);

const form = useForm({ email: '', password: '' });
const submit = () => {
    form.post('/login', { onFinish: () => form.reset('password') });
};

// Styles adaptatifs
const inputStyle = computed(() => ({
    background: isDark.value ? '#1f2937' : '#ffffff',
    color:      isDark.value ? '#f3f4f6' : '#111827',
    border:     isDark.value ? '1px solid #374151' : '1px solid #d1d5db',
}));

const oauthBtnStyle = computed(() => ({
    background: isDark.value ? '#1f2937' : '#ffffff',
    border:     isDark.value ? '1px solid #374151' : '1px solid #e5e7eb',
    boxShadow:  isDark.value ? 'none' : '0 1px 3px rgba(0,0,0,0.06)',
}));
</script>
