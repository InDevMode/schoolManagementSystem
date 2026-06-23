<template>
    <GuestLayout>
        <!-- Icône + Titre -->
        <div class="mb-8">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-5 transition-colors duration-300"
                 :style="iconBoxStyle">
                <svg class="w-7 h-7" style="color: #9189f5;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <h2 class="text-2xl font-bold mb-1 transition-colors duration-300"
                :style="{ color: isDark ? '#f9fafb' : '#111827' }">
                Mot de passe oublié ?
            </h2>
            <p class="text-sm transition-colors duration-300"
               :style="{ color: isDark ? '#9ca3af' : '#6b7280' }">
                Pas de panique. Entrez votre email et nous vous enverrons un lien de réinitialisation.
            </p>
        </div>

        <!-- Alertes -->
        <AppAlert v-if="flash.success" variant="success" :message="flash.success" class="mb-5" />
        <AppAlert v-if="flash.error"   variant="danger"  :message="flash.error"   class="mb-5" />

        <!-- Formulaire -->
        <form @submit.prevent="submit" class="space-y-4">
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
                        placeholder="votre@email.com"
                        required
                        class="w-full pl-10 pr-4 py-3 rounded-xl text-sm outline-none transition-all duration-200 focus:ring-2 focus:ring-primary-500"
                        :class="form.errors.email ? 'ring-2 ring-red-500' : ''"
                        :style="inputStyle"
                    />
                </div>
                <p v-if="form.errors.email" class="mt-1 text-xs text-red-500">{{ form.errors.email }}</p>
            </div>

            <button
                type="submit"
                :disabled="form.processing"
                class="w-full py-3.5 px-4 rounded-xl font-semibold text-sm text-white transition-all duration-200 flex items-center justify-center gap-2 hover:opacity-90 active:scale-[0.98] disabled:opacity-60 disabled:cursor-not-allowed"
                style="background: linear-gradient(135deg, #9189f5, #6660d4); box-shadow: 0 4px 18px rgba(123,116,240,0.35);"
            >
                <svg v-if="form.processing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
                <span>{{ form.processing ? 'Envoi en cours...' : 'Envoyer le lien' }}</span>
            </button>
        </form>

        <!-- Retour connexion -->
        <div class="mt-6 text-center">
            <a href="/login"
               class="inline-flex items-center gap-2 text-sm font-medium transition-colors hover:opacity-80"
               style="color: #7B74F0;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour à la connexion
            </a>
        </div>
    </GuestLayout>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { useDark } from '@vueuse/core';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { AppAlert } from '@/Components/UI';

defineOptions({ layout: null });

const isDark = useDark();
const page   = usePage();
const flash  = computed(() => page.props.flash as any);

const form = useForm({ email: '' });
const submit = () => form.post('/forgot_password');

const inputStyle = computed(() => ({
    background: isDark.value ? 'rgba(109,40,217,0.12)' : '#f5f3ff',
    color:      isDark.value ? '#ede9fe'                : '#1e1b4b',
    border:     isDark.value ? '1px solid rgba(139,92,246,0.25)' : '1px solid #ddd6fe',
}));

const iconBoxStyle = computed(() => ({
    background: isDark.value ? 'rgba(124,58,237,0.15)' : 'rgba(124,58,237,0.08)',
    border:     isDark.value ? '1px solid rgba(124,58,237,0.3)' : '1px solid rgba(124,58,237,0.2)',
}));
</script>
