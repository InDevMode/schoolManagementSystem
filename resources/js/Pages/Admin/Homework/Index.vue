<template>
    <div class="space-y-6">
        <!-- En-tête -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Travaux de maison</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ works.total }} travail(aux) enregistré(s)</p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <AppButton variant="ghost" size="sm" :href="'/admin/practicalworks/homework/trash'">
                    <template #icon>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </template>
                    Corbeille
                </AppButton>
                <AppButton @click="openCreate" v-if="props.canCreate">
                    <template #icon>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </template>
                    Nouveau travail
                </AppButton>
            </div>
        </div>

        <!-- Table -->
        <DataTable
            ref="tableRef"
            :columns="columns"
            :rows="tableRows"
            row-key="id"
            export-filename="travaux_maison"
            :selectable="false"
        >
            <template #cell-work_date="{ row }">
                <span class="text-sm text-gray-600 dark:text-gray-400">{{ formatDate(row.work_date as string) }}</span>
            </template>
            <template #cell-submission_date="{ row }">
                <span class="text-sm text-gray-600 dark:text-gray-400">{{ formatDate(row.submission_date as string) }}</span>
            </template>
            <template #cell-description="{ row }">
                <span class="line-clamp-2 text-sm text-gray-600 dark:text-gray-400">{{ row.description }}</span>
            </template>
            <template #actions="{ row }">
                <div class="flex items-center justify-end gap-1.5">
                    <!-- Voir détails -->
                    <button
                        v-if="props.canView"
                        class="p-1.5 rounded-lg transition-all duration-150
                               text-white bg-violet-500 hover:bg-violet-600 active:bg-violet-700
                               shadow-sm shadow-violet-200 dark:shadow-violet-900/40"
                        title="Voir les détails"
                        @click="openDetails(row.id as number)"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                    <!-- Modifier — permission + créateur ou super_admin -->
                    <button
                        v-if="props.canEdit && canEditRow(row)"
                        class="p-1.5 rounded-lg transition-all duration-150
                               text-white bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700
                               shadow-sm shadow-emerald-200 dark:shadow-emerald-900/40"
                        title="Modifier"
                        @click="openEdit(row.id as number)"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </button>
                    <!-- Supprimer — permission + créateur ou super_admin -->
                    <button
                        v-if="props.canDelete && canEditRow(row)"
                        class="p-1.5 rounded-lg transition-all duration-150
                               text-white bg-red-500 hover:bg-red-600 active:bg-red-700
                               shadow-sm shadow-red-200 dark:shadow-red-900/40"
                        title="Mettre à la corbeille"
                        @click="confirmDelete(row.id as number, row.class_name as string)"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                    <!-- Aucune action disponible -->
                    <span v-if="!props.canView && !props.canEdit && !props.canDelete" class="text-xs text-gray-400 italic px-2">—</span>
                </div>
            </template>
        </DataTable>

        <!-- Modal Créer -->
        <AppModal v-model="showCreateForm" title="Nouveau travail de maison" size="xl">
            <form :id="createFormId" @submit.prevent="submitCreate" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <AppSelect
                        v-model="createForm.class_id"
                        label="Classe"
                        :options="classOptions"
                        required
                    />
                    <AppSelect
                        v-model="createForm.subject_id"
                        label="Matière"
                        :options="createSubjectOptions"
                        required
                        :disabled="!createForm.class_id"
                    />
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <AppInput v-model="createForm.work_date" label="Date du travail" type="date" required />
                    <AppInput v-model="createForm.submission_date" label="Date de remise" type="date" required />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Description</label>
                    <textarea
                        v-model="createForm.description"
                        rows="3"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"
                    />
                </div>
                <!-- Pièces jointes multiples -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Pièces jointes <span class="text-gray-400 font-normal">(optionnel — plusieurs fichiers acceptés)</span>
                    </label>
                    <div
                        class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4 text-center cursor-pointer hover:border-primary-400 transition-colors"
                        @dragover.prevent
                        @drop.prevent="onDropCreate"
                        @click="createFileInput?.click()"
                    >
                        <svg class="w-8 h-8 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        <p class="text-sm text-gray-500">Glissez vos fichiers ici ou <span class="text-primary-600 font-medium">cliquez pour parcourir</span></p>
                        <p class="text-xs text-gray-400 mt-1">PDF, DOCX, PPTX, images… max 20 Mo par fichier</p>
                        <input ref="createFileInput" type="file" multiple class="hidden" @change="onCreateFileChange" />
                    </div>
                    <!-- Liste des fichiers sélectionnés -->
                    <div v-if="createFiles.length" class="mt-3 space-y-2">
                        <div
                            v-for="(f, idx) in createFiles"
                            :key="idx"
                            class="flex items-center justify-between bg-gray-50 dark:bg-gray-700/50 rounded-lg px-3 py-2"
                        >
                            <div class="flex items-center gap-2 min-w-0">
                                <FileTypeIcon :filename="f.name" size="sm" />
                                <span class="text-sm text-gray-700 dark:text-gray-300 truncate">{{ f.name }}</span>
                                <span class="text-xs text-gray-400 shrink-0">{{ formatFileSize(f.size) }}</span>
                            </div>
                            <button type="button" @click="removeCreateFile(idx)" class="ml-2 text-gray-400 hover:text-danger-500 transition-colors shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            <template #footer>
                <AppButton variant="ghost" @click="showCreateForm = false">Annuler</AppButton>
                <AppButton type="submit" :form="createFormId" :loading="submitting">Créer</AppButton>
            </template>
        </AppModal>

        <!-- Modal Modifier -->
        <AppModal v-model="showEditForm" :title="editWork ? `Modifier — ${editWork.class_name} / ${editWork.subject_name}` : 'Modifier le travail'" size="xl">
            <div v-if="loadingEdit" class="flex items-center justify-center py-12">
                <svg class="animate-spin w-8 h-8 text-primary-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" />
                </svg>
            </div>
            <form v-else-if="editWork" :id="editFormId" @submit.prevent="submitEdit" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <AppSelect
                        v-model="editForm.class_id"
                        label="Classe"
                        :options="classOptions"
                        required
                    />
                    <AppSelect
                        v-model="editForm.subject_id"
                        label="Matière"
                        :options="editSubjectOptions"
                        required
                        :disabled="!editForm.class_id"
                    />
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <AppInput v-model="editForm.work_date" label="Date du travail" type="date" required />
                    <AppInput v-model="editForm.submission_date" label="Date de remise" type="date" required />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Description</label>
                    <textarea
                        v-model="editForm.description"
                        rows="3"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"
                    />
                </div>
                <!-- Pièces jointes existantes -->
                <div v-if="editWork.attachments?.length">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Fichiers existants</label>
                    <div class="space-y-2">
                        <div
                            v-for="att in editWork.attachments"
                            :key="att.id"
                            class="flex items-center justify-between bg-gray-50 dark:bg-gray-700/50 rounded-lg px-3 py-2"
                            :class="{ 'opacity-40 line-through': attachmentsToRemove.includes(att.id) }"
                        >
                            <div class="flex items-center gap-2 min-w-0">
                                <FileTypeIcon :filename="att.file_name" size="sm" />
                                <a :href="att.url" target="_blank" class="text-sm text-primary-600 hover:underline truncate">{{ att.file_name }}</a>
                                <span class="text-xs text-gray-400 shrink-0">{{ att.readable_size }}</span>
                            </div>
                            <button
                                type="button"
                                @click="toggleRemoveAttachment(att.id)"
                                class="ml-2 shrink-0 transition-colors"
                                :class="attachmentsToRemove.includes(att.id)
                                    ? 'text-success-500 hover:text-success-600'
                                    : 'text-gray-400 hover:text-danger-500'"
                                :title="attachmentsToRemove.includes(att.id) ? 'Annuler la suppression' : 'Supprimer ce fichier'"
                            >
                                <svg v-if="attachmentsToRemove.includes(att.id)" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                </svg>
                                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Ajouter de nouvelles pièces jointes -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Ajouter des fichiers</label>
                    <div
                        class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4 text-center cursor-pointer hover:border-primary-400 transition-colors"
                        @dragover.prevent
                        @drop.prevent="onDropEdit"
                        @click="editFileInput?.click()"
                    >
                        <p class="text-sm text-gray-500">Glissez ou <span class="text-primary-600 font-medium">cliquez pour parcourir</span></p>
                        <input ref="editFileInput" type="file" multiple class="hidden" @change="onEditFileChange" />
                    </div>
                    <div v-if="editFiles.length" class="mt-3 space-y-2">
                        <div
                            v-for="(f, idx) in editFiles"
                            :key="idx"
                            class="flex items-center justify-between bg-gray-50 dark:bg-gray-700/50 rounded-lg px-3 py-2"
                        >
                            <div class="flex items-center gap-2 min-w-0">
                                <FileTypeIcon :filename="f.name" size="sm" />
                                <span class="text-sm text-gray-700 dark:text-gray-300 truncate">{{ f.name }}</span>
                                <span class="text-xs text-gray-400 shrink-0">{{ formatFileSize(f.size) }}</span>
                            </div>
                            <button type="button" @click="removeEditFile(idx)" class="ml-2 text-gray-400 hover:text-danger-500 transition-colors shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            <template #footer>
                <AppButton variant="ghost" @click="showEditForm = false">Annuler</AppButton>
                <AppButton type="submit" :form="editFormId" :loading="submitting">Enregistrer</AppButton>
            </template>
        </AppModal>

        <!-- Modal Détails -->
        <AppModal v-model="showDetails" title="Détails du travail" size="xl">
            <div v-if="loadingDetails" class="flex items-center justify-center py-16">
                <svg class="animate-spin w-8 h-8 text-primary-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" />
                </svg>
            </div>
            <div v-else-if="detailWork" class="space-y-5">

                <!-- Bannière classe / matière -->
                <div class="flex items-center gap-4 p-4 rounded-xl bg-gradient-to-r from-primary-50 to-violet-50 dark:from-primary-900/20 dark:to-violet-900/20 border border-primary-100 dark:border-primary-800">
                    <div class="w-12 h-12 rounded-xl bg-primary-600 flex items-center justify-center shrink-0 shadow-md shadow-primary-200 dark:shadow-primary-900/40">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-primary-600 dark:text-primary-400 uppercase tracking-wide">Travail de maison</p>
                        <p class="text-base font-bold text-gray-900 dark:text-white truncate">{{ detailWork.subject_name }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Classe : <span class="font-medium text-gray-700 dark:text-gray-200">{{ detailWork.class_name }}</span></p>
                    </div>
                </div>

                <!-- Dates -->
                <div class="grid grid-cols-2 gap-3">
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-violet-50 dark:bg-violet-900/20 border border-violet-100 dark:border-violet-800">
                        <div class="w-9 h-9 rounded-lg bg-violet-100 dark:bg-violet-800 flex items-center justify-center shrink-0">
                            <svg class="w-4.5 h-4.5 text-violet-600 dark:text-violet-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-violet-600 dark:text-violet-400 font-medium">Date du travail</p>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ formatDate(detailWork.work_date) }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-orange-50 dark:bg-orange-900/20 border border-orange-100 dark:border-orange-800">
                        <div class="w-9 h-9 rounded-lg bg-orange-100 dark:bg-orange-800 flex items-center justify-center shrink-0">
                            <svg class="w-4.5 h-4.5 text-orange-600 dark:text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-orange-600 dark:text-orange-400 font-medium">Date de remise</p>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ formatDate(detailWork.submission_date) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div v-if="detailWork.description" class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h12" />
                        </svg>
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Description</span>
                    </div>
                    <div class="p-4 prose prose-sm dark:prose-invert max-w-none text-gray-700 dark:text-gray-300" v-html="detailWork.description" />
                </div>

                <!-- Pièces jointes -->
                <div v-if="detailWork.attachments?.length" class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                            </svg>
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Pièces jointes</span>
                        </div>
                        <span class="text-xs bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 font-semibold px-2 py-0.5 rounded-full">
                            {{ detailWork.attachments.length }}
                        </span>
                    </div>
                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        <a
                            v-for="att in detailWork.attachments"
                            :key="att.id"
                            :href="att.url"
                            target="_blank"
                            class="flex items-center gap-3 px-4 py-3 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors group"
                        >
                            <FileTypeIcon :filename="att.file_name" size="sm" />
                            <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-primary-600 dark:group-hover:text-primary-400 truncate flex-1 font-medium">
                                {{ att.file_name }}
                            </span>
                            <span class="text-xs text-gray-400 shrink-0">{{ att.readable_size }}</span>
                            <svg class="w-4 h-4 text-gray-300 group-hover:text-primary-500 shrink-0 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                        </a>
                    </div>
                </div>
                <!-- Fichier legacy -->
                <div v-else-if="detailWork.document_file" class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Document</span>
                    </div>
                    <div class="p-4">
                        <a :href="`/upload/practicalworks/${detailWork.document_file}`" target="_blank"
                           class="inline-flex items-center gap-2 text-sm text-primary-600 hover:text-primary-700 font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Télécharger le document
                        </a>
                    </div>
                </div>

                <!-- Soumissions -->
                <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <!-- En-tête avec compteur cliquable -->
                    <button
                        type="button"
                        class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 flex items-center justify-between hover:bg-gray-100 dark:hover:bg-gray-700/60 transition-colors"
                        @click="showSubmissions = !showSubmissions"
                    >
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Soumissions</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <!-- Compteur X / total -->
                            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full"
                                  :class="(detailWork.homeworks?.length ?? 0) > 0
                                    ? 'bg-success-100 dark:bg-success-900/30 text-success-700 dark:text-success-300'
                                    : 'bg-gray-100 dark:bg-gray-700 text-gray-500'">
                                {{ detailWork.homeworks?.length ?? 0 }}
                                <span class="opacity-60">/ {{ detailWork.total_students ?? '?' }}</span>
                            </span>
                            <!-- Chevron -->
                            <svg class="w-4 h-4 text-gray-400 transition-transform duration-200"
                                 :class="showSubmissions ? 'rotate-180' : ''"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </button>

                    <!-- Liste déroulante -->
                    <div v-show="showSubmissions">
                        <div v-if="detailWork.homeworks?.length" class="divide-y divide-gray-100 dark:divide-gray-700">
                            <div v-for="hw in detailWork.homeworks" :key="hw.id"
                                 class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors">
                                <!-- Avatar initiales -->
                                <div class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center shrink-0">
                                    <span class="text-xs font-bold text-primary-600 dark:text-primary-400">
                                        {{ ((hw.student?.last_name ?? hw.student_last_name ?? '?')[0] ?? '?').toUpperCase() }}
                                    </span>
                                </div>
                                <!-- Nom -->
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                        {{ hw.student?.last_name ?? hw.student_last_name ?? '—' }}
                                        {{ hw.student?.name ?? hw.student_name ?? '' }}
                                    </p>
                                    <p class="text-xs text-gray-400">{{ formatDate(hw.created_at) }}</p>
                                </div>
                                <!-- Statut traduit -->
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold shrink-0"
                                      :class="{
                                          'bg-success-100 dark:bg-success-900/30 text-success-700 dark:text-success-300': hw.status === 'submitted',
                                          'bg-warning-100 dark:bg-warning-900/30 text-warning-700 dark:text-warning-300': hw.status === 'late',
                                          'bg-gray-100 dark:bg-gray-700 text-gray-500': !['submitted','late'].includes(hw.status),
                                      }">
                                    <span class="w-1.5 h-1.5 rounded-full"
                                          :class="{
                                              'bg-success-500': hw.status === 'submitted',
                                              'bg-warning-500': hw.status === 'late',
                                              'bg-gray-400': !['submitted','late'].includes(hw.status),
                                          }"/>
                                    {{ statusLabel(hw.status) }}
                                </span>
                                <!-- Fichier -->
                                <a v-if="hw.document_file"
                                   :href="`/upload/homeworks/${hw.document_file}`"
                                   target="_blank"
                                   class="inline-flex items-center gap-1 text-xs text-primary-600 hover:text-primary-700 font-medium hover:underline shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    Voir
                                </a>
                                <span v-else class="text-xs text-gray-300 shrink-0">—</span>
                            </div>
                        </div>
                        <div v-else class="py-8 text-center">
                            <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-sm text-gray-400">Aucune soumission pour ce travail.</p>
                        </div>
                    </div>
                </div>
            </div>
        </AppModal>

        <!-- Confirm Delete Dialog -->
        <ConfirmDialog
            v-model="showConfirmDelete"
            title="Mettre à la corbeille"
            :message="`Le travail « ${deleteTarget.label} » sera déplacé dans la corbeille. Vous pourrez le restaurer.`"
            confirm-label="Mettre à la corbeille"
            confirm-variant="danger"
            @confirm="doDelete"
        />
    </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { AppButton, AppInput, AppSelect, AppModal, DataTable, ConfirmDialog, FileTypeIcon } from '@/Components/UI';
