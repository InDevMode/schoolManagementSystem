<script setup lang="ts">
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import DataTable from '@/Components/UI/DataTable.vue';
import type { DtColumn, DtAction } from '@/Components/UI/DataTable.vue';

interface Permission {
    id: number;
    name: string;
    module: string;
    guard_name: string;
    created_at: string;
}

const props = defineProps<{
    permissions: Permission[];
    grouped: Record<string, Permission[]>;
}>();

// ── Modal ──────────────────────────────────────────────────────────────────
const showModal = ref(false);
const isEdit    = ref(false);
const form      = ref({ id: 0, name: '' });
const error     = ref('');

const openCreate = () => {
    isEdit.value = false;
    form.value   = { id: 0, name: '' };
    error.value  = '';
    showModal.value = true;
};

const openEdit = (p: Permission) => {
    isEdit.value = true;
    form.value   = { id: p.id, name: p.name };
    error.value  = '';
    showModal.value = true;
};

const closeModal = () => { showModal.value = false; error.value = ''; };

const submit = () => {
    if (!form.value.name.trim()) { error.value = 'Le nom est requis.'; return; }
    const url = isEdit.value
        ? `/superadmin/config/permissions/edit/${form.value.id}`
        : '/superadmin/config/permissions/add';
    router.post(url, { name: form.value.name }, {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => { error.value = 'Erreur. Vérifiez que le nom est unique.'; },
    });
};

// ── Vue active : tableau ou groupé ────────────────────────────────────────
const viewMode = ref<'table'|'grouped'>('grouped');

// ── DataTable ──────────────────────────────────────────────────────────────
const columns: DtColumn[] = [
    { key: 'module',     label: 'Module',      sortable: true },
    { key: 'name',       label: 'Permission',  sortable: true },
    { key: 'created_at', label: 'Créée le',    sortable: true },
];

const actions: DtAction[] = [
    { key: 'edit',   label: 'Modifier',  variant: 'warning' },
    { key: 'delete', label: 'Supprimer', variant: 'danger', confirm: (row) => `Supprimer « ${row.name} » ?` },
];

const handleAction = (key: string, row: Record<string, unknown>) => {
    if (key === 'edit')   openEdit(row as unknown as Permission);
    if (key === 'delete') router.get(`/superadmin/config/permissions/delete/${row.id}`, {}, { preserveScroll: true });
};

const modules = computed(() => Object.keys(props.grouped).sort());
</script>

<template>
    <div class="space-y-6">
        <!-- En-tête -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Permissions</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    {{ permissions.length }} permission{{ permissions.length > 1 ? 's' : '' }} au total
                </p>
            </div>
            <div class="flex items-center gap-2">
                <!-- Toggle vue -->
                <div class="flex items-center rounded-lg border border-gray-200 dark:border-gray-600 overflow-hidden">
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
                <button @click="openCreate"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary-600 hover:bg-primary-700
                               text-white text-sm font-medium transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nouvelle permission
                </button>
            </div>
        </div>

        <!-- Vue groupée par module -->
        <div v-if="viewMode === 'grouped'" class="space-y-4">
            <div v-for="mod in modules" :key="mod"
                 class="card overflow-hidden">
                <!-- Header module -->
                <div class="px-5 py-3 bg-gray-50 dark:bg-gray-800/60 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">{{ mod }}</span>
                    <span class="text-xs text-gray-400 dark:text-gray-500 tabular-nums">
                        {{ grouped[mod].length }} permission{{ grouped[mod].length > 1 ? 's' : '' }}
                    </span>
                </div>
                <!-- Permissions du module -->
                <div class="p-4 flex flex-wrap gap-2">
                    <div v-for="perm in grouped[mod]" :key="perm.id"
                         class="group inline-flex items-center gap-1.5 pl-3 pr-1.5 py-1.5 rounded-lg
                                border border-gray-200 dark:border-gray-600
                                bg-white dark:bg-gray-800
                                hover:border-primary-300 dark:hover:border-primary-600
                                transition-colors text-sm">
                        <span class="text-gray-700 dark:text-gray-300">{{ perm.name }}</span>
                        <div class="flex items-center gap-0.5 ml-1">
                            <button @click="openEdit(perm)" title="Modifier"
                                    class="p-1 rounded text-gray-400 hover:text-amber-500 hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <button @click="router.get(`/superadmin/config/permissions/delete/${perm.id}`, {}, { preserveScroll: true })"
                                    title="Supprimer"
                                    class="p-1 rounded text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vue tableau -->
        <DataTable v-else
            :rows="permissions"
            :columns="columns"
            :actions="actions"
            row-key="id"
            title="Toutes les permissions"
            export-filename="permissions"
            @action="handleAction"
        />

        <!-- Conseil de nommage -->
        <div class="card p-4 flex items-start gap-3">
            <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Convention de nommage</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    Utilisez le format <code class="px-1.5 py-0.5 rounded bg-gray-100 dark:bg-gray-700 font-mono">module.action</code>.
                    Exemples : <code class="font-mono">students.view</code>, <code class="font-mono">fees.manage</code>, <code class="font-mono">roles.delete</code>
                </p>
            </div>
        </div>

        <!-- Modal -->
        <Teleport to="body">
            <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0"
                        enter-to-class="opacity-100" leave-active-class="transition duration-150 ease-in"
                        leave-from-class="opacity-100" leave-to-class="opacity-0">
                <div v-if="showModal"
                     class="fixed inset-0 z-50 flex items-center justify-center p-4"
                     @click.self="closeModal">
                    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"/>
                    <div class="relative w-full max-w-md bg-white dark:bg-gray-900
                                rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            {{ isEdit ? 'Modifier la permission' : 'Nouvelle permission' }}
                        </h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    Nom de la permission
                                </label>
                                <input v-model="form.name" type="text" placeholder="ex: reports.export"
                                       class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 dark:border-gray-600
                                              bg-white dark:bg-gray-800 text-gray-900 dark:text-white
                                              focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-400
                                              transition-colors font-mono"
                                       @keyup.enter="submit"/>
                                <p v-if="error" class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ error }}</p>
                            </div>
                            <div class="flex justify-end gap-2.5 pt-2">
                                <button @click="closeModal"
                                        class="px-4 py-2 text-sm font-medium rounded-lg border border-gray-200 dark:border-gray-600
                                               text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    Annuler
                                </button>
                                <button @click="submit"
                                        class="px-4 py-2 text-sm font-semibold rounded-lg bg-primary-600 hover:bg-primary-700 text-white transition-colors">
                                    {{ isEdit ? 'Enregistrer' : 'Créer' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>
