<template>
    <div class="space-y-6 max-w-2xl mx-auto">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Envoyer un mail</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Envoyez un email à un ou plusieurs utilisateurs</p>
        </div>

        <div class="card p-6">
            <form @submit.prevent="submitForm" class="space-y-4">
                <!-- Recipients -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Destinataires individuels
                    </label>
                    <AppMultiSelect
                        v-model="form.user_ids"
                        :options="userOptions"
                        placeholder="Sélectionner des utilisateurs"
                        :error="form.errors.user_id"
                    />
                </div>

                <!-- Group recipients -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Envoyer à un groupe
                    </label>
                    <div class="flex flex-wrap gap-3">
                        <label v-for="opt in groupOptions" :key="opt.value" class="flex items-center gap-2 cursor-pointer">
                            <input
                                type="checkbox"
                                :value="opt.value"
                                v-model="form.message_to"
                                class="w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                            />
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ opt.label }}</span>
                        </label>
                    </div>
                </div>

                <AppInput v-model="form.subject" label="Sujet" required :error="form.errors.subject" />

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Message <span class="text-danger-500 ml-0.5">*</span>
                    </label>
                    <textarea
                        v-model="form.message"
                        rows="6"
                        required
                        placeholder="Rédigez votre message..."
                        class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                    />
                    <p v-if="form.errors.message" class="mt-1 text-xs text-danger-600">{{ form.errors.message }}</p>
                </div>

                <div class="flex justify-end pt-2">
                    <AppButton type="submit" :loading="form.processing">
                        <template #icon>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>
                        </template>
                        Envoyer
                    </AppButton>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { AppButton, AppInput } from '@/Components/UI';
import AppMultiSelect from '@/Components/UI/AppMultiSelect.vue';

interface UserItem {
    id: number;
    name: string;
    last_name: string;
    email: string;
}

const props = defineProps<{
    users: UserItem[];
}>();

const userOptions = computed(() =>
    props.users.map(u => ({ value: String(u.id), label: `${u.last_name} ${u.name} (${u.email})` }))
);

const groupOptions = [
    { value: '2', label: 'Tous les professeurs' },
    { value: '3', label: 'Tous les apprenants' },
    { value: '4', label: 'Tous les parents' },
];

const form = useForm({
    user_ids:   [] as string[],
    message_to: [] as string[],
    subject:    '',
    message:    '',
});

const submitForm = () => {
    const data = new FormData();
    data.append('subject', form.subject);
    data.append('message', form.message);
    form.user_ids.forEach(id => data.append('user_id[]', id));
    form.message_to.forEach(t => data.append('message_to[]', t));

    form.post('/admin/communicate/send_mail', {
        onSuccess: () => form.reset(),
    });
};
</script>
