<template>
    <div class="space-y-6">
        <!-- Header -->
        <PageHeader title="Assignation matières-Classes" :subtitle="`${classSubjects.total} assignation(s)`" color="indigo">
            <template #icon>
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                </svg>
            </template>
            <template #actions>
                <AppButton v-if="canCreate" @click="openCreate">
                    <template #icon>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    </template>
                    Nouvelle assignation
                </AppButton>
            </template>
        </PageHeader>

        <!-- Table -->
        <DataTable
            ref="tableRef"
            :columns="columns"
            :rows="classSubjects.data"
            row-key="id"
            export-filename="assignations-matieres"
            :context-menu="true"
            @delete="handleDelete"
        >
            <template #cell-status="{ row }">
                <AppBadge :variant="row.status == 1 ? 'success' : 'danger'" dot>
                    {{ row.status == 1 ? 'Actif' : 'Inactif' }}
                </AppBadge>
            </template>
            <template #actions="{ row }">
                <div class="flex items-center justify-end gap-1.5">
                    <button title="Voir les détails" @click="openDetails(row as any)"
                            class="p-1.5 rounded-xl transition-all duration-150
                                   text-white bg-violet-500 hover:bg-violet-600 active:bg-violet-700
                                   shadow-sm shadow-violet-200 dark:shadow-violet-900/40">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                    <button v-if="canEdit" title="Modifier" @click="openEdit(row as any)"
                            class="p-1.5 rounded-xl transition-all duration-150
                                   text-white bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700
                                   shadow-sm shadow-emerald-200 dark:shadow-emerald-900/40">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </button>
                    <button v-if="canDelete" title="Supprimer" @click="tableRef?.confirmDelete(row.id as number, `${row.class_name} - ${row.subject_name}`)"
                            class="p-1.5 rounded-xl transition-all duration-150
                                   text-white bg-red-500 hover:bg-red-600 active:bg-red-700
                                   shadow-sm shadow-red-200 dark:shadow-red-900/40">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </template>
            <template #context-menu="{ row }">
                <button @click="openDetails(row as any)"
                        class="flex w-full items-center gap-2.5 px-3.5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-violet-50 dark:hover:bg-gray-700/60 hover:text-violet-700 transition-colors">
                    <svg class="w-4 h-4 text-violet-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                <template v-if="canDelete">
                    <div class="my-1 border-t border-gray-100 dark:border-gray-700"/>
                    <button @click="tableRef?.confirmDelete(row.id as number, `${row.class_name} - ${row.subject_name}`)"
                            class="flex w-full items-center gap-2.5 px-3.5 py-2.5 text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Supprimer
                    </button>
                </template>
            </template>
        </DataTable>

        <!-- Modal Créer -->
        <AppModal v-model="showCreateForm" title="Nouvelle assignation" size="md">
            <form :id="createFormId" @submit.prevent="submitCreate" class="space-y-4">
                <AppSelect
                    v-model="createForm.class_id"
                    label="Classe"
                    :options="classOptions"
                    placeholder="Sélectionner une classe"
                    required
                    :error="createForm.errors.class_id"
                />

                <!-- Alerte toutes matières déjà assignées -->
                <div
                    v-if="createForm.class_id && availableCount === 0"
                    class="flex items-start gap-2 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-3"
                >
                    <svg class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-xs text-amber-700 dark:text-amber-300">
                        Toutes les matières sont déjà assignées à cette classe. Modifiez les assignations existantes si nécessaire.
                    </p>
                </div>

                <!-- Info matières déjà assignées -->
                <div
                    v-else-if="createForm.class_id && alreadyAssignedSubjectIds.length > 0"
                    class="flex items-start gap-2 bg-violet-50 dark:bg-violet-900/20 border border-violet-200 dark:border-violet-800 rounded-xl p-3"
                >
                    <svg class="w-4 h-4 text-violet-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-xs text-violet-700 dark:text-violet-300">
                        {{ alreadyAssignedSubjectIds.length }} Matière(s) déjà assignée(s) à cette classe sont grisées et non sélectionnables.
                    </p>
                </div>

                <AppMultiSelect
                    v-model="createForm.subject_ids"
                    label="matières"
                    :options="subjectOptions"
                    placeholder="Sélectionner des matières"
                    required
                    :error="createForm.errors.subject_ids"
                />
                <AppInput v-model="createForm.coefficient" label="Coefficient" type="number" min="1" required :error="createForm.errors.coefficient" />
                <AppSelect v-model="createForm.status" label="Statut" :options="statusOptions" required :error="createForm.errors.status" />
            </form>
            <template #footer>
                <AppButton variant="ghost" @click="showCreateForm = false">Annuler</AppButton>
                <AppButton
                    type="submit"
                    :form="createFormId"
                    :loading="createForm.processing"
                    :disabled="createForm.class_id !== '' && availableCount === 0"
                >
                    Assigner
                </AppButton>
            </template>
        </AppModal>

        <!-- Modal Modifier (single) -->
        <AppModal v-model="showEditForm" title="Modifier l'assignation" size="md">
            <form :id="editFormId" @submit.prevent="submitEdit" class="space-y-4">
                <AppSelect
                    v-model="editForm.class_id"
                    label="Classe"
                    :options="classOptions"
                    placeholder="Sélectionner une classe"
                    required
                    :error="editForm.errors.class_id"
                />
                <AppSelect
                    v-model="editForm.subject_id"
                    label="Matière"
                    :options="subjectOptionsEdit"
                    placeholder="Sélectionner une Matière"
                    required
                    :error="editForm.errors.subject_id"
                />
                <AppInput v-model="editForm.coefficient" label="Coefficient" type="number" min="1" required :error="editForm.errors.coefficient" />
                <AppSelect v-model="editForm.status" label="Statut" :options="statusOptions" required :error="editForm.errors.status" />
            </form>
            <template #footer>
                <AppButton variant="ghost" @click="showEditForm = false">Annuler</AppButton>
                <AppButton type="submit" :form="editFormId" :loading="editForm.processing">Enregistrer</AppButton>
            </template>
        </AppModal>

        <!-- Drawer Voir détails -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition-all duration-300 ease-out"
                leave-active-class="transition-all duration-200 ease-in"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div v-if="showDetails" class="fixed inset-0 z-50 flex justify-end" @click.self="showDetails = false">
                    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="showDetails = false" />

                    <Transition
                        enter-active-class="transition-transform duration-300 ease-out"
                        leave-active-class="transition-transform duration-200 ease-in"
                        enter-from-class="translate-x-full"
                        enter-to-class="translate-x-0"
                        leave-from-class="translate-x-0"
                        leave-to-class="translate-x-full"
                    >
                        <div v-if="showDetails" class="relative w-full max-w-sm bg-white dark:bg-gray-900 h-full shadow-2xl flex flex-col">

                            <!-- ── Header illustré ───────────────────────────── -->
                            <div class="relative flex-shrink-0 overflow-hidden bg-gradient-to-br from-indigo-500 via-indigo-600 to-violet-700" style="min-height:120px;">
                                <!-- Pattern géométrique décoratif -->
                                <div class="pointer-events-none absolute inset-0" aria-hidden="true">
                                    <svg class="absolute inset-0 w-full h-full opacity-[0.12]" xmlns="http://www.w3.org/2000/svg">
                                        <defs>
                                            <pattern id="as-diamond" x="0" y="0" width="24" height="24" patternUnits="userSpaceOnUse">
                                                <path d="M12 0 L24 12 L12 24 L0 12 Z" fill="none" stroke="white" stroke-width="1"/>
                                            </pattern>
                                        </defs>
                                        <rect width="100%" height="100%" fill="url(#as-diamond)"/>
                                    </svg>
                                    <!-- Cercles décoratifs -->
                                    <div class="absolute -top-6 -right-6 w-32 h-32 rounded-full bg-white/10 blur-2xl"/>
                                    <div class="absolute -bottom-4 -left-4 w-24 h-24 rounded-full bg-violet-400/20 blur-xl"/>
                                    <!-- Fade bas -->
                                    <div class="absolute bottom-0 left-0 right-0 h-10 bg-gradient-to-t from-black/10 to-transparent"/>
                                </div>

                                <!-- Bouton fermer -->
                                <button @click="showDetails = false"
                                    class="absolute top-3 right-3 z-20 p-1.5 rounded-xl bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white transition-colors shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>

                                <!-- Icône matière + titre -->
                                <div class="relative z-10 flex items-center gap-4 px-6 pt-5 pb-5">
                                    <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-sm ring-4 ring-white/25 flex items-center justify-center flex-shrink-0 shadow-lg">
                                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h2 class="text-lg font-bold text-white truncate leading-tight">Détails de l'assignation</h2>
                                        <p class="text-white/70 text-sm mt-0.5">Matière à Classe</p>
                                    </div>
                                </div>
                            </div>

                            <!-- ── Body ──────────────────────────────────────── -->
                            <div v-if="detailsTarget" class="flex-1 overflow-y-auto px-6 py-5 space-y-4">

                                <!-- Classe + Matière -->
                                <div class="rounded-xl bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-800/40 p-4 space-y-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-800/50 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-bold text-indigo-500 uppercase tracking-wider mb-0.5">Classe</p>
                                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ detailsTarget.class_name }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 pt-3 border-t border-indigo-100 dark:border-indigo-800/40">
                                        <div class="w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-800/50 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-bold text-indigo-500 uppercase tracking-wider mb-0.5">Matière</p>
                                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ detailsTarget.subject_name }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Coefficient -->
                                <div class="rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 p-4 flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Coefficient</p>
                                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ detailsTarget.coefficient ?? 1 }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Statut -->
                                <div class="rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 p-4 flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                                         :class="detailsTarget.status == 1 ? 'bg-emerald-100 dark:bg-emerald-900/30' : 'bg-red-100 dark:bg-red-900/30'">
                                        <svg class="w-5 h-5" :class="detailsTarget.status == 1 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="detailsTarget.status == 1 ? 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' : 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Statut</p>
                                        <AppBadge :variant="detailsTarget.status == 1 ? 'success' : 'danger'" dot>
                                            {{ detailsTarget.status == 1 ? 'Actif' : 'Inactif' }}
                                        </AppBadge>
                                    </div>
                                </div>

                                <!-- Métadonnées -->
                                <div class="rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 divide-y divide-gray-100 dark:divide-gray-700 overflow-hidden">
                                    <div v-if="detailsTarget.created_by_name" class="flex items-center gap-3 p-4">
                                        <div class="w-8 h-8 rounded-lg bg-gray-200 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Créé par</p>
                                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ detailsTarget.created_by_name }}</p>
                                        </div>
                                    </div>
                                    <div v-if="detailsTarget.created_at" class="flex items-center gap-3 p-4">
                                        <div class="w-8 h-8 rounded-lg bg-gray-200 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Date de création</p>
                                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ formatDate(detailsTarget.created_at) }}</p>
                                        </div>
                                    </div>
                                    <div v-if="detailsTarget.updated_at" class="flex items-center gap-3 p-4">
                                        <div class="w-8 h-8 rounded-lg bg-gray-200 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Dernière modification</p>
                                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ formatDate(detailsTarget.updated_at) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ── Footer ────────────────────────────────────── -->
                            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex gap-2 bg-white/80 dark:bg-gray-900/80 backdrop-blur-sm">
                                <AppButton variant="close" class="flex-1" @click="showDetails = false">Fermer</AppButton>
                                <AppButton v-if="canEdit" class="flex-1" @click="() => { showDetails = false; openEdit(detailsTarget!) }">
                                    <template #icon>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </template>
                                    Modifier
                                </AppButton>
                            </div>
                        </div>
                    </Transition>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { PageHeader, AppButton, AppInput, AppSelect, AppModal, DataTable, AppBadge } from '@/Components/UI';
