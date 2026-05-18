<template>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Apprenants</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ students.total }} apprenant(s)</p>
            </div>
            <div class="flex items-center gap-2">
                <AppButton @click="openCreate">
                    <template #icon><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg></template>
                    Nouvel apprenant
                </AppButton>
            </div>
        </div>

        <!-- Table -->
        <DataTable
            ref="tableRef"
            :columns="columns"
            :rows="tableRows"
            row-key="id"
            export-filename="apprenants"
            :show-reset-password="true"
            @delete="handleDelete"
            @reset-password="handleResetPassword"
        >
            <template #cell-user="{ row }">
                <div class="flex items-center gap-3">
                    <UserAvatar :src="row.profile_url as string" :name="row.name as string" :last-name="row.last_name as string" size="sm" />
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ row.last_name }} {{ row.name }}</p>
                        <p class="text-xs text-gray-500">{{ row.admission_number ?? '—' }}</p>
                    </div>
                </div>
            </template>
            <template #cell-class_name="{ row }">
                <AppBadge variant="primary">{{ row.class_name ?? '—' }}</AppBadge>
            </template>
            <template #cell-status="{ row }">
                <AppBadge :variant="row.status == 1 ? 'success' : 'danger'" dot>{{ row.status == 1 ? 'Actif' : 'Inactif' }}</AppBadge>
            </template>
            <template #actions="{ row }">
                <div class="flex items-center justify-end gap-1">
                    <button class="p-1.5 rounded-lg text-gray-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors" @click="openEdit(row as any)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    </button>
                    <button class="p-1.5 rounded-lg text-gray-400 hover:text-danger-600 hover:bg-danger-50 dark:hover:bg-danger-900/20 transition-colors" @click="tableRef?.confirmDelete(row.id as number, `${row.last_name} ${row.name}`)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>
                </div>
            </template>
        </DataTable>

        <!-- Modal Formulaire -->
        <AppModal v-model="showForm" :title="editTarget ? 'Modifier l\'apprenant' : 'Nouvel apprenant'" size="xl">
            <form :id="formId" @submit.prevent="submitForm" class="space-y-4">
                <div class="flex items-center gap-4 pb-2 border-b border-gray-100 dark:border-gray-700">
                    <UserAvatar :src="previewUrl" :name="form.name" :last-name="form.last_name" size="xl" />
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Photo de profil</label>
                        <input type="file" accept="image/*" class="text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" @change="onFileChange" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <AppInput v-model="form.last_name" label="Prénoms" required />
                    <AppInput v-model="form.name" label="Nom" required />
                    <AppInput v-model="form.email" label="Email" type="email" required />
                    <AppInput v-model="form.admission_number" label="N° d'admission" />
                    <AppInput v-model="form.roll_number" label="N° de rôle" />
                    <AppSelect v-model="form.class_id" label="Classe" :options="classOptions" required />
                    <AppSelect v-model="form.gender" label="Genre" :options="genderOptions" placeholder="Sélectionner..." />
                    <AppInput v-model="form.date_of_birth" label="Date de naissance" type="date" />
                    <AppInput v-model="form.admission_date" label="Date d'admission" type="date" />
                    <AppInput v-model="form.mobile_number" label="Téléphone" />
                    <AppSelect v-model="form.blood_group" label="Groupe sanguin" :options="bloodGroupOptions" placeholder="Sélectionner..." />
                    <AppSelect v-model="form.status" label="Statut" :options="statusOptions" required />
                    <AppInput v-model="form.height" label="Taille (cm)" type="number" />
                    <AppInput v-model="form.weight" label="Poids (kg)" type="number" />
                    <AppInput v-model="form.password" :label="editTarget ? 'Nouveau mot de passe (optionnel)' : 'Mot de passe'" type="password" :required="!editTarget" />
                </div>
            </form>
            <template #footer>
                <AppButton variant="ghost" @click="showForm = false">Annuler</AppButton>
                <AppButton type="submit" :form="formId" :loading="submitting">{{ editTarget ? 'Enregistrer' : 'Créer' }}</AppButton>
            </template>
        </AppModal>

        <!-- Modal Supprimer -->
        <AppModal v-model="showDelete" title="Supprimer l'apprenant" size="sm" persistent>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Voulez-vous vraiment supprimer <strong class="text-gray-900 dark:text-white">{{ deleteTarget?.last_name }} {{ deleteTarget?.name }}</strong> ?
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
import { router } from '@inertiajs/vue3';
import { AppButton, AppInput, AppSelect, AppModal, DataTable, AppBadge } from '@/Components/UI';
import UserAvatar from '@/Components/Shared/UserAvatar.vue';
import { useToast } from '@/Composables/useToast';

