<template>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Parents</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ parents.total }} parent(s)</p>
            </div>
            <AppButton v-if="canCreate" @click="openCreate">
                <template #icon>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </template>
                Nouveau parent
            </AppButton>
        </div>

        <div v-if="isSuperAdmin"
             class="flex items-center gap-2.5 px-4 py-2.5 rounded-lg bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-700 text-sm">
            <svg class="w-4 h-4 text-primary-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            <span class="text-primary-700 dark:text-primary-300 font-medium">Mode Super Admin</span>
            <span class="text-primary-600 dark:text-primary-400">— Double-cliquez sur une cellule pour l'éditer · Clic droit pour le menu rapide</span>
        </div>

        <DataTable
            ref="tableRef"
            :columns="columns"
            :rows="tableRows"
            row-key="id"
            export-filename="parents"
            :exportable="canExport"
            :show-reset-password="canResetPassword"
            :inline-edit="isSuperAdmin"
            :inline-edit-endpoint="inlineEditEndpoint"
            :inline-edit-id-key="'id'"
            :context-menu="true"
            @delete="handleDelete"
            @reset-password="handleResetPassword"
        >
            <template #cell-user="{ row }">
                <div class="flex items-center gap-3">
                    <UserAvatar :src="row.profile_url as string" :name="row.name as string"
                                :last-name="row.last_name as string" size="sm"/>
                    <div>
                        <div class="group/name flex items-center">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ row.last_name }} {{ row.name }}</p>
                            <button type="button"
                                    class="ml-1.5 opacity-0 group-hover/name:opacity-100 transition-opacity duration-150
                                           p-0.5 rounded text-gray-400 hover:text-primary-600 hover:bg-primary-50
                                           dark:hover:text-primary-400 dark:hover:bg-primary-900/20"
                                    :title="copiedField === `name-${row.id}` ? 'Copié !' : 'Copier le nom'"
                                    @click.stop="copyToClipboard(`${row.last_name} ${row.name}`, `name-${row.id}`)">
                                <svg v-if="copiedField !== `name-${row.id}`" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                <svg v-else class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </button>
                        </div>
                        <div class="group/email flex items-center mt-0.5">
                            <p class="text-xs text-gray-500">{{ row.email }}</p>
                            <button type="button"
                                    class="ml-1.5 opacity-0 group-hover/email:opacity-100 transition-opacity duration-150
                                           p-0.5 rounded text-gray-400 hover:text-blue-600 hover:bg-blue-50
                                           dark:hover:text-blue-400 dark:hover:bg-blue-900/20"
                                    :title="copiedField === `email-${row.id}` ? 'Copié !' : 'Copier l\'email'"
                                    @click.stop="copyToClipboard(row.email as string, `email-${row.id}`)">
                                <svg v-if="copiedField !== `email-${row.id}`" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                <svg v-else class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </template>

            <template #cell-status="{ row }">
                <AppBadge :variant="row.status == 1 ? 'success' : 'danger'" dot>
                    {{ row.status == 1 ? 'Actif' : 'Inactif' }}
                </AppBadge>
            </template>

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

            <template #actions="{ row }">
                <div class="flex items-center justify-end gap-1.5">
                    <button v-if="canView" title="Voir les détails" @click="openView(row as any)"
                            class="p-1.5 rounded-lg transition-all duration-150
                                   text-white bg-violet-500 hover:bg-violet-600 active:bg-violet-700
                                   shadow-sm shadow-violet-200 dark:shadow-violet-900/40">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                    <button v-if="canEdit" title="Modifier" @click="openEdit(row as any)"
                            class="p-1.5 rounded-lg transition-all duration-150
                                   text-white bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700
                                   shadow-sm shadow-emerald-200 dark:shadow-emerald-900/40">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </button>
                    <Link :href="`/admin/parent/student/${row.id}`" title="Gérer les enfants"
                       class="p-1.5 rounded-lg transition-all duration-150
                              text-white bg-indigo-500 hover:bg-indigo-600 active:bg-indigo-700
                              shadow-sm shadow-indigo-200 dark:shadow-indigo-900/40">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </Link>
                    <button v-if="canDelete" title="Supprimer"
                            @click="tableRef?.confirmDelete(row.id as number, `${row.last_name} ${row.name}`)"
                            class="p-1.5 rounded-lg transition-all duration-150
                                   text-white bg-red-500 hover:bg-red-600 active:bg-red-700
                                   shadow-sm shadow-red-200 dark:shadow-red-900/40">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </template>

            <template #context-menu="{ row }">
                <button v-if="canView" @click="openView(row as any)"
                        class="flex w-full items-center gap-2.5 px-3.5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-primary-50 dark:hover:bg-gray-700/60 hover:text-primary-700 transition-colors">
                    <svg class="w-4 h-4 text-primary-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Voir les détails
                </button>
                <button v-if="canEdit" @click="openEdit(row as any)"
                        class="flex w-full items-center gap-2.5 px-3.5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-emerald-50 dark:hover:bg-gray-700/60 hover:text-emerald-700 transition-colors">
                    <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Modifier
                </button>
                <Link :href="`/admin/parent/student/${(row as any).id}`"
                   class="flex items-center gap-2.5 px-3.5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-primary-50 dark:hover:bg-gray-700/60 hover:text-primary-700 transition-colors">
                    <svg class="w-4 h-4 text-primary-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Gérer les enfants
                </Link>
                <Link :href="`/chat?receiver_id=${(row as any).id_encoded}`"
                   class="flex items-center gap-2.5 px-3.5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700/60 hover:text-blue-700 transition-colors">
                    <svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    Envoyer un message
                </Link>
                <template v-if="canDelete">
                    <div class="my-1 border-t border-gray-100 dark:border-gray-700"/>
                    <button @click="tableRef?.confirmDelete((row as any).id, `${row.last_name} ${row.name}`)"
                            class="flex w-full items-center gap-2.5 px-3.5 py-2.5 text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Supprimer
                    </button>
                </template>
            </template>
        </DataTable>

        <!-- Modal Détails Parent -->
        <AppModal v-model="showView" title="Détails du parent" size="md">
            <div v-if="viewTarget" class="space-y-5">
                <div class="relative rounded-2xl overflow-hidden bg-gradient-to-br from-blue-600 to-indigo-700 p-6">
                    <div class="absolute inset-0 opacity-10"
                         style="background-image:radial-gradient(circle at 80% 20%, white 0%, transparent 60%)"/>
                    <div class="relative flex items-center gap-4">
                        <div class="relative flex-shrink-0">
                            <img v-if="viewTarget.profile_picture"
                                 :src="`/upload/profile/${viewTarget.profile_picture}`"
                                 class="w-16 h-16 rounded-full object-cover ring-4 ring-white/30 shadow-xl"/>
                            <div v-else
                                 class="w-16 h-16 rounded-full bg-white/20 backdrop-blur flex items-center justify-center ring-4 ring-white/30 shadow-xl">
                                <span class="text-xl font-bold text-white">
                                    {{ (viewTarget.last_name?.[0] ?? '') }}{{ (viewTarget.name?.[0] ?? '') }}
                                </span>
                            </div>
                            <span class="absolute bottom-0.5 right-0.5 w-3.5 h-3.5 rounded-full border-2 border-white shadow-sm"
                                  :class="viewTarget.is_online ? 'bg-emerald-400' : 'bg-gray-400'"/>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h2 class="text-lg font-bold text-white truncate">{{ viewTarget.last_name }} {{ viewTarget.name }}</h2>
                            <p class="text-blue-100 text-sm mt-0.5">{{ viewTarget.email }}</p>
                            <div class="flex items-center gap-2 mt-2 flex-wrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                      :class="viewTarget.status == 1 ? 'bg-emerald-400/30 text-emerald-100' : 'bg-red-400/30 text-red-100'">
                                    {{ viewTarget.status == 1 ? '✓ Actif' : '✗ Inactif' }}
                                </span>
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-white/10 text-white/80">
                                    <span class="w-1.5 h-1.5 rounded-full" :class="viewTarget.is_online ? 'bg-emerald-400' : 'bg-gray-400'"/>
                                    {{ viewTarget.is_online ? 'En ligne' : 'Hors ligne' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="bg-gray-50 dark:bg-gray-800/60 rounded-lg p-4 border border-gray-100 dark:border-gray-700">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">Téléphone</p>
                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 font-mono">{{ viewTarget.mobile_number ?? '—' }}</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800/60 rounded-lg p-4 border border-gray-100 dark:border-gray-700">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">Genre</p>
                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                            {{ viewTarget.gender === 'male' ? 'Masculin' : viewTarget.gender === 'female' ? 'Féminin' : '—' }}
                        </p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800/60 rounded-lg p-4 border border-gray-100 dark:border-gray-700">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">Profession</p>
                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ viewTarget.occupation ?? '—' }}</p>
                    </div>
                    <div v-if="viewTarget.address" class="bg-gray-50 dark:bg-gray-800/60 rounded-lg p-4 border border-gray-100 dark:border-gray-700">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">Adresse</p>
                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ viewTarget.address }}</p>
                    </div>
                </div>
            </div>
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
        </AppModal>

        <!-- Modal Formulaire -->
        <AppModal v-model="showForm" :title="editTarget ? 'Modifier le parent' : 'Nouveau parent'" size="lg">
            <form :id="formId" @submit.prevent="submitForm" class="space-y-4">
                <div class="flex items-center gap-4 pb-2 border-b border-gray-100 dark:border-gray-700">
                    <UserAvatar :src="previewUrl" :name="form.name" :last-name="form.last_name" size="xl"/>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Photo de profil</label>
                        <input type="file" accept="image/*"
                               class="text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100"
                               @change="onFileChange"/>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <AppInput v-model="form.last_name" label="Prénoms" required/>
                    <AppInput v-model="form.name" label="Nom" required/>
                    <AppInput v-model="form.email" label="Email" type="email" required/>
                    <AppInput v-model="form.mobile_number" label="Téléphone"/>
                    <AppSelect v-model="form.gender" label="Genre" :options="genderOptions" placeholder="Sélectionner..."/>
                    <AppInput v-model="form.occupation" label="Profession"/>
                    <AppSelect v-model="form.status" label="Statut" :options="statusOptions" required/>
                    <AppInput v-model="form.password"
                              :label="editTarget ? 'Nouveau mot de passe (optionnel)' : 'Mot de passe'"
                              type="password" :required="!editTarget"/>
                </div>
                <AppInput v-model="form.address" label="Adresse"/>
            </form>
            <template #footer>
                <AppButton variant="ghost" @click="showForm = false">Annuler</AppButton>
                <AppButton type="submit" :form="formId" :loading="submitting">
                    {{ editTarget ? 'Enregistrer' : 'Créer' }}
                </AppButton>
            </template>
        </AppModal>

        <!-- Modal Supprimer -->
        <AppModal v-model="showDelete" title="Supprimer le parent" size="sm" persistent>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Voulez-vous vraiment supprimer
                <strong class="text-gray-900 dark:text-white">{{ deleteTarget?.last_name }} {{ deleteTarget?.name }}</strong> ?
            </p>
            <template #footer>
                <AppButton variant="ghost" @click="showDelete = false">Annuler</AppButton>
                <AppButton variant="danger" :loading="deleting" @click="confirmDelete">Supprimer</AppButton>
            </template>
        </AppModal>
    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import { AppButton, AppInput, AppSelect, AppModal, DataTable, AppBadge } from '@/Components/UI';