import AppMultiSelect from '@/Components/UI/AppMultiSelect.vue';
import { useCan } from '@/Composables/useCan';
import { useToast } from '@/Composables/useToast';
import { fmtDate } from '@/Utils/dateFormat';

const { can } = useCan();
const canCreate = computed(() => can('action.assign_subjects.create'));
const canEdit   = computed(() => can('action.assign_subjects.edit'));
const canDelete = computed(() => can('action.assign_subjects.delete'));

interface ClassSubject {
    id: number;
    class_id: number;
    subject_id: number;
    class_name: string;
    subject_name: string;
    status: number;
    coefficient: number;
    created_by_name?: string;
    created_at?: string;
    updated_at?: string;
}

interface ClassItem   { id: number; name: string; }
interface SubjectItem { id: number; name: string; }

const props = defineProps<{
    classSubjects: {
        data: ClassSubject[];
        total: number;
        from: number;
        to: number;
        links: { url: string | null; label: string; active: boolean }[];
    };
    classes:  ClassItem[];
    subjects: SubjectItem[];
}>();

const createFormId = 'assign-subject-create-form';
const editFormId   = 'assign-subject-edit-form';

const showCreateForm = ref(false);
const showEditForm   = ref(false);
const showDetails    = ref(false);

const editTarget    = ref<ClassSubject | null>(null);
const detailsTarget = ref<ClassSubject | null>(null);

