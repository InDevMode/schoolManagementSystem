<template>
    <div class="space-y-6" @click="closeContextMenu">

        <!-- ── Header ─────────────────────────────────────────────────────── -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tous les utilisateurs</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    {{ users.total }} utilisateur(s) au total
                </p>
            </div>
        </div>

        <!-- ── Bannière ────────────────────────────────────────────────────── -->
        <div class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl
                    bg-violet-50 dark:bg-violet-900/20 border border-violet-200 dark:border-violet-700 text-sm">
            <svg class="w-4 h-4 text-violet-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            <span class="text-violet-700 dark:text-violet-300 font-medium">Vue Super Admin</span>
            <span class="text-violet-600 dark:text-violet-400">— Double-clic sur une cellule pour éditer · Clic droit pour le menu rapide</span>
        </div>

        <!-- ── Filtres ─────────────────────────────────────────────────────── -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3">
                <input v-model="filters.name"          type="text" placeholder="Nom..."       class="input-field" @keyup.enter="applyFilters"/>
                <input v-model="filters.last_name"     type="text" placeholder="Prénoms..."   class="input-field" @keyup.enter="applyFilters"/>
                <input v-model="filters.email"         type="text" placeholder="Email..."     class="input-field" @keyup.enter="applyFilters"/>
                <input v-model="filters.mobile_number" type="text" placeholder="Téléphone..." class="input-field" @keyup.enter="applyFilters"/>
                <select v-model="filters.user_type" class="input-field">
                    <option value="">Tous les rôles</option>
                    <option value="0">Super Administrateur</option>
                    <option value="1">Administrateur</option>
                    <option value="2">Professeur</option>
                    <option value="3">Apprenant</option>
                    <option value="4">Parent</option>
                </select>
                <select v-model="filters.status" class="input-field">
                    <option value="">Tous les statuts</option>
                    <option value="1">Actif</option>
                    <option value="0">Inactif</option>
                </select>
            </div>
            <div class="flex items-center gap-2 mt-3">
                <button @click="applyFilters"
                        class="px-4 py-2 rounded-xl text-sm font-medium bg-violet-600 hover:bg-violet-700
                               text-white transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Filtrer
                </button>
                <button @click="resetFilters"
                        class="px-4 py-2 rounded-xl text-sm font-medium border border-gray-200 dark:border-gray-600
                               text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Réinitialiser
                </button>
                <div class="ml-auto flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                    <span>Afficher</span>
                    <select v-model="perPage" class="input-field !w-20 !py-1.5" @change="applyFilters">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span>par page</span>
                </div>
            </div>
        </div>

        <!-- ── Tableau ─────────────────────────────────────────────────────── -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">

            <!-- Barre sélection en masse -->
            <div v-if="selectedIds.length > 0"
                 class="flex items-center gap-3 px-5 py-3 bg-violet-50 dark:bg-violet-900/20
                         border-b border-violet-100 dark:border-violet-800">
                <span class="text-sm font-medium text-violet-700 dark:text-violet-300">
                    {{ selectedIds.length }} sélectionné(s)
                </span>
                <button @click="openBulkReset"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium
                               bg-amber-500 hover:bg-amber-600 text-white transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                    Réinitialiser les MDP sélectionnés
                </button>
                <button @click="selectedIds = []"
                        class="text-xs text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 ml-auto">
                    Tout déselectionner
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                            <th class="px-4 py-3 text-left w-10">
                                <input type="checkbox" class="checkbox-field" :checked="allSelected" @change="toggleSelectAll"/>
                            </th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 whitespace-nowrap">Utilisateur</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 whitespace-nowrap">Email</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 whitespace-nowrap">Téléphone</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 whitespace-nowrap">Rôle</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-600 dark:text-gray-300 whitespace-nowrap">Permissions</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 whitespace-nowrap">École</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-600 dark:text-gray-300 whitespace-nowrap">Statut</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 whitespace-nowrap">Créé le</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-300 whitespace-nowrap pr-5">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                        <template v-if="users.data.length">
                            <tr v-for="u in users.data" :key="u.id"
                                :class="[
                                    'group transition-colors hover:bg-gray-50/80 dark:hover:bg-gray-700/30 cursor-default',
                                    selectedIds.includes(u.id) ? 'bg-violet-50/60 dark:bg-violet-900/10' : '',
                                ]"
                                @contextmenu.prevent="openContextMenu($event, u)">

                                <!-- Checkbox -->
                                <td class="px-4 py-3">
                                    <input type="checkbox" class="checkbox-field"
                                           :checked="selectedIds.includes(u.id)"
                                           @change="toggleSelect(u.id)"/>
                                </td>

                                <!-- Utilisateur -->
                                <td class="px-4 py-3" @dblclick="openEdit(u)">
                                    <div class="flex items-center gap-3 min-w-[180px]">
                                        <div class="relative flex-shrink-0">
                                            <img v-if="u.profile_picture"
                                                 :src="`/upload/profile/${u.profile_picture}`"
                                                 :alt="`${u.last_name} ${u.name}`"
                                                 class="w-9 h-9 rounded-full object-cover ring-2 ring-gray-100 dark:ring-gray-700"/>
                                            <div v-else
                                                 class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold text-white ring-2 ring-gray-100 dark:ring-gray-700"
                                                 :style="{ background: avatarColor(u.user_type) }">
                                                {{ initials(u) }}
                                            </div>
                                            <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 rounded-full border-2 border-white dark:border-gray-800"
                                                  :class="u.is_online ? 'bg-emerald-400' : 'bg-gray-300 dark:bg-gray-600'"/>
                                        </div>
                                        <div class="min-w-0">
                                            <!-- Nom avec icône copie au hover -->
                                            <div class="group/name flex items-center gap-1">
                                                <p class="font-semibold text-gray-900 dark:text-white truncate text-sm">
                                                    {{ u.last_name }} {{ u.name }}
                                                </p>
                                                <button
                                                    type="button"
                                                    class="opacity-0 group-hover/name:opacity-100 transition-opacity duration-150
                                                           p-0.5 rounded text-gray-400 hover:text-violet-600 hover:bg-violet-50
                                                           dark:hover:text-violet-400 dark:hover:bg-violet-900/20 flex-shrink-0"
                                                    :title="copied === `name-${u.id}` ? 'Copié !' : 'Copier le nom'"
                                                    @click.stop="copy(`${u.last_name} ${u.name}`, `name-${u.id}`)"
                                                >
                                                    <svg v-if="copied !== `name-${u.id}`" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                    </svg>
                                                    <svg v-else class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                </button>
                                            </div>
                                            <p class="text-xs text-gray-400 dark:text-gray-500">ID #{{ u.id }}</p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Email -->
                                <td class="px-4 py-3 max-w-[200px]" @dblclick="openEdit(u)">
                                    <div class="group/email flex items-center gap-1">
                                        <span class="text-sm text-gray-700 dark:text-gray-300 truncate" :title="u.email">
                                            {{ u.email }}
                                        </span>
                                        <button
                                            type="button"
                                            class="opacity-0 group-hover/email:opacity-100 transition-opacity duration-150 flex-shrink-0
                                                   p-0.5 rounded text-gray-400 hover:text-blue-600 hover:bg-blue-50
                                                   dark:hover:text-blue-400 dark:hover:bg-blue-900/20"
                                            :title="copied === `email-${u.id}` ? 'Copié !' : 'Copier l\'email'"
                                            @click.stop="copy(u.email, `email-${u.id}`)"
                                        >
                                            <svg v-if="copied !== `email-${u.id}`" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            </svg>
                                            <svg v-else class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>

                                <!-- Téléphone -->
                                <td class="px-4 py-3 whitespace-nowrap" @dblclick="openEdit(u)">
                                    <div class="group/tel flex items-center gap-1">
                                        <span class="text-sm font-mono text-gray-600 dark:text-gray-400">
                                            {{ u.mobile_number ?? '—' }}
                                        </span>
                                        <button v-if="u.mobile_number"
                                            type="button"
                                            class="opacity-0 group-hover/tel:opacity-100 transition-opacity duration-150 flex-shrink-0
                                                   p-0.5 rounded text-gray-400 hover:text-emerald-600 hover:bg-emerald-50
                                                   dark:hover:text-emerald-400 dark:hover:bg-emerald-900/20"
                                            :title="copied === `tel-${u.id}` ? 'Copié !' : 'Copier'"
                                            @click.stop="copy(u.mobile_number!, `tel-${u.id}`)"
                                        >
                                            <svg v-if="copied !== `tel-${u.id}`" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            </svg>
                                            <svg v-else class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>

                                <!-- Rôle -->
                                <td class="px-4 py-3 whitespace-nowrap" @dblclick="openEdit(u)">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold"
                                          :class="roleStyle(u.user_type).badge">
                                        <span class="w-1.5 h-1.5 rounded-full flex-shrink-0" :class="roleStyle(u.user_type).dot"/>
                                        {{ u.role_label }}
                                    </span>
                                </td>

                                <!-- Permissions -->
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center justify-center min-w-[2rem] px-2 py-0.5 rounded-full text-xs font-bold"
                                          :class="permBadgeClass(u.permissions_count)">
                                        {{ u.permissions_count }}
                                    </span>
                                </td>

                                <!-- École -->
                                <td class="px-4 py-3 max-w-[150px]">
                                    <span class="text-xs text-gray-600 dark:text-gray-400 truncate block" :title="u.school_name ?? ''">
                                        {{ u.school_name ?? '—' }}
                                    </span>
                                </td>

                                <!-- Statut -->
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold"
                                          :class="u.status == 1
                                            ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400'
                                            : 'bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-400'">
                                        <span class="w-1.5 h-1.5 rounded-full"
                                              :class="u.status == 1 ? 'bg-emerald-500 animate-pulse' : 'bg-red-400'"/>
                                        {{ u.status == 1 ? 'Actif' : 'Inactif' }}
                                    </span>
                                </td>

                                <!-- Date -->
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ formatDate(u.created_at) }}</span>
                                </td>

                                <!-- Actions -->
                                <td class="px-4 py-3 pr-5">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- Voir -->
                                        <button @click="openView(u)" title="Voir les détails"
                                                class="p-1.5 rounded-lg text-white bg-violet-500 hover:bg-violet-600 shadow-sm transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>
                                        <!-- Modifier -->
                                        <button @click="openEdit(u)" title="Modifier"
                                                class="p-1.5 rounded-lg text-white bg-emerald-500 hover:bg-emerald-600 shadow-sm transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                        <!-- Message -->
                                        <a :href="`/chat?receiver_id=${encodedId(u.id)}`" title="Envoyer un message"
                                           class="p-1.5 rounded-lg text-white bg-blue-500 hover:bg-blue-600 shadow-sm transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                            </svg>
                                        </a>
                                        <!-- Réinitialiser MDP -->
                                        <button @click="openResetConfirm(u)" title="Réinitialiser le mot de passe"
                                                class="p-1.5 rounded-lg text-white bg-amber-500 hover:bg-amber-600 shadow-sm transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                            </svg>
                                        </button>
                                        <!-- Supprimer -->
                                        <button v-if="u.user_type !== 0" @click="openDeleteConfirm(u)" title="Supprimer"
                                                class="p-1.5 rounded-lg text-white bg-red-500 hover:bg-red-600 shadow-sm transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <tr v-else>
                            <td colspan="10" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-14 h-14 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                        <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </div>
                                    <p class="text-gray-500 dark:text-gray-400 font-medium">Aucun utilisateur trouvé</p>
                                    <button @click="resetFilters" class="text-sm text-violet-600 hover:underline">Effacer les filtres</button>
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
                    <span v-if="users.total > 0">{{ users.from }}–{{ users.to }} sur {{ users.total }} résultat(s)</span>
                    <span v-else>Aucun résultat</span>
                </p>
                <div class="flex items-center gap-1">
                    <!-- Précédent -->
                    <button :disabled="!users.prev_page_url"
                            @click="users.prev_page_url && goToPage(users.prev_page_url)"
                            class="w-8 h-8 flex items-center justify-center rounded-lg text-sm transition-colors
                                   disabled:opacity-30 disabled:cursor-not-allowed
                                   text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                    <!-- Numéros de page -->
                    <template v-for="link in users.links.slice(1, -1)" :key="link.label">
                        <button v-if="link.url" @click="goToPage(link.url)"
                                :class="['w-8 h-8 flex items-center justify-center rounded-lg text-sm font-medium transition-colors',
                                    link.active ? 'bg-violet-600 text-white shadow-sm'
                                               : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700']">
                            {{ link.label }}
                        </button>
                        <span v-else class="w-8 h-8 flex items-center justify-center text-sm text-gray-300 dark:text-gray-600">
                            {{ link.label }}
                        </span>
                    </template>
                    <!-- Suivant -->
                    <button :disabled="!users.next_page_url"
                            @click="users.next_page_url && goToPage(users.next_page_url)"
                            class="w-8 h-8 flex items-center justify-center rounded-lg text-sm transition-colors
                                   disabled:opacity-30 disabled:cursor-not-allowed
                                   text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════════════
             MENU CONTEXTUEL (clic droit)
        ══════════════════════════════════════════════════════════════════ -->
        <Teleport to="body">
            <Transition enter-active-class="transition duration-100 ease-out" enter-from-class="opacity-0 scale-95"
                        enter-to-class="opacity-100 scale-100" leave-active-class="transition duration-75 ease-in"
                        leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-95">
                <div v-if="ctxMenu.visible"
                     :style="{ top: ctxMenu.y + 'px', left: ctxMenu.x + 'px' }"
                     class="fixed z-[9999] w-52 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100
                            dark:border-gray-700 overflow-hidden py-1"
                     @click.stop>
                    <button @click="openView(ctxMenu.user!); closeContextMenu()"
                            class="ctx-item text-gray-700 dark:text-gray-300 hover:bg-violet-50 hover:text-violet-700">
                        <span class="ctx-icon bg-violet-100 dark:bg-violet-900/30 text-violet-600">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </span>
                        Voir les détails
                    </button>
                    <button @click="openEdit(ctxMenu.user!); closeContextMenu()"
                            class="ctx-item text-gray-700 dark:text-gray-300 hover:bg-emerald-50 hover:text-emerald-700">
                        <span class="ctx-icon bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </span>
                        Modifier
                    </button>
                    <a :href="ctxMenu.user ? `/chat?receiver_id=${encodedId(ctxMenu.user.id)}` : '#'"
                       class="ctx-item text-gray-700 dark:text-gray-300 hover:bg-blue-50 hover:text-blue-700">
                        <span class="ctx-icon bg-blue-100 dark:bg-blue-900/30 text-blue-600">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </span>
                        Envoyer un message
                    </a>
                    <button @click="openResetConfirm(ctxMenu.user!); closeContextMenu()"
                            class="ctx-item text-gray-700 dark:text-gray-300 hover:bg-amber-50 hover:text-amber-700">
                        <span class="ctx-icon bg-amber-100 dark:bg-amber-900/30 text-amber-600">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                            </svg>
                        </span>
                        Réinitialiser MDP
                    </button>
                    <template v-if="ctxMenu.user?.user_type !== 0">
                        <div class="my-1 border-t border-gray-100 dark:border-gray-700"/>
                        <button @click="openDeleteConfirm(ctxMenu.user!); closeContextMenu()"
                                class="ctx-item text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20">
                            <span class="ctx-icon bg-red-100 dark:bg-red-900/30 text-red-600">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </span>
                            Supprimer
                        </button>
                    </template>
                </div>
            </Transition>
        </Teleport>

        <!-- ══════════════════════════════════════════════════════════════════
             MODAL VOIR DÉTAILS
        ══════════════════════════════════════════════════════════════════ -->
        <Teleport to="body">
            <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0" enter-to-class="opacity-100"
                        leave-active-class="transition duration-150 ease-in" leave-from-class="opacity-100" leave-to-class="opacity-0">
                <div v-if="viewTarget" class="fixed inset-0 z-[9990] flex items-center justify-center p-4">
                    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="viewTarget = null"/>
                    <div class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden">
                        <!-- Header -->
                        <div class="relative p-6 bg-gradient-to-br from-violet-600 to-purple-700">
                            <div class="absolute inset-0 opacity-10" style="background-image:radial-gradient(circle at 80% 20%, white 0%, transparent 60%)"/>
                            <button @click="viewTarget = null"
                                    class="absolute top-3 right-3 w-7 h-7 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center text-white">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                            <div class="relative flex items-center gap-4">
                                <div class="relative">
                                    <img v-if="viewTarget.profile_picture" :src="`/upload/profile/${viewTarget.profile_picture}`"
                                         class="w-16 h-16 rounded-full object-cover ring-4 ring-white/30"/>
                                    <div v-else class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center ring-4 ring-white/30 text-xl font-bold text-white">
                                        {{ initials(viewTarget) }}
                                    </div>
                                    <span class="absolute bottom-0.5 right-0.5 w-3.5 h-3.5 rounded-full border-2 border-white"
                                          :class="viewTarget.is_online ? 'bg-emerald-400' : 'bg-gray-400'"/>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h2 class="text-lg font-bold text-white truncate">{{ viewTarget.last_name }} {{ viewTarget.name }}</h2>
                                    <p class="text-violet-200 text-sm truncate">{{ viewTarget.email }}</p>
                                    <div class="flex items-center gap-2 mt-2 flex-wrap">
                                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold"
                                              :class="viewTarget.status == 1 ? 'bg-emerald-400/30 text-emerald-100' : 'bg-red-400/30 text-red-100'">
                                            {{ viewTarget.status == 1 ? '✓ Actif' : '✗ Inactif' }}
                                        </span>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-white/10 text-white/80">
                                            <span class="w-1.5 h-1.5 rounded-full" :class="viewTarget.is_online ? 'bg-emerald-400' : 'bg-gray-400'"/>
                                            {{ viewTarget.is_online ? 'En ligne' : 'Hors ligne' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Corps -->
                        <div class="p-5 grid grid-cols-2 gap-3">
                            <div class="info-card"><p class="info-label">Rôle</p><p class="text-sm font-semibold text-violet-700 dark:text-violet-400">{{ viewTarget.role_label }}</p></div>
                            <div class="info-card"><p class="info-label">Permissions</p><p class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ viewTarget.permissions_count }}</p></div>
                            <div class="info-card"><p class="info-label">Téléphone</p><p class="text-sm font-mono text-gray-700 dark:text-gray-300">{{ viewTarget.mobile_number ?? '—' }}</p></div>
                            <div class="info-card"><p class="info-label">Créé le</p><p class="text-sm text-gray-700 dark:text-gray-300">{{ formatDate(viewTarget.created_at) }}</p></div>
                            <div class="info-card col-span-2"><p class="info-label">École</p><p class="text-sm text-gray-700 dark:text-gray-300">{{ viewTarget.school_name ?? '—' }}</p></div>
                        </div>
                        <!-- Footer -->
                        <div class="flex gap-2 px-5 pb-5">
                            <button @click="viewTarget = null"
                                    class="flex-1 px-3 py-2 rounded-xl text-sm font-medium border border-gray-200 dark:border-gray-600
                                           text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                Fermer
                            </button>
                            <button @click="openEdit(viewTarget!); viewTarget = null"
                                    class="flex-1 px-3 py-2 rounded-xl text-sm font-medium bg-emerald-500 hover:bg-emerald-600 text-white transition-colors">
                                Modifier
                            </button>
                            <button @click="openResetConfirm(viewTarget!); viewTarget = null"
                                    class="flex-1 px-3 py-2 rounded-xl text-sm font-medium bg-amber-500 hover:bg-amber-600 text-white transition-colors">
                                Réinit. MDP
                            </button>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>

        <!-- ══════════════════════════════════════════════════════════════════
             MODAL MODIFICATION
        ══════════════════════════════════════════════════════════════════ -->
        <Teleport to="body">
            <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0" enter-to-class="opacity-100"
                        leave-active-class="transition duration-150 ease-in" leave-from-class="opacity-100" leave-to-class="opacity-0">
                <div v-if="editTarget" class="fixed inset-0 z-[9990] flex items-center justify-center p-4">
                    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="editTarget = null"/>
                    <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden">
                        <!-- Titre -->
                        <div class="flex items-center justify-between px-6 pt-5 pb-4 border-b border-gray-100 dark:border-gray-700">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Modifier l'utilisateur</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ editTarget.last_name }} {{ editTarget.name }}</p>
                                </div>
                            </div>
                            <button @click="editTarget = null"
                                    class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        <!-- Formulaire -->
                        <form @submit.prevent="submitEdit" class="p-6 space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="form-label">Prénoms <span class="text-red-500">*</span></label>
                                    <input v-model="editForm.last_name" type="text" class="input-field" required/>
                                </div>
                                <div>
                                    <label class="form-label">Nom <span class="text-red-500">*</span></label>
                                    <input v-model="editForm.name" type="text" class="input-field" required/>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="form-label">Email <span class="text-red-500">*</span></label>
                                    <input v-model="editForm.email" type="email" class="input-field" required/>
                                </div>
                                <div>
                                    <label class="form-label">Téléphone</label>
                                    <input v-model="editForm.mobile_number" type="text" class="input-field"/>
                                </div>
                                <div>
                                    <label class="form-label">Statut <span class="text-red-500">*</span></label>
                                    <select v-model="editForm.status" class="input-field" required>
                                        <option value="1">Actif</option>
                                        <option value="0">Inactif</option>
                                    </select>
                                </div>
                                <!-- Rôle -->
                                <div class="sm:col-span-2">
                                    <label class="form-label">Rôle <span class="text-red-500">*</span></label>
                                    <select v-model="editForm.user_type" class="input-field" required>
                                        <option v-for="r in roles" :key="r.id" :value="r.user_type">
                                            {{ r.label }}
                                        </option>
                                    </select>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                        Changer le rôle met à jour automatiquement les permissions associées.
                                    </p>
                                </div>
                            </div>
                            <!-- Erreur -->
                            <p v-if="editError" class="text-sm text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 px-3 py-2 rounded-lg">
                                {{ editError }}
                            </p>
                        </form>
                        <!-- Footer -->
                        <div class="flex gap-3 px-6 pb-6">
                            <button @click="editTarget = null"
                                    class="flex-1 px-4 py-2.5 rounded-xl text-sm font-medium border border-gray-200 dark:border-gray-600
                                           text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                Annuler
                            </button>
                            <button @click="submitEdit" :disabled="saving"
                                    class="flex-1 px-4 py-2.5 rounded-xl text-sm font-medium bg-emerald-600 hover:bg-emerald-700
                                           text-white transition-colors disabled:opacity-50 flex items-center justify-center gap-2">
                                <svg v-if="saving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                {{ saving ? 'Enregistrement...' : 'Enregistrer' }}
                            </button>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>

        <!-- ══════════════════════════════════════════════════════════════════
             MODAL CONFIRMATION RÉINITIALISATION MDP
        ══════════════════════════════════════════════════════════════════ -->
        <Teleport to="body">
            <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0" enter-to-class="opacity-100"
                        leave-active-class="transition duration-150 ease-in" leave-from-class="opacity-100" leave-to-class="opacity-0">
                <div v-if="resetTarget" class="fixed inset-0 z-[9995] flex items-center justify-center p-4">
                    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="resetTarget = null"/>
                    <div class="relative w-full max-w-sm bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden">
                        <div class="flex flex-col items-center px-6 pt-8 pb-4 text-center">
                            <div class="w-14 h-14 rounded-full bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center mb-4">
                                <svg class="w-7 h-7 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Réinitialiser le mot de passe</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Un nouveau mot de passe aléatoire sera généré et envoyé par email à
                                <strong class="text-gray-800 dark:text-gray-200">{{ resetTarget.last_name }} {{ resetTarget.name }}</strong>
                                (<span class="font-mono text-xs">{{ resetTarget.email }}</span>).
                            </p>
                        </div>
                        <div class="px-6 pb-6 flex gap-3 mt-2">
                            <button @click="resetTarget = null"
                                    class="flex-1 px-4 py-2.5 rounded-xl text-sm font-medium border border-gray-200 dark:border-gray-600
                                           text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                Annuler
                            </button>
                            <button @click="executeReset" :disabled="resetting"
                                    class="flex-1 px-4 py-2.5 rounded-xl text-sm font-medium bg-amber-500 hover:bg-amber-600
                                           text-white transition-colors disabled:opacity-50 flex items-center justify-center gap-2">
                                <svg v-if="resetting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                {{ resetting ? 'Envoi...' : 'Confirmer' }}
                            </button>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>

        <!-- ══════════════════════════════════════════════════════════════════
             MODAL CONFIRMATION SUPPRESSION
        ══════════════════════════════════════════════════════════════════ -->
        <Teleport to="body">
            <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0" enter-to-class="opacity-100"
                        leave-active-class="transition duration-150 ease-in" leave-from-class="opacity-100" leave-to-class="opacity-0">
                <div v-if="deleteTarget" class="fixed inset-0 z-[9995] flex items-center justify-center p-4">
                    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="deleteTarget = null"/>
                    <div class="relative w-full max-w-sm bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden">
                        <div class="flex flex-col items-center px-6 pt-8 pb-4 text-center">
                            <div class="w-14 h-14 rounded-full bg-red-50 dark:bg-red-900/30 flex items-center justify-center mb-4">
                                <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Supprimer l'utilisateur</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Êtes-vous sûr de vouloir supprimer
                                <strong class="text-gray-800 dark:text-gray-200">{{ deleteTarget.last_name }} {{ deleteTarget.name }}</strong> ?
                                Cette action est réversible depuis les journaux.
                            </p>
                        </div>
                        <div class="px-6 pb-6 flex gap-3 mt-2">
                            <button @click="deleteTarget = null"
                                    class="flex-1 px-4 py-2.5 rounded-xl text-sm font-medium border border-gray-200 dark:border-gray-600
                                           text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                Annuler
                            </button>
                            <button @click="executeDelete" :disabled="deleting"
                                    class="flex-1 px-4 py-2.5 rounded-xl text-sm font-medium bg-red-600 hover:bg-red-700
                                           text-white transition-colors disabled:opacity-50 flex items-center justify-center gap-2">
                                <svg v-if="deleting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                {{ deleting ? 'Suppression...' : 'Supprimer' }}
                            </button>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>

    </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import { useToast } from '@/Composables/useToast';
