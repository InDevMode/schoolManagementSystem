<template>
    <div class="space-y-6 max-w-3xl mx-auto">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Mon Compte</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Gérez vos informations personnelles</p>
        </div>

        <div class="card p-6 space-y-6">
            <!-- Avatar -->
            <div class="flex items-center gap-5">
                <div class="relative">
                    <img
                        :src="previewUrl || profilePictureUrl"
                        :alt="userData.name"
                        class="w-20 h-20 rounded-full object-cover ring-4 ring-primary-100 dark:ring-primary-900/30"
                    />
                </div>
                <div>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ userData.last_name }} {{ userData.name }}</p>
                    <p class="text-sm text-primary-600 dark:text-primary-400">{{ roleLabel }}</p>
                    <label class="mt-2 inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        Changer la photo
                        <input type="file" accept="image/*" class="hidden" @change="onFileChange" />
                    </label>
                </div>
            </div>

            <!-- Form -->
            <form @submit.prevent="submitForm" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <AppInput v-model="form.last_name" label="Prénoms" required :error="form.errors.last_name" />
                    <AppInput v-model="form.name" label="Nom" required :error="form.errors.name" />
                    <AppInput v-model="form.email" label="Email" type="email" required :error="form.errors.email" />
                    <AppInput v-model="form.mobile_number" label="Téléphone" :error="form.errors.mobile_number" />
                    <AppInput v-model="form.address" label="Adresse" :error="form.errors.address" class="sm:col-span-2" />
                </div>

                <div class="flex justify-end pt-2">
                    <AppButton type="submit" :loading="form.processing">Enregistrer les modifications</AppButton>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { AppButton, AppInput } from '@/Components/UI';

interface UserData {
    id: number;
    name: string;
    last_name: string;
    email: string;
    mobile_number?: string;
    address?: string;
    profile_picture?: string;
    user_type: number;
}

const props = defineProps<{
    userData: UserData;
    profilePictureUrl: string;
}>();

const previewUrl = ref<string | null>(null);

const roleLabelMap: Record<number, string> = {
    0: 'Super Admin', 1: 'Administrateur', 2: 'Professeur', 3: 'Apprenant', 4: 'Parent',
};
const roleLabel = computed(() => roleLabelMap[props.userData.user_type] ?? 'Utilisateur');

const updateUrlMap: Record<number, string> = {
    0: '/superadmin/account',
    1: '/admin/account',
    2: '/teacher/account',
    3: '/student/account',
    4: '/parent/account',
};
const updateUrl = computed(() => updateUrlMap[props.userData.user_type] ?? '/admin/account');

const form = useForm({
    name:             props.userData.name,
    last_name:        props.userData.last_name,
    email:            props.userData.email,
    mobile_number:    props.userData.mobile_number ?? '',
    address:          props.userData.address ?? '',
    profile_picture:  null as File | null,
});

const onFileChange = (e: Event) => {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (file) {
        form.profile_picture = file;
        previewUrl.value = URL.createObjectURL(file);
    }
};

const submitForm = () => {
    form.post(updateUrl.value, {
        forceFormData: true,
    });
};
</script>
