<template>
    <div class="space-y-6">
        <PageHeader title="Personnel" :subtitle="`${staff.total} membre(s) du personnel`" color="amber">
            <template #icon>
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </template>
            <template #actions>
                <AppButton v-if="can('action.staff.create')" @click="openCreate">
                    <template #icon>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </template>
                    Ajouter un membre
                </AppButton>
            </template>
        </PageHeader>

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
                    class="flex-shrink-0 px-3 py-2 rounded-xl text-xs font-medium
                           text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200
                           bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600
                           transition-colors whitespace-nowrap">
                    Réinitialiser
                </button>
            </div>
        </div>

        <!-- Grille de cartes -->
        <div v-if="staff.data.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            <div v-for="member in staff.data" :key="member.id"
                class="card overflow-hidden flex flex-col hover:shadow-lg transition-all duration-200 group">

                <!-- Bannière colorée + avatar -->
                <div class="relative h-20 flex-shrink-0"
                     :style="roleBannerStyle(member.role)">
                    <!-- Motif décoratif -->
                    <div class="absolute inset-0 opacity-10">
                        <div class="absolute -top-4 -right-4 w-24 h-24 rounded-full bg-white"/>
                        <div class="absolute -bottom-6 -left-4 w-16 h-16 rounded-full bg-white"/>
                    </div>
                    <!-- Badge statut en haut à droite -->
                    <div class="absolute top-3 right-3">
                        <span :class="[
                            'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold',
                            member.status === 'active'
                                ? 'bg-white/90 text-emerald-700'
                                : member.status === 'suspended'
                                ? 'bg-white/90 text-amber-700'
                                : 'bg-white/90 text-gray-600',
                        ]">
                            <span :class="[
                                'w-1.5 h-1.5 rounded-full',
                                member.status === 'active' ? 'bg-emerald-500' : member.status === 'suspended' ? 'bg-amber-500' : 'bg-gray-400',
                            ]"/>
                            {{ member.status === 'active' ? 'Actif' : member.status === 'suspended' ? 'Suspendu' : 'Inactif' }}
                        </span>
                    </div>
                    <!-- Avatar chevauchant la bannière -->
                    <div class="absolute -bottom-7 left-4">
                        <div class="relative">
                            <img v-if="member.profile_picture"
                                :src="`/upload/profile/${member.profile_picture}`"
                                :alt="`${member.last_name} ${member.name}`"
                                class="w-14 h-14 rounded-xl object-cover ring-3 ring-white dark:ring-gray-800 shadow-md"/>
                            <div v-else
                                class="w-14 h-14 rounded-xl flex items-center justify-center ring-3 ring-white dark:ring-gray-800 shadow-md text-white font-bold text-xl"
                                :style="roleBannerStyle(member.role)">
                                {{ (member.last_name?.[0] ?? member.name?.[0] ?? '?').toUpperCase() }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Corps de la carte -->
                <div class="flex-1 flex flex-col pt-9 px-4 pb-4 gap-3">
                    <!-- Nom + profil -->
                    <div>
                        <p class="text-sm font-bold text-gray-900 dark:text-white leading-tight">
                            {{ member.last_name }} {{ member.name }}
                        </p>
                        <p class="text-xs text-gray-400 truncate mt-0.5">{{ member.email }}</p>
                        <span class="inline-flex mt-1.5 items-center px-2 py-0.5 rounded-full text-[11px] font-semibold"
                            :class="roleBadgeClass(member.role)">
                            {{ roleLabels[member.role] ?? member.role }}
                        </span>
                    </div>

                    <!-- Infos détaillées -->
                    <div class="space-y-1.5 border-t border-gray-100 dark:border-gray-700 pt-3">
                        <div v-if="member.employee_number" class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                            <svg class="w-3.5 h-3.5 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                            </svg>
                            <span class="font-mono font-medium">{{ member.employee_number }}</span>
                        </div>
                        <div v-if="member.department" class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                            <svg class="w-3.5 h-3.5 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <span class="truncate">{{ member.department }}</span>
                        </div>
                        <div v-if="member.hire_date" class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                            <svg class="w-3.5 h-3.5 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>Depuis le {{ formatDate(member.hire_date) }}</span>
                        </div>
                        <div v-if="member.bio" class="flex items-start gap-2 text-xs text-gray-500 dark:text-gray-400">
                            <svg class="w-3.5 h-3.5 flex-shrink-0 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="line-clamp-2">{{ member.bio }}</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-2 pt-2 border-t border-gray-100 dark:border-gray-700 mt-auto">
                        <button v-if="can('action.staff.edit')"
                            class="flex-1 flex items-center justify-center gap-1.5 py-1.5 rounded-lg text-xs font-medium
                                   bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400
                                   hover:bg-emerald-100 dark:hover:bg-emerald-900/40 transition-colors"
                            @click="openEdit(member)">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Modifier
                        </button>
                        <button v-if="can('action.staff.delete')"
                            class="flex-1 flex items-center justify-center gap-1.5 py-1.5 rounded-lg text-xs font-medium
                                   bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400
                                   hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors"
                            @click="openDelete(member)">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Supprimer
                        </button>
                    </div>
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
                <div class="p-3 rounded-xl bg-violet-50 dark:bg-violet-900/20 border border-violet-100 dark:border-violet-800 text-xs text-violet-700 dark:text-violet-300 flex items-start gap-2">
                    <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Le membre du personnel doit avoir un compte utilisateur. Sélectionnez-le depuis la liste.</span>
                </div>
                <!-- Sélection user existant (prof, directeur, etc.) -->
                <AppSelect v-model="form.user_id" label="Utilisateur (compte existant)" :options="userOptions" required :error="form.errors.user_id"/>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <AppSelect v-model="form.role"   label="Profil"  :options="roleOpts"   required :error="form.errors.role"/>
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
import { fmtDate } from '@/utils/dateFormat';
import { ref, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { PageHeader, AppButton, AppInput, AppSelect, AppModal } from '@/Components/UI';
import { useCan } from '@/Composables/useCan';
import { useToast } from '@/Composables/useToast';

const { can } = useCan();
const toast   = useToast();

const props = defineProps<{
    staff:        { data: any[]; total: number; from: number; to: number; links: any[] };
    roleLabels:   Record<string, string>;
    users?:       { id: number; name: string; last_name: string; user_type: number; school_id: number | null }[];
    isSuperAdmin: boolean;
    schools?:     { id: number; school_name: string }[];
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

const userTypeLabel = (t: number) => ({ 1: 'Admin', 2: 'Prof', 3: 'apprenant', 4: 'Parent' }[t] ?? 'Autre');

// Tous les users disponibles — le scoping par école est fait côté backend
const userOptions = computed(() =>
    (props.users ?? []).map(u => ({ value: String(u.id), label: `${u.last_name} ${u.name} (${userTypeLabel(u.user_type)})` }))
);

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

const roleBannerStyle = (role: string): Record<string, string> => ({
    teacher:    { background: 'linear-gradient(135deg, #7B74F0, #9189f5)' },
    director:   { background: 'linear-gradient(135deg, #7c3aed, #a855f7)' },
    accountant: { background: 'linear-gradient(135deg, #059669, #10b981)' },
    supervisor: { background: 'linear-gradient(135deg, #d97706, #f59e0b)' },
    secretary:  { background: 'linear-gradient(135deg, #0284c7, #38bdf8)' },
    librarian:  { background: 'linear-gradient(135deg, #db2777, #f472b6)' },
    other:      { background: 'linear-gradient(135deg, #475569, #94a3b8)' },
}[role] ?? { background: 'linear-gradient(135deg, #475569, #94a3b8)' }) as Record<string, string>;

const formatDate = fmtDate;
</script>
