<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';

interface RoleItem  { id: number; name: string; user_type: number | null; description: string | null; permissions: string[]; }
interface UserItem  { id: number; name: string; last_name: string; email: string; user_type: number;
                      roles: string[]; direct_perms: string[]; role_perms: string[]; all_perms: string[]; profile_picture: string | null; }
interface PermItem  { id: number; name: string; module: string; }

const props = defineProps<{ roles: RoleItem[]; users: UserItem[]; permissions: PermItem[]; }>();

// ── Onglets ────────────────────────────────────────────────────────────────
const tab = ref<'role' | 'user'>('role');
const switchTab = (t: 'role' | 'user') => { tab.value = t; permSearch.value = ''; };

// ── Recherche permissions ──────────────────────────────────────────────────
const permSearch = ref('');
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
watch(
    () => props.roles.find(r => r.id === selectedRoleId.value)?.permissions,
    (newPerms) => { if (newPerms !== undefined) rolePerms.value = new Set(newPerms); },
    { deep: true }
);
const toggleRolePerm   = (name: string) => { if (rolePerms.value.has(name)) rolePerms.value.delete(name); else rolePerms.value.add(name); };
const toggleRoleModule = (mod: string)  => {
    const perms = modules.value.get(mod) ?? [];
    if (perms.every(p => rolePerms.value.has(p.name))) perms.forEach(p => rolePerms.value.delete(p.name));
    else perms.forEach(p => rolePerms.value.add(p.name));
};
const isRoleModuleChecked = (mod: string) => (modules.value.get(mod) ?? []).every(p => rolePerms.value.has(p.name));
const isRoleModuleIndet   = (mod: string) => { const ps = modules.value.get(mod) ?? []; const c = ps.filter(p => rolePerms.value.has(p.name)); return c.length > 0 && c.length < ps.length; };
const allRolePermsChecked = computed(() => props.permissions.every(p => rolePerms.value.has(p.name)));
const toggleAllRolePerms  = () => { if (allRolePermsChecked.value) rolePerms.value = new Set(); else rolePerms.value = new Set(props.permissions.map(p => p.name)); };
const roleCheckedCount    = computed(() => rolePerms.value.size);

