<script setup lang="ts">
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';

interface RoleItem  { id: number; name: string; user_type: number | null; description: string | null; permissions: string[]; }
interface UserItem  { id: number; name: string; last_name: string; email: string; user_type: number;
                      roles: string[]; direct_perms: string[]; role_perms: string[]; all_perms: string[]; profile_picture: string | null; }
interface PermItem  { id: number; name: string; module: string; }

const props = defineProps<{ roles: RoleItem[]; users: UserItem[]; permissions: PermItem[]; }>();

// ── Onglets ────────────────────────────────────────────────────────────────
const tab = ref<'role' | 'user'>('role');

const switchTab = (t: 'role' | 'user') => {
    tab.value = t;
    permSearch.value = '';
};

// ── Recherche de permissions ──────────────────────────────────────────────
const permSearch = ref('');

// ── Permissions groupées par module ───────────────────────────────────────
const filteredPermissions = computed(() => {
    const q = permSearch.value.toLowerCase().trim();
    if (!q) return props.permissions;
    return props.permissions.filter(p => p.name.toLowerCase().includes(q) || p.module.toLowerCase().includes(q));
});

const modules = computed(() => {
    const map = new Map<string, PermItem[]>();
    for (const p of filteredPermissions.value) {
        if (!map.has(p.module)) map.set(p.module, []);
        map.get(p.module)!.push(p);
    }
    return map;
});

// ════════════════════════════════════════════════════════════════════════════
// SECTION RÔLES
// ════════════════════════════════════════════════════════════════════════════
const selectedRoleId  = ref<number | null>(props.roles[0]?.id ?? null);
const selectedRole    = computed(() => props.roles.find(r => r.id === selectedRoleId.value) ?? null);
const rolePerms       = ref<Set<string>>(new Set(selectedRole.value?.permissions ?? []));
const roleSaved       = ref(false);
const roleSaving      = ref(false);

const switchRole = (id: number) => {
    selectedRoleId.value = id;
    const r = props.roles.find(r => r.id === id);
    rolePerms.value = new Set(r?.permissions ?? []);
    roleSaved.value = false;
};
const toggleRolePerm = (name: string) => {
    if (rolePerms.value.has(name)) rolePerms.value.delete(name);
    else rolePerms.value.add(name);
};
const toggleRoleModule = (mod: string) => {
    const perms = modules.value.get(mod) ?? [];
    if (perms.every(p => rolePerms.value.has(p.name))) perms.forEach(p => rolePerms.value.delete(p.name));
    else perms.forEach(p => rolePerms.value.add(p.name));
};
const isRoleModuleChecked = (mod: string) => (modules.value.get(mod) ?? []).every(p => rolePerms.value.has(p.name));
const isRoleModuleIndet   = (mod: string) => { const ps = modules.value.get(mod) ?? []; const c = ps.filter(p => rolePerms.value.has(p.name)); return c.length > 0 && c.length < ps.length; };
const allRolePermsChecked = computed(() => props.permissions.every(p => rolePerms.value.has(p.name)));
const toggleAllRolePerms  = () => {
    if (allRolePermsChecked.value) rolePerms.value = new Set();
    else rolePerms.value = new Set(props.permissions.map(p => p.name));
};
const roleCheckedCount = computed(() => rolePerms.value.size);

const saveRole = () => {
    if (!selectedRoleId.value || selectedRole.value?.name === 'super_admin') return;
    roleSaving.value = true;
    router.post(`/superadmin/config/assign/role/${selectedRoleId.value}/sync`,
        { permissions: Array.from(rolePerms.value) },
        { preserveScroll: true,
          onSuccess: () => { roleSaved.value = true; roleSaving.value = false; },
          onError:   () => { roleSaving.value = false; } }
    );
};

// ════════════════════════════════════════════════════════════════════════════
// SECTION UTILISATEURS
// ════════════════════════════════════════════════════════════════════════════
const userSearch      = ref('');
const selectedUserId  = ref<number | null>(null);
const selectedUser    = computed(() => props.users.find(u => u.id === selectedUserId.value) ?? null);
const userDirectPerms = ref<Set<string>>(new Set());
const userSaved       = ref(false);
const userSaving      = ref(false);

const filteredUsers = computed(() => {
    const q = userSearch.value.toLowerCase().trim();
    if (!q) return props.users;
    return props.users.filter(u =>
        u.name.toLowerCase().includes(q) ||
        u.last_name.toLowerCase().includes(q) ||
        u.email.toLowerCase().includes(q)
    );
});

