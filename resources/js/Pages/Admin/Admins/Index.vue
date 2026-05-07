<template>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Administrateurs</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ admins.total }} administrateur(s) enregistré(s)</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="/admin/admin/export" class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                    Exporter
                </a>
                <AppButton @click="openCreate">
                    <template #icon>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    </template>
                    Nouvel administrateur
                </AppButton>
            </div>
        </div>

        <!-- Filtres -->
        <div class="card p-4">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <AppInput v-model="filters.name" placeholder="Rechercher par nom..." @input="applyFilters" />
                <AppInput v-model="filters.email" placeholder="Rechercher par email..." @input="applyFilters" />
                <AppSelect v-model="filters.status" :options="statusOptions" placeholder="Tous les statuts" @change="applyFilters" />
            </div>
        </div>

        <!-- Table -->
        <div class="card overflow-hidden">
            <AppTable :columns="columns" :rows="tableRows" :pagination="admins" row-key="id">
                <template #cell-user="{ row }">
                    <div class="flex items-center gap-3">
                        <UserAvatar :src="row.profile_url" :name="row.name" :last-name="row.last_name" size="sm" />
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ row.last_name }} {{ row.name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ row.email }}</p>
                        </div>
                    </div>
                </template>
                <template #cell-status="{ row }">
                    <AppBadge :variant="row.status == 1 ? 'success' : 'danger'" dot>
                        {{ row.status == 1 ? 'Actif' : 'Inactif' }}
                    </AppBadge>
                </template>
                <template #cell-created_at="{ row }">
                    <span class="text-xs text-gray-500">{{ formatDate(row.created_at) }}</span>
                </template>
                <template #actions="{ row }">
                    <div class="flex items-center justify-end gap-1">
                        <button class="p-1.5 rounded-lg text-gray-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors" title="Modifier" @click="openEdit(row)">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                        </button>
                        <button class="p-1.5 rounded-lg text-gray-400 hover:text-danger-600 hover:bg-danger-50 dark:hover:bg-danger-900/20 transition-colors" title="Supprimer" @click="openDelete(row)">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </div>
                </template>
            </AppTable>
        </div>

        <!-- Modal Créer / Modifier -->
        <AppModal v-model="showForm" :title="editTarget ? 'Modifier l\'administrateur' : 'Nouvel administrateur'" size="lg">
            <form :id="formId" @submit.prevent="submitForm" class="space-y-4">
                <!-- Photo -->
                <div class="flex items-center gap-4">
                    <UserAvatar :src="previewUrl" :name="form.name" :last-name="form.last_name" size="xl" />
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Photo de profil</label>
                        <input type="file" accept="image/*" class="text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 dark:file:bg-primary-900/30 dark:file:text-primary-300" @change="onFileChange" />
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
                <AppButton type="submit" :form="formId" :loading="form.processing">
                    {{ editTarget ? 'Enregistrer' : 'Créer' }}
                </AppButton>
            </template>
        </AppModal>

        <!-- Modal Supprimer -->
        <AppModal v-model="showDelete" title="Supprimer l'administrateur" size="sm" persistent>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Voulez-vous vraiment supprimer <strong class="text-gray-900 dark:text-white">{{ deleteTarget?.last_name }} {{ deleteTarget?.name }}</strong> ?
                Cette action est irréversible.
            </p>
            <template #footer>
                <AppButton variant="ghost" @click="showDelete = false">Annuler</AppButton>
                <AppButton variant="danger" :loading="deleting" @click="confirmDelete">Supprimer</AppButton>
            </template>
        </AppModal>
    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { AppButton, AppInput, AppSelect, AppModal, AppTable, AppBadge } from '@/Components/UI';
import UserAvatar from '@/Components/Shared/UserAvatar.vue';

interface Admin {
    id: number;
    name: string;
    last_name: string;
    email: string;
    status: number;
    profile_picture: string | null;
    created_at: string;
}

const props = defineProps<{
    admins: {
        data: Admin[];
        total: number;
        from: number;
        to: number;
        links: { url: string | null; label: string; active: boolean }[];
    };
}>();

const formId = 'admin-form';
const showForm   = ref(false);
const showDelete = ref(false);
const editTarget   = ref<Admin | null>(null);
const deleteTarget = ref<Admin | null>(null);
const deleting     = ref(false);
const showPwd      = ref(false);
const previewUrl   = ref<string | null>(null);
const picFile      = ref<File | null>(null);

const filters = ref({ name: '', email: '', status: '' });

const statusOptions = [
    { value: '1', label: 'Actif' },
    { value: '0', label: 'Inactif' },
];

const columns = [
    { key: 'user',       label: 'Administrateur' },
    { key: 'status',     label: 'Statut' },
    { key: 'created_at', label: 'Créé le' },
];

const tableRows = computed(() =>
    props.admins.data.map(a => ({
        ...a,
        profile_url: a.profile_picture ? `/upload/profile/${a.profile_picture}` : null,
    }))
);

const form = useForm({
    name: '', last_name: '', email: '', password: '', status: '1',
});

const openCreate = () => {
    editTarget.value = null;
    previewUrl.value = null;
    picFile.value = null;
    showPwd.value = false;
    form.reset();
    form.status = '1';
    showForm.value = true;
};

const openEdit = (admin: Admin) => {
    editTarget.value = admin;
    previewUrl.value = admin.profile_picture ? `/upload/profile/${admin.profile_picture}` : null;
    picFile.value = null;
    showPwd.value = false;
    form.name      = admin.name;
    form.last_name = admin.last_name;
    form.email     = admin.email;
    form.password  = '';
    form.status    = String(admin.status);
    showForm.value = true;
};

const openDelete = (admin: Admin) => {
    deleteTarget.value = admin;
    showDelete.value = true;
};

const onFileChange = (e: Event) => {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (file) {
        picFile.value = file;
        previewUrl.value = URL.createObjectURL(file);
    }
};

const submitForm = () => {
    const data = new FormData();
    data.append('name',      form.name);
    data.append('last_name', form.last_name);
    data.append('email',     form.email);
    data.append('status',    form.status);
    if (form.password) data.append('password', form.password);
    if (picFile.value) data.append('profile_picture', picFile.value);

    if (editTarget.value) {
        data.append('_method', 'POST');
        router.post(`/admin/admin/edit/${editTarget.value.id}`, data, {
            onSuccess: () => { showForm.value = false; },
        });
    } else {
        router.post('/admin/admin/add', data, {
            onSuccess: () => { showForm.value = false; },
        });
    }
};

const confirmDelete = () => {
    if (!deleteTarget.value) return;
    deleting.value = true;
    router.get(`/admin/admin/delete/${deleteTarget.value.id}`, {}, {
        onFinish: () => { deleting.value = false; showDelete.value = false; },
    });
};

const applyFilters = () => {
    router.get('/admin/admin/list', filters.value, { preserveState: true, replace: true });
};

const formatDate = (d: string) =>
    new Date(d).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' });
</script>
