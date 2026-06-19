<script setup lang="ts">
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import DataTable from '@/Components/UI/DataTable.vue';
import type { DtColumn, DtAction } from '@/Components/UI/DataTable.vue';
import { PageHeader } from '@/Components/UI';

interface Permission {
    id: number; name: string; module: string;
    guard_name: string; is_delete: number;
    deleted_at: string | null; created_at: string;
}

const props = defineProps<{
    permissions: Permission[];
    grouped: Record<string, Permission[]>;
}>();

// ── Filtres ────────────────────────────────────────────────────────────────
const showDeleted = ref(false);
const search      = ref('');
const activePerms  = computed(() => props.permissions.filter(p => p.is_delete === 0));
const deletedPerms = computed(() => props.permissions.filter(p => p.is_delete === 1));
const shownPerms   = computed(() => showDeleted.value ? deletedPerms.value : activePerms.value);

// ── Recherche ──────────────────────────────────────────────────────────────
const filteredActivePerms = computed(() => {
    const q = search.value.toLowerCase().trim();
    if (!q) return activePerms.value;
    return activePerms.value.filter(p =>
        p.name.toLowerCase().includes(q) || p.module.toLowerCase().includes(q)
    );
});

const filteredDeletedPerms = computed(() => {
    const q = search.value.toLowerCase().trim();
    if (!q) return deletedPerms.value;
    return deletedPerms.value.filter(p =>
        p.name.toLowerCase().includes(q) || p.module.toLowerCase().includes(q)
    );
});

const activeGrouped  = computed(() => {
    const map: Record<string, Permission[]> = {};
    filteredActivePerms.value.forEach(p => { (map[p.module] ??= []).push(p); });
    return map;
});
const activeModules = computed(() => Object.keys(activeGrouped.value).sort());
const totalFiltered = computed(() => filteredActivePerms.value.length);

// ── Vue active ─────────────────────────────────────────────────────────────
const viewMode = ref<'grouped' | 'table'>('grouped');

// ── Modal création/édition ─────────────────────────────────────────────────
const showModal  = ref(false);
const isEdit     = ref(false);
const submitting = ref(false);
const formErrors = ref<Record<string, string>>({});
const form       = ref({ id: 0, name: '' });

const openCreate = () => { isEdit.value = false; form.value = { id: 0, name: '' }; formErrors.value = {}; showModal.value = true; };
const openEdit   = (p: Permission) => { isEdit.value = true; form.value = { id: p.id, name: p.name }; formErrors.value = {}; showModal.value = true; };
const closeModal = () => { showModal.value = false; formErrors.value = {}; };

const submit = () => {
    if (!form.value.name.trim()) { formErrors.value.name = 'Le nom est requis.'; return; }
    submitting.value = true;
    const url = isEdit.value
        ? `/superadmin/config/permissions/edit/${form.value.id}`
        : '/superadmin/config/permissions/add';
    router.post(url, { name: form.value.name.trim() }, {
        preserveScroll: true,
        onSuccess: () => { closeModal(); submitting.value = false; },
        onError:   (e) => { formErrors.value = e as Record<string, string>; submitting.value = false; },
        onFinish:  () => { submitting.value = false; },
    });
};

// ── Confirm dialog interne ─────────────────────────────────────────────────
const confirm = ref<{ show: boolean; title: string; message: string; onConfirm: () => void }>({
    show: false, title: '', message: '', onConfirm: () => {},
});
const askConfirm = (title: string, message: string, fn: () => void) => {
    confirm.value = { show: true, title, message, onConfirm: fn };
};
const doConfirm = () => { confirm.value.onConfirm(); confirm.value.show = false; };

// ── Actions soft-delete / restore ─────────────────────────────────────────
const softDelete = (p: Permission) => {
    askConfirm(
        'Supprimer la permission',
        `Voulez-vous supprimer « ${p.name} » ?\nElle sera masquée mais récupérable depuis l'onglet "Supprimées".`,
        () => router.get(`/superadmin/config/permissions/delete/${p.id}`, {}, { preserveScroll: true })
    );
};

const restore = (p: Permission) => {
    router.get(`/superadmin/config/permissions/restore/${p.id}`, {}, { preserveScroll: true });
};

// ── DataTable (vue tableau) ────────────────────────────────────────────────
const columns: DtColumn[] = [
    { key: 'module',     label: 'Module',     sortable: true },
    { key: 'name',       label: 'Permission', sortable: true },
    { key: 'created_at', label: 'Créée le',   sortable: true },
];
const activeActions: DtAction[] = [
    { key: 'edit',   label: 'Modifier',   variant: 'warning' },
    { key: 'delete', label: 'Supprimer',  variant: 'danger' },
];
const deletedActions: DtAction[] = [
    { key: 'restore', label: 'Restaurer', variant: 'success' },
];

