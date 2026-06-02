<script setup lang="ts">
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import DataTable from '@/Components/UI/DataTable.vue';
import type { DtColumn, DtAction } from '@/Components/UI/DataTable.vue';

interface Role {
    id: number;
    name: string;
    guard_name: string;
    user_type: number | null;
    description: string | null;
    permissions_count: number;
    created_at: string;
}

const props = defineProps<{
    roles: Role[];
    usedUserTypes: number[];
}>();

// ── Rôles système (user_type 0-4) — protégés ─────────────────────────────
const isSystem = (role: Role) => role.user_type !== null && role.user_type <= 4;

const SYSTEM_LABELS: Record<number, string> = {
    0: 'Super Admin', 1: 'Admin', 2: 'Professeur', 3: 'Apprenant', 4: 'Parent',
};

// ── Modal ──────────────────────────────────────────────────────────────────
const showModal  = ref(false);
const isEdit     = ref(false);
const submitting = ref(false);
const errors     = ref<Record<string, string>>({});

const emptyForm = () => ({ id: 0, name: '', user_type: '', description: '' });
const form = ref(emptyForm());

const openCreate = () => {
    isEdit.value = false;
    form.value   = emptyForm();
    errors.value = {};
    showModal.value = true;
};

const openEdit = (role: Role) => {
    if (isSystem(role)) return;
    isEdit.value = true;
    form.value   = {
        id:          role.id,
        name:        role.name,
        user_type:   role.user_type !== null ? String(role.user_type) : '',
        description: role.description ?? '',
    };
    errors.value = {};
    showModal.value = true;
};

const closeModal = () => { showModal.value = false; errors.value = {}; };

const validate = () => {
    errors.value = {};
    if (!form.value.name.trim())    errors.value.name = 'Le nom est requis.';
    if (!form.value.user_type)      errors.value.user_type = 'Le user_type est requis.';
    const ut = parseInt(form.value.user_type as string);
    if (isNaN(ut) || ut < 5)        errors.value.user_type = 'Le user_type doit être ≥ 5 (les valeurs 0–4 sont réservées aux rôles système).';
    return Object.keys(errors.value).length === 0;
};

const submit = () => {
    if (!validate()) return;
    submitting.value = true;
    const url = isEdit.value
        ? `/superadmin/config/roles/edit/${form.value.id}`
        : '/superadmin/config/roles/add';

    router.post(url, {
        name:        form.value.name.trim(),
        user_type:   parseInt(form.value.user_type as string),
        description: form.value.description?.trim() || null,
    }, {
        preserveScroll: true,
        onSuccess: () => { closeModal(); submitting.value = false; },
        onError:   (e) => { errors.value = e as Record<string, string>; submitting.value = false; },
        onFinish:  () => { submitting.value = false; },
    });
};

// ── Suggestion user_type — prochain disponible ≥ 5 ────────────────────────
const nextAvailableUserType = computed(() => {
    const used = new Set(props.usedUserTypes);
    let n = 5;
    while (used.has(n)) n++;
    return n;
});

// ── DataTable ──────────────────────────────────────────────────────────────
const columns: DtColumn[] = [
    { key: 'name',              label: 'Nom du rôle',   sortable: true },
    { key: 'user_type',         label: 'user_type',     sortable: true, align: 'center' },
    { key: 'description',       label: 'Description',   sortable: false },
    { key: 'permissions_count', label: 'Permissions',   sortable: true, align: 'center', dataType: 'number' },
    { key: 'created_at',        label: 'Créé le',       sortable: true },
];

const actions: DtAction[] = [
    {
        key: 'edit', label: 'Modifier', variant: 'warning',
        condition: (row) => !isSystem(row as unknown as Role),
    },
    {
        key: 'delete', label: 'Supprimer', variant: 'danger',
        confirm: (row) => `Supprimer le rôle « ${row.name} » ? Les utilisateurs avec ce rôle le perdront.`,
        condition: (row) => !isSystem(row as unknown as Role),
    },
];

const handleAction = (key: string, row: Record<string, unknown>) => {
    if (key === 'edit')   openEdit(row as unknown as Role);
    if (key === 'delete') router.get(`/superadmin/config/roles/delete/${row.id}`, {}, { preserveScroll: true });
};
</script>