import { stripHtml } from '@/Utils/html';
import { useToast } from '@/Composables/useToast';

// ─── Types ────────────────────────────────────────────────────────────────────
interface Work {
    [key: string]: unknown;
    id: number;
    class_name: string;
    subject_name: string;
    work_date: string;
    submission_date: string;
    description: string;
    created_by: number;
}

interface Attachment {
    id: number;
    file_name: string;
    file_path: string;
    file_ext: string;
    file_size: number;
    url: string;
    readable_size: string;
}

interface WorkDetail extends Work {
    document_file: string | null;
    total_students?: number;
    attachments?: Attachment[];
    homeworks?: {
        id: number;
        status: string;
        document_file: string | null;
        created_at: string;
        student_name?: string;
        student_last_name?: string;
        student?: { id: number; name: string; last_name: string };
    }[];
}

// ─── Props ─────────────────────────────────────────────────────────────────────
const props = defineProps<{
    works: { data: Work[]; total: number; from: number; to: number; links: any[] };
    classes: { id: number; name: string }[];
    /** ID de l'utilisateur connecté */
    currentUserId?: number;
    /** user_type de l'utilisateur connecté (0 = super_admin) */
    currentUserType?: number;
    /** Permissions d'action sur les travaux */
    canCreate?: boolean;
    canView?: boolean;
    canEdit?: boolean;
    canDelete?: boolean;
}>();

