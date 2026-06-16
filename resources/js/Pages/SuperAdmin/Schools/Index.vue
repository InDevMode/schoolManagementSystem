<template>
    <div class="space-y-6">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Gestion des écoles</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    {{ schools.total }} école(s) enregistrée(s)
                </p>
            </div>
            <button @click="openCreate"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold
                           bg-violet-600 hover:bg-violet-700 text-white transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nouvelle école
            </button>
        </div>

        <!-- Alerte flash -->
        <AppAlert v-if="flashSuccess" variant="success" :message="flashSuccess" dismissible />
        <AppAlert v-if="flashError"   variant="danger"  :message="flashError"   dismissible />

        <!-- Filtres -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-5">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <AppInput
                    v-model="filters.name"
                    label="Nom de l'école"
                    placeholder="Rechercher par nom..."
                    @keyup.enter="applyFilters"
                >
                    <template #prefix>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </template>
                </AppInput>

                <AppInput
                    v-model="filters.code"
                    label="Code école"
                    placeholder="Rechercher par code..."
                    @keyup.enter="applyFilters"
                >
                    <template #prefix>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                        </svg>
                    </template>
                </AppInput>

                <AppSelect
                    v-model="filters.status"
                    label="Statut"
                    placeholder="Tous les statuts"
                    :options="[
                        { value: '1', label: 'Actif' },
                        { value: '0', label: 'Inactif' },
                    ]"
                />
            </div>
            <div class="flex items-center gap-2 mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                <button @click="applyFilters"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium
                               bg-violet-600 hover:bg-violet-700 text-white transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Filtrer
                </button>
                <button @click="resetFilters"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium
                               border border-gray-200 dark:border-gray-600
                               text-gray-600 dark:text-gray-300
                               hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Réinitialiser
                </button>
            </div>
        </div>

        <!-- Tableau -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                            <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">École</th>
                            <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Code</th>
                            <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Contact</th>
                            <th class="px-5 py-3 text-center font-semibold text-gray-600 dark:text-gray-300">Utilisateurs</th>
                            <th class="px-5 py-3 text-center font-semibold text-gray-600 dark:text-gray-300">Admins</th>
                            <th class="px-5 py-3 text-center font-semibold text-gray-600 dark:text-gray-300">Statut</th>
                            <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Créée le</th>
                            <th class="px-5 py-3 text-right font-semibold text-gray-600 dark:text-gray-300 pr-5">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                        <template v-if="schools.data.length">
                            <tr v-for="school in schools.data" :key="school.id"
                                class="hover:bg-gray-50/80 dark:hover:bg-gray-700/30 transition-colors">

                                <!-- École -->
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-violet-100 dark:bg-violet-900/30 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900 dark:text-white">{{ school.school_name }}</p>
                                            <p class="text-xs text-gray-500">{{ school.school_type ?? '—' }}</p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Code -->
                                <td class="px-5 py-3">
                                    <span class="font-mono text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-lg text-gray-700 dark:text-gray-300">
                                        {{ school.school_code }}
                                    </span>
                                </td>

                                <!-- Contact -->
                                <td class="px-5 py-3">
                                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ school.email ?? '—' }}</p>
                                    <p class="text-xs text-gray-500">{{ school.phone ?? '—' }}</p>
                                </td>

                                <!-- Utilisateurs -->
                                <td class="px-5 py-3 text-center">
                                    <span class="inline-flex items-center justify-center min-w-[2rem] px-2 py-0.5 rounded-full text-xs font-bold
                                                 bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400">
                                        {{ school.total_users }}
                                    </span>
                                </td>

                                <!-- Admins -->
                                <td class="px-5 py-3 text-center">
                                    <span class="inline-flex items-center justify-center min-w-[2rem] px-2 py-0.5 rounded-full text-xs font-bold
                                                 bg-violet-50 text-violet-700 dark:bg-violet-900/20 dark:text-violet-400">
                                        {{ school.total_admins }}
                                    </span>
                                </td>

                                <!-- Statut -->
                                <td class="px-5 py-3 text-center">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold"
                                          :class="school.status == 1
                                            ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400'
                                            : 'bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-400'">
                                        <span class="w-1.5 h-1.5 rounded-full"
                                              :class="school.status == 1 ? 'bg-emerald-500' : 'bg-red-400'"/>
                                        {{ school.status == 1 ? 'Actif' : 'Inactif' }}
                                    </span>
                                </td>

                                <!-- Date -->
                                <td class="px-5 py-3">
                                    <span class="text-xs text-gray-500">{{ formatDate(school.created_at) }}</span>
                                </td>

                                <!-- Actions -->
                                <td class="px-5 py-3 pr-5">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button @click="openEdit(school)" title="Modifier"
                                                class="p-1.5 rounded-lg text-white bg-emerald-500 hover:bg-emerald-600 shadow-sm transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                        <button @click="confirmDelete(school)" title="Supprimer"
                                                class="p-1.5 rounded-lg text-white bg-red-500 hover:bg-red-600 shadow-sm transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <tr v-else>
                            <td colspan="8" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-14 h-14 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                        <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                  d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                    </div>
                                    <p class="text-gray-500 dark:text-gray-400 font-medium">Aucune école trouvée</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 px-5 py-4
                        border-t border-gray-100 dark:border-gray-700">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    <span v-if="schools.total > 0">{{ schools.from }}–{{ schools.to }} sur {{ schools.total }} résultat(s)</span>
                    <span v-else>Aucun résultat</span>
                </p>
                <div class="flex items-center gap-1">
                    <button :disabled="!schools.prev_page_url"
                            @click="schools.prev_page_url && goToPage(schools.prev_page_url)"
                            class="w-8 h-8 flex items-center justify-center rounded-lg text-sm transition-colors
                                   disabled:opacity-30 disabled:cursor-not-allowed
                                   text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                    <template v-for="link in schools.links.slice(1, -1)" :key="link.label">
                        <button v-if="link.url" @click="goToPage(link.url)"
                                :class="['w-8 h-8 flex items-center justify-center rounded-lg text-sm font-medium transition-colors',
                                    link.active ? 'bg-violet-600 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700']">
                            {{ link.label }}
                        </button>
                    </template>
                    <button :disabled="!schools.next_page_url"
                            @click="schools.next_page_url && goToPage(schools.next_page_url)"
                            class="w-8 h-8 flex items-center justify-center rounded-lg text-sm transition-colors
                                   disabled:opacity-30 disabled:cursor-not-allowed
                                   text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Créer / Modifier -->
        <AppModal v-model="showForm"
                  :title="editTarget ? `Modifier — ${editTarget.school_name}` : 'Nouvelle école'"
                  size="xl">
            <form :id="formId" @submit.prevent="submitForm" class="space-y-5">
                <!-- Informations générales -->
                <div class="space-y-3">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 border-b border-gray-100 dark:border-gray-700 pb-2">
                        Informations générales
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <AppInput v-model="form.school_name" label="Nom de l'école" required :error="formErrors.school_name" />
                        <AppInput v-model="form.school_type" label="Type (Lycée, Collège...)" :error="formErrors.school_type" />
                        <AppInput v-model="form.email" label="Email" type="email" :error="formErrors.email" />
                        <AppInput v-model="form.phone" label="Téléphone" :error="formErrors.phone" />
                        <AppInput v-model="form.uai_number" label="Numéro UAI / Identifiant" :error="formErrors.uai_number" />
                        <AppSelect v-model="form.status" label="Statut"
                                   :options="[{value:'1',label:'Actif'},{value:'0',label:'Inactif'}]" required />
                        <div class="sm:col-span-2">
                            <AppInput v-model="form.address" label="Adresse" :error="formErrors.address" />
                        </div>
                    </div>
                </div>

                <!-- Logo & Favicon -->
                <div class="space-y-3">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 border-b border-gray-100 dark:border-gray-700 pb-2">
                        Logo & Favicon
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Logo</label>
                            <input type="file" accept="image/*" @change="onLogoChange"
                                   class="text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg
                                          file:border-0 file:text-xs file:font-medium file:bg-violet-50 file:text-violet-700
                                          hover:file:bg-violet-100" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Favicon</label>
                            <input type="file" accept="image/*,.ico" @change="onFaviconChange"
                                   class="text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg
                                          file:border-0 file:text-xs file:font-medium file:bg-violet-50 file:text-violet-700
                                          hover:file:bg-violet-100" />
                        </div>
                    </div>
                </div>
            </form>

            <template #footer>
                <button @click="showForm = false"
                        class="px-4 py-2 rounded-xl text-sm font-medium border border-gray-200 dark:border-gray-600
                               text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Annuler
                </button>
                <button type="submit" :form="formId" :disabled="submitting"
                        class="px-4 py-2 rounded-xl text-sm font-semibold bg-violet-600 hover:bg-violet-700
                               text-white transition-colors shadow-sm disabled:opacity-60 flex items-center gap-2">
                    <svg v-if="submitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    {{ editTarget ? 'Enregistrer' : 'Créer l\'école' }}
                </button>
            </template>
        </AppModal>

        <!-- Modal confirmation suppression -->
        <AppModal v-model="showDeleteModal" title="Supprimer l'école" size="sm">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Voulez-vous vraiment supprimer l'école
                <span class="font-semibold text-gray-900 dark:text-white">{{ deleteTarget?.school_name }}</span> ?
                Cette action est irréversible si elle a des utilisateurs actifs.
            </p>
            <template #footer>
                <button @click="showDeleteModal = false"
                        class="px-4 py-2 rounded-xl text-sm font-medium border border-gray-200 dark:border-gray-600
                               text-gray-600 hover:bg-gray-50 transition-colors">
                    Annuler
                </button>
                <button @click="doDelete"
                        class="px-4 py-2 rounded-xl text-sm font-semibold bg-red-600 hover:bg-red-700 text-white transition-colors shadow-sm">
                    Supprimer
                </button>
            </template>
        </AppModal>

    </div>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { AppInput, AppSelect, AppModal, AppAlert } from '@/Components/UI';

