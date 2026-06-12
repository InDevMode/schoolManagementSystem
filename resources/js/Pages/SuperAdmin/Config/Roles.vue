<script setup lang="ts">
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import DataTable from '@/Components/UI/DataTable.vue';
import type { DtColumn, DtAction } from '@/Components/UI/DataTable.vue';

interface Role {
    id: number; name: string; guard_name: string;
    user_type: number | null; description: string | null;
    is_delete: number; deleted_at: string | null;
    permissions_count: number; created_at: string;
}

const props = defineProps<{ roles: Role[]; usedUserTypes: number[]; }>();

// ── Filtres actifs/supprimés ───────────────────────────────────────────────
const showDeleted  = ref(false);
const search       = ref('');
const activeRoles  = computed(() => props.roles.filter(r => r.is_delete === 0));
const deletedRoles = computed(() => props.roles.filter(r => r.is_delete === 1));

// ── Recherche ──────────────────────────────────────────────────────────────
const filteredActiveRoles = computed(() => {
    const q = search.value.toLowerCase().trim();
    if (!q) return activeRoles.value;
    return activeRoles.value.filter(r =>
        r.name.toLowerCase().includes(q) ||
        (r.description ?? '').toLowerCase().includes(q) ||
        String(r.user_type ?? '').includes(q)
    );
});

const filteredDeletedRoles = computed(() => {
    const q = search.value.toLowerCase().trim();
    if (!q) return deletedRoles.value;
    return deletedRoles.value.filter(r =>
        r.name.toLowerCase().includes(q) ||
        (r.description ?? '').toLowerCase().includes(q)
    );
});

const shownRoles = computed(() => showDeleted.value ? filteredDeletedRoles.value : filteredActiveRoles.value);

// ── Rôles système protégés (user_type 0-4) ────────────────────────────────
const isSystem = (role: Role) => role.user_type !== null && role.user_type <= 4;

const SYSTEM_LABELS: Record<number, string> = {
    0: 'Super Admin', 1: 'Admin', 2: 'Professeur', 3: 'Apprenant', 4: 'Parent',
};

// ── Modal création/édition ─────────────────────────────────────────────────
const showModal  = ref(false);
const isEdit     = ref(false);
const submitting = ref(false);
const errors     = ref<Record<string, string>>({});
const emptyForm  = () => ({ id: 0, name: '', user_type: '', description: '' });
const form       = ref(emptyForm());

const openCreate = () => {
    isEdit.value = false; form.value = emptyForm(); errors.value = {};
    showModal.value = true;
};
const openEdit = (role: Role) => {
    if (isSystem(role)) return;
    isEdit.value = true;
    form.value = {
        id: role.id, name: role.name,
        user_type: role.user_type !== null ? String(role.user_type) : '',
        description: role.description ?? '',
    };
    errors.value = {}; showModal.value = true;
};
const closeModal = () => { showModal.value = false; errors.value = {}; };

const validate = () => {
    errors.value = {};
    if (!form.value.name.trim()) errors.value.name = 'Le nom est requis.';
    const ut = parseInt(form.value.user_type as string);
    if (!form.value.user_type)   errors.value.user_type = 'Le user_type est requis.';
    else if (isNaN(ut) || ut < 5) errors.value.user_type = 'Le user_type doit être ≥ 5 (0–4 sont réservés aux rôles système).';
    return Object.keys(errors.value).length === 0;
};

const submit = () => {
    if (!validate()) return;
    submitting.value = true;
    const url = isEdit.value
        ? `/superadmin/config/roles/edit/${form.value.id}`
        : '/superadmin/config/roles/add';
    router.post(url, {
        name: form.value.name.trim(),
        user_type: parseInt(form.value.user_type as string),
        description: form.value.description?.trim() || null,
    }, {
        preserveScroll: true,
        onSuccess: () => { closeModal(); submitting.value = false; },
        onError:   (e) => { errors.value = e as Record<string, string>; submitting.value = false; },
        onFinish:  () => { submitting.value = false; },
    });
};

// ── Prochain user_type disponible ─────────────────────────────────────────
const nextAvailableUserType = computed(() => {
    const used = new Set(props.usedUserTypes);
    let n = 5;
    while (used.has(n)) n++;
    return n;
});

// ── Confirm dialog ────────────────────────────────────────────────────────
const confirm = ref<{ show: boolean; title: string; message: string; onConfirm: () => void }>({
    show: false, title: '', message: '', onConfirm: () => {},
});
const askConfirm = (title: string, message: string, fn: () => void) => {
    confirm.value = { show: true, title, message, onConfirm: fn };
};
const doConfirm = () => { confirm.value.onConfirm(); confirm.value.show = false; };

