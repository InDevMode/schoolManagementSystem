<template>
    <div class="space-y-6">
        <PageHeader title="Événements scolaires" :subtitle="`${events.total} événement(s) enregistré(s)`" color="violet">
            <template #icon>
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </template>
            <template #actions>
                <AppButton @click="openCreate">
                    <template #icon>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </template>
                    Nouvel événement
                </AppButton>
            </template>
        </PageHeader>

        <!-- Mini calendrier des prochains événements -->
        <div v-if="calendarEvents.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            <div v-for="ev in calendarEvents.slice(0, 6)" :key="ev.id"
                class="flex items-center gap-3 p-4 rounded-lg border border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 hover:shadow-sm transition-shadow">
                <!-- Couleur + date -->
                <div class="flex-shrink-0 flex flex-col items-center justify-center w-12 h-12 rounded-lg text-white font-bold"
                    :style="{ background: ev.color }">
                    <span class="text-xs leading-none">{{ new Date(ev.start).toLocaleDateString('fr-FR', { day: '2-digit' }) }}</span>
                    <span class="text-[9px] leading-none mt-0.5 uppercase">
                        {{ new Date(ev.start).toLocaleDateString('fr-FR', { month: 'short' }) }}
                    </span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ ev.title }}</p>
                    <p class="text-xs text-gray-400 truncate">{{ typeLabels[ev.extendedProps.type] ?? ev.extendedProps.type }}</p>
                </div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="card p-4">
            <div class="flex flex-row flex-wrap items-center gap-3">
                <div class="flex-1 min-w-[180px]">
                    <AppSelect v-model="filters.event_type" :options="typeOptions" placeholder="Tous les types" @change="applyFilters"/>
                </div>
                <div class="flex-1 min-w-[160px]">
                    <AppInput v-model="filters.date_from" type="date" @change="applyFilters"/>
                </div>
                <div class="flex-1 min-w-[160px]">
                    <AppInput v-model="filters.date_to" type="date" @change="applyFilters"/>
                </div>
                <button v-if="filters.event_type || filters.date_from || filters.date_to"
                    @click="filters = { event_type: '', date_from: '', date_to: '' }; applyFilters()"
                    class="flex-shrink-0 px-3 py-2 rounded-lg text-xs font-medium
                           text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200
                           bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600
                           transition-colors whitespace-nowrap">
                    Réinitialiser
                </button>
            </div>
        </div>

        <!-- Table -->
        <DataTable :columns="columns" :rows="events.data" row-key="id" :pagination="events">
            <template #cell-title="{ row }">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0"
                        :style="{ background: typeColors[row.event_type] ?? '#6366f1' }"/>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ row.title }}</p>
                </div>
            </template>
            <template #cell-event_type="{ row }">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold"
                    :style="{ background: (typeColors[row.event_type] ?? '#6366f1') + '20', color: typeColors[row.event_type] ?? '#6366f1' }">
                    {{ typeLabels[row.event_type] ?? row.event_type }}
                </span>
            </template>
            <template #cell-event_date="{ row }">
                <span class="text-sm text-gray-600 dark:text-gray-400">{{ formatDate(row.event_date) }}</span>
            </template>
            <template #cell-time="{ row }">
                <span class="text-xs text-gray-400">
                    {{ row.start_time && row.end_time ? `${row.start_time} – ${row.end_time}` : row.start_time ?? '—' }}
                </span>
            </template>
            <template #actions="{ row }">
                <div class="flex items-center justify-end gap-1.5">
                    <!-- Modifier -->
                    <button class="p-1.5 rounded-lg transition-all duration-150
                                   text-white bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700
                                   shadow-sm shadow-emerald-200 dark:shadow-emerald-900/40"
                            title="Modifier"
                            @click="openEdit(row)">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </button>
                    <!-- Supprimer -->
                    <button class="p-1.5 rounded-lg transition-all duration-150
                                   text-white bg-red-500 hover:bg-red-600 active:bg-red-700
                                   shadow-sm shadow-red-200 dark:shadow-red-900/40"
                            title="Supprimer"
                            @click="openDelete(row)">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </template>
        </DataTable>

        <!-- Modal créer/modifier -->
        <AppModal v-model="showForm" :title="editTarget ? 'Modifier l\'événement' : 'Nouvel événement'" size="md">
            <form id="event-form" @submit.prevent="submitEvent" class="space-y-4">
                <AppInput v-model="evForm.title" label="Titre" required :error="evForm.errors.title"/>
                <div class="grid grid-cols-2 gap-4">
                    <AppSelect v-model="evForm.event_type" label="Type" :options="typeOpts" required/>
                    <AppInput  v-model="evForm.event_date" label="Date" type="date" required/>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <AppInput v-model="evForm.start_time" label="Heure de début" type="time"/>
                    <AppInput v-model="evForm.end_time"   label="Heure de fin"   type="time"/>
                </div>
                <AppInput v-model="evForm.location" label="Lieu" placeholder="Salle, terrain..."/>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                    <textarea v-model="evForm.description" rows="3"
                        class="w-full text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-transparent px-3 py-2 dark:text-gray-300 placeholder-gray-300 dark:placeholder-gray-600"
                        placeholder="Détails de l'événement..."/>
                </div>
            </form>
            <template #footer>
                <AppButton variant="ghost" @click="showForm = false">Annuler</AppButton>
                <AppButton type="submit" form="event-form" :loading="evForm.processing">
                    {{ editTarget ? 'Enregistrer' : 'Créer' }}
                </AppButton>
            </template>
        </AppModal>

        <!-- Modal confirmation suppression -->
        <AppModal v-model="showDelete" title="Masquer cet événement" size="sm" persistent>
            <div class="space-y-3">
                <div class="flex items-start gap-3 p-3 rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/50">
                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-amber-800 dark:text-amber-300">Confirmation requise</p>
                        <p class="text-xs text-amber-700 dark:text-amber-400 mt-0.5">
                            L'événement sera masqué de l'affichage. Le super administrateur peut le retrouver dans l'historique.
                        </p>
                    </div>
                </div>
                <p class="text-sm text-gray-700 dark:text-gray-300 px-1">
                    Voulez-vous masquer l'événement
                    <span class="font-semibold text-gray-900 dark:text-white">« {{ deleteTarget?.title }} »</span> ?
                </p>
            </div>
            <template #footer>
                <AppButton variant="ghost" @click="showDelete = false">Annuler</AppButton>
                <AppButton variant="danger" :loading="deleting" @click="confirmDelete">
                    <template #icon>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </template>
                    Masquer l'événement
                </AppButton>
            </template>
        </AppModal>

    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { PageHeader, AppButton, AppInput, AppSelect, AppModal, DataTable } from '@/Components/UI';