const switchUser = (id: number) => {
    selectedUserId.value = id;
    const u = props.users.find(u => u.id === id);
    userDirectPerms.value = new Set(u?.direct_perms ?? []);
    userSaved.value = false;
};

// Est-ce que la permission vient du rôle (héritée) ?
const isFromRole = (name: string) => selectedUser.value?.role_perms.includes(name) ?? false;

const toggleUserPerm = (name: string) => {
    // On ne peut pas modifier une permission héritée via le rôle depuis ici
    if (isFromRole(name)) return;
    if (userDirectPerms.value.has(name)) userDirectPerms.value.delete(name);
    else userDirectPerms.value.add(name);
};

const toggleUserModule = (mod: string) => {
    const perms = (modules.value.get(mod) ?? []).filter(p => !isFromRole(p.name));
    if (perms.every(p => userDirectPerms.value.has(p.name))) perms.forEach(p => userDirectPerms.value.delete(p.name));
    else perms.forEach(p => userDirectPerms.value.add(p.name));
};

const isUserModuleChecked = (mod: string) => {
    const ps = modules.value.get(mod) ?? [];
    return ps.every(p => isFromRole(p.name) || userDirectPerms.value.has(p.name));
};
const isUserModuleIndet = (mod: string) => {
    const ps = modules.value.get(mod) ?? [];
    const checked = ps.filter(p => isFromRole(p.name) || userDirectPerms.value.has(p.name));
    return checked.length > 0 && checked.length < ps.length;
};

const userDirectCount  = computed(() => userDirectPerms.value.size);
const userInheritCount = computed(() => selectedUser.value?.role_perms.length ?? 0);

const saveUser = () => {
    if (!selectedUserId.value) return;
    userSaving.value = true;
    router.post(`/superadmin/config/assign/user/${selectedUserId.value}/sync`,
        { permissions: Array.from(userDirectPerms.value) },
        { preserveScroll: true,
          onSuccess: () => { userSaved.value = true; userSaving.value = false; },
          onError:   () => { userSaving.value = false; } }
    );
};

const userTypeLabel = (ut: number) => {
    const map: Record<number, string> = { 0:'Super Admin', 1:'Admin', 2:'Professeur', 3:'Apprenant', 4:'Parent' };
    return map[ut] ?? `Rôle custom (${ut})`;
};
const avatarUrl = (u: UserItem) => u.profile_picture ? `/upload/profile/${u.profile_picture}` : '/upload/default.jpg';
</script>