import UserAvatar from '@/Components/Shared/UserAvatar.vue';
import { useToast } from '@/Composables/useToast';
import { useCan } from '@/Composables/useCan';

interface Parent {
    id: number; name: string; last_name: string; email: string;
    status: number; gender: string; mobile_number: string;
    occupation: string; address: string;
    profile_picture: string | null; is_online?: boolean;
}

const props = defineProps<{
    parents: { data: Parent[]; total: number; from: number; to: number; links: any[] };
}>();

const { can, isSuperAdmin } = useCan();
const canView          = computed(() => can('action.parents.view'));
const canCreate        = computed(() => can('action.parents.create'));
const canEdit          = computed(() => can('action.parents.edit'));
const canDelete        = computed(() => can('action.parents.delete'));
const canResetPassword = computed(() => can('action.parents.reset_password'));
const canExport        = computed(() => can('action.parents.export'));

const inlineEditEndpoint = '/superadmin/users/inline-edit';

const formId     = 'parent-form';
const showForm   = ref(false);
const showDelete = ref(false);
const showView   = ref(false);
const editTarget   = ref<Parent | null>(null);
const deleteTarget = ref<Parent | null>(null);
const viewTarget   = ref<Parent | null>(null);
const deleting   = ref(false);
const submitting = ref(false);
const previewUrl = ref<string | null>(null);
const picFile    = ref<File | null>(null);
const toast      = useToast();
const tableRef   = ref<InstanceType<typeof DataTable> | null>(null);

