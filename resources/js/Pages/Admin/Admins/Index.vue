<template>
    <div class="space-y-6">
        <!-- Header -->
        <PageHeader title="Administrateurs" :subtitle="`${admins.total} administrateur(s)`" color="primary">
            <template #icon>
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </template>
            <template #actions>
                <AppButton v-if="canCreate" @click="openCreate">
                    <template #icon>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </template>
                    Nouvel administrateur
                </AppButton>
            </template>
        </PageHeader>

        <!-- Bannière mode super admin -->
        <div v-if="isSuperAdmin"
             class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl
                    bg-primary-50 dark:bg-primary-900/20
                    border border-primary-200 dark:border-primary-700 text-sm">
            <svg class="w-4 h-4 text-primary-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            <span class="text-primary-700 dark:text-primary-300 font-medium">Mode Super Admin</span>
            <span class="text-primary-600 dark:text-primary-400">— Double-cliquez sur une cellule pour l'éditer directement · Clic droit pour le menu rapide</span>
        </div>

        <!-- DataTable -->
        <DataTable
            ref="tableRef"
            :columns="columns"
            :rows="tableRows"
            row-key="id"
            export-filename="administrateurs"
            :exportable="canExport"
            :show-reset-password="canResetPassword"
            :inline-edit="isSuperAdmin"
            :inline-edit-endpoint="inlineEditEndpoint"
            :context-menu="true"
            :actions="rowActions"
            @delete="handleDelete"
            @reset-password="handleResetPassword"
            @action="handleAction"
        >
            <!-- Cellule utilisateur avec avatar -->
            <template #cell-user="{ row }">
                <div class="flex items-center gap-3">
                    <UserAvatar :src="row.profile_url as string" :name="row.name as string"
                                :last-name="row.last_name as string" size="sm"/>
                    <div>
                        <!-- Nom avec bouton copie au survol -->
                        <div class="group/name relative flex items-center">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                {{ row.last_name }} {{ row.name }}
                            </p>
                            <button
                                type="button"
                                class="ml-1.5 opacity-0 group-hover/name:opacity-100 transition-opacity duration-150
                                       p-0.5 rounded text-gray-400 hover:text-primary-600 hover:bg-primary-50
                                       dark:hover:text-primary-400 dark:hover:bg-primary-900/20"
                                :title="copiedField === `name-${row.id}` ? 'Copié !' : 'Copier le nom'"
                                @click.stop="copyToClipboard(`${row.last_name} ${row.name}`, `name-${row.id}`)"
                            >
                                <svg v-if="copiedField !== `name-${row.id}`" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                <svg v-else class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </button>
                        </div>
                        <!-- Email avec bouton copie au survol -->
                        <div class="group/email relative flex items-center">
                            <p class="text-xs text-gray-500">{{ row.email }}</p>
                            <button
                                type="button"
                                class="ml-1.5 opacity-0 group-hover/email:opacity-100 transition-opacity duration-150
                                       p-0.5 rounded text-gray-400 hover:text-violet-600 hover:bg-violet-50
                                       dark:hover:text-violet-400 dark:hover:bg-violet-900/20"
                                :title="copiedField === `email-${row.id}` ? 'Copié !' : 'Copier l\'email'"
                                @click.stop="copyToClipboard(row.email as string, `email-${row.id}`)"
                            >
                                <svg v-if="copiedField !== `email-${row.id}`" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                <svg v-else class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Statut -->
            <template #cell-status="{ row }">
                <AppBadge :variant="row.status == 1 ? 'success' : 'danger'" dot>
                    {{ row.status == 1 ? 'Actif' : 'Inactif' }}
                </AppBadge>
            </template>

            <!-- En ligne -->
            <template #cell-online="{ row }">
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full"
                      :class="row.is_online
                        ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-400'
                        : 'bg-gray-100 text-gray-500 border border-gray-200 dark:bg-gray-800 dark:text-gray-400'">
                    <span class="w-2 h-2 rounded-full flex-shrink-0"
                          :class="row.is_online
                            ? 'bg-emerald-500 shadow-[0_0_0_2px_rgba(16,185,129,0.25)] animate-pulse'
                            : 'bg-gray-400'"/>
                    {{ row.is_online ? 'En ligne' : 'Hors ligne' }}
                </span>
            </template>
            <template #cell-created_at="{ row }">
                <span class="text-xs text-gray-500">{{ formatDate(row.created_at as string) }}</span>
            </template>

            <!-- École (super admin uniquement) -->
            <template #cell-school_name="{ row }">
                <span v-if="row.school_name"
                      class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium
                             bg-indigo-50 text-indigo-700 border border-indigo-200
                             dark:bg-indigo-900/20 dark:text-indigo-400 dark:border-indigo-700 whitespace-nowrap">
                    <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    {{ row.school_name }}
                </span>
                <span v-else class="text-gray-400 dark:text-gray-600">—</span>
            </template>

            <!-- Actions row -->
            <template #actions="{ row }">
                <div class="flex items-center justify-end gap-1.5">
                    <!-- Voir les détails -->
                    <button v-if="canView" title="Voir les détails" @click="openView(row as any)"
                            class="p-1.5 rounded-xl transition-all duration-150
                                   text-white bg-violet-500 hover:bg-violet-600 active:bg-violet-700
                                   shadow-sm shadow-violet-200 dark:shadow-violet-900/40">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                    <!-- Modifier -->
                    <button v-if="canEdit" title="Modifier" @click="openEdit(row as any)"
                            class="p-1.5 rounded-xl transition-all duration-150
                                   text-white bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700
                                   shadow-sm shadow-emerald-200 dark:shadow-emerald-900/40">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </button>
                    <!-- Réinitialiser MDP -->
                    <button v-if="canResetPassword" title="Réinit. MDP" @click="tableRef?.confirmResetPassword(row.id as number, `${row.last_name} ${row.name}`)"
                            class="p-1.5 rounded-xl transition-all duration-150
                                   text-white bg-amber-500 hover:bg-amber-600 active:bg-amber-700
                                   shadow-sm shadow-amber-200 dark:shadow-amber-900/40">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                    </button>
                    <!-- Supprimer -->
                    <button v-if="canDelete" title="Supprimer" @click="tableRef?.confirmDelete(row.id as number, `${row.last_name} ${row.name}`)"
                            class="p-1.5 rounded-xl transition-all duration-150
                                   text-white bg-red-500 hover:bg-red-600 active:bg-red-700
                                   shadow-sm shadow-red-200 dark:shadow-red-900/40">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                    <!-- Aucune action disponible -->
                    <span v-if="!hasAnyAction" class="text-xs text-gray-400 italic px-2">—</span>
                </div>
            </template>

            <!-- Menu contextuel personnalisé -->
            <template #context-menu="{ row }">
                <button v-if="canView" @click="openView(row as any)"
                   class="flex items-center gap-2.5 px-3.5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-primary-50 dark:hover:bg-gray-700/60 hover:text-primary-700 transition-colors w-full text-left">
                    <svg class="w-4 h-4 text-primary-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Voir les détails
                </button>
                <button v-if="canEdit" @click="openEdit(row as any)"
                        class="flex w-full items-center gap-2.5 px-3.5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-emerald-50 dark:hover:bg-gray-700/60 hover:text-emerald-700 transition-colors">
                    <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Modifier
                </button>
                <Link :href="`/chat?receiver_id=${row.id_encoded}`"
                   class="flex items-center gap-2.5 px-3.5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-violet-50 dark:hover:bg-gray-700/60 hover:text-violet-700 transition-colors">
                    <svg class="w-4 h-4 text-violet-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    Envoyer un message
                </Link>
                <template v-if="canDelete">
                    <div class="my-1 border-t border-gray-100 dark:border-gray-700"/>
                    <button @click="tableRef?.confirmDelete(row.id as number, `${row.last_name} ${row.name}`)"
                            class="flex w-full items-center gap-2.5 px-3.5 py-2.5 text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Supprimer
                    </button>
                </template>
            </template>
        </DataTable>

        <!-- Modal Détails Administrateur — style settings panel -->
        <DetailModal
            v-model="showView"
            :title="viewTarget ? `${viewTarget.last_name} ${viewTarget.name}` : ''"
            subtitle="Administrateur"
            :initials="viewTarget ? (viewTarget.last_name?.[0] ?? '') + (viewTarget.name?.[0] ?? '') : '?'"
            :tabs="adminTabs"
            default-tab="profile"
            size="lg"
        >
            <template #avatar>
                <div class="relative">
                    <img v-if="viewTarget?.profile_picture"
                         :src="`/upload/profile/${viewTarget.profile_picture}`"
                         class="w-16 h-16 rounded-2xl object-cover shadow-md ring-2 ring-white dark:ring-gray-700"/>
                    <div v-else class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary-500 to-violet-600 flex items-center justify-center shadow-md">
                        <span class="text-xl font-bold text-white">
                            {{ (viewTarget?.last_name?.[0] ?? '') }}{{ (viewTarget?.name?.[0] ?? '') }}
                        </span>
                    </div>
                    <span :class="['absolute -bottom-1 -right-1 w-4 h-4 rounded-full border-2 border-white dark:border-gray-800 shadow-sm', viewTarget?.is_online ? 'bg-emerald-400' : 'bg-gray-300']" />
                </div>
            </template>

            <template #sidebar-footer>
                <Link v-if="viewTarget" :href="`/chat?receiver_id=${viewTarget.id_encoded}`"
                    class="flex items-center justify-center gap-2 w-full px-3 py-2 rounded-xl bg-primary-600 hover:bg-primary-700 text-white text-xs font-semibold transition-colors shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    Envoyer un message
                </Link>
            </template>

            <template #default="{ activeTab }">
                <div v-if="viewTarget">
                    <div v-show="activeTab === 'profile'" class="space-y-5">
                        <!-- Bannière -->
                        <div class="relative rounded-2xl overflow-hidden bg-gradient-to-br from-primary-600 to-violet-700 p-5">
                            <div class="absolute inset-0 opacity-10" style="background-image:radial-gradient(circle at 80% 20%, white 0%, transparent 60%)"/>
                            <div class="relative flex items-center gap-4">
                                <div class="relative flex-shrink-0">
                                    <img v-if="viewTarget.profile_picture"
                                         :src="`/upload/profile/${viewTarget.profile_picture}`"
                                         class="w-14 h-14 rounded-xl object-cover ring-4 ring-white/30 shadow-xl"/>
                                    <div v-else class="w-14 h-14 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center ring-4 ring-white/30 shadow-xl">
                                        <span class="text-lg font-bold text-white">{{ viewTarget.last_name?.[0] }}{{ viewTarget.name?.[0] }}</span>
                                    </div>
                                    <span class="absolute -bottom-1 -right-1 w-3.5 h-3.5 rounded-full border-2 border-white shadow-sm"
                                          :class="viewTarget.is_online ? 'bg-emerald-400' : 'bg-gray-400'"/>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h2 class="text-base font-bold text-white truncate">{{ viewTarget.last_name }} {{ viewTarget.name }}</h2>
                                    <p class="text-primary-200 text-xs mt-0.5">{{ viewTarget.email }}</p>
                                    <div class="flex items-center gap-2 mt-2 flex-wrap">
                                        <span :class="['px-2 py-0.5 rounded-full text-xs font-semibold', viewTarget.status == 1 ? 'bg-emerald-400/30 text-emerald-100' : 'bg-red-400/30 text-red-100']">
                                            {{ viewTarget.status == 1 ? '? Actif' : '? Inactif' }}
                                        </span>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-white/10 text-white/80">
                                            <span class="w-1.5 h-1.5 rounded-full" :class="viewTarget.is_online ? 'bg-emerald-400' : 'bg-gray-400'"/>
                                            {{ viewTarget.is_online ? 'En ligne' : 'Hors ligne' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <InfoCard label="Rôle" value="Administrateur" highlight />
                            <InfoCard label="Téléphone" :value="viewTarget.mobile_number" mono />
                            <InfoCard label="Email" :value="viewTarget.email" />
                            <InfoCard label="Inscrit le" :value="formatDate(viewTarget.created_at)" />
                            <InfoCard label="Statut" :badge="viewTarget.status == 1 ? 'success' : 'danger'" :value="viewTarget.status == 1 ? 'Actif' : 'Inactif'" />
                            <InfoCard v-if="isSuperAdmin && viewTarget.school_name" label="École" :value="viewTarget.school_name" highlight />
                        </div>
                    </div>
                </div>
            </template>

            <template #footer>
                <AppButton variant="ghost" @click="showView = false">Fermer</AppButton>
                <AppButton v-if="canEdit" @click="showView = false; openEdit(viewTarget!)">
                    <template #icon>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </template>
                    Modifier
                </AppButton>
            </template>
        </DetailModal>

        <!-- Modal Créer / Modifier -->
        <AppModal v-model="showForm" :title="editTarget ? 'Modifier l\'administrateur' : 'Nouvel administrateur'" size="lg">
            <form :id="formId" @submit.prevent="submitForm" class="space-y-4">
                <div class="flex items-center gap-4">
                    <UserAvatar :src="previewUrl" :name="form.name" :last-name="form.last_name" size="xl"/>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Photo de profil</label>
                        <input type="file" accept="image/*"
                               class="text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100"
                               @change="onFileChange"/>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <AppInput v-model="form.last_name" label="Prénoms" required :error="form.errors.last_name"/>
                    <AppInput v-model="form.name" label="Nom" required :error="form.errors.name"/>
                    <AppInput v-model="form.email" label="Email" type="email" required :error="form.errors.email"/>
                    <AppInput v-model="form.mobile_number" label="Téléphone" :error="form.errors.mobile_number"/>
                    <AppInput v-model="form.password"
                              :label="editTarget ? 'Nouveau mot de passe (optionnel)' : 'Mot de passe'"
                              :type="showPwd ? 'text' : 'password'" :required="!editTarget" :error="form.errors.password">
                        <template #suffix>
                            <button type="button" @click="showPwd = !showPwd" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path v-if="showPwd" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                    <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </template>
                    </AppInput>
                    <AppSelect v-model="form.status" label="Statut" :options="statusOptions" required :error="form.errors.status"/>
                </div>
            </form>
            <template #footer>
                <AppButton variant="ghost" @click="showForm = false">Annuler</AppButton>
                <AppButton type="submit" :form="formId" :loading="submitting">
                    {{ editTarget ? 'Enregistrer' : 'Créer' }}
                </AppButton>
            </template>
        </AppModal>
    </div>
</template>

<script setup lang="ts">
import { fmtDate } from '@/utils/dateFormat';
import { ref, computed, h, defineComponent } from 'vue';
import { router, useForm, Link } from '@inertiajs/vue3';
import { PageHeader, AppButton, AppInput, AppSelect, AppModal, AppBadge, DataTable, DetailModal } from '@/Components/UI';
import UserAvatar from '@/Components/Shared/UserAvatar.vue';
import { useToast } from '@/Composables/useToast';

// -- Composant InfoCard réutilisable ------------------------------------------
const InfoCard = defineComponent({
    props: {
        label:     { type: String, required: true },
        value:     { type: String, default: '' },
        highlight: { type: Boolean, default: false },
        mono:      { type: Boolean, default: false },
        badge:     { type: String, default: '' },
    },
    setup(p) {
        return () => h('div', {
            class: 'bg-gray-50 dark:bg-gray-800/60 rounded-xl p-4 border border-gray-100 dark:border-gray-700/60',
        }, [
            h('p', { class: 'text-[10px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1.5' }, p.label),
            p.badge
                ? h('span', {
                    class: ['inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold',
                        p.badge === 'success'
                            ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400'
                            : 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400'].join(' '),
                }, [h('span', { class: ['w-1.5 h-1.5 rounded-full', p.badge === 'success' ? 'bg-emerald-500' : 'bg-red-400'].join(' ') }), p.value || '—'])
                : h('p', {
                    class: ['text-sm font-semibold', p.highlight ? 'text-primary-700 dark:text-primary-400' : 'text-gray-800 dark:text-gray-200', p.mono ? 'font-mono' : ''].filter(Boolean).join(' '),
                }, p.value || '—'),
        ]);
    },
});
import { useCan } from '@/Composables/useCan';

interface Admin {
    id: number; name: string; last_name: string; email: string;
    status: number; profile_picture: string | null; created_at: string;
    is_online?: boolean; mobile_number?: string;
    id_encoded?: string;
    school_name?: string;
}

const props = defineProps<{
    admins: { data: Admin[]; total: number; from: number; to: number; links: any[] };
}>();

const { can, isSuperAdmin } = useCan();

// -- Helpers de permission ----------------------------------------------------
const canView           = computed(() => can('action.admins.view'));
const canCreate         = computed(() => can('action.admins.create'));
const canEdit           = computed(() => can('action.admins.edit'));
const canDelete         = computed(() => can('action.admins.delete'));
const canResetPassword  = computed(() => can('action.admins.reset_password'));
const canExport         = computed(() => can('action.admins.export'));
const hasAnyAction      = computed(() => canView.value || canEdit.value || canDelete.value || canResetPassword.value);

const inlineEditEndpoint = '/superadmin/users/inline-edit';

const toast    = useToast();
const tableRef = ref<InstanceType<typeof DataTable> | null>(null);
const formId   = 'admin-form';
const showForm = ref(false);
const showView = ref(false);
const editTarget = ref<Admin | null>(null);
const viewTarget = ref<Admin | null>(null);
const submitting = ref(false);
const showPwd    = ref(false);
const previewUrl = ref<string | null>(null);
const picFile    = ref<File | null>(null);

const statusOptions = [{ value: '1', label: 'Actif' }, { value: '0', label: 'Inactif' }];

const adminTabs = [
    {
        id: 'profile',
        label: 'Profil',
        description: 'Informations personnelles',
        icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>',
    },
];

// Actions pour le menu contextuel
const rowActions = computed(() => [
    ...(canView.value          ? [{ key: 'view',    label: 'Voir le profil', variant: 'primary' as const }] : []),
    ...(canEdit.value          ? [{ key: 'edit',    label: 'Modifier',       variant: 'success' as const }] : []),
    { key: 'message', label: 'Message', variant: 'info' as const },
    ...(canDelete.value        ? [{ key: 'delete',  label: 'Supprimer',      variant: 'danger'  as const }] : []),
]);

const columns = computed(() => [
    { key: 'user',       label: 'Administrateur', searchable: false },
    { key: 'last_name',  label: '',               searchable: true,  visible: false },
    { key: 'name',       label: '',               searchable: true,  visible: false },
    { key: 'email',      label: 'Email',          editable: isSuperAdmin.value, dataType: 'email' as const, sortable: true, searchable: true },
    { key: 'mobile_number', label: 'Téléphone',   editable: isSuperAdmin.value, dataType: 'tel' as const, searchable: true },
    ...(isSuperAdmin.value ? [{ key: 'school_name', label: 'École', sortable: false, searchable: false }] : []),
    { key: 'status',     label: 'Statut',         sortable: true,    searchable: false },
    { key: 'online',     label: 'En ligne',       sortable: false,   searchable: false },
    { key: 'created_at', label: 'Créé le',        sortable: true,    searchable: false },
]);

const tableRows = computed(() =>
    props.admins.data.map(a => ({
        ...a,
        profile_url: a.profile_picture ? `/upload/profile/${a.profile_picture}` : null,
        id_encoded:  btoa(String(a.id)),
        is_online:   a.is_online ?? false,
    }))
);

const form = useForm({ name: '', last_name: '', email: '', password: '', status: '1', mobile_number: '' });

// -- Copie dans le presse-papier -----------------------------------------------
const copiedField = ref<string | null>(null);
let copiedTimeout: ReturnType<typeof setTimeout> | null = null;

const copyToClipboard = (text: string, fieldKey: string) => {
    navigator.clipboard.writeText(text).then(() => {
        copiedField.value = fieldKey;
        if (copiedTimeout) clearTimeout(copiedTimeout);
        copiedTimeout = setTimeout(() => { copiedField.value = null; }, 1500);
    }).catch(() => toast.error('Impossible de copier.'));
};

const openCreate = () => {
    editTarget.value = null; previewUrl.value = null; picFile.value = null;
    showPwd.value = false; form.reset(); form.status = '1';
    showForm.value = true;
};
const openView = (admin: Admin) => {
    viewTarget.value = admin; showView.value = true;
};
const openEdit = (admin: Admin) => {
    editTarget.value = admin;
    previewUrl.value = admin.profile_picture ? `/upload/profile/${admin.profile_picture}` : null;
    picFile.value = null; showPwd.value = false;
    form.name = admin.name; form.last_name = admin.last_name;
    form.email = admin.email; form.password = ''; form.status = String(admin.status);
    form.mobile_number = admin.mobile_number ?? '';
    showForm.value = true;
};
const onFileChange = (e: Event) => {
    const f = (e.target as HTMLInputElement).files?.[0];
    if (f) { picFile.value = f; previewUrl.value = URL.createObjectURL(f); }
};
const submitForm = () => {
    const data = new FormData();
    data.append('name', form.name); data.append('last_name', form.last_name);
    data.append('email', form.email); data.append('status', form.status);
    data.append('mobile_number', form.mobile_number);
    if (form.password) data.append('password', form.password);
    if (picFile.value) data.append('profile_picture', picFile.value);
    submitting.value = true;
    const url = editTarget.value ? `/admin/admin/edit/${editTarget.value.id}` : '/admin/admin/add';
    router.post(url, data, {
        onSuccess: () => { showForm.value = false; toast.success(editTarget.value ? 'Administrateur modifié.' : 'Administrateur créé.'); },
        onError:   () => toast.error('Erreur lors de l\'enregistrement.'),
        onFinish:  () => { submitting.value = false; },
    });
};

// Gestionnaire d'actions (menu contextuel + dropdown)
const handleAction = (key: string, row: Record<string, unknown>) => {
    if (key === 'edit')    { openEdit(row as any); return; }
    if (key === 'view')    { openView(row as any); return; }
    if (key === 'message') { router.visit(`/chat?receiver_id=${row.id_encoded}`); return; }
    if (key === 'delete')  { tableRef.value?.confirmDelete(row.id as number, `${row.last_name} ${row.name}`); }
};

const handleDelete = (ids: (string | number)[]) => {
    ids.forEach(id => router.get(`/admin/admin/delete/${id}`, {}, {
        onSuccess: () => toast.success('Administrateur supprimé.'),
        onError:   () => toast.error('Erreur lors de la suppression.'),
    }));
};
const handleResetPassword = async (ids: (string | number)[]) => {
    try {
        const csrf = (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? '';
        const res  = await fetch('/admin/users/reset-password', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
            body: JSON.stringify({ ids }),
        });
        const data = await res.json();
        data.success ? toast.success(data.message) : toast.error(data.message);
    } catch { toast.error('Erreur lors de la réinitialisation.'); }
};
const formatDate = fmtDate;
</script>
