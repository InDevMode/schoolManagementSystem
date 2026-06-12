<template>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Personnel</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ staff.total }} membre(s) du personnel</p>
            </div>
            <AppButton v-if="can('action.staff.create')" @click="openCreate">
                <template #icon>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </template>
                Ajouter un membre
            </AppButton>
        </div>

        <!-- Filtres -->
        <div class="card p-4">
            <div class="flex flex-row flex-wrap items-center gap-3">
                <div class="flex-1 min-w-[150px]">
                    <AppSelect v-model="filters.role" :options="roleOptions" placeholder="Tous les rôles" @change="applyFilters"/>
                </div>
                <div class="flex-1 min-w-[150px]">
                    <AppSelect v-model="filters.status" :options="statusOptions" placeholder="Tous les statuts" @change="applyFilters"/>
                </div>
                <div class="flex-1 min-w-[180px]">
                    <AppInput v-model="filters.search" placeholder="Rechercher..." @input="applyFilters"/>
                </div>
                <button v-if="filters.role || filters.status || filters.search"
                    @click="filters = { role: '', status: '', search: '' }; applyFilters()"
                    class="flex-shrink-0 px-3 py-2 rounded-lg text-xs font-medium
                           text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200
                           bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600
                           transition-colors whitespace-nowrap">
                    Réinitialiser
                </button>
            </div>
        </div>

        <!-- Grille de cartes -->
        <div v-if="staff.data.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            <div v-for="member in staff.data" :key="member.id"
                class="card p-5 flex flex-col gap-4 hover:shadow-md transition-shadow">
                <!-- Avatar + infos -->
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <img v-if="member.profile_picture"
                            :src="`/upload/profile/${member.profile_picture}`"
                            :alt="`${member.last_name} ${member.name}`"
                            class="w-12 h-12 rounded-full object-cover ring-2 ring-white dark:ring-gray-700"/>
                        <div v-else
                            class="w-12 h-12 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-600 dark:text-primary-400 font-bold text-lg">
                            {{ (member.last_name?.[0] ?? member.name?.[0] ?? '?').toUpperCase() }}
                        </div>
                        <!-- Badge statut -->
                        <span :class="[
                            'absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full border-2 border-white dark:border-gray-800',
                            member.status === 'active' ? 'bg-success-500' : member.status === 'suspended' ? 'bg-warning-500' : 'bg-gray-400',
                        ]"/>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                            {{ member.last_name }} {{ member.name }}
                        </p>
                        <p class="text-xs text-gray-400 truncate">{{ member.email }}</p>
                    </div>
                </div>

                <!-- Rôle + dates -->
                <div class="flex flex-col gap-1.5">
                    <span class="inline-flex w-fit items-center px-2.5 py-1 rounded-full text-xs font-semibold"
                        :class="roleBadgeClass(member.role)">
                        {{ roleLabels[member.role] ?? member.role }}
                    </span>
                    <p v-if="member.hire_date" class="text-xs text-gray-400">
                        Depuis : {{ formatDate(member.hire_date) }}
                    </p>
                    <p v-if="member.employee_number" class="text-xs text-gray-400 font-mono">
                        Matricule : {{ member.employee_number }}
                    </p>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-1.5 pt-2 border-t border-gray-100 dark:border-gray-700">
                    <button v-if="can('action.staff.edit')"
                        class="flex-1 inline-flex items-center justify-center gap-1.5 text-xs font-medium py-1.5 rounded-lg
                               text-white bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700
                               shadow-sm shadow-emerald-200 dark:shadow-emerald-900/40 transition-all duration-150"
                        @click="openEdit(member)">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Modifier
                    </button>
                    <button v-if="can('action.staff.delete')"
                        class="p-1.5 rounded-lg transition-all duration-150
                               text-white bg-red-500 hover:bg-red-600 active:bg-red-700
                               shadow-sm shadow-red-200 dark:shadow-red-900/40"
                        title="Supprimer"
                        @click="openDelete(member)">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Empty state -->
        <div v-else class="card p-12 text-center">
            <div class="w-16 h-16 rounded-2xl bg-gray-50 dark:bg-gray-700 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                </svg>
            </div>
            <p class="text-sm text-gray-400">Aucun membre du personnel trouvé.</p>
        </div>

        <!-- Modal Créer/Modifier -->
        <AppModal v-model="showForm" :title="editTarget ? 'Modifier le membre' : 'Ajouter un membre'" size="lg">
            <form :id="formId" @submit.prevent="submitForm" class="space-y-4">
                <div class="p-3 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 text-xs text-blue-700 dark:text-blue-300">
                    💡 Le membre du personnel doit avoir un compte utilisateur. Sélectionnez-le depuis la liste.
                </div>
                <!-- Sélection user existant (prof, directeur, etc.) -->
                <AppSelect v-model="form.user_id" label="Utilisateur (compte existant)" :options="userOptions" required :error="form.errors.user_id"/>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <AppSelect v-model="form.role"   label="Rôle RH"  :options="roleOpts"   required :error="form.errors.role"/>
                    <AppSelect v-model="form.status" label="Statut"   :options="statusOpts" required :error="form.errors.status"/>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <AppInput v-model="form.hire_date"        label="Date d'embauche"   type="date"/>
                    <AppInput v-model="form.end_date"         label="Date de fin (CDI = vide)" type="date"/>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <AppInput v-model="form.employee_number"  label="Numéro matricule"  placeholder="ex: ENS-2024-001"/>
                    <AppInput v-model="form.department"       label="Département/Section"/>
                </div>
                <AppInput v-model="form.bio" label="Biographie courte" placeholder="Quelques mots sur ce membre..."/>
            </form>
            <template #footer>
                <AppButton variant="ghost" @click="showForm = false">Annuler</AppButton>
                <AppButton type="submit" :form="formId" :loading="form.processing">
                    {{ editTarget ? 'Enregistrer' : 'Ajouter' }}
                </AppButton>
            </template>
        </AppModal>

        <!-- Modal Supprimer -->
        <AppModal v-model="showDelete" title="Supprimer le membre" size="sm" persistent>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Voulez-vous vraiment supprimer <strong>{{ deleteTarget?.last_name }} {{ deleteTarget?.name }}</strong> du personnel ?
                Son compte utilisateur ne sera pas supprimé.
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
import { AppButton, AppInput, AppSelect, AppModal } from '@/Components/UI';
import { useCan } from '@/Composables/useCan';
import { useToast } from '@/Composables/useToast';