<template>
    <div class="space-y-6">

        <!-- En-tête -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Rôles</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    Gérez les rôles et leur <code class="px-1.5 py-0.5 rounded bg-gray-100 dark:bg-gray-700 text-xs font-mono">user_type</code> associé
                </p>
            </div>
            <button @click="openCreate"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary-600 hover:bg-primary-700
                           text-white text-sm font-medium transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nouveau rôle
            </button>
        </div>

        <!-- Info rôles système -->
        <div class="flex items-start gap-3 p-4 rounded-xl bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-700/40">
            <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div class="text-sm text-amber-700 dark:text-amber-400">
                <p>Les rôles avec <code class="font-mono px-1 bg-amber-100 dark:bg-amber-800/30 rounded">user_type 0–4</code> sont des rôles système protégés.</p>
                <p class="mt-0.5">Les rôles custom doivent avoir un <code class="font-mono px-1 bg-amber-100 dark:bg-amber-800/30 rounded">user_type ≥ 5</code>.
                    Prochain disponible : <strong>{{ nextAvailableUserType }}</strong></p>
            </div>
        </div>

        <!-- Tableau -->
        <DataTable
            :rows="roles"
            :columns="columns"
            :actions="actions"
            row-key="id"
            title="Liste des rôles"
            export-filename="roles"
            @action="handleAction"
        >
            <!-- Colonne name : badge système -->
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

            <!-- Colonne user_type : badge coloré -->
            <template #cell-user_type="{ row }">
                <span v-if="row.user_type !== null && row.user_type !== undefined"
                      :class="[
                          'inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold tabular-nums',
                          (row.user_type as number) <= 4
                              ? 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300'
                              : 'bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-300',
                      ]">
                    {{ row.user_type }}
                </span>
                <span v-else class="text-gray-300 dark:text-gray-600 text-xs">—</span>
            </template>

            <!-- Colonne description -->
            <template #cell-description="{ row }">
                <span class="text-gray-500 dark:text-gray-400 text-sm">
                    {{ row.description || '—' }}
                </span>
            </template>
        </DataTable>

        <!-- ══ MODAL CRÉATION / ÉDITION ══════════════════════════════════════ -->
        <Teleport to="body">
            <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0"
                        enter-to-class="opacity-100" leave-active-class="transition duration-150 ease-in"
                        leave-from-class="opacity-100" leave-to-class="opacity-0">
                <div v-if="showModal"
                     class="fixed inset-0 z-50 flex items-center justify-center p-4"
                     @click.self="closeModal">
                    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"/>
                    <div class="relative w-full max-w-lg bg-white dark:bg-gray-900
                                rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700
                                overflow-hidden">

                        <!-- Barre colorée -->
                        <div class="h-1 bg-gradient-to-r from-primary-500 to-primary-700"/>

                        <div class="p-6 space-y-5">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ isEdit ? 'Modifier le rôle' : 'Créer un rôle custom' }}
                            </h3>

                            <!-- Nom du rôle -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    Nom du rôle <span class="text-red-500">*</span>
                                </label>
                                <input v-model="form.name" type="text"
                                       placeholder="ex: comptable, délégué, coordinateur..."
                                       class="w-full px-3 py-2.5 text-sm rounded-lg border
                                              bg-white dark:bg-gray-800 text-gray-900 dark:text-white
                                              focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-400
                                              transition-colors"
                                       :class="errors.name ? 'border-red-400 dark:border-red-500' : 'border-gray-200 dark:border-gray-600'"
                                       @keyup.enter="submit"/>
                                <p v-if="errors.name" class="mt-1 text-xs text-red-600 dark:text-red-400">{{ errors.name }}</p>
                            </div>

                            <!-- user_type -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    user_type <span class="text-red-500">*</span>
                                    <span class="ml-2 text-xs font-normal text-gray-400">
                                        (doit être ≥ 5, unique par rôle)
                                    </span>
                                </label>
                                <div class="flex gap-2">
                                    <input v-model="form.user_type" type="number" min="5"
                                           :placeholder="`ex: ${nextAvailableUserType}`"
                                           class="flex-1 px-3 py-2.5 text-sm rounded-lg border font-mono
                                                  bg-white dark:bg-gray-800 text-gray-900 dark:text-white
                                                  focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-400
                                                  transition-colors"
                                           :class="errors.user_type ? 'border-red-400 dark:border-red-500' : 'border-gray-200 dark:border-gray-600'"/>
                                    <!-- Bouton suggestion -->
                                    <button type="button"
                                            class="px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-600
                                                   bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-400
                                                   hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:border-primary-300
                                                   hover:text-primary-700 dark:hover:text-primary-400 transition-colors whitespace-nowrap"
                                            @click="form.user_type = String(nextAvailableUserType)"
                                            :title="`Utiliser le prochain disponible : ${nextAvailableUserType}`">
                                        Suggérer ({{ nextAvailableUserType }})
                                    </button>
                                </div>
                                <p v-if="errors.user_type" class="mt-1 text-xs text-red-600 dark:text-red-400">{{ errors.user_type }}</p>

                                <!-- Aperçu des user_type déjà pris -->
                                <div v-if="usedUserTypes.length" class="mt-2 flex flex-wrap gap-1.5">
                                    <span class="text-xs text-gray-400 dark:text-gray-500 mr-1">Déjà utilisés :</span>
                                    <span v-for="ut in usedUserTypes" :key="ut"
                                          class="inline-flex items-center px-2 py-0.5 rounded text-xs font-mono
                                                 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                                        {{ ut }}
                                    </span>
                                </div>
                            </div>

                            <!-- Description -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    Description
                                    <span class="ml-1 text-xs font-normal text-gray-400">(optionnelle)</span>
                                </label>
                                <input v-model="form.description" type="text"
                                       placeholder="ex: Gestion de la comptabilité, accès aux frais..."
                                       class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 dark:border-gray-600
                                              bg-white dark:bg-gray-800 text-gray-900 dark:text-white
                                              focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-400
                                              transition-colors"/>
                                <p v-if="errors.description" class="mt-1 text-xs text-red-600 dark:text-red-400">{{ errors.description }}</p>
                            </div>

                            <!-- Info résumé -->
                            <div v-if="form.name && form.user_type"
                                 class="flex items-center gap-3 p-3 rounded-xl bg-primary-50 dark:bg-primary-900/10
                                        border border-primary-200 dark:border-primary-700/40">
                                <svg class="w-4 h-4 text-primary-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-xs text-primary-700 dark:text-primary-300">
                                    Les utilisateurs créés avec
                                    <code class="font-mono font-bold">user_type = {{ form.user_type }}</code>
                                    auront automatiquement le rôle <strong>{{ form.name }}</strong>
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
    </div>
</template>