const statusOptions = [{ value: '1', label: 'Actif' }, { value: '0', label: 'Inactif' }];
const genderOptions = [{ value: 'male', label: 'Masculin' }, { value: 'female', label: 'Féminin' }, { value: 'other', label: 'Autre' }];

const columns = computed(() => [
    { key: 'user',          label: 'Parent',     searchable: false },
    { key: 'last_name',     label: '',           searchable: true,  visible: false },
    { key: 'name',          label: '',           searchable: true,  visible: false },
    { key: 'email',         label: '',           searchable: true,  visible: false },
    { key: 'mobile_number', label: 'Téléphone',  editable: isSuperAdmin.value, dataType: 'tel' as const, searchable: true },
    { key: 'occupation',    label: 'Profession', editable: isSuperAdmin.value, sortable: true, searchable: true },
    { key: 'status',        label: 'Statut',     sortable: true,    searchable: false },
    { key: 'online',        label: 'En ligne',   sortable: false,   searchable: false },
]);

const tableRows = computed(() =>
    props.parents.data.map(p => ({
        ...p,
        profile_url: p.profile_picture ? `/upload/profile/${p.profile_picture}` : null,
        id_encoded:  btoa(String(p.id)),
        is_online:   p.is_online ?? false,
    }))
);

const emptyForm = () => ({
    name: '', last_name: '', email: '', password: '', status: '1',
    gender: '', mobile_number: '', occupation: '', address: '',
});
const form = ref(emptyForm());