// ── Soft-delete / restore ─────────────────────────────────────────────────
const softDelete = (role: Role) => {
    askConfirm(
        'Supprimer le rôle',
        `Voulez-vous supprimer le rôle « ${role.name} » ?\nIl sera masqué mais récupérable depuis l'onglet "Supprimés".`,
        () => router.get(`/superadmin/config/roles/delete/${role.id}`, {}, { preserveScroll: true })
    );
};
const restore = (role: Role) => {
    router.get(`/superadmin/config/roles/restore/${role.id}`, {}, { preserveScroll: true });
};

// ── DataTable ─────────────────────────────────────────────────────────────
const columns: DtColumn[] = [
    { key: 'name',              label: 'Nom du rôle',   sortable: true },
    { key: 'user_type',         label: 'user_type',     sortable: true, align: 'center' },
    { key: 'description',       label: 'Description',   sortable: false },
    { key: 'permissions_count', label: 'Permissions',   sortable: true, align: 'center', dataType: 'number' },
    { key: 'created_at',        label: 'Créé le',       sortable: true },
];

const activeActions: DtAction[] = [
    { key: 'edit',   label: 'Modifier',  variant: 'warning',
      condition: (row) => !isSystem(row as unknown as Role) },
    { key: 'delete', label: 'Supprimer', variant: 'danger',
      condition: (row) => !isSystem(row as unknown as Role) },
];

const handleAction = (key: string, row: Record<string, unknown>) => {
    const r = row as unknown as Role;
    if (key === 'edit')   openEdit(r);
    if (key === 'delete') softDelete(r);
};
</script>

