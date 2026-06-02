<template>
    <div class="space-y-6 max-w-lg mx-auto">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Changer le mot de passe</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Mettez à jour votre mot de passe de connexion</p>
        </div>

        <div class="card p-6">
            <form @submit.prevent="submitForm" class="space-y-4">
                <!-- Old password -->
                <AppInput
                    v-model="form.old_password"
                    label="Ancien mot de passe"
                    :type="showOld ? 'text' : 'password'"
                    required
                    :error="form.errors.old_password"
                >
                    <template #suffix>
                        <button type="button" @click="showOld = !showOld" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path v-if="showOld" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </template>
                </AppInput>

                <!-- New password -->
                <AppInput
                    v-model="form.new_password"
                    label="Nouveau mot de passe"
                    :type="showNew ? 'text' : 'password'"
                    required
                    :error="form.errors.new_password"
                >
                    <template #suffix>
                        <button type="button" @click="showNew = !showNew" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path v-if="showNew" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </template>
                </AppInput>

                <!-- Confirm password -->
                <AppInput
                    v-model="form.confirm_password"
                    label="Confirmer le mot de passe"
                    :type="showConfirm ? 'text' : 'password'"
                    required
                    :error="confirmError"
                >
                    <template #suffix>
                        <button type="button" @click="showConfirm = !showConfirm" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path v-if="showConfirm" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </template>
                </AppInput>

                <div class="flex justify-end pt-2">
                    <AppButton type="submit" :loading="form.processing">Mettre à jour</AppButton>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { AppButton, AppInput } from '@/Components/UI';
import type { PageProps } from '@/types';

const page = usePage<PageProps>();

const showOld     = ref(false);
const showNew     = ref(false);
const showConfirm = ref(false);

const form = useForm({
    old_password:     '',
    new_password:     '',
    confirm_password: '',
});

const confirmError = computed(() => {
    if (form.confirm_password && form.new_password !== form.confirm_password) {
        return 'Les mots de passe ne correspondent pas.';
    }
    return form.errors.confirm_password ?? '';
});

const userType = computed(() => page.props.auth?.user?.user_type ?? 1);

const updateUrlMap: Record<number, string> = {
    1: '/admin/change_password',
    2: '/teacher/change_password',
    3: '/student/change_password',
    4: '/parent/change_password',
};

const submitForm = () => {
    if (form.new_password !== form.confirm_password) return;
    const url = updateUrlMap[userType.value] ?? '/admin/change_password';
    form.post(url, {
        onSuccess: () => form.reset(),
    });
};
</script>