// ─── État ──────────────────────────────────────────────────────────────────────
const toast      = useToast();
const createFormId = 'hw-create-form';
const editFormId   = 'hw-edit-form';

const showCreateForm = ref(false);
const showEditForm   = ref(false);
const showDetails    = ref(false);
const submitting     = ref(false);
const loadingEdit    = ref(false);
const loadingDetails = ref(false);

const detailWork = ref<WorkDetail | null>(null);
const editWork   = ref<WorkDetail | null>(null);
const editWorkId = ref<number | null>(null);

// État accordéon soumissions
const showSubmissions = ref(true);

// Traduction des statuts
const statusLabel = (s: string) => ({
    submitted: 'Soumis',
    late:      'En retard',
    graded:    'Noté',
    rejected:  'Rejeté',
}[s] ?? s);

// Sujets chargés dynamiquement
const createSubjects = ref<{ id: number; name: string }[]>([]);
const editSubjects   = ref<{ id: number; name: string }[]>([]);

// Fichiers
const createFileInput = ref<HTMLInputElement | null>(null);
const editFileInput   = ref<HTMLInputElement | null>(null);
const createFiles     = ref<File[]>([]);
const editFiles       = ref<File[]>([]);
const attachmentsToRemove = ref<number[]>([]);

// Formulaires
const createForm = ref({ class_id: '', subject_id: '', work_date: '', submission_date: '', description: '' });
const editForm   = ref({ class_id: '', subject_id: '', work_date: '', submission_date: '', description: '' });