const openCreate = () => {
    editTarget.value = null; previewUrl.value = null; picFile.value = null;
    form.value = emptyForm(); showForm.value = true;
};
const openView = (p: Parent) => { viewTarget.value = p; showView.value = true; };
const openEdit = (p: Parent) => {
    editTarget.value = p;
    previewUrl.value = p.profile_picture ? `/upload/profile/${p.profile_picture}` : null;
    picFile.value = null;
    form.value = {
        name: p.name, last_name: p.last_name, email: p.email, password: '',
        status: String(p.status), gender: p.gender ?? '',
        mobile_number: p.mobile_number ?? '', occupation: p.occupation ?? '',
        address: p.address ?? '',
    };
    showForm.value = true;
};
const openDelete = (p: Parent) => { deleteTarget.value = p; showDelete.value = true; };
const onFileChange = (e: Event) => {
    const f = (e.target as HTMLInputElement).files?.[0];
    if (f) { picFile.value = f; previewUrl.value = URL.createObjectURL(f); }
};
const submitForm = () => {
    const data = new FormData();
    Object.entries(form.value).forEach(([k, v]) => { if (v) data.append(k, String(v)); });
    if (picFile.value) data.append('profile_picture', picFile.value);
    submitting.value = true;
    const url = editTarget.value ? `/admin/parent/edit/${editTarget.value.id}` : '/admin/parent/add';
    router.post(url, data, {
        onSuccess: () => { showForm.value = false; toast.success(editTarget.value ? 'Parent modifié.' : 'Parent créé.'); },
        onError:   () => toast.error('Erreur lors de l\'enregistrement.'),
        onFinish:  () => { submitting.value = false; },
    });
};
const confirmDelete = () => {
    if (!deleteTarget.value) return;
    deleting.value = true;
    router.get(`/admin/parent/delete/${deleteTarget.value.id}`, {}, {
        onFinish: () => { deleting.value = false; showDelete.value = false; },
    });
};

// ── Copie presse-papier ───────────────────────────────────────────────────────
const copiedField = ref<string | null>(null);
let copiedTimeout: ReturnType<typeof setTimeout> | null = null;
const copyToClipboard = (text: string, fieldKey: string) => {
    navigator.clipboard.writeText(text).then(() => {
        copiedField.value = fieldKey;
        if (copiedTimeout) clearTimeout(copiedTimeout);
        copiedTimeout = setTimeout(() => { copiedField.value = null; }, 1500);
    }).catch(() => toast.error('Impossible de copier.'));
};

const handleDelete = (ids: (string | number)[]) => {
    ids.forEach(id => router.get(`/admin/parent/delete/${id}`, {}, {
        onSuccess: () => toast.success('Parent supprimé.'),
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
</script>
