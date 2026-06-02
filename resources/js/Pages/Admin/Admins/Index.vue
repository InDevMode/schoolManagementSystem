<template>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Administrateurs</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ admins.total }} administrateur(s)</p>
            </div>
            <AppButton @click="openCreate">
                <template #icon>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                </template>
                Nouvel administrateur
            </AppButton>
        </div>

        <!-- DataTable -->
        <DataTable
            ref="tableRef"
            :columns="columns"
            :rows="tableRows"
            row-key="id"
            export-filename="administrateurs"
            :show-reset-password="true"
            @delete="handleDelete"
            @reset-password="handleResetPassword"
        >
            <template #cell-user="{ row }">
                <div class="flex items-center gap-3">
                    <UserAvatar :src="row.profile_url as string" :name="row.name as string" :last-name="row.last_name as string" size="sm" />
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ row.last_name }} {{ row.name }}</p>
                        <p class="text-xs text-gray-500">{{ row.email }}</p>
                    </div>
                </div>
            </template>
            <template #cell-status="{ row }">
                <AppBadge :variant="row.status == 1 ? 'success' : 'danger'" dot>
                    {{ row.status == 1 ? 'Actif' : 'Inactif' }}
                </AppBadge>
            </template>
            <template #cell-created_at="{ row }">
                <span class="text-xs text-gray-500">{{ formatDate(row.created_at as string) }}</span>
            </template>
            <template #actions="{ row }">
                <div class="flex items-center justify-end gap-1">
                    <button class="p-1.5 rounded-lg text-gray-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors" title="Modifier" @click="openEdit(row as any)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    </button>
                    <button class="p-1.5 rounded-lg text-gray-400 hover:text-warning-600 hover:bg-warning-50 dark:hover:bg-warning-900/20 transition-colors" title="Réinitialiser MDP" @click="handleResetPassword([row.id as number])">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" /></svg>
                    </button>
                    <button class="p-1.5 rounded-lg text-gray-400 hover:text-danger-600 hover:bg-danger-50 dark:hover:bg-danger-900/20 transition-colors" title="Supprimer" @click="tableRef?.confirmDelete(row.id as number, `${row.last_name} ${row.name}`)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>
                </div>
            </template>
        </DataTable>

        <!-- Modal Créer / Modifier -->
        <AppModal v-model="showForm" :title="editTarget ? 'Modifier l\'administrateur' : 'Nouvel administrateur'" size="lg">
            <form :id="formId" @submit.prevent="submitForm" class="space-y-4">
                <div class="flex items-center gap-4">
                    <UserAvatar :src="previewUrl" :name="form.name" :last-name="form.last_name" size="xl" />
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Photo de profil</label>
                        <input type="file" accept="image/*" class="text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" @change="onFileChange" />
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <AppInput v-model="form.last_name" label="Prénoms" required :error="form.errors.last_name" />
                    <AppInput v-model="form.name" label="Nom" required :error="form.errors.name" />
                    <AppInput v-model="form.email" label="Email" type="email" required :error="form.errors.email" />
                    <AppInput v-model="form.password" :label="editTarget ? 'Nouveau mot de passe (optionnel)' : 'Mot de passe'" :type="showPwd ? 'text' : 'password'" :required="!editTarget" :error="form.errors.password">
                        <template #suffix>
                            <button type="button" @click="showPwd = !showPwd" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path v-if="showPwd" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </template>
                    </AppInput>
                    <AppSelect v-model="form.status" label="Statut" :options="statusOptions" required :error="form.errors.status" />
                </div>
            </form>
            <template #footer>
                <AppButton variant="ghost" @click="showForm = false">Annuler</AppButton>
                <AppButton type="submit" :form="formId" :loading="submitting">
                    {{ editTarget ? 'Enregistrer' : 'Créer' }}
                </AppButton>
            </template>
        </AppModal>
    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { AppButton, AppInput, AppSelect, AppModal, AppBadge, DataTable } from '@/Components/UI';