const handleAction = (key: string, row: Record<string, unknown>) => {
    const p = row as unknown as Permission;
    if (key === 'edit')    openEdit(p);
    if (key === 'delete')  softDelete(p);
    if (key === 'restore') restore(p);
};
</script>

<template>
<div class="space-y-5">

    <!-- En-tête -->
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-violet-500 to-purple-700 flex items-center justify-center shadow-lg shadow-violet-500/30 flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Permissions</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    <span class="font-semibold text-gray-700 dark:text-gray-200">{{ activePerms.length }}</span> actives
                    <span v-if="deletedPerms.length" class="ml-2 text-red-500">·
                        <span class="font-semibold">{{ deletedPerms.length }}</span> supprimée{{ deletedPerms.length > 1 ? 's' : '' }}
                    </span>
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <!-- Toggle actives/supprimées -->
            <div class="flex items-center rounded-xl border border-gray-200 dark:border-gray-600 overflow-hidden">
                <button @click="showDeleted = false"
                        :class="['px-3 py-1.5 text-xs font-medium transition-colors',
                                 !showDeleted ? 'bg-primary-600 text-white' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700']">
                    Actives ({{ activePerms.length }})
                </button>
                <button @click="showDeleted = true"
                        :class="['px-3 py-1.5 text-xs font-medium transition-colors',
                                 showDeleted ? 'bg-red-600 text-white' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700']">
                    Supprimées ({{ deletedPerms.length }})
                </button>
            </div>
            <!-- Toggle vue -->
            <div v-if="!showDeleted" class="flex items-center rounded-xl border border-gray-200 dark:border-gray-600 overflow-hidden">
                <button @click="viewMode = 'grouped'"
                        :class="['px-3 py-1.5 text-xs font-medium transition-colors',
                                 viewMode === 'grouped' ? 'bg-primary-600 text-white' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700']">
                    Groupé
                </button>
                <button @click="viewMode = 'table'"
                        :class="['px-3 py-1.5 text-xs font-medium transition-colors',
                                 viewMode === 'table' ? 'bg-primary-600 text-white' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700']">
                    Tableau
                </button>
            </div>
            <button v-if="!showDeleted" @click="openCreate"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary-600 hover:bg-primary-700
                           text-white text-sm font-medium transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nouvelle permission
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
               :placeholder="showDeleted ? 'Rechercher dans les supprimées...' : 'Rechercher une permission ou un module...'"
               class="w-full pl-9 pr-8 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-600
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
    <p v-if="search && !showDeleted" class="text-xs text-gray-400 -mt-2">
        {{ totalFiltered }} résultat{{ totalFiltered !== 1 ? 's' : '' }} pour « {{ search }} »
    </p>

    <!-- Vue supprimées -->
    <template v-if="showDeleted">
        <div v-if="!filteredDeletedPerms.length"
             class="card p-10 text-center text-gray-400 dark:text-gray-500 text-sm">
            {{ search ? `Aucune permission supprimée trouvée pour « ${search} ».` : 'Aucune permission supprimée.' }}
        </div>
        <div v-else class="card overflow-hidden">
            <div class="px-5 py-3 bg-red-50 dark:bg-red-900/10 border-b border-red-100 dark:border-red-800/30">
                <p class="text-sm font-medium text-red-700 dark:text-red-400">
                    Ces permissions ont été supprimées (soft delete). Vous pouvez les restaurer.
                </p>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700/50">
                <div v-for="p in filteredDeletedPerms" :key="p.id"
                     class="flex items-center justify-between px-5 py-3 hover:bg-gray-50 dark:hover:bg-gray-800/40">
                    <div>
                        <span class="font-mono text-sm text-gray-500 dark:text-gray-400 line-through">{{ p.name }}</span>
                        <span class="ml-3 text-xs text-red-400">Supprimée le {{ p.deleted_at }}</span>
                    </div>
                    <button @click="restore(p)"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-medium
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

    <!-- Vue groupée actives -->
    <template v-else-if="viewMode === 'grouped'">
        <!-- Aucun résultat -->
        <div v-if="!activeModules.length"
             class="card p-10 text-center">
            <svg class="w-10 h-10 text-gray-300 dark:text-gray-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Aucune permission trouvée pour « {{ search }} »
            </p>
        </div>
        <div v-for="mod in activeModules" :key="mod" class="card overflow-hidden">
            <div class="px-5 py-3 bg-gray-50 dark:bg-gray-800/60 border-b border-gray-100 dark:border-gray-700
                        flex items-center justify-between">
                <span class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">{{ mod }}</span>
                <span class="text-xs text-gray-400 tabular-nums">{{ activeGrouped[mod].length }}</span>
            </div>
            <div class="p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-2">
                <div v-for="perm in activeGrouped[mod]" :key="perm.id"
                     class="group flex items-center gap-1.5 pl-3 pr-1.5 py-1.5 rounded-xl
                            border border-gray-200 dark:border-gray-600
                            bg-white dark:bg-gray-800 hover:border-primary-300 dark:hover:border-primary-600
                            transition-colors">
                    <span class="font-mono text-xs text-gray-700 dark:text-gray-300 truncate flex-1 min-w-0">{{ perm.name }}</span>
                    <div class="flex gap-0.5 flex-shrink-0 ml-1">
                        <button @click="openEdit(perm)" title="Modifier"
                                class="w-6 h-6 rounded-xl inline-flex items-center justify-center
                                       bg-amber-100 text-amber-600 hover:bg-amber-500 hover:text-white
                                       dark:bg-amber-900/30 dark:text-amber-400 dark:hover:bg-amber-500 dark:hover:text-white
                                       transition-all duration-150">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        <button @click="softDelete(perm)" title="Supprimer"
                                class="w-6 h-6 rounded-xl inline-flex items-center justify-center
                                       bg-red-100 text-red-600 hover:bg-red-500 hover:text-white
                                       dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-500 dark:hover:text-white
                                       transition-all duration-150">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <!-- Vue tableau actives -->
    <DataTable v-else
        :rows="filteredActivePerms"
        :columns="columns"
        :actions="activeActions"
        row-key="id"
        title="Toutes les permissions"
        export-filename="permissions"
        @action="handleAction"
    />

    <!-- Conseil nommage -->
    <div class="card p-4 flex items-start gap-3">
        <svg class="w-4 h-4 text-violet-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-xs text-gray-500 dark:text-gray-400">
            Convention recommandée :
            <code class="px-1.5 py-0.5 rounded bg-gray-100 dark:bg-gray-700 font-mono">module.action</code>
            — ex : <code class="font-mono">students.view</code>, <code class="font-mono">fees.manage</code>,
            <code class="font-mono">roles.delete</code>
        </p>
    </div>

    <!-- Modal création/édition -->
    <Teleport to="body">
        <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0"
                    enter-to-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4"
                 @click.self="closeModal">
                <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"/>
                <div class="relative w-full max-w-md bg-white dark:bg-gray-900 rounded-2xl shadow-2xl
                             border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="h-1 bg-gradient-to-r from-primary-500 to-primary-700"/>
                    <div class="p-6 space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ isEdit ? 'Modifier la permission' : 'Nouvelle permission' }}
                        </h3>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                Nom <span class="text-red-500">*</span>
                                <span class="ml-1 text-xs font-normal text-gray-400">format : module.action</span>
                            </label>
                            <input v-model="form.name" type="text" placeholder="ex: reports.export"
                                   class="w-full px-3 py-2.5 text-sm rounded-xl border font-mono
                                          bg-white dark:bg-gray-800 text-gray-900 dark:text-white
                                          focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-400
                                          transition-colors"
                                   :class="formErrors.name ? 'border-red-400' : 'border-gray-200 dark:border-gray-600'"
                                   @keyup.enter="submit"/>
                            <p v-if="formErrors.name" class="mt-1 text-xs text-red-600">{{ formErrors.name }}</p>
                        </div>
                        <div class="flex justify-end gap-2.5 pt-2 border-t border-gray-100 dark:border-gray-700">
                            <button @click="closeModal"
                                    class="px-4 py-2 text-sm font-medium rounded-xl border border-gray-200 dark:border-gray-600
                                           text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                Annuler
                            </button>
                            <button @click="submit" :disabled="submitting"
                                    class="px-4 py-2 text-sm font-semibold rounded-xl bg-primary-600 hover:bg-primary-700
                                           text-white disabled:opacity-50 transition-colors">
                                {{ isEdit ? 'Enregistrer' : 'Créer' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>

    <!-- Dialog de confirmation suppression -->
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
                        <div class="flex items-start gap-3 mb-4">
                            <div class="w-10 h-10 rounded-xl bg-red-100 dark:bg-red-900/30 flex items-center justify-center flex-shrink-0">
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
                                    class="px-4 py-2 text-sm font-medium rounded-xl border border-gray-200 dark:border-gray-600
                                           text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                Annuler
                            </button>
                            <button @click="doConfirm"
                                    class="px-4 py-2 text-sm font-semibold rounded-xl bg-red-600 hover:bg-red-700 text-white transition-colors">
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