<template>
<div class="space-y-5">

    <!-- En-tête -->
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-violet-500 to-purple-700 flex items-center justify-center shadow-lg shadow-violet-500/30 flex-shrink-0">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Attribuer des permissions</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Par rôle ou directement à un utilisateur</p>
        </div>
    </div>

    <!-- Onglets -->
    <div class="flex items-center gap-1 p-1 bg-gray-100 dark:bg-gray-800 rounded-lg w-fit">
        <button @click="switchTab('role')"
                :class="['px-5 py-2 text-sm font-medium rounded-lg transition-all',
                         tab === 'role' ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm' :
                                          'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300']">
            <svg class="w-4 h-4 inline mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            Par rôle
            <span class="ml-1.5 px-1.5 py-0.5 rounded-full text-xs bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-300">{{ roles.length }}</span>
        </button>
        <button @click="switchTab('user')"
                :class="['px-5 py-2 text-sm font-medium rounded-lg transition-all',
                         tab === 'user' ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm' :
                                          'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300']">
            <svg class="w-4 h-4 inline mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            Par utilisateur
            <span class="ml-1.5 px-1.5 py-0.5 rounded-full text-xs bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-300">{{ users.length }}</span>
        </button>
    </div>

    <!-- ════════════════ ONGLET RÔLES ════════════════ -->
    <div v-if="tab === 'role'" class="grid grid-cols-1 lg:grid-cols-4 gap-5">

        <!-- Liste des rôles -->
        <div class="lg:col-span-1 card overflow-hidden h-fit">
            <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Rôles</h2>
            </div>
            <nav class="py-1">
                <button v-for="role in roles" :key="role.id" @click="switchRole(role.id)"
                        :class="['w-full flex items-center justify-between px-4 py-2.5 text-sm transition-colors text-left',
                                 selectedRoleId === role.id
                                     ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 font-medium'
                                     : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50']">
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="truncate">{{ role.name }}</span>
                        <span v-if="role.user_type !== null"
                              class="flex-shrink-0 text-[10px] font-mono px-1.5 py-0.5 rounded
                                     bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                            #{{ role.user_type }}
                        </span>
                    </div>
                    <span class="text-xs tabular-nums flex-shrink-0 ml-2"
                          :class="selectedRoleId === role.id ? 'text-primary-500' : 'text-gray-400 dark:text-gray-500'">
                        {{ role.permissions.length }}
                    </span>
                </button>
            </nav>
        </div>

        <!-- Permissions du rôle -->
        <div class="lg:col-span-3 space-y-4">
            <!-- Barre d'action -->
            <div class="flex items-center justify-between gap-3 flex-wrap">
                <div class="flex items-center gap-3">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" :checked="allRolePermsChecked"
                               class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 cursor-pointer"
                               style="accent-color:#7B74F0"
                               :disabled="selectedRole?.name === 'super_admin'"
                               @change="toggleAllRolePerms"/>
                        <span class="text-sm text-gray-700 dark:text-gray-300">Tout sélectionner</span>
                    </label>
                    <span class="text-xs text-gray-400 tabular-nums">
                        {{ roleCheckedCount }} / {{ permissions.length }}
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <Transition enter-active-class="transition duration-150" enter-from-class="opacity-0"
                                enter-to-class="opacity-100" leave-to-class="opacity-0">
                        <span v-if="roleSaved" class="text-sm text-emerald-600 dark:text-emerald-400 font-medium flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Enregistré
                        </span>
                    </Transition>
                    <button @click="saveRole" :disabled="roleSaving || selectedRole?.name === 'super_admin'"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors disabled:opacity-50"
                            :class="selectedRole?.name === 'super_admin'
                                ? 'bg-gray-100 text-gray-400 dark:bg-gray-800 dark:text-gray-500 cursor-not-allowed'
                                : 'bg-primary-600 hover:bg-primary-700 text-white'">
                        <svg v-if="roleSaving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        {{ roleSaving ? 'Sauvegarde...' : 'Enregistrer' }}
                    </button>
                </div>
            </div>

            <!-- Warning super_admin -->
            <div v-if="selectedRole?.name === 'super_admin'"
                 class="flex items-center gap-3 p-3 rounded-lg bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-700/40">
                <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                </svg>
                <p class="text-sm text-red-700 dark:text-red-400">
                    <strong>super_admin</strong> possède toutes les permissions et ne peut pas être modifié.
                </p>
            </div>

            <!-- Recherche permissions -->
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input v-model="permSearch" type="text" placeholder="Rechercher une permission..."
                       class="w-full pl-9 pr-4 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-600
                              bg-white dark:bg-gray-800 text-gray-900 dark:text-white
                              focus:outline-none focus:ring-2 focus:ring-primary-500/40 transition-colors
                              placeholder-gray-400 dark:placeholder-gray-500"/>
                <button v-if="permSearch" @click="permSearch = ''"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Aucun résultat -->
            <div v-if="modules.size === 0" class="card p-8 text-center">
                <svg class="w-10 h-10 text-gray-300 dark:text-gray-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <p class="text-sm text-gray-500 dark:text-gray-400">Aucune permission trouvée pour « {{ permSearch }} »</p>
            </div>

            <!-- Modules / permissions -->
            <div v-for="[mod, perms] in modules" :key="mod" class="card overflow-hidden">
                <label class="flex items-center gap-3 px-5 py-3
                              bg-gray-50 dark:bg-gray-800/60 border-b border-gray-100 dark:border-gray-700
                              cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700/60 transition-colors">
                    <input type="checkbox" :checked="isRoleModuleChecked(mod)" :indeterminate="isRoleModuleIndet(mod)"
                           :disabled="selectedRole?.name === 'super_admin'"
                           class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 cursor-pointer"
                           style="accent-color:#7B74F0"
                           @change="toggleRoleModule(mod)"/>
                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider flex-1">{{ mod }}</span>
                    <span class="text-xs text-gray-400 tabular-nums">
                        {{ perms.filter(p => rolePerms.has(p.name)).length }} / {{ perms.length }}
                    </span>
                </label>
                <div class="p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                    <label v-for="perm in perms" :key="perm.id"
                           class="inline-flex items-center gap-2.5 px-3 py-2 rounded-lg cursor-pointer border transition-colors"
                           :class="rolePerms.has(perm.name)
                               ? 'border-primary-300 dark:border-primary-600 bg-primary-50/60 dark:bg-primary-900/15'
                               : 'border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500'">
                        <input type="checkbox" :checked="rolePerms.has(perm.name)"
                               :disabled="selectedRole?.name === 'super_admin'"
                               class="w-3.5 h-3.5 rounded flex-shrink-0 cursor-pointer"
                               style="accent-color:#7B74F0"
                               @change="toggleRolePerm(perm.name)"/>
                        <span class="text-xs font-mono truncate"
                              :class="rolePerms.has(perm.name)
                                  ? 'text-primary-700 dark:text-primary-300'
                                  : 'text-gray-600 dark:text-gray-400'">
                            {{ perm.name }}
                        </span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- ════════════════ ONGLET UTILISATEURS ════════════════ -->
    <div v-else class="grid grid-cols-1 lg:grid-cols-4 gap-5">

        <!-- Liste des utilisateurs -->
        <div class="lg:col-span-1 card overflow-hidden h-fit">
            <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Utilisateurs</h2>
                <input v-model="userSearch" type="text" placeholder="Rechercher..."
                       class="w-full px-3 py-1.5 text-sm rounded-lg border border-gray-200 dark:border-gray-600
                              bg-white dark:bg-gray-800 text-gray-900 dark:text-white
                              focus:outline-none focus:ring-2 focus:ring-primary-500/40 transition-colors
                              placeholder-gray-400 dark:placeholder-gray-500"/>
            </div>
            <nav class="py-1 max-h-[60vh] overflow-y-auto">
                <button v-for="u in filteredUsers" :key="u.id" @click="switchUser(u.id)"
                        :class="['w-full flex items-center gap-3 px-4 py-2.5 text-left transition-colors',
                                 selectedUserId === u.id
                                     ? 'bg-primary-50 dark:bg-primary-900/20'
                                     : 'hover:bg-gray-50 dark:hover:bg-gray-700/50']">
                    <img :src="avatarUrl(u)" :alt="u.name"
                         class="w-8 h-8 rounded-full object-cover ring-2 ring-gray-200 dark:ring-gray-600 flex-shrink-0"/>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate"
                           :class="selectedUserId === u.id ? 'text-primary-700 dark:text-primary-300' : ''">
                            {{ u.last_name }} {{ u.name }}
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 truncate">{{ userTypeLabel(u.user_type) }}</p>
                    </div>
                    <span class="text-xs tabular-nums text-gray-400 flex-shrink-0">
                        {{ u.all_perms.length }}
                    </span>
                </button>
                <div v-if="!filteredUsers.length" class="px-4 py-6 text-center text-xs text-gray-400">
                    Aucun utilisateur trouvé
                </div>
            </nav>
        </div>

        <!-- Permissions de l'utilisateur -->
        <div class="lg:col-span-3 space-y-4">
            <!-- Placeholder si aucun user sélectionné -->
            <div v-if="!selectedUser" class="card p-12 text-center">
                <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <p class="text-sm text-gray-500 dark:text-gray-400">Sélectionnez un utilisateur dans la liste</p>
            </div>

            <template v-else>
                <!-- Profil utilisateur -->
                <div class="card p-4 flex items-center gap-4">
                    <img :src="avatarUrl(selectedUser)" :alt="selectedUser.name"
                         class="w-12 h-12 rounded-full object-cover ring-2 ring-primary-200 dark:ring-primary-700"/>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-900 dark:text-white">
                            {{ selectedUser.last_name }} {{ selectedUser.name }}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ selectedUser.email }}</p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <div class="flex flex-wrap gap-1.5 justify-end">
                            <span v-for="role in selectedUser.roles" :key="role"
                                  class="px-2 py-0.5 rounded-full text-xs font-medium
                                         bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-300">
                                {{ role }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-400 mt-1 tabular-nums">
                            <span class="text-emerald-600 dark:text-emerald-400 font-medium">{{ userInheritCount }} héritées</span>
                            · <span class="text-primary-600 dark:text-primary-400 font-medium">{{ userDirectCount }} directes</span>
                        </p>
                    </div>
                </div>

                <!-- Légende -->
                <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                    <span class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded border-2 border-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 inline-block"/>
                        Héritée via le rôle (non modifiable ici)
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded border-2 border-primary-400 bg-primary-50 dark:bg-primary-900/20 inline-block"/>
                        Permission directe
                    </span>
                </div>

                <!-- Barre save -->
                <div class="flex items-center justify-between gap-3">
                    <span class="text-xs text-gray-400 tabular-nums">
                        {{ userDirectCount }} permission{{ userDirectCount > 1 ? 's' : '' }} directe{{ userDirectCount > 1 ? 's' : '' }}
                    </span>
                    <div class="flex items-center gap-2">
                        <Transition enter-active-class="transition duration-150" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-to-class="opacity-0">
                            <span v-if="userSaved" class="text-sm text-emerald-600 dark:text-emerald-400 font-medium flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Enregistré
                            </span>
                        </Transition>
                        <button @click="saveUser" :disabled="userSaving"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium
                                       bg-primary-600 hover:bg-primary-700 text-white disabled:opacity-50 transition-colors">
                            <svg v-if="userSaving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            {{ userSaving ? 'Sauvegarde...' : 'Enregistrer les permissions directes' }}
                        </button>
                    </div>
                </div>

                <!-- Recherche permissions utilisateur -->
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input v-model="permSearch" type="text" placeholder="Rechercher une permission..."
                           class="w-full pl-9 pr-4 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-600
                                  bg-white dark:bg-gray-800 text-gray-900 dark:text-white
                                  focus:outline-none focus:ring-2 focus:ring-primary-500/40 transition-colors
                                  placeholder-gray-400 dark:placeholder-gray-500"/>
                    <button v-if="permSearch" @click="permSearch = ''"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Aucun résultat -->
                <div v-if="modules.size === 0" class="card p-8 text-center">
                    <svg class="w-10 h-10 text-gray-300 dark:text-gray-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Aucune permission trouvée pour « {{ permSearch }} »</p>
                </div>

                <!-- Modules/permissions utilisateur -->
                <div v-for="[mod, perms] in modules" :key="mod" class="card overflow-hidden">
                    <label class="flex items-center gap-3 px-5 py-3
                                  bg-gray-50 dark:bg-gray-800/60 border-b border-gray-100 dark:border-gray-700
                                  cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700/60 transition-colors">
                        <input type="checkbox" :checked="isUserModuleChecked(mod)" :indeterminate="isUserModuleIndet(mod)"
                               class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 cursor-pointer"
                               style="accent-color:#7B74F0"
                               @change="toggleUserModule(mod)"/>
                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider flex-1">{{ mod }}</span>
                        <span class="text-xs text-gray-400 tabular-nums">
                            {{ perms.filter(p => isFromRole(p.name) || userDirectPerms.has(p.name)).length }} / {{ perms.length }}
                        </span>
                    </label>
                    <div class="p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                        <label v-for="perm in perms" :key="perm.id"
                               class="inline-flex items-center gap-2.5 px-3 py-2 rounded-lg border transition-colors"
                               :class="[
                                   isFromRole(perm.name)
                                       ? 'border-emerald-300 dark:border-emerald-700 bg-emerald-50/60 dark:bg-emerald-900/15 cursor-default'
                                       : userDirectPerms.has(perm.name)
                                           ? 'border-primary-300 dark:border-primary-600 bg-primary-50/60 dark:bg-primary-900/15 cursor-pointer'
                                           : 'border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500 cursor-pointer',
                               ]">
                            <input type="checkbox"
                                   :checked="isFromRole(perm.name) || userDirectPerms.has(perm.name)"
                                   :disabled="isFromRole(perm.name)"
                                   class="w-3.5 h-3.5 rounded flex-shrink-0"
                                   :style="isFromRole(perm.name) ? 'accent-color:#10b981' : 'accent-color:#7B74F0'"
                                   @change="toggleUserPerm(perm.name)"/>
                            <span class="text-xs font-mono truncate"
                                  :class="isFromRole(perm.name)
                                      ? 'text-emerald-700 dark:text-emerald-400'
                                      : userDirectPerms.has(perm.name)
                                          ? 'text-primary-700 dark:text-primary-300'
                                          : 'text-gray-600 dark:text-gray-400'">
                                {{ perm.name }}
                            </span>
                            <!-- Badge "rôle" -->
                            <span v-if="isFromRole(perm.name)"
                                  class="ml-auto flex-shrink-0 text-[9px] font-semibold px-1 py-0.5 rounded
                                         bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400">
                                rôle
                            </span>
                        </label>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
</template>
