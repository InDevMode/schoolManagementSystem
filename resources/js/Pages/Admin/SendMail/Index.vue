<template>
    <div class="space-y-6 max-w-2xl mx-auto">
        <PageHeader title="Envoyer un mail" subtitle="Envoyez un email à un ou plusieurs utilisateurs" color="indigo">
            <template #icon>
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </template>
        </PageHeader>

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
                        :error="form.errors.user_ids"
                    />
                </div>

                <!-- Group recipients -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Envoyer à un groupe
                    </label>
                    <div class="flex flex-wrap gap-3">
                        <label
                            v-for="opt in groupOptions"
                            :key="opt.value"
                            class="flex items-center gap-2 cursor-pointer select-none"
                            @click="toggleGroup(opt.value)"
                        >
                            <div
                                :class="[
                                    'w-4 h-4 rounded border-2 flex items-center justify-center flex-shrink-0 transition-colors',
                                    form.message_to.includes(opt.value)
                                        ? 'bg-primary-600 border-primary-600'
                                        : 'border-gray-300 dark:border-gray-500 bg-white dark:bg-gray-800',
                                ]"
                            >
                                <svg
                                    v-if="form.message_to.includes(opt.value)"
                                    class="w-2.5 h-2.5 text-white"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ opt.label }}</span>
                        </label>
                    </div>
                </div>

                <AppInput v-model="form.subject" label="Sujet" required :error="form.errors.subject" />

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Message <span class="text-danger-500 ml-0.5">*</span>
                    </label>
                    <AppRichEditor
                        v-model="form.message"
                        placeholder="Rédigez votre message..."
                        :error="form.errors.message"
                    />
                </div>

                <div class="flex justify-end pt-2">
                    <AppButton v-if="can('action.mail.send')" type="submit" :loading="form.processing">
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
import { PageHeader, AppButton, AppInput, AppRichEditor } from '@/Components/UI';
import { useCan } from '@/Composables/useCan';
import { useToast } from '@/Composables/useToast';
import AppMultiSelect from '@/Components/UI/AppMultiSelect.vue';

const { can } = useCan();
const toast   = useToast();

interface UserItem {
    id: number;
    name: string;
    last_name: string;
    full_name: string;
}

const props = defineProps<{
    users: UserItem[];
}>();

const userOptions = computed(() =>
    props.users.map(u => ({ value: String(u.id), label: u.full_name }))
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
    form.post('/admin/communicate/send_mail', {
        onSuccess: () => {
            form.reset();
            toast.success('Les mails ont été envoyés avec succès.');
        },
        onError: () => toast.error('Erreur lors de l\'envoi. Veuillez réessayer.'),
    });
};

const toggleGroup = (value: string) => {
    const idx = form.message_to.indexOf(value);
    if (idx === -1) form.message_to.push(value);
    else form.message_to.splice(idx, 1);
};
</script>