import UserAvatar from '@/Components/Shared/UserAvatar.vue';
import { useToast } from '@/Composables/useToast';
import { useForm } from '@inertiajs/vue3';

interface Admin {
    id: number; name: string; last_name: string; email: string;
    status: number; profile_picture: string | null; created_at: string;
}

const props = defineProps<{
    admins: { data: Admin[]; total: number; from: number; to: number; links: any[] };
}>();

const toast    = useToast();
const tableRef = ref<InstanceType<typeof DataTable> | null>(null);
const formId   = 'admin-form';
const showForm = ref(false);
const editTarget = ref<Admin | null>(null);
const submitting = ref(false);
const showPwd    = ref(false);
const previewUrl = ref<string | null>(null);
const picFile    = ref<File | null>(null);

const statusOptions = [{ value: '1', label: 'Actif' }, { value: '0', label: 'Inactif' }];

const columns = [
    { key: 'user',       label: 'Administrateur' },
    { key: 'status',     label: 'Statut',   sortable: false },
    { key: 'created_at', label: 'Créé le' },
];

const tableRows = computed(() =>
    props.admins.data.map(a => ({
        ...a,
        profile_url: a.profile_picture ? `/upload/profile/${a.profile_picture}` : null,
    }))
);

const form = useForm({ name: '', last_name: '', email: '', password: '', status: '1' });

const openCreate = () => {
    editTarget.value = null; previewUrl.value = null; picFile.value = null;
    showPwd.value = false; form.reset(); form.status = '1';
    showForm.value = true;
};

const openEdit = (admin: Admin) => {
    editTarget.value = admin;
    previewUrl.value = admin.profile_picture ? `/upload/profile/${admin.profile_picture}` : null;
    picFile.value = null; showPwd.value = false;
    form.name = admin.name; form.last_name = admin.last_name;
    form.email = admin.email; form.password = ''; form.status = String(admin.status);
    showForm.value = true;
};

const onFileChange = (e: Event) => {
    const f = (e.target as HTMLInputElement).files?.[0];
    if (f) { picFile.value = f; previewUrl.value = URL.createObjectURL(f); }
};

const submitForm = () => {
    const data = new FormData();
    data.append('name', form.name); data.append('last_name', form.last_name);
    data.append('email', form.email); data.append('status', form.status);
    if (form.password) data.append('password', form.password);
    if (picFile.value) data.append('profile_picture', picFile.value);
    submitting.value = true;
    const url = editTarget.value ? `/admin/admin/edit/${editTarget.value.id}` : '/admin/admin/add';
    router.post(url, data, {
        onSuccess: () => { showForm.value = false; toast.success(editTarget.value ? 'Administrateur modifié.' : 'Administrateur créé.'); },
        onError:   () => toast.error('Erreur lors de l\'enregistrement.'),
        onFinish:  () => { submitting.value = false; },
    });
};

const handleDelete = (ids: (string | number)[]) => {
    ids.forEach(id => {
        router.get(`/admin/admin/delete/${id}`, {}, {
            onSuccess: () => toast.success('Administrateur supprimé.'),
            onError:   () => toast.error('Erreur lors de la suppression.'),
        });
    });
};

const handleResetPassword = async (ids: (string | number)[]) => {
    // Confirmation via DataTable si appel unique depuis le bouton ligne
    if (ids.length === 1) {
        tableRef.value?.confirmDelete; // déjà géré par DataTable pour multi
    }
    try {
        const csrf = (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? '';
        const res  = await fetch('/admin/users/reset-password', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
            body: JSON.stringify({ ids }),
        });
        const data = await res.json();
        if (data.success) toast.success(data.message);
        else toast.error(data.message);
    } catch {
        toast.error('Erreur lors de la réinitialisation.');
    }
};

const formatDate = (d: string) => {
    if (!d) return '—';
    try { return new Date(d).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' }); }
    catch { return d; }
};
</script>
