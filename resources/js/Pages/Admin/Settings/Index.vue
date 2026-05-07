<template>
    <div class="space-y-6 max-w-3xl mx-auto">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Paramètres</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Configuration de l'établissement</p>
        </div>

        <form @submit.prevent="submitForm" class="space-y-6">
            <!-- Informations générales -->
            <div class="card p-6 space-y-4">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-3">
                    Informations générales
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <AppInput v-model="form.school_name" label="Nom de l'établissement" :error="form.errors.school_name" />
                    <AppInput v-model="form.school_type" label="Type d'établissement" :error="form.errors.school_type" />
                    <AppInput v-model="form.phone" label="Téléphone" :error="form.errors.phone" />
                    <AppInput v-model="form.email" label="Email" type="email" :error="form.errors.email" />
                    <AppInput v-model="form.uai_number" label="Numéro UAI" :error="form.errors.uai_number" />
                    <AppInput v-model="form.address" label="Adresse" :error="form.errors.address" class="sm:col-span-2" />
                </div>
            </div>

            <!-- Logo & Favicon -->
            <div class="card p-6 space-y-4">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-3">
                    Logo & Favicon
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Logo -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Logo</label>
                        <div class="flex items-center gap-4">
                            <img :src="logoPreview || logoUrl" alt="Logo" class="h-16 w-auto object-contain rounded-lg border border-gray-200 dark:border-gray-700 p-1 bg-white dark:bg-gray-800" />
                            <label class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                Changer
                                <input type="file" accept="image/*" class="hidden" @change="onLogoChange" />
                            </label>
                        </div>
                    </div>

                    <!-- Favicon -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Favicon</label>
                        <div class="flex items-center gap-4">
                            <img :src="faviconPreview || faviconUrl" alt="Favicon" class="h-16 w-16 object-contain rounded-lg border border-gray-200 dark:border-gray-700 p-1 bg-white dark:bg-gray-800" />
                            <label class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                Changer
                                <input type="file" accept="image/*" class="hidden" @change="onFaviconChange" />
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <AppButton type="submit" :loading="form.processing">Enregistrer les paramètres</AppButton>
            </div>
        </form>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { AppButton, AppInput } from '@/Components/UI';

interface Setting {
    school_name?: string;
    school_type?: string;
    address?: string;
    phone?: string;
    email?: string;
    uai_number?: string;
    status?: string;
}

const props = defineProps<{
    setting:    Setting | null;
    faviconUrl: string;
    logoUrl:    string;
}>();

const logoPreview    = ref<string | null>(null);
const faviconPreview = ref<string | null>(null);
const logoFile       = ref<File | null>(null);
const faviconFile    = ref<File | null>(null);

const form = useForm({
    school_name: props.setting?.school_name ?? '',
    school_type: props.setting?.school_type ?? '',
    address:     props.setting?.address     ?? '',
    phone:       props.setting?.phone       ?? '',
    email:       props.setting?.email       ?? '',
    uai_number:  props.setting?.uai_number  ?? '',
    status:      props.setting?.status      ?? '1',
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
    Object.entries(form.data()).forEach(([k, v]) => data.append(k, String(v)));
    if (logoFile.value)    data.append('logo',    logoFile.value);
    if (faviconFile.value) data.append('favicon', faviconFile.value);

    form.post('/admin/settings/setting_data', { forceFormData: true });
};
</script>