interface School {
    id: number;
    school_name: string;
    school_type: string | null;
    school_code: string;
    address: string | null;
    phone: string | null;
    email: string | null;
    uai_number: string | null;
    status: number;
    total_users: number;
    total_admins: number;
    created_at: string;
}

const props = defineProps<{
    schools: {
        data: School[];
        total: number;
        from: number;
        to: number;
        links: { url: string | null; label: string; active: boolean }[];
        prev_page_url: string | null;
        next_page_url: string | null;
    };
}>();

const page        = usePage<any>();
const flashSuccess = ref(page.props.flash?.success ?? '');
const flashError   = ref(page.props.flash?.error   ?? '');

const showForm       = ref(false);
const showDeleteModal = ref(false);
const editTarget     = ref<School | null>(null);
const deleteTarget   = ref<School | null>(null);
const submitting     = ref(false);
const formId         = 'school-form';
const logoFile       = ref<File | null>(null);
const faviconFile    = ref<File | null>(null);

const filters = reactive({ name: '', code: '', status: '' });

const form = reactive({
    school_name: '',
    school_type: '',
    address:     '',
    phone:       '',
    email:       '',
    uai_number:  '',
    status:      '1',
});

const formErrors = reactive<Record<string, string>>({});

// ── Helpers ─────────────────────────────────────────────────────────────────

