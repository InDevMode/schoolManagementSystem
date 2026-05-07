<template>
    <GuestLayout>
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2 text-center">Mot de passe oublié</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 text-center mb-6">
            Entrez votre email et nous vous enverrons un lien de réinitialisation.
        </p>

        <AppAlert v-if="flash.success" variant="success" :message="flash.success" class="mb-4" />
        <AppAlert v-if="flash.error"   variant="danger"  :message="flash.error"   class="mb-4" />

        <form @submit.prevent="submit" class="space-y-4">
            <AppInput v-model="form.email" label="Adresse email" type="email" placeholder="votre@email.com" required :error="form.errors.email" />

            <AppButton type="submit" :loading="form.processing" block>
                Envoyer le lien
            </AppButton>
        </form>

        <p class="mt-4 text-center text-sm text-gray-500">
            <a href="/login" class="text-primary-600 hover:text-primary-700 font-medium">← Retour à la connexion</a>
        </p>
    </GuestLayout>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { AppInput, AppButton, AppAlert } from '@/Components/UI';

defineOptions({ layout: null });

const page  = usePage();
const flash = computed(() => page.props.flash as any);

const form = useForm({ email: '' });
const submit = () => form.post('/forgot_password');
</script>