// Suppression
const showConfirmDelete = ref(false);
const deleteTarget = ref({ id: 0, label: '' });

// ─── Computed ─────────────────────────────────────────────────────────────────
const classOptions = computed(() =>
    props.classes.map(c => ({ value: String(c.id), label: c.name }))
);

const createSubjectOptions = computed(() =>
    createSubjects.value.map(s => ({ value: String(s.id), label: s.name }))
);

const editSubjectOptions = computed(() =>
    editSubjects.value.map(s => ({ value: String(s.id), label: s.name }))
);

const tableRows = computed(() =>
    props.works.data.map(w => ({ ...w, description: stripHtml(w.description as string, 80) }))
);

const columns = [
    { key: 'class_name',      label: 'Classe' },
    { key: 'subject_name',    label: 'Matière' },
    { key: 'work_date',       label: 'Date' },
    { key: 'submission_date', label: 'Remise' },
    { key: 'description',     label: 'Description' },
];

// ─── Permissions ──────────────────────────────────────────────────────────────
/**
 * Un admin peut modifier/supprimer uniquement ses propres travaux,
 * sauf le super_admin qui peut tout modifier.
 */
const canEditRow = (row: Work) => {
    if (props.currentUserType === 0) return true; // super_admin
    return row.created_by === props.currentUserId;
};

