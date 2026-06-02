<script setup lang="ts">
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';

interface RoleItem {
    id: number;
    name: string;
    permissions: string[];
}
interface PermItem {
    id: number;
    name: string;
    module: string;
}

const props = defineProps<{
    roles: RoleItem[];
    permissions: PermItem[];
}>();

// ── Rôle sélectionné ──────────────────────────────────────────────────────
const selectedRoleId = ref<number | null>(props.roles[0]?.id ?? null);

const selectedRole = computed(() =>
    props.roles.find(r => r.id === selectedRoleId.value) ?? null
);

// Copie locale des permissions cochées pour ce rôle
const checkedPerms = ref<Set<string>>(
    new Set(selectedRole.value?.permissions ?? [])
);

const switchRole = (id: number) => {
    selectedRoleId.value = id;
    const role = props.roles.find(r => r.id === id);
    checkedPerms.value = new Set(role?.permissions ?? []);
    saved.value = false;
};

// ── Modules ────────────────────────────────────────────────────────────────
const modules = computed(() => {
    const map = new Map<string, PermItem[]>();
    for (const p of props.permissions) {
        if (!map.has(p.module)) map.set(p.module, []);
        map.get(p.module)!.push(p);
    }
    return map;
});

const isModuleChecked = (mod: string) => {
    const perms = modules.value.get(mod) ?? [];
    return perms.every(p => checkedPerms.value.has(p.name));
};
const isModuleIndeterminate = (mod: string) => {
    const perms = modules.value.get(mod) ?? [];
    const checked = perms.filter(p => checkedPerms.value.has(p.name));
    return checked.length > 0 && checked.length < perms.length;
};
const toggleModule = (mod: string) => {
    const perms = modules.value.get(mod) ?? [];
    if (isModuleChecked(mod)) {
        perms.forEach(p => checkedPerms.value.delete(p.name));
    } else {
        perms.forEach(p => checkedPerms.value.add(p.name));
    }
};
const togglePerm = (name: string) => {
    if (checkedPerms.value.has(name)) checkedPerms.value.delete(name);
    else checkedPerms.value.add(name);
};

// ── Sélection globale ─────────────────────────────────────────────────────
const allChecked = computed(() =>
    props.permissions.every(p => checkedPerms.value.has(p.name))
);
const toggleAll = () => {
    if (allChecked.value) checkedPerms.value = new Set();
    else checkedPerms.value = new Set(props.permissions.map(p => p.name));
};

// ── Sauvegarde ─────────────────────────────────────────────────────────────
const saving = ref(false);
const saved  = ref(false);
const PROTECTED = 'super_admin';

const save = () => {
    if (!selectedRoleId.value) return;
    if (selectedRole.value?.name === PROTECTED) return;
    saving.value = true;
    saved.value  = false;
    router.post(
        `/superadmin/config/assign/${selectedRoleId.value}/sync`,
        { permissions: Array.from(checkedPerms.value) },
        {
            preserveScroll: true,
            onSuccess: () => { saved.value = true; saving.value = false; },
            onError:   () => { saving.value = false; },
        }
    );
};

const checkedCount = computed(() => checkedPerms.value.size);
</script>