const saveRole = () => {
    if (!selectedRoleId.value || selectedRole.value?.name === 'super_admin') return;
    roleSaving.value = true; roleSaved.value = false;
    router.post(`/superadmin/config/assign/role/${selectedRoleId.value}/sync`,
        { permissions: Array.from(rolePerms.value) },
        { preserveScroll: true,
          onSuccess: () => { roleSaving.value = false; roleSaved.value = true; setTimeout(() => { roleSaved.value = false; }, 3000); },
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
    return props.users.filter(u => u.name.toLowerCase().includes(q) || u.last_name.toLowerCase().includes(q) || u.email.toLowerCase().includes(q));
});
const switchUser = (id: number) => {
    selectedUserId.value = id;
    const u = props.users.find(u => u.id === id);
    userDirectPerms.value = new Set(u?.direct_perms ?? []);
    userSaved.value = false;
};
watch(
    () => props.users.find(u => u.id === selectedUserId.value)?.direct_perms,
    (newPerms) => { if (newPerms !== undefined) userDirectPerms.value = new Set(newPerms); },
    { deep: true }
);
const isFromRole       = (name: string) => selectedUser.value?.role_perms.includes(name) ?? false;
const toggleUserPerm   = (name: string) => { if (isFromRole(name)) return; if (userDirectPerms.value.has(name)) userDirectPerms.value.delete(name); else userDirectPerms.value.add(name); };
const toggleUserModule = (mod: string)  => {
    const perms = (modules.value.get(mod) ?? []).filter(p => !isFromRole(p.name));
    if (perms.every(p => userDirectPerms.value.has(p.name))) perms.forEach(p => userDirectPerms.value.delete(p.name));
    else perms.forEach(p => userDirectPerms.value.add(p.name));
};
const isUserModuleChecked = (mod: string) => { const ps = modules.value.get(mod) ?? []; return ps.every(p => isFromRole(p.name) || userDirectPerms.value.has(p.name)); };
const isUserModuleIndet   = (mod: string) => { const ps = modules.value.get(mod) ?? []; const c = ps.filter(p => isFromRole(p.name) || userDirectPerms.value.has(p.name)); return c.length > 0 && c.length < ps.length; };
const userDirectCount  = computed(() => userDirectPerms.value.size);
const userInheritCount = computed(() => selectedUser.value?.role_perms.length ?? 0);

const saveUser = () => {
    if (!selectedUserId.value) return;
    userSaving.value = true; userSaved.value = false;
    router.post(`/superadmin/config/assign/user/${selectedUserId.value}/sync`,
        { permissions: Array.from(userDirectPerms.value) },
        { preserveScroll: true,
          onSuccess: () => { userSaving.value = false; userSaved.value = true; setTimeout(() => { userSaved.value = false; }, 3000); },
          onError:   () => { userSaving.value = false; } }
    );
};
const userTypeLabel = (ut: number) => ({ 0:'Super Admin', 1:'Admin', 2:'Professeur', 3:'Apprenant', 4:'Parent' } as Record<number,string>)[ut] ?? `Rôle custom (${ut})`;
const avatarUrl     = (u: UserItem) => u.profile_picture ? `/upload/profile/${u.profile_picture}` : '/upload/default.jpg';
</script>

<template>
<div class="flex flex-col h-full">

    <!-- En-tête + onglets (toujours visible) -->
    <div class="flex-shrink-0 space-y-4 mb-4">
        <div class="flex items-center gap-4">
            <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-violet-500 to-purple-700 flex items-center justify-center shadow-lg shadow-violet-500/30 flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-white">Attribuer des permissions</h1>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Par rôle ou directement à un utilisateur</p>
            </div>
        </div>
        <div class="flex items-center gap-1 p-1 bg-gray-100 dark:bg-gray-800 rounded-xl w-fit">
            <button @click="switchTab('role')"
                    :class="['px-4 py-1.5 text-sm font-medium rounded-xl transition-all',
                             tab === 'role' ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm'
                                            : 'text-gray-500 dark:text-gray-400 hover:text-gray-700']">
                <svg class="w-3.5 h-3.5 inline mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                Par rôle
                <span class="ml-1 px-1.5 py-0.5 rounded-full text-[10px] bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-300">{{ roles.length }}</span>
            </button>
            <button @click="switchTab('user')"
                    :class="['px-4 py-1.5 text-sm font-medium rounded-xl transition-all',
                             tab === 'user' ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm'
                                            : 'text-gray-500 dark:text-gray-400 hover:text-gray-700']">
                <svg class="w-3.5 h-3.5 inline mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Par utilisateur
                <span class="ml-1 px-1.5 py-0.5 rounded-full text-[10px] bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-300">{{ users.length }}</span>
            </button>
        </div>
    </div>

    <!-- ════════════════ ONGLET RÔLES ════════════════ -->
    <div v-if="tab === 'role'" class="flex gap-5 min-h-0 flex-1">

        <!-- Colonne gauche STICKY : liste rôles + action bar -->
        <div class="w-64 flex-shrink-0 flex flex-col gap-3 sticky top-0 self-start" style="max-height: calc(100vh - 160px);">

            <!-- Liste des rôles (scrollable si besoin) -->
            <div class="card overflow-hidden flex-shrink-0">
                <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rôles</h2>
                </div>
                <nav class="py-1 overflow-y-auto" style="max-height: 280px;">
                    <button v-for="role in roles" :key="role.id" @click="switchRole(role.id)"
                            :class="['w-full flex items-center justify-between px-4 py-2.5 text-sm transition-colors text-left',
                                     selectedRoleId === role.id
                                         ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 font-medium'
                                         : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50']">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="truncate capitalize">{{ role.name }}</span>
                            <span v-if="role.user_type !== null" class="flex-shrink-0 text-[9px] font-mono px-1 py-0.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">#{{ role.user_type }}</span>
                        </div>
                        <span class="text-xs tabular-nums flex-shrink-0 ml-2 font-medium"
                              :class="selectedRoleId === role.id ? 'text-primary-500' : 'text-gray-400'">{{ role.permissions.length }}</span>
                    </button>
                </nav>
            </div>

            <!-- Panneau d'action sticky -->
            <div class="card p-4 space-y-3">
                <!-- Tout sélectionner -->
                <label class="flex items-center gap-2.5 cursor-pointer group">
                    <input type="checkbox" :checked="allRolePermsChecked"
                           class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 cursor-pointer"
                           style="accent-color:#7B74F0"
                           :disabled="selectedRole?.name === 'super_admin'"
                           @change="toggleAllRolePerms"/>
                    <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white transition-colors">Tout sélectionner</span>
                </label>

                <!-- Compteur -->
                <div class="flex items-center justify-between text-xs text-gray-400">
                    <span>Sélectionnées</span>
                    <span class="font-semibold tabular-nums text-primary-600 dark:text-primary-400">{{ roleCheckedCount }} / {{ permissions.length }}</span>
                </div>

                <!-- Barre de progression -->
                <div class="w-full h-1.5 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                    <div class="h-full bg-primary-500 rounded-full transition-all duration-300"
                         :style="{ width: permissions.length ? (roleCheckedCount / permissions.length * 100) + '%' : '0%' }"/>
                </div>

                <!-- Recherche -->
                <div class="relative">
                    <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input v-model="permSearch" type="text" placeholder="Filtrer..."
                           class="w-full pl-8 pr-3 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500/40 transition-colors placeholder-gray-400"/>
                    <button v-if="permSearch" @click="permSearch = ''" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Warning super_admin -->
                <div v-if="selectedRole?.name === 'super_admin'" class="flex items-start gap-2 p-2.5 rounded-lg bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-700/40">
                    <svg class="w-3.5 h-3.5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <p class="text-xs text-amber-700 dark:text-amber-400">Le rôle <strong>super_admin</strong> ne peut pas être modifié.</p>
                </div>

                <!-- Bouton enregistrer -->
                <button @click="saveRole" :disabled="roleSaving || selectedRole?.name === 'super_admin'"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium transition-all disabled:opacity-50"
                        :class="selectedRole?.name === 'super_admin'
                            ? 'bg-gray-100 text-gray-400 dark:bg-gray-800 dark:text-gray-500 cursor-not-allowed'
                            : 'bg-primary-600 hover:bg-primary-700 active:scale-95 text-white shadow-sm shadow-primary-500/30'">
                    <svg v-if="roleSaving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <svg v-else-if="roleSaved" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>
                    <span>{{ roleSaving ? 'Sauvegarde...' : roleSaved ? 'Enregistré !' : 'Enregistrer' }}</span>
                </button>
            </div>
        </div><!-- fin colonne gauche rôles -->

        <!-- Colonne droite : permissions scrollables -->
        <div class="flex-1 min-w-0 overflow-y-auto space-y-3 pr-1" style="max-height: calc(100vh - 160px);">
            <div v-if="modules.size === 0" class="card p-10 text-center">
                <svg class="w-10 h-10 text-gray-300 dark:text-gray-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <p class="text-sm text-gray-500 dark:text-gray-400">Aucune permission pour « {{ permSearch }} »</p>
            </div>

            <div v-for="[mod, perms] in modules" :key="mod" class="card overflow-hidden">
                <label class="flex items-center gap-3 px-4 py-2.5 bg-gray-50 dark:bg-gray-800/60 border-b border-gray-100 dark:border-gray-700 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700/60 transition-colors">
                    <input type="checkbox" :checked="isRoleModuleChecked(mod)" :indeterminate="isRoleModuleIndet(mod)"
                           :disabled="selectedRole?.name === 'super_admin'"
                           class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 cursor-pointer" style="accent-color:#7B74F0"
                           @change="toggleRoleModule(mod)"/>
                    <span class="text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-widest flex-1">{{ mod }}</span>
                    <span class="text-xs text-gray-400 tabular-nums font-medium">
                        {{ perms.filter(p => rolePerms.has(p.name)).length }}<span class="text-gray-300 dark:text-gray-600">/{{ perms.length }}</span>
                    </span>
                </label>
                <div class="p-3 grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-1.5">
                    <label v-for="perm in perms" :key="perm.id"
                           class="inline-flex items-center gap-2 px-2.5 py-1.5 rounded-lg cursor-pointer border transition-all"
                           :class="rolePerms.has(perm.name)
                               ? 'border-primary-300 dark:border-primary-600 bg-primary-50/70 dark:bg-primary-900/20'
                               : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800'">
                        <input type="checkbox" :checked="rolePerms.has(perm.name)"
                               :disabled="selectedRole?.name === 'super_admin'"
                               class="w-3.5 h-3.5 rounded flex-shrink-0 cursor-pointer" style="accent-color:#7B74F0"
                               @change="toggleRolePerm(perm.name)"/>
                        <span class="text-[11px] font-mono truncate"
                              :class="rolePerms.has(perm.name) ? 'text-primary-700 dark:text-primary-300' : 'text-gray-500 dark:text-gray-400'">
                            {{ perm.name }}
                        </span>
                    </label>
                </div>
            </div>
        </div><!-- fin colonne droite rôles -->

    </div><!-- fin onglet rôles -->

    <!-- ════════════════ ONGLET UTILISATEURS ════════════════ -->
    <div v-else class="flex gap-5 min-h-0 flex-1">

        <!-- Colonne gauche STICKY : liste utilisateurs + action bar -->
        <div class="w-72 flex-shrink-0 flex flex-col gap-3 sticky top-0 self-start" style="max-height: calc(100vh - 160px);">

            <!-- Liste utilisateurs -->
            <div class="card overflow-hidden flex-shrink-0">
                <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 space-y-2">
                    <h2 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Utilisateurs</h2>
                    <input v-model="userSearch" type="text" placeholder="Rechercher..."
                           class="w-full px-3 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500/40 transition-colors placeholder-gray-400"/>
                </div>
                <nav class="py-1 overflow-y-auto" style="max-height: 260px;">
                    <button v-for="u in filteredUsers" :key="u.id" @click="switchUser(u.id)"
                            :class="['w-full flex items-center gap-2.5 px-3 py-2 text-left transition-colors',
                                     selectedUserId === u.id ? 'bg-primary-50 dark:bg-primary-900/20' : 'hover:bg-gray-50 dark:hover:bg-gray-700/50']">
                        <img :src="avatarUrl(u)" :alt="u.name" class="w-7 h-7 rounded-full object-cover ring-2 ring-gray-200 dark:ring-gray-600 flex-shrink-0"/>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-medium truncate" :class="selectedUserId === u.id ? 'text-primary-700 dark:text-primary-300' : 'text-gray-800 dark:text-gray-200'">
                                {{ u.last_name }} {{ u.name }}
                            </p>
                            <p class="text-[10px] text-gray-400 truncate">{{ userTypeLabel(u.user_type) }}</p>
                        </div>
                        <span class="text-[10px] tabular-nums text-gray-400 flex-shrink-0 font-medium">{{ u.all_perms.length }}</span>
                    </button>
                    <div v-if="!filteredUsers.length" class="px-4 py-5 text-center text-xs text-gray-400">Aucun utilisateur trouvé</div>
                </nav>
            </div>

            <!-- Panneau action utilisateur (visible uniquement si user sélectionné) -->
            <template v-if="selectedUser">
                <!-- Profil mini -->
                <div class="card p-3 flex items-center gap-3">
                    <img :src="avatarUrl(selectedUser)" :alt="selectedUser.name" class="w-9 h-9 rounded-full object-cover ring-2 ring-primary-200 dark:ring-primary-700 flex-shrink-0"/>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ selectedUser.last_name }} {{ selectedUser.name }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ selectedUser.email }}</p>
                        <div class="flex flex-wrap gap-1 mt-1">
                            <span v-for="role in selectedUser.roles" :key="role" class="px-1.5 py-0.5 rounded-full text-[9px] font-medium bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-300">{{ role }}</span>
                        </div>
                    </div>
                </div>

                <!-- Stats + recherche + enregistrer -->
                <div class="card p-4 space-y-3">
                    <div class="grid grid-cols-2 gap-2">
                        <div class="rounded-lg bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-200 dark:border-emerald-700/30 p-2 text-center">
                            <p class="text-lg font-bold text-emerald-600 dark:text-emerald-400 tabular-nums">{{ userInheritCount }}</p>
                            <p class="text-[10px] text-emerald-600/70 dark:text-emerald-500/70 font-medium">héritées</p>
                        </div>
                        <div class="rounded-lg bg-primary-50 dark:bg-primary-900/10 border border-primary-200 dark:border-primary-700/30 p-2 text-center">
                            <p class="text-lg font-bold text-primary-600 dark:text-primary-400 tabular-nums">{{ userDirectCount }}</p>
                            <p class="text-[10px] text-primary-600/70 dark:text-primary-500/70 font-medium">directes</p>
                        </div>
                    </div>

                    <!-- Légende -->
                    <div class="space-y-1 text-[10px] text-gray-400 dark:text-gray-500">
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded border-2 border-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 inline-block flex-shrink-0"/>Héritée du rôle (lecture seule)</span>
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded border-2 border-primary-400 bg-primary-50 dark:bg-primary-900/20 inline-block flex-shrink-0"/>Permission directe (modifiable)</span>
                    </div>

                    <!-- Filtre -->
                    <div class="relative">
                        <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input v-model="permSearch" type="text" placeholder="Filtrer..."
                               class="w-full pl-8 pr-3 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500/40 transition-colors placeholder-gray-400"/>
                        <button v-if="permSearch" @click="permSearch = ''" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- Bouton enregistrer -->
                    <button @click="saveUser" :disabled="userSaving"
                            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium transition-all disabled:opacity-50 bg-primary-600 hover:bg-primary-700 active:scale-95 text-white shadow-sm shadow-primary-500/30">
                        <svg v-if="userSaving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        <svg v-else-if="userSaved" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        <span>{{ userSaving ? 'Sauvegarde...' : userSaved ? 'Enregistré !' : 'Enregistrer' }}</span>
                    </button>
                </div>
            </template>
        </div><!-- fin colonne gauche utilisateurs -->

        <!-- Colonne droite : permissions utilisateur scrollables -->
        <div class="flex-1 min-w-0 overflow-y-auto space-y-3 pr-1" style="max-height: calc(100vh - 160px);">

            <!-- Placeholder aucun user sélectionné -->
            <div v-if="!selectedUser" class="card p-14 text-center">
                <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <p class="text-sm text-gray-500 dark:text-gray-400">Sélectionnez un utilisateur dans la liste</p>
            </div>

            <template v-else>
                <div v-if="modules.size === 0" class="card p-10 text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Aucune permission pour « {{ permSearch }} »</p>
                </div>

                <div v-for="[mod, perms] in modules" :key="mod" class="card overflow-hidden">
                    <label class="flex items-center gap-3 px-4 py-2.5 bg-gray-50 dark:bg-gray-800/60 border-b border-gray-100 dark:border-gray-700 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700/60 transition-colors">
                        <input type="checkbox" :checked="isUserModuleChecked(mod)" :indeterminate="isUserModuleIndet(mod)"
                               class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 cursor-pointer" style="accent-color:#7B74F0"
                               @change="toggleUserModule(mod)"/>
                        <span class="text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-widest flex-1">{{ mod }}</span>
                        <span class="text-xs text-gray-400 tabular-nums font-medium">
                            {{ perms.filter(p => isFromRole(p.name) || userDirectPerms.has(p.name)).length }}<span class="text-gray-300 dark:text-gray-600">/{{ perms.length }}</span>
                        </span>
                    </label>
                    <div class="p-3 grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-1.5">
                        <label v-for="perm in perms" :key="perm.id"
                               class="inline-flex items-center gap-2 px-2.5 py-1.5 rounded-lg border transition-all"
                               :class="[
                                   isFromRole(perm.name)
                                       ? 'border-emerald-300 dark:border-emerald-700 bg-emerald-50/70 dark:bg-emerald-900/15 cursor-default'
                                       : userDirectPerms.has(perm.name)
                                           ? 'border-primary-300 dark:border-primary-600 bg-primary-50/70 dark:bg-primary-900/20 cursor-pointer'
                                           : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer',
                               ]">
                            <input type="checkbox"
                                   :checked="isFromRole(perm.name) || userDirectPerms.has(perm.name)"
                                   :disabled="isFromRole(perm.name)"
                                   class="w-3.5 h-3.5 rounded flex-shrink-0"
                                   :style="isFromRole(perm.name) ? 'accent-color:#10b981' : 'accent-color:#7B74F0'"
                                   @change="toggleUserPerm(perm.name)"/>
                            <span class="text-[11px] font-mono truncate"
                                  :class="isFromRole(perm.name)
                                      ? 'text-emerald-700 dark:text-emerald-400'
                                      : userDirectPerms.has(perm.name)
                                          ? 'text-primary-700 dark:text-primary-300'
                                          : 'text-gray-500 dark:text-gray-400'">
                                {{ perm.name }}
                            </span>
                            <span v-if="isFromRole(perm.name)" class="ml-auto flex-shrink-0 text-[8px] font-bold px-1 py-0.5 rounded bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400 uppercase">rôle</span>
                        </label>
                    </div>
                </div>
            </template>
        </div><!-- fin colonne droite utilisateurs -->

    </div><!-- fin onglet utilisateurs -->

</div><!-- fin container principal -->
</template>