const toast    = useToast();
const tableRef = ref<InstanceType<typeof DataTable> | null>(null);

// IDs des matières déjà assignées é la classe sélectionnée dans le formulaire de création
const alreadyAssignedSubjectIds = ref<number[]>([]);

const statusOptions = [
    { value: '1', label: 'Actif' },
    { value: '0', label: 'Inactif' },
];

const classOptions = computed(() =>
    props.classes.map(c => ({ value: String(c.id), label: c.name }))
);

/** matières avec disabled=true si déjà assignées à la classe sélectionnée */
const subjectOptions = computed(() =>
    props.subjects.map(s => ({
        value:    String(s.id),
        label:    s.name,
        disabled: alreadyAssignedSubjectIds.value.includes(s.id),
    }))
);

/** Options pour le formulaire d'édition (toutes activées) */
const subjectOptionsEdit = computed(() =>
    props.subjects.map(s => ({ value: String(s.id), label: s.name }))
);

const columns = [
    { key: 'class_name',      label: 'Classe',        sortable: true  },
    { key: 'subject_name',    label: 'Matière',        sortable: true  },
    { key: 'coefficient',     label: 'Coefficient',    sortable: true  },
    { key: 'status',          label: 'Statut',         sortable: true, exportFormat: (v: unknown) => (v == 1 ? 'Actif' : 'Inactif')  },
    { key: 'created_by_name', label: 'Créé par',       sortable: false },
    { key: 'created_at',      label: 'Date création',  sortable: true,
      format: (v: unknown) => fmtDate(v as string) },
];