import AppLayout from '@/Layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

// ── Types ─────────────────────────────────────────────────────────────────────
interface UserRow {
    id: number;
    name: string;
    last_name: string;
    email: string;
    mobile_number: string | null;
    user_type: number;
    status: number;
    profile_picture: string | null;
    created_at: string;
    is_online: boolean;
    role_label: string;
    role_names: string[];
    permissions_count: number;
    school_name: string | null;
}
interface RoleOption { id: number; name: string; user_type: number; label: string; }
interface PaginatedUsers {
    data: UserRow[];
    total: number; from: number; to: number;
    last_page: number;
    prev_page_url: string | null;
    next_page_url: string | null;
    links: { url: string | null; label: string; active: boolean }[];
}

const props = defineProps<{ users: PaginatedUsers; roles: RoleOption[] }>();
const toast = useToast();

// ── CSRF helper ───────────────────────────────────────────────────────────────
const csrf = () =>
    (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? '';

// ── Filtres ───────────────────────────────────────────────────────────────────
const filters    = reactive({ name: '', last_name: '', email: '', mobile_number: '', user_type: '', status: '' });
const perPage    = ref(5);

const applyFilters = () => {
    const p: Record<string, string | number> = { per_page: perPage.value };
    if (filters.name)          p.name          = filters.name;
    if (filters.last_name)     p.last_name     = filters.last_name;
    if (filters.email)         p.email         = filters.email;
    if (filters.mobile_number) p.mobile_number = filters.mobile_number;
    if (filters.user_type)     p.user_type     = filters.user_type;
    if (filters.status)        p.status        = filters.status;
    router.get('/superadmin/users', p, { preserveState: true, replace: true });
};
const resetFilters = () => {
    Object.assign(filters, { name: '', last_name: '', email: '', mobile_number: '', user_type: '', status: '' });
    perPage.value = 5;
    router.get('/superadmin/users', { per_page: 5 }, { preserveState: false });
};
const goToPage = (url: string) => router.get(url, {}, { preserveState: true });

// ── Sélection ─────────────────────────────────────────────────────────────────
const selectedIds = ref<number[]>([]);
const allSelected = computed(() =>
    props.users.data.length > 0 && props.users.data.every(u => selectedIds.value.includes(u.id))
);
const toggleSelectAll = () => {
    allSelected.value ? selectedIds.value = [] : selectedIds.value = props.users.data.map(u => u.id);
};
const toggleSelect = (id: number) => {
    const i = selectedIds.value.indexOf(id);
    i === -1 ? selectedIds.value.push(id) : selectedIds.value.splice(i, 1);
};

// ── Copie presse-papier ───────────────────────────────────────────────────────
const copied = ref<string | null>(null);
let copyTimer: ReturnType<typeof setTimeout> | null = null;
const copy = (text: string, key: string) => {
    navigator.clipboard.writeText(text).then(() => {
        copied.value = key;
        if (copyTimer) clearTimeout(copyTimer);
        copyTimer = setTimeout(() => { copied.value = null; }, 1500);
    }).catch(() => toast.error('Impossible de copier.'));
};

// ── Contexte menu (clic droit) ────────────────────────────────────────────────
const ctxMenu = reactive<{ visible: boolean; x: number; y: number; user: UserRow | null }>({
    visible: false, x: 0, y: 0, user: null,
});
const openContextMenu = (e: MouseEvent, u: UserRow) => {
    e.preventDefault();
    // Ajuste pour ne pas dépasser l'écran
    const margin = 8;
    const menuW = 208; const menuH = 220;
    ctxMenu.x = Math.min(e.clientX, window.innerWidth  - menuW - margin);
    ctxMenu.y = Math.min(e.clientY, window.innerHeight - menuH - margin);
    ctxMenu.user    = u;
    ctxMenu.visible = true;
};
const closeContextMenu = () => { ctxMenu.visible = false; };

onMounted(() => {
    document.addEventListener('keydown', onEsc);
    document.addEventListener('scroll', closeContextMenu, true);
});
onUnmounted(() => {
    document.removeEventListener('keydown', onEsc);
    document.removeEventListener('scroll', closeContextMenu, true);
});
const onEsc = (e: KeyboardEvent) => { if (e.key === 'Escape') closeContextMenu(); };

// ── Modal Voir ────────────────────────────────────────────────────────────────
const viewTarget = ref<UserRow | null>(null);
const openView   = (u: UserRow) => { viewTarget.value = u; };

// ── Modal Modifier ────────────────────────────────────────────────────────────
const editTarget = ref<UserRow | null>(null);
const editForm   = reactive({ name: '', last_name: '', email: '', mobile_number: '', status: '1', user_type: 1 });
const saving     = ref(false);
const editError  = ref('');

const openEdit = (u: UserRow) => {
    editTarget.value        = u;
    editForm.name           = u.name;
    editForm.last_name      = u.last_name;
    editForm.email          = u.email;
    editForm.mobile_number  = u.mobile_number ?? '';
    editForm.status         = String(u.status);
    editForm.user_type      = u.user_type;
    editError.value         = '';
};

const submitEdit = async () => {
    if (!editTarget.value) return;
    saving.value    = true;
    editError.value = '';
    try {
        const res = await fetch(`/superadmin/users/update/${editTarget.value.id}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf(), 'Accept': 'application/json' },
            body: JSON.stringify({
                name:          editForm.name,
                last_name:     editForm.last_name,
                email:         editForm.email,
                mobile_number: editForm.mobile_number || null,
                status:        editForm.status,
                user_type:     editForm.user_type,
            }),
        });
        const json = await res.json();
        if (json.success) {
            toast.success(json.message);
            editTarget.value = null;
            router.reload({ only: ['users'] });
        } else {
            editError.value = json.message ?? 'Erreur lors de la mise à jour.';
        }
    } catch {
        editError.value = 'Erreur réseau.';
    } finally {
        saving.value = false;
    }
};

// ── Réinitialisation MDP (individuelle + masse) ────────────────────────────────
const resetTarget = ref<UserRow | null>(null);
const resetting   = ref(false);

const openResetConfirm  = (u: UserRow) => { resetTarget.value = u; };
const openBulkReset     = () => {
    // Pour la masse on ouvre une confirmation générique en réutilisant le même modal
    // On crée un faux user représentant la sélection
    resetTarget.value = {
        id: -1, name: 'sélectionnés', last_name: `${selectedIds.value.length} utilisateur(s)`,
        email: selectedIds.value.length + ' adresses email',
        mobile_number: null, user_type: -1, status: 1, profile_picture: null,
        created_at: '', is_online: false, role_label: '', role_names: [],
        permissions_count: 0, school_name: null,
    } as UserRow;
};

const executeReset = async () => {
    if (!resetTarget.value) return;
    resetting.value = true;
    const ids = resetTarget.value.id === -1 ? selectedIds.value : [resetTarget.value.id];
    try {
        const res = await fetch('/superadmin/users/reset-password', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf(), 'Accept': 'application/json' },
            body: JSON.stringify({ ids }),
        });
        const json = await res.json();
        if (json.success) {
            toast.success(json.message);
            resetTarget.value = null;
            selectedIds.value = [];
        } else {
            toast.error(json.message);
        }
    } catch {
        toast.error('Erreur réseau.');
    } finally {
        resetting.value = false;
    }
};

// ── Suppression ───────────────────────────────────────────────────────────────
const deleteTarget = ref<UserRow | null>(null);
const deleting     = ref(false);

const openDeleteConfirm = (u: UserRow) => { deleteTarget.value = u; };
const executeDelete = async () => {
    if (!deleteTarget.value) return;
    deleting.value = true;
    try {
        const res = await fetch(`/superadmin/users/delete/${deleteTarget.value.id}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf(), 'Accept': 'application/json' },
        });
        const json = await res.json();
        if (json.success) {
            toast.success(json.message);
            deleteTarget.value = null;
            router.reload({ only: ['users'] });
        } else {
            toast.error(json.message);
        }
    } catch {
        toast.error('Erreur réseau.');
    } finally {
        deleting.value = false;
    }
};