<template>
    <div class="space-y-6">
        <!-- En-tête -->
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Attribuer des permissions</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                Sélectionnez un rôle puis cochez les permissions à lui accorder
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-5">

            <!-- ── Colonne rôles ───────────────────────────────────────────── -->
            <div class="lg:col-span-1">
                <div class="card overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Rôles</h2>
                    </div>
                    <nav class="py-1">
                        <button v-for="role in roles" :key="role.id"
                                @click="switchRole(role.id)"
                                :class="[
                                    'w-full flex items-center justify-between px-4 py-2.5 text-sm transition-colors text-left',
                                    selectedRoleId === role.id
                                        ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 font-medium'
                                        : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50',
                                ]">
                            <span>{{ role.name }}</span>
                            <span class="text-xs tabular-nums"
                                  :class="selectedRoleId === role.id ? 'text-primary-500' : 'text-gray-400 dark:text-gray-500'">
                                {{ role.permissions.length }}
                            </span>
                        </button>
                    </nav>
                </div>
            </div>

            <!-- ── Colonne permissions ────────────────────────────────────── -->
            <div class="lg:col-span-3 space-y-4">

                <!-- Barre d'action -->
                <div class="flex items-center justify-between gap-3 flex-wrap">
                    <div class="flex items-center gap-3">
                        <!-- Tout sélectionner -->
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox"
                                   :checked="allChecked"
                                   class="w-4 h-4 rounded border-gray-300 dark:border-gray-600"
                                   style="accent-color:#7c3aed"
                                   @change="toggleAll"/>
                            <span class="text-sm text-gray-700 dark:text-gray-300">Tout sélectionner</span>
                        </label>
                        <span class="text-xs text-gray-400 dark:text-gray-500 tabular-nums">
                            {{ checkedCount }} / {{ permissions.length }} sélectionnées
                        </span>
                    </div>

                    <div class="flex items-center gap-2">
                        <Transition enter-active-class="transition duration-200" enter-from-class="opacity-0 scale-95"
                                    enter-to-class="opacity-100 scale-100" leave-to-class="opacity-0">
                            <span v-if="saved"
                                  class="inline-flex items-center gap-1.5 text-sm text-emerald-600 dark:text-emerald-400 font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Enregistré
                            </span>
                        </Transition>

                        <button @click="save"
                                :disabled="saving || selectedRole?.name === 'super_admin'"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium
                                       transition-colors shadow-sm disabled:opacity-50 disabled:cursor-not-allowed"
                                :class="selectedRole?.name === 'super_admin'
                                    ? 'bg-gray-100 text-gray-400 dark:bg-gray-800 dark:text-gray-500'
                                    : 'bg-primary-600 hover:bg-primary-700 text-white'">
                            <svg v-if="saving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ saving ? 'Sauvegarde...' : 'Enregistrer' }}
                        </button>
                    </div>
                </div>

                <!-- Avertissement super_admin -->
                <div v-if="selectedRole?.name === 'super_admin'"
                     class="flex items-center gap-3 p-3 rounded-xl bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-700/40">
                    <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                    <p class="text-sm text-red-700 dark:text-red-400">
                        Le rôle <strong>super_admin</strong> possède toutes les permissions et ne peut pas être modifié.
                    </p>
                </div>

                <!-- Grid des modules / permissions -->
                <div class="space-y-3">
                    <div v-for="[mod, perms] in modules" :key="mod"
                         class="card overflow-hidden">
                        <!-- Header module -->
                        <label class="flex items-center gap-3 px-5 py-3
                                      bg-gray-50 dark:bg-gray-800/60
                                      border-b border-gray-100 dark:border-gray-700
                                      cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700/60 transition-colors">
                            <input type="checkbox"
                                   :checked="isModuleChecked(mod)"
                                   :indeterminate="isModuleIndeterminate(mod)"
                                   :disabled="selectedRole?.name === 'super_admin'"
                                   class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 cursor-pointer"
                                   style="accent-color:#7c3aed"
                                   @change="toggleModule(mod)"/>
                            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider flex-1">
                                {{ mod }}
                            </span>
                            <span class="text-xs text-gray-400 dark:text-gray-500 tabular-nums">
                                {{ perms.filter(p => checkedPerms.has(p.name)).length }} / {{ perms.length }}
                            </span>
                        </label>

                        <!-- Permissions du module -->
                        <div class="p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                            <label v-for="perm in perms" :key="perm.id"
                                   class="inline-flex items-center gap-2.5 px-3 py-2 rounded-lg cursor-pointer
                                          border transition-colors"
                                   :class="checkedPerms.has(perm.name)
                                       ? 'border-primary-300 dark:border-primary-600 bg-primary-50/60 dark:bg-primary-900/15'
                                       : 'border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500'">
                                <input type="checkbox"
                                       :checked="checkedPerms.has(perm.name)"
                                       :disabled="selectedRole?.name === 'super_admin'"
                                       class="w-3.5 h-3.5 rounded border-gray-300 dark:border-gray-600 flex-shrink-0"
                                       style="accent-color:#7c3aed"
                                       @change="togglePerm(perm.name)"/>
                                <span class="text-xs font-mono truncate"
                                      :class="checkedPerms.has(perm.name)
                                          ? 'text-primary-700 dark:text-primary-300'
                                          : 'text-gray-600 dark:text-gray-400'">
                                    {{ perm.name }}
                                </span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