interface Student {
    id: number; name: string; last_name: string; email: string;
    status: number; gender: string; class_id: number; class_name: string;
    admission_number: string; roll_number: string; profile_picture: string | null;
    date_of_birth?: string; admission_date?: string; mobile_number?: string;
    blood_group?: string; height?: string; weight?: string;
}

interface ClassItem { id: number; name: string; }

const props = defineProps<{
    students: { data: Student[]; total: number; from: number; to: number; links: any[] };
    classes: ClassItem[];
}>();

const formId = 'student-form';
const showForm = ref(false); const showDelete = ref(false);
const editTarget = ref<Student | null>(null); const deleteTarget = ref<Student | null>(null);
const deleting = ref(false); const submitting = ref(false);
const previewUrl = ref<string | null>(null); const picFile = ref<File | null>(null);
const toast = useToast();
const tableRef = ref<InstanceType<typeof DataTable> | null>(null);

const statusOptions    = [{ value: '1', label: 'Actif' }, { value: '0', label: 'Inactif' }];
const genderOptions    = [{ value: 'male', label: 'Masculin' }, { value: 'female', label: 'Féminin' }, { value: 'other', label: 'Autre' }];
const bloodGroupOptions = ['A+','A-','B+','B-','AB+','AB-','O+','O-'].map(v => ({ value: v.toLowerCase(), label: v }));
const classOptions     = computed(() => props.classes.map(c => ({ value: String(c.id), label: c.name })));

const columns = [
    { key: 'user', label: 'Apprenant' },
    { key: 'class_name', label: 'Classe' },
    { key: 'mobile_number', label: 'Téléphone' },
    { key: 'status', label: 'Statut' },
];

const tableRows = computed(() => props.students.data.map(s => ({
    ...s, profile_url: s.profile_picture ? `/upload/profile/${s.profile_picture}` : null,
})));

const emptyForm = () => ({ name: '', last_name: '', email: '', password: '', status: '1', gender: '', class_id: '', admission_number: '', roll_number: '', date_of_birth: '', admission_date: '', mobile_number: '', blood_group: '', height: '', weight: '' });
const form = ref({ ...emptyForm() });

const openCreate = () => { editTarget.value = null; previewUrl.value = null; picFile.value = null; form.value = emptyForm(); showForm.value = true; };
const openEdit = (s: Student) => {
    editTarget.value = s; previewUrl.value = s.profile_picture ? `/upload/profile/${s.profile_picture}` : null; picFile.value = null;
    form.value = { name: s.name, last_name: s.last_name, email: s.email, password: '', status: String(s.status), gender: s.gender ?? '', class_id: String(s.class_id), admission_number: s.admission_number ?? '', roll_number: s.roll_number ?? '', date_of_birth: s.date_of_birth ?? '', admission_date: s.admission_date ?? '', mobile_number: s.mobile_number ?? '', blood_group: s.blood_group ?? '', height: s.height ?? '', weight: s.weight ?? '' };
    showForm.value = true;
};
const openDelete = (s: Student) => { deleteTarget.value = s; showDelete.value = true; };
const onFileChange = (e: Event) => { const f = (e.target as HTMLInputElement).files?.[0]; if (f) { picFile.value = f; previewUrl.value = URL.createObjectURL(f); } };

const submitForm = () => {
    const data = new FormData();
    Object.entries(form.value).forEach(([k, v]) => { if (v) data.append(k, String(v)); });
    if (picFile.value) data.append('profile_picture', picFile.value);
    submitting.value = true;
    const url = editTarget.value ? `/admin/student/edit/${editTarget.value.id}` : '/admin/student/add';
    router.post(url, data, { onSuccess: () => { showForm.value = false; }, onFinish: () => { submitting.value = false; } });
};

const confirmDelete = () => {
    if (!deleteTarget.value) return;
    deleting.value = true;
    router.get(`/admin/student/delete/${deleteTarget.value.id}`, {}, { onFinish: () => { deleting.value = false; showDelete.value = false; } });
};

const applyFilters = () => router.get('/admin/student/list', filters.value, { preserveState: true, replace: true });

const handleDelete = (ids: (string | number)[]) => {
    ids.forEach(id => {
        router.get(`/admin/student/delete/${id}`, {}, {
            onSuccess: () => toast.success('Apprenant supprimé avec succès.'),
            onError: () => toast.error('Erreur lors de la suppression.'),
        });
    });
};

const handleResetPassword = async (ids: (string | number)[]) => {
    try {
        const csrf = (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? '';
        const res = await fetch('/admin/users/reset-password', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
            body: JSON.stringify({ ids }),
        });
        const data = await res.json();
        if (data.success) toast.success(data.message);
        else toast.error(data.message);
    } catch { toast.error('Erreur lors de la réinitialisation.'); }
};
</script>