import { useToast } from '@/Composables/useToast';

const toast = useToast();

const props = defineProps<{
    events:         { data: any[]; total: number; from: number; to: number; links: any[] };
    typeLabels:     Record<string, string>;
    typeColors:     Record<string, string>;
    calendarEvents: any[];
}>();

const showForm   = ref(false);
const showDelete = ref(false);
const editTarget   = ref<any>(null);
const deleteTarget = ref<any>(null);
const deleting     = ref(false);
const filters      = ref({ event_type: '', date_from: '', date_to: '' });

const typeOpts = computed(() =>
    Object.entries(props.typeLabels).map(([k, v]) => ({ value: k, label: v }))
);
const typeOptions = computed(() => [{ value: '', label: 'Tous les types' }, ...typeOpts.value]);

const evForm = useForm({
    title:       '',
    event_type:  'academic',
    event_date:  '',
    start_time:  '',
    end_time:    '',
    location:    '',
    description: '',
});

const columns = [
    { key: 'title',       label: 'Titre' },
    { key: 'event_type',  label: 'Type' },
    { key: 'event_date',  label: 'Date' },
    { key: 'time',        label: 'Horaire' },
    { key: 'location',    label: 'Lieu' },
];

const openCreate = () => {
    editTarget.value = null;
    evForm.reset();
    evForm.event_type = 'academic';
    showForm.value = true;
};

const openEdit = (ev: any) => {
    editTarget.value = ev;
    evForm.title       = ev.title;
    evForm.event_type  = ev.event_type;
    evForm.event_date  = ev.event_date;
    evForm.start_time  = ev.start_time ?? '';
    evForm.end_time    = ev.end_time ?? '';
    evForm.location    = ev.location ?? '';
    evForm.description = ev.description ?? '';
    showForm.value = true;
};

const submitEvent = () => {
    const url = editTarget.value
        ? `/admin/staff/events/edit/${editTarget.value.id}`
        : '/admin/staff/events/add';
    evForm.post(url, {
        onSuccess: () => { showForm.value = false; toast.success('Événement enregistré.'); },
    });
};

const openDelete = (ev: any) => {
    deleteTarget.value = ev;
    showDelete.value   = true;
};

const confirmDelete = () => {
    if (!deleteTarget.value) return;
    deleting.value = true;
    router.get(`/admin/staff/events/delete/${deleteTarget.value.id}`, {}, {
        onFinish:  () => { deleting.value = false; showDelete.value = false; },
        onSuccess: () => toast.success('Événement masqué avec succès.'),
        onError:   () => toast.error('Erreur lors de la suppression.'),
    });
};

const applyFilters = () => {
    router.get('/admin/staff/events/list', {
        event_type: filters.value.event_type || undefined,
        date_from:  filters.value.date_from  || undefined,
        date_to:    filters.value.date_to    || undefined,
    }, { preserveState: true });
};

const formatDate = (d: string) => d ? new Date(d).toLocaleDateString('fr-FR') : '—';
</script>