// ── Helpers visuels ───────────────────────────────────────────────────────────
const formatDate = (d: string) => d ? new Date(d).toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' }) : '—';
const initials   = (u: UserRow) => ((u.last_name?.[0] ?? '') + (u.name?.[0] ?? '')).toUpperCase() || '?';
const encodedId  = (id: number) => btoa(String(id));

const avatarColor = (t: number) => ({
    0: 'linear-gradient(135deg,#7c3aed,#5b21b6)',
    1: 'linear-gradient(135deg,#0ea5e9,#0284c7)',
    2: 'linear-gradient(135deg,#10b981,#059669)',
    3: 'linear-gradient(135deg,#f59e0b,#d97706)',
    4: 'linear-gradient(135deg,#ec4899,#db2777)',
} as Record<number,string>)[t] ?? 'linear-gradient(135deg,#6b7280,#4b5563)';

const roleStyle = (t: number) => ({
    0: { badge: 'bg-violet-100 text-violet-800 dark:bg-violet-900/30 dark:text-violet-300', dot: 'bg-violet-600' },
    1: { badge: 'bg-sky-100 text-sky-800 dark:bg-sky-900/30 dark:text-sky-300',           dot: 'bg-sky-500'    },
    2: { badge: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300', dot: 'bg-emerald-500' },
    3: { badge: 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',   dot: 'bg-amber-500'  },
    4: { badge: 'bg-pink-100 text-pink-800 dark:bg-pink-900/30 dark:text-pink-300',       dot: 'bg-pink-500'   },
} as Record<number,{badge:string;dot:string}>)[t] ?? { badge: 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300', dot: 'bg-gray-500' };

const permBadgeClass = (n: number) =>
    n === 0  ? 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' :
    n <= 5   ? 'bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-400' :
    n <= 10  ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' :
               'bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-400';
</script>

<style scoped>
.input-field {
    @apply w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600
           bg-white dark:bg-gray-700 text-sm text-gray-700 dark:text-gray-200
           placeholder-gray-400 dark:placeholder-gray-500
           focus:outline-none focus:ring-2 focus:ring-violet-500/30 focus:border-violet-400
           transition-all duration-150;
}
.checkbox-field {
    @apply w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-violet-600
           focus:ring-violet-500/30 cursor-pointer;
}
.form-label {
    @apply block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1;
}
.info-card {
    @apply bg-gray-50 dark:bg-gray-700/50 rounded-xl p-3 border border-gray-100 dark:border-gray-600;
}
.info-label {
    @apply text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500 mb-1;
}
/* Menu contextuel items */
.ctx-item {
    @apply flex w-full items-center gap-2.5 px-3 py-2.5 text-sm font-medium transition-colors;
}
.ctx-icon {
    @apply w-6 h-6 rounded-lg flex items-center justify-center flex-shrink-0;
}
</style>