const createForm = useForm({
    class_id:    '',
    subject_ids: [] as string[],
    coefficient: '1',
    status:      '1',
});

const editForm = useForm({
    class_id:    '',
    subject_id:  '',
    coefficient: '1',
    status:      '1',
});

// -- Quand la classe change dans le formulaire création, recalculer les doublons --
watch(() => createForm.class_id, (newClassId) => {
    if (!newClassId) {
        alreadyAssignedSubjectIds.value = [];
        createForm.subject_ids = [];
        return;
    }
    const classIdNum = parseInt(newClassId);
    // On cherche dans les données déjà chargées (pagination é on compare sur la page courante)
    // Pour étre exhaustif on fait un appel API
    fetch(`/admin/practicalworks/homework/getSubjectByClassId/${newClassId}`, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin',
    })
        .then(r => r.ok ? r.json() : { getSubject: [] })
        .then(data => {
            alreadyAssignedSubjectIds.value = (data.getSubject ?? []).map((s: any) => s.subject_id);
        })
        .catch(() => { alreadyAssignedSubjectIds.value = []; });

    // Retirer de la sélection les matières déjà assignées
    createForm.subject_ids = createForm.subject_ids.filter(
        id => !alreadyAssignedSubjectIds.value.includes(parseInt(id))
    );
});

const formatDate = fmtDate;

// -- Nombre de matières encore disponibles pour cette classe --
const availableCount = computed(() => {
    if (!createForm.class_id) return props.subjects.length;
    return props.subjects.filter(s => !alreadyAssignedSubjectIds.value.includes(s.id)).length;
});

// -- Ouvrir créer -------------------------------------------------------------
const openCreate = () => {
    createForm.reset();
    createForm.coefficient = '1';
    createForm.status = '1';
    alreadyAssignedSubjectIds.value = [];
    showCreateForm.value = true;
};

// -- Ouvrir modifier ----------------------------------------------------------
const openEdit = (item: ClassSubject) => {
    editTarget.value = item;
    editForm.class_id    = String(item.class_id);
    editForm.subject_id  = String(item.subject_id);
    editForm.coefficient = String(item.coefficient ?? 1);
    editForm.status      = String(item.status);
    showEditForm.value = true;
};

// -- Ouvrir détails -----------------------------------------------------------
const openDetails = (item: ClassSubject) => {
    detailsTarget.value = item;
    showDetails.value = true;
};

// -- Soumettre créer ----------------------------------------------------------
const submitCreate = () => {
    // Filtrer les matières déjà assignées avant soumission (sécurité cété client)
    const filteredIds = createForm.subject_ids.filter(
        id => !alreadyAssignedSubjectIds.value.includes(parseInt(id))
    );

    if (filteredIds.length === 0) {
        toast.error('Toutes les matières sélectionnées sont déjà assignées é cette classe.');
        return;
    }

    const data = new FormData();
    data.append('class_id', createForm.class_id);
    data.append('coefficient', createForm.coefficient);
    data.append('status', createForm.status);
    filteredIds.forEach(id => data.append('subject_id[]', id));

    router.post('/admin/assign_subject/add', data, {
        onSuccess: () => {
            showCreateForm.value = false;
            createForm.reset();
            createForm.coefficient = '1';
            createForm.status = '1';
            alreadyAssignedSubjectIds.value = [];
        },
        onError: (errors) => {
            toast.error(Object.values(errors)[0] as string || 'Erreur lors de l\'assignation.');
        },
    });
};

// -- Soumettre modifier -------------------------------------------------------
const submitEdit = () => {
    if (!editTarget.value) return;
    editForm.post(`/admin/assign_subject/edit_single/${editTarget.value.id}`, {
        onSuccess: () => { showEditForm.value = false; },
        onError: (errors) => {
            toast.error(Object.values(errors)[0] as string || 'Erreur lors de la modification.');
        },
    });
};

// -- Supprimer (bulk via DataTable) -------------------------------------------
const handleDelete = (ids: (string | number)[]) => {
    ids.forEach(id => {
        router.get(`/admin/assign_subject/delete/${id}`, {}, {
            onSuccess: () => toast.success('Assignation supprimée avec succés.'),
            onError: () => toast.error('Erreur lors de la suppression.'),
        });
    });
};
</script>