const { can } = useCan();
const toast   = useToast();

const props = defineProps<{
    staff:      { data: any[]; total: number; from: number; to: number; links: any[] };
    roleLabels: Record<string, string>;
    users?:     { id: number; name: string; last_name: string; user_type: number }[];
}>();

const formId     = 'staff-form';
const showForm   = ref(false);
const showDelete = ref(false);
const editTarget = ref<any>(null);
const deleteTarget = ref<any>(null);
const deleting   = ref(false);
const filters    = ref({ role: '', status: '', search: '' });

const roleOpts = computed(() => Object.entries(props.roleLabels).map(([k, v]) => ({ value: k, label: v })));
const roleOptions = computed(() => [{ value: '', label: 'Tous les rôles' }, ...roleOpts.value]);
const statusOpts = [
    { value: 'active',    label: 'Actif' },
    { value: 'inactive',  label: 'Inactif' },
    { value: 'suspended', label: 'Suspendu' },
];
const statusOptions = [{ value: '', label: 'Tous les statuts' }, ...statusOpts];

const userOptions = computed(() =>
    (props.users ?? []).map(u => ({
        value: String(u.id),
        label: `${u.last_name} ${u.name} (${userTypeLabel(u.user_type)})`,
    }))
);

const userTypeLabel = (t: number) => ({ 1: 'Admin', 2: 'Prof', 3: 'Élève', 4: 'Parent' }[t] ?? 'Autre');

const form = useForm({
    user_id:         '',
    role:            'teacher',
    status:          'active',
    hire_date:       '',
    end_date:        '',
    employee_number: '',
    department:      '',
    bio:             '',
});

const openCreate = () => {
    editTarget.value = null;
    form.reset();
    form.role   = 'teacher';
    form.status = 'active';
    showForm.value = true;
};

const openEdit = (m: any) => {
    editTarget.value = m;
    form.user_id         = String(m.user_id);
    form.role            = m.role;
    form.status          = m.status;
    form.hire_date       = m.hire_date ?? '';
    form.end_date        = m.end_date ?? '';
    form.employee_number = m.employee_number ?? '';
    form.department      = m.department ?? '';
    form.bio             = m.bio ?? '';
    showForm.value = true;
};

const openDelete = (m: any) => {
    deleteTarget.value = m;
    showDelete.value   = true;
};

const submitForm = () => {
    const url = editTarget.value
        ? `/admin/staff/edit/${editTarget.value.id}`
        : '/admin/staff/add';
    form.post(url, {
        onSuccess: () => { showForm.value = false; toast.success('Personnel mis à jour.'); },
    });
};

const confirmDelete = () => {
    if (!deleteTarget.value) return;
    deleting.value = true;
    router.get(`/admin/staff/delete/${deleteTarget.value.id}`, {}, {
        onFinish: () => { deleting.value = false; showDelete.value = false; },
        onSuccess: () => toast.success('Membre supprimé.'),
    });
};

const applyFilters = () => {
    router.get('/admin/staff/list', {
        role:   filters.value.role   || undefined,
        status: filters.value.status || undefined,
        search: filters.value.search || undefined,
    }, { preserveState: true });
};

const roleBadgeClass = (role: string) => ({
    teacher:    'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400',
    director:   'bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-400',
    accountant: 'bg-success-50 dark:bg-success-900/20 text-success-700 dark:text-success-400',
    supervisor: 'bg-warning-50 dark:bg-warning-900/20 text-warning-700 dark:text-warning-400',
    secretary:  'bg-info-50 dark:bg-info-900/20 text-info-700 dark:text-info-400',
}[role] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400');

const formatDate = (d: string) => d ? new Date(d).toLocaleDateString('fr-FR') : '—';
</script>