// ─── Utilitaires ──────────────────────────────────────────────────────────────
const formatDate = (d: string) => {
    if (!d) return '—';
    try { return new Date(d).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' }); }
    catch { return d; }
};

const formatFileSize = (bytes: number) => {
    if (bytes < 1024) return bytes + ' o';
    if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' Ko';
    return (bytes / 1048576).toFixed(1) + ' Mo';
};

// ─── Chargement des matières ───────────────────────────────────────────────────
// AppSelect n'émet pas d'événement @change natif — on utilise watch() à la place.

const fetchSubjects = async (classId: string): Promise<{ id: number; name: string }[]> => {
    if (!classId) return [];
    try {
        const res  = await fetch(`/admin/practicalworks/homework/getSubjectByClassId/${classId}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        });
        if (!res.ok) return [];
        const data = await res.json();
        return (data.getSubject ?? []).map((s: any) => ({ id: s.subject_id, name: s.subject_name }));
    } catch { return []; }
};

// Surveille la classe dans le formulaire création
watch(() => createForm.value.class_id, async (newClassId) => {
    createForm.value.subject_id = '';
    createSubjects.value = await fetchSubjects(newClassId);
});

// Surveille la classe dans le formulaire édition
watch(() => editForm.value.class_id, async (newClassId) => {
    editSubjects.value = await fetchSubjects(newClassId);
});

// ─── Gestion fichiers ─────────────────────────────────────────────────────────
const onCreateFileChange = (e: Event) => {
    const files = (e.target as HTMLInputElement).files;
    if (files) createFiles.value.push(...Array.from(files));
};
const onDropCreate = (e: DragEvent) => {
    if (e.dataTransfer?.files) createFiles.value.push(...Array.from(e.dataTransfer.files));
};
const removeCreateFile = (idx: number) => { createFiles.value.splice(idx, 1); };

const onEditFileChange = (e: Event) => {
    const files = (e.target as HTMLInputElement).files;
    if (files) editFiles.value.push(...Array.from(files));
};
const onDropEdit = (e: DragEvent) => {
    if (e.dataTransfer?.files) editFiles.value.push(...Array.from(e.dataTransfer.files));
};
const removeEditFile = (idx: number) => { editFiles.value.splice(idx, 1); };

const toggleRemoveAttachment = (id: number) => {
    const idx = attachmentsToRemove.value.indexOf(id);
    if (idx === -1) attachmentsToRemove.value.push(id);
    else attachmentsToRemove.value.splice(idx, 1);
};

// ─── Actions ──────────────────────────────────────────────────────────────────
const openCreate = () => {
    createForm.value = { class_id: '', subject_id: '', work_date: '', submission_date: '', description: '' };
    createFiles.value = [];
    createSubjects.value = [];
    showCreateForm.value = true;
};

const submitCreate = () => {
    submitting.value = true;
    const data = new FormData();
    data.append('class_id',        createForm.value.class_id);
    data.append('subject_id',      createForm.value.subject_id);
    data.append('work_date',       createForm.value.work_date);
    data.append('submission_date', createForm.value.submission_date);
    data.append('description',     createForm.value.description);
    createFiles.value.forEach(f => data.append('attachments[]', f));

    router.post('/admin/practicalworks/homework/create', data, {
        onSuccess: () => { showCreateForm.value = false; toast.success('Travail créé avec succès.'); },
        onError:   (errors) => { toast.error(Object.values(errors)[0] as string || 'Erreur lors de la création.'); },
        onFinish:  () => { submitting.value = false; },
    });
};

const openEdit = async (id: number) => {
    editWorkId.value  = id;
    loadingEdit.value = true;
    editWork.value    = null;
    editFiles.value   = [];
    attachmentsToRemove.value = [];
    showEditForm.value = true;

    try {
        const res  = await fetch(`/admin/practicalworks/homework/edit-json/${id}`, { headers: { Accept: 'application/json' } });
        if (!res.ok) { toast.error('Vous n\'êtes pas autorisé à modifier ce travail.'); showEditForm.value = false; return; }
        const json = await res.json();
        editWork.value = json.work;
        // Charger les matières d'abord, puis affecter subject_id
        editSubjects.value = await fetchSubjects(String(json.work.class_id));
        editForm.value = {
            class_id:        String(json.work.class_id),
            subject_id:      String(json.work.subject_id),
            work_date:       json.work.work_date,
            submission_date: json.work.submission_date,
            description:     stripHtml(json.work.description ?? ''),
        };
    } catch {
        toast.error('Erreur lors du chargement du travail.');
        showEditForm.value = false;
    } finally {
        loadingEdit.value = false;
    }
};

const submitEdit = () => {
    if (!editWorkId.value) return;
    submitting.value = true;
    const data = new FormData();
    data.append('class_id',        editForm.value.class_id);
    data.append('subject_id',      editForm.value.subject_id);
    data.append('work_date',       editForm.value.work_date);
    data.append('submission_date', editForm.value.submission_date);
    data.append('description',     editForm.value.description);
    attachmentsToRemove.value.forEach(id => data.append('remove_attachments[]', String(id)));
    editFiles.value.forEach(f => data.append('attachments[]', f));

    router.post(`/admin/practicalworks/homework/edit/${editWorkId.value}`, data, {
        onSuccess: () => { showEditForm.value = false; toast.success('Travail modifié avec succès.'); },
        onError:   (errors) => { toast.error(Object.values(errors)[0] as string || 'Erreur lors de la modification.'); },
        onFinish:  () => { submitting.value = false; },
    });
};

const openDetails = async (id: number) => {
    showDetails.value    = true;
    loadingDetails.value = true;
    detailWork.value     = null;
    showSubmissions.value = true;
    try {
        const res  = await fetch(`/admin/practicalworks/homework/details-json/${id}`, { headers: { Accept: 'application/json' } });
        const json = await res.json();
        detailWork.value = json.work ?? null;
    } catch { detailWork.value = null; }
    finally { loadingDetails.value = false; }
};

const confirmDelete = (id: number, label: string) => {
    deleteTarget.value = { id, label };
    showConfirmDelete.value = true;
};

const doDelete = () => {
    router.get(`/admin/practicalworks/homework/delete/${deleteTarget.value.id}`, {}, {
        onSuccess: () => toast.success('Travail déplacé dans la corbeille.'),
        onError:   () => toast.error('Erreur lors de la suppression.'),
    });
};
</script>