<template>
<div class="space-y-5">

    <!-- En-tête -->
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Rôles</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                <span class="font-semibold text-gray-700 dark:text-gray-200">{{ activeRoles.length }}</span> actifs
                <span v-if="deletedRoles.length" class="ml-2 text-red-500">·
                    <span class="font-semibold">{{ deletedRoles.length }}</span> supprimé{{ deletedRoles.length > 1 ? 's' : '' }}
                </span>
            </p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <!-- Toggle actifs/supprimés -->
            <div class="flex items-center rounded-lg border border-gray-200 dark:border-gray-600 overflow-hidden">
                <button @click="showDeleted = false"
                        :class="['px-3 py-1.5 text-xs font-medium transition-colors',
                                 !showDeleted ? 'bg-primary-600 text-white' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700']">
                    Actifs ({{ activeRoles.length }})
                </button>
                <button @click="showDeleted = true"
                        :class="['px-3 py-1.5 text-xs font-medium transition-colors',
                                 showDeleted ? 'bg-red-600 text-white' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700']">
                    Supprimés ({{ deletedRoles.length }})
                </button>
            </div>
            <button v-if="!showDeleted" @click="openCreate"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary-600 hover:bg-primary-700
                           text-white text-sm font-medium transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nouveau rôle
            </button>
        </div>
    </div>

    <!-- Barre de recherche -->
    <div class="relative max-w-sm">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input v-model="search" type="text"
               :placeholder="showDeleted ? 'Rechercher dans les supprimés...' : 'Rechercher un rôle, une description...'"
               class="w-full pl-9 pr-8 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-600
                      bg-white dark:bg-gray-800 text-gray-900 dark:text-white
                      focus:outline-none focus:ring-2 focus:ring-primary-500/40 transition-colors
                      placeholder-gray-400 dark:placeholder-gray-500"/>
        <button v-if="search" @click="search = ''"
                class="absolute right-2.5 top-1/2 -translate-y-1/2 p-0.5 rounded
                       text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
    <p v-if="search" class="text-xs text-gray-400 -mt-2">
        {{ shownRoles.length }} résultat{{ shownRoles.length !== 1 ? 's' : '' }} pour « {{ search }} »
    </p>

    <!-- Info rôles système -->
    <div v-if="!showDeleted"
         class="flex items-start gap-3 p-4 rounded-lg bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-700/40">
        <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <div class="text-sm text-amber-700 dark:text-amber-400">
            Rôles système protégés :
            <code v-for="(label, ut) in SYSTEM_LABELS" :key="ut"
                  class="mx-1 px-1.5 py-0.5 rounded bg-amber-100 dark:bg-amber-800/30 font-mono text-xs">
                {{ label }} (#{{ ut }})
            </code>
            — non modifiables, non supprimables.
            Les rôles custom doivent avoir <code class="font-mono px-1 bg-amber-100 dark:bg-amber-800/30 rounded">user_type ≥ 5</code>.
            Prochain disponible : <strong>{{ nextAvailableUserType }}</strong>
        </div>
    </div>

    <!-- Vue supprimés -->
    <template v-if="showDeleted">
        <div v-if="!filteredDeletedRoles.length"
             class="card p-10 text-center text-gray-400 dark:text-gray-500 text-sm">
            {{ search ? `Aucun rôle supprimé trouvé pour « ${search} ».` : 'Aucun rôle supprimé.' }}
        </div>
        <div v-else class="card overflow-hidden">
            <div class="px-5 py-3 bg-red-50 dark:bg-red-900/10 border-b border-red-100 dark:border-red-800/30">
                <p class="text-sm font-medium text-red-700 dark:text-red-400">
                    Ces rôles ont été supprimés (soft delete). Vous pouvez les restaurer.
                </p>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700/50">
                <div v-for="r in filteredDeletedRoles" :key="r.id"
                     class="flex items-center justify-between px-5 py-3 hover:bg-gray-50 dark:hover:bg-gray-800/40">
                    <div class="flex items-center gap-3">
                        <span class="font-medium text-gray-500 dark:text-gray-400 line-through">{{ r.name }}</span>
                        <span v-if="r.user_type !== null"
                              class="text-xs font-mono px-1.5 py-0.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-400">
                            #{{ r.user_type }}
                        </span>
                        <span class="text-xs text-red-400 dark:text-red-500">Supprimé le {{ r.deleted_at }}</span>
                    </div>
                    <button @click="restore(r)"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium
                                   bg-emerald-50 text-emerald-700 hover:bg-emerald-100
                                   dark:bg-emerald-900/20 dark:text-emerald-400 dark:hover:bg-emerald-900/30 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Restaurer
                    </button>
                </div>
            </div>
        </div>
    </template>

    <!-- Tableau actifs -->
    <DataTable v-else
        :rows="filteredActiveRoles"
        :columns="columns"
        :actions="activeActions"
        row-key="id"
        title="Liste des rôles"
        export-filename="roles"
        @action="handleAction"
    >
        <!-- Nom + badge système -->
        <template #cell-name="{ row }">
            <div class="flex items-center gap-2">
                <span class="font-medium text-gray-800 dark:text-gray-200">{{ row.name }}</span>
                <span v-if="isSystem(row as unknown as Role)"
                      class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold
                             bg-gray-100 text-gray-500 dark:bg-white/10 dark:text-gray-400
                             border border-gray-200 dark:border-white/10">
                    Système
                </span>
            </div>
        </template>

        <!-- user_type : badge coloré selon type -->
        <template #cell-user_type="{ row }">
            <template v-if="row.user_type !== null && row.user_type !== undefined">
                <span :class="[
                    'inline-flex items-center justify-center w-8 h-8 rounded-lg text-sm font-bold tabular-nums',
                    (row.user_type as number) === 0
                        ? 'bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-300'
                        : (row.user_type as number) <= 4
                            ? 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300'
                            : 'bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-300',
                ]">
                    {{ row.user_type }}
                </span>
            </template>
            <span v-else class="text-gray-300 dark:text-gray-600 text-xs">—</span>
        </template>

        <!-- Description -->
        <template #cell-description="{ row }">
            <span class="text-sm text-gray-500 dark:text-gray-400 italic">
                {{ row.description || '—' }}
            </span>
        </template>
    </DataTable>

    <!-- ══════════════ MODAL CRÉATION / ÉDITION ══════════════ -->
    <Teleport to="body">
        <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0"
                    enter-to-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4"
                 @click.self="closeModal">
                <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"/>
                <div class="relative w-full max-w-lg bg-white dark:bg-gray-900 rounded-2xl shadow-2xl
                             border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="h-1 bg-gradient-to-r from-primary-500 to-primary-700"/>
                    <div class="p-6 space-y-5">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ isEdit ? 'Modifier le rôle' : 'Créer un rôle custom' }}
                        </h3>

                        <!-- Nom -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                Nom du rôle <span class="text-red-500">*</span>
                            </label>
                            <input v-model="form.name" type="text"
                                   placeholder="ex: comptable, délégué, coordinateur..."
                                   class="w-full px-3 py-2.5 text-sm rounded-lg border bg-white dark:bg-gray-800
                                          text-gray-900 dark:text-white focus:outline-none focus:ring-2
                                          focus:ring-primary-500/40 focus:border-primary-400 transition-colors"
                                   :class="errors.name ? 'border-red-400' : 'border-gray-200 dark:border-gray-600'"
                                   @keyup.enter="submit"/>
                            <p v-if="errors.name" class="mt-1 text-xs text-red-600">{{ errors.name }}</p>
                        </div>

                        <!-- user_type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                user_type <span class="text-red-500">*</span>
                                <span class="ml-1 text-xs font-normal text-gray-400">(≥ 5, unique par rôle)</span>
                            </label>
                            <div class="flex gap-2">
                                <input v-model="form.user_type" type="number" min="5"
                                       :placeholder="`ex: ${nextAvailableUserType}`"
                                       class="flex-1 px-3 py-2.5 text-sm rounded-lg border font-mono
                                              bg-white dark:bg-gray-800 text-gray-900 dark:text-white
                                              focus:outline-none focus:ring-2 focus:ring-primary-500/40
                                              focus:border-primary-400 transition-colors"
                                       :class="errors.user_type ? 'border-red-400' : 'border-gray-200 dark:border-gray-600'"/>
                                <button type="button"
                                        @click="form.user_type = String(nextAvailableUserType)"
                                        class="px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-600
                                               bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-400
                                               hover:bg-primary-50 dark:hover:bg-primary-900/20
                                               hover:border-primary-300 hover:text-primary-700 transition-colors whitespace-nowrap">
                                    Suggérer ({{ nextAvailableUserType }})
                                </button>
                            </div>
                            <p v-if="errors.user_type" class="mt-1 text-xs text-red-600">{{ errors.user_type }}</p>
                            <!-- user_types déjà pris -->
                            <div v-if="usedUserTypes.length" class="mt-2 flex flex-wrap gap-1.5 items-center">
                                <span class="text-xs text-gray-400 dark:text-gray-500">Déjà utilisés :</span>
                                <span v-for="ut in [...usedUserTypes].sort((a,b) => a-b)" :key="ut"
                                      class="inline-flex items-center px-2 py-0.5 rounded text-xs font-mono
                                             bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                                    {{ ut }}
                                </span>
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                Description <span class="text-xs font-normal text-gray-400">(optionnelle)</span>
                            </label>
                            <input v-model="form.description" type="text"
                                   placeholder="ex: Gestion de la comptabilité, accès limité aux frais..."
                                   class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 dark:border-gray-600
                                          bg-white dark:bg-gray-800 text-gray-900 dark:text-white
                                          focus:outline-none focus:ring-2 focus:ring-primary-500/40
                                          focus:border-primary-400 transition-colors"/>
                        </div>

                        <!-- Aperçu -->
                        <div v-if="form.name && form.user_type"
                             class="flex items-start gap-3 p-3 rounded-lg bg-primary-50 dark:bg-primary-900/10
                                    border border-primary-200 dark:border-primary-700/40">
                            <svg class="w-4 h-4 text-primary-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-xs text-primary-700 dark:text-primary-300">
                                Les utilisateurs avec
                                <code class="font-mono font-bold">user_type = {{ form.user_type }}</code>
                                auront le rôle <strong>{{ form.name }}</strong>
                                et accéderont à <code class="font-mono">/admin/dashboard</code>.
                            </p>
                        </div>

                        <!-- Boutons -->
                        <div class="flex justify-end gap-2.5 pt-2 border-t border-gray-100 dark:border-gray-700">
                            <button @click="closeModal"
                                    class="px-4 py-2 text-sm font-medium rounded-lg border border-gray-200 dark:border-gray-600
                                           text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                Annuler
                            </button>
                            <button @click="submit" :disabled="submitting"
                                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg
                                           bg-primary-600 hover:bg-primary-700 disabled:opacity-50 text-white transition-colors">
                                <svg v-if="submitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                {{ isEdit ? 'Enregistrer' : 'Créer le rôle' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>

    <!-- ══════════════ CONFIRM DIALOG ══════════════ -->
    <Teleport to="body">
        <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0"
                    enter-to-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="confirm.show" class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
                 @click.self="confirm.show = false">
                <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"/>
                <div class="relative w-full max-w-sm bg-white dark:bg-gray-900 rounded-2xl shadow-2xl
                             border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="h-1 bg-red-500"/>
                    <div class="p-6">
                        <div class="flex items-start gap-3 mb-5">
                            <div class="w-10 h-10 rounded-lg bg-red-100 dark:bg-red-900/30
                                        flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-white">{{ confirm.title }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 whitespace-pre-line">{{ confirm.message }}</p>
                            </div>
                        </div>
                        <div class="flex justify-end gap-2">
                            <button @click="confirm.show = false"
                                    class="px-4 py-2 text-sm font-medium rounded-lg border border-gray-200 dark:border-gray-600
                                           text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                Annuler
                            </button>
                            <button @click="doConfirm"
                                    class="px-4 py-2 text-sm font-semibold rounded-lg bg-red-600 hover:bg-red-700
                                           text-white transition-colors">
                                Supprimer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</div>
</template>