const formatDate = (d: string) =>
    d ? new Date(d).toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' }) : '—';

const applyFilters = () => {
    router.get('/superadmin/schools', { ...filters }, { preserveState: true, replace: true });
};

const resetFilters = () => {
    filters.name   = '';
    filters.code   = '';
    filters.status = '';
    router.get('/superadmin/schools', {}, { preserveState: false });
};

const goToPage = (url: string) => {
    router.get(url, {}, { preserveState: true });
};

const onLogoChange = (e: Event) => {
    logoFile.value = (e.target as HTMLInputElement).files?.[0] ?? null;
};

const onFaviconChange = (e: Event) => {
    faviconFile.value = (e.target as HTMLInputElement).files?.[0] ?? null;
};

// ── CRUD ─────────────────────────────────────────────────────────────────────

const openCreate = () => {
    editTarget.value = null;
    Object.assign(form, { school_name: '', school_type: '', address: '', phone: '', email: '', uai_number: '', status: '1' });
    Object.keys(formErrors).forEach(k => delete formErrors[k]);
    logoFile.value    = null;
    faviconFile.value = null;
    showForm.value    = true;
};

const openEdit = (school: School) => {
    editTarget.value = school;
    Object.assign(form, {
        school_name: school.school_name,
        school_type: school.school_type ?? '',
        address:     school.address     ?? '',
        phone:       school.phone       ?? '',
        email:       school.email       ?? '',
        uai_number:  school.uai_number  ?? '',
        status:      String(school.status),
    });
    Object.keys(formErrors).forEach(k => delete formErrors[k]);
    logoFile.value    = null;
    faviconFile.value = null;
    showForm.value    = true;
};

const submitForm = () => {
    submitting.value = true;
    const data = new FormData();
    Object.entries(form).forEach(([k, v]) => { if (v !== null && v !== undefined) data.append(k, String(v)); });
    if (logoFile.value)    data.append('logo',    logoFile.value);
    if (faviconFile.value) data.append('favicon', faviconFile.value);

    const url = editTarget.value
        ? `/superadmin/schools/edit/${editTarget.value.id}`
        : '/superadmin/schools/add';

    router.post(url, data, {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            showForm.value = false;
            flashSuccess.value = editTarget.value ? 'École modifiée avec succès.' : 'École créée avec succès.';
            setTimeout(() => { flashSuccess.value = ''; }, 4000);
        },
        onError: (errors) => {
            Object.assign(formErrors, errors);
        },
        onFinish: () => { submitting.value = false; },
    });
};

const confirmDelete = (school: School) => {
    deleteTarget.value  = school;
    showDeleteModal.value = true;
};

const doDelete = () => {
    if (!deleteTarget.value) return;
    router.get(`/superadmin/schools/delete/${deleteTarget.value.id}`, {}, {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteModal.value = false;
            flashSuccess.value = 'École supprimée.';
            setTimeout(() => { flashSuccess.value = ''; }, 4000);
        },
        onError: () => {
            showDeleteModal.value = false;
            flashError.value = 'Impossible de supprimer cette école.';
        },
    });
};
</script>
