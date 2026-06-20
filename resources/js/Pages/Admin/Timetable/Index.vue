<template>
    <div class="space-y-6">

        <!-- ── Header ──────────────────────────────────────────────────── -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <PageHeader title="Emploi du temps" subtitle="Gestion des horaires de cours par classe" color="primary">
                <template #icon>
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </template>
                <template #actions>
                    <!-- Stat chips -->
                    <div class="flex items-center gap-2 flex-wrap">
                        <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800">
                            <span class="w-1.5 h-1.5 rounded-full bg-primary-500 animate-pulse"/>
                            <span class="text-xs font-semibold text-primary-700 dark:text-primary-300">
                                {{ activeCount }} créneau{{ activeCount > 1 ? 'x' : '' }} actif{{ activeCount > 1 ? 's' : '' }}
                            </span>
                        </div>
                    </div>
                </template>
            </PageHeader>
        </div>

        <!-- ── Filtres ─────────────────────────────────────────────────── -->
        <div class="card overflow-hidden">
            <div class="p-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/80 dark:bg-gray-800/60 flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                </svg>
                <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Sélection classe & matière</span>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <AppSelect
                        v-model="filters.class_id"
                        :options="classOptions"
                        placeholder="Sélectionner une classe"
                    />
                    <AppSelect
                        v-model="filters.subject_id"
                        :options="subjectOptions"
                        placeholder="Sélectionner une matière"
                        :disabled="!filters.class_id || loadingSubjects"
                    />
                    <AppButton @click="applyFilters" :disabled="!filters.class_id || !filters.subject_id" class="w-full">
                        <template #icon>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                            </svg>
                        </template>
                        Afficher l'emploi du temps
                    </AppButton>
                </div>
                <!-- Loading subjects -->
                <div v-if="loadingSubjects" class="flex items-center gap-2 mt-3">
                    <svg class="w-3.5 h-3.5 animate-spin text-primary-500" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                    </svg>
                    <span class="text-xs text-gray-400">Chargement des matières…</span>
                </div>
            </div>
        </div>

        <!-- ── Grille éditable ─────────────────────────────────────────── -->
        <div v-if="week.length && filters.class_id && filters.subject_id" class="card overflow-hidden">

            <!-- En-tête section -->
            <div class="p-5 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between flex-wrap gap-3">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white">Horaires de la semaine</h2>
                            <AppBadge variant="info" v-if="hasExistingData">
                                <span class="flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"/>
                                    EDT existant
                                </span>
                            </AppBadge>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            <span class="font-medium text-gray-700 dark:text-gray-300">{{ selectedClassName }}</span>
                            <span class="mx-1.5 text-gray-300 dark:text-gray-600">·</span>
                            <span class="font-medium text-primary-600 dark:text-primary-400">{{ selectedSubjectName }}</span>
                        </p>
                    </div>
                    <!-- Mini stats + actions haut -->
                    <div class="flex items-center gap-3 flex-wrap">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">{{ activeCount }}</p>
                            <p class="text-xs text-gray-400">jour(s)</p>
                        </div>
                        <div v-if="totalHours > 0" class="text-center border-l border-gray-200 dark:border-gray-700 pl-3">
                            <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ totalHours }}h</p>
                            <p class="text-xs text-gray-400">/ semaine</p>
                        </div>
                        <!-- Actions en haut -->
                        <div v-if="can('action.timetable.manage')" class="flex items-center gap-2 border-l border-gray-200 dark:border-gray-700 pl-3">
                            <button
                                v-if="activeCount > 0"
                                type="button"
                                @click="clearAll"
                                class="flex items-center gap-1.5 px-3 py-2 text-sm font-semibold text-white bg-red-500 hover:bg-red-600 active:bg-red-700 rounded-xl transition-colors shadow-sm shadow-red-500/30"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Tout effacer
                            </button>
                            <AppButton @click="saveTimetable" :loading="saving">
                                <template #icon>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </template>
                                Enregistrer
                            </AppButton>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-5">
                <!-- Grille par jour -->
                <div class="space-y-2 mb-6">
                    <div
                        v-for="(day, index) in week"
                        :key="day.week_id"
                        class="group relative flex items-center gap-3 p-4 rounded-xl border-2 transition-all duration-200 cursor-pointer"
                        :class="timetableForm[index].start_time
                            ? 'border-primary-300 dark:border-primary-700 bg-gradient-to-r from-primary-50/80 to-indigo-50/40 dark:from-primary-900/20 dark:to-indigo-900/10 shadow-sm shadow-primary-100 dark:shadow-primary-900/20'
                            : 'border-gray-200 dark:border-gray-700 bg-gray-50/60 dark:bg-gray-800/30 hover:border-gray-300 dark:hover:border-gray-600'"
                    >
                        <!-- Indicateur jour -->
                        <div
                            class="w-12 h-12 rounded-xl flex flex-col items-center justify-center flex-shrink-0 transition-all"
                            :class="timetableForm[index].start_time
                                ? 'bg-primary-500 shadow-md shadow-primary-500/40'
                                : 'bg-gray-200 dark:bg-gray-700'"
                        >
                            <span
                                class="text-xs font-bold uppercase leading-none"
                                :class="timetableForm[index].start_time ? 'text-white' : 'text-gray-500 dark:text-gray-400'"
                            >{{ day.week_name.slice(0, 3) }}</span>
                        </div>

                        <!-- Nom du jour (desktop) -->
                        <div class="w-24 hidden sm:block flex-shrink-0">
                            <p
                                class="text-sm font-semibold"
                                :class="timetableForm[index].start_time ? 'text-primary-700 dark:text-primary-300' : 'text-gray-500 dark:text-gray-400'"
                            >{{ day.week_name }}</p>
                            <p v-if="timetableForm[index].start_time" class="text-xs text-gray-400 mt-0.5">
                                {{ getDuration(index) }}
                            </p>
                        </div>

                        <!-- Champs temps + salle -->
                        <div class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                            <!-- Heure début -->
                            <div class="relative">
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 ml-0.5">
                                    Heure début
                                </label>
                                <div class="relative">
                                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <input
                                        v-model="timetableForm[index].start_time"
                                        type="time"
                                        class="w-full pl-9 pr-3 py-2 text-sm bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-all"
                                    />
                                </div>
                            </div>

                            <!-- Heure fin -->
                            <div class="relative">
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 ml-0.5">
                                    Heure fin
                                </label>
                                <div class="relative">
                                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <input
                                        v-model="timetableForm[index].end_time"
                                        type="time"
                                        class="w-full pl-9 pr-3 py-2 text-sm bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-all"
                                    />
                                </div>
                            </div>

                            <!-- Salle -->
                            <div class="relative">
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 ml-0.5">
                                    Salle
                                </label>
                                <div class="relative">
                                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    <input
                                        v-model="timetableForm[index].room_number"
                                        type="text"
                                        placeholder="Ex: A101"
                                        class="w-full pl-9 pr-3 py-2 text-sm bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-all placeholder-gray-400"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Bouton effacer -->
                        <button
                            v-if="timetableForm[index].start_time"
                            @click="clearDay(index)"
                            type="button"
                            title="Effacer ce jour"
                            class="flex-shrink-0 p-2 rounded-xl text-white bg-red-500 hover:bg-red-600 active:bg-red-700 transition-all shadow-sm shadow-red-500/30"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                        <div v-else class="w-8 flex-shrink-0"/>
                    </div>
                </div>

                <!-- ── Aperçu grille calendrier ─────────────────────── -->
                <div v-if="activeCount > 0" class="mb-5">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">
                        Aperçu de la semaine
                    </p>
                    <div class="grid grid-cols-7 gap-1.5">
                        <div
                            v-for="(day, index) in week"
                            :key="'preview-' + day.week_id"
                            class="rounded-xl p-2.5 text-center min-h-[88px] flex flex-col items-center justify-center border-2 transition-all"
                            :class="timetableForm[index].start_time
                                ? 'border-primary-400 dark:border-primary-600 bg-primary-500 shadow-lg shadow-primary-500/30'
                                : 'border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/30'"
                        >
                            <p
                                class="text-xs font-bold uppercase tracking-wide mb-1.5"
                                :class="timetableForm[index].start_time ? 'text-primary-100' : 'text-gray-400 dark:text-gray-500'"
                            >{{ day.week_name.slice(0, 3) }}</p>
                            <template v-if="timetableForm[index].start_time">
                                <p class="text-xs font-bold text-white leading-tight">
                                    {{ timetableForm[index].start_time }}
                                </p>
                                <div class="w-4 h-px bg-primary-300 my-1"/>
                                <p class="text-xs text-primary-200 leading-tight">{{ timetableForm[index].end_time }}</p>
                                <p v-if="timetableForm[index].room_number"
                                   class="text-xs text-primary-200 mt-1.5 bg-primary-600/60 px-1.5 py-0.5 rounded-md truncate max-w-full">
                                    {{ timetableForm[index].room_number }}
                                </p>
                            </template>
                            <span v-else class="text-gray-300 dark:text-gray-600 text-xl font-light">–</span>
                        </div>
                    </div>
                </div>

                <!-- ── Actions bas ─────────────────────────────────────────── -->
                <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-700">
                    <button
                        v-if="can('action.timetable.manage') && activeCount > 0"
                        type="button"
                        @click="clearAll"
                        class="flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white bg-red-500 hover:bg-red-600 active:bg-red-700 rounded-xl transition-colors shadow-sm shadow-red-500/30"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Tout effacer
                    </button>
                    <div v-else/>
                    <AppButton v-if="can('action.timetable.manage')" @click="saveTimetable" :loading="saving">
                        <template #icon>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </template>
                        Enregistrer l'emploi du temps
                    </AppButton>
                </div>
            </div>
        </div>

        <!-- ── État vide ───────────────────────────────────────────────── -->
        <div v-else class="card p-12 text-center">
            <div class="w-20 h-20 mx-auto mb-5 rounded-3xl bg-gradient-to-br from-primary-100 to-indigo-100 dark:from-primary-900/40 dark:to-indigo-900/40 flex items-center justify-center">
                <svg class="w-10 h-10 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <p class="text-gray-700 dark:text-gray-300 font-semibold text-base">
                {{ filters.class_id && filters.subject_id
                    ? 'Aucun horaire trouvé'
                    : 'Sélectionnez une classe et une matière' }}
            </p>
            <p class="text-gray-400 dark:text-gray-500 text-sm mt-1.5 max-w-xs mx-auto">
                {{ filters.class_id
                    ? 'Choisissez une matière puis cliquez sur « Afficher l\'emploi du temps ».'
                    : 'Utilisez les filtres ci-dessus pour commencer à configurer les horaires.' }}
            </p>
        </div>

    </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { PageHeader, AppButton, AppSelect, AppBadge } from '@/Components/UI';
import { useCan } from '@/Composables/useCan';

const { can } = useCan();

interface WeekEntry {
    week_id: number;
    week_name: string;
    start_time: string;
    end_time: string;
    room_number: string;
}

interface SubjectOption {
    id: number;
    name: string;
}

const props = defineProps<{
    classes: { id: number; name: string }[];
    subjects: SubjectOption[];
    week: WeekEntry[];
    selectedClass?: string | number;
    selectedSubject?: string | number;
}>();

const filters = ref({
    class_id:   props.selectedClass  ? String(props.selectedClass)  : '',
    subject_id: props.selectedSubject ? String(props.selectedSubject) : '',
});

const saving          = ref(false);
const loadingSubjects = ref(false);
const dynamicSubjects = ref<SubjectOption[]>(props.subjects ?? []);

const timetableForm = ref<WeekEntry[]>(props.week.map(w => ({ ...w })));

// ── Options selects ────────────────────────────────────────────────────────
const classOptions = computed(() =>
    props.classes.map(c => ({ value: String(c.id), label: c.name }))
);

const subjectOptions = computed(() =>
    dynamicSubjects.value.map(s => ({ value: String(s.id), label: s.name }))
);

const selectedClassName = computed(() =>
    props.classes.find(c => String(c.id) === filters.value.class_id)?.name ?? ''
);

const selectedSubjectName = computed(() =>
    dynamicSubjects.value.find(s => String(s.id) === filters.value.subject_id)?.name ?? ''
);

const hasExistingData = computed(() =>
    props.week.some(w => w.start_time)
);

// ── Stats ──────────────────────────────────────────────────────────────────
const activeCount = computed(() =>
    timetableForm.value.filter(d => d.start_time).length
);

const totalHours = computed(() => {
    let total = 0;
    timetableForm.value.forEach(d => {
        if (d.start_time && d.end_time) {
            const [sh, sm] = d.start_time.split(':').map(Number);
            const [eh, em] = d.end_time.split(':').map(Number);
            total += (eh * 60 + em) - (sh * 60 + sm);
        }
    });
    return Math.round(total / 60 * 10) / 10;
});

const getDuration = (index: number): string => {
    const d = timetableForm.value[index];
    if (!d.start_time || !d.end_time) return '';
    const [sh, sm] = d.start_time.split(':').map(Number);
    const [eh, em] = d.end_time.split(':').map(Number);
    const mins = (eh * 60 + em) - (sh * 60 + sm);
    if (mins <= 0) return '';
    const h = Math.floor(mins / 60), m = mins % 60;
    return h > 0 ? (m > 0 ? `${h}h${String(m).padStart(2,'0')}` : `${h}h`) : `${m}min`;
};

// ── Watch class_id → charger les matières ─────────────────────────────────
watch(() => filters.value.class_id, async (newClassId) => {
    filters.value.subject_id = '';
    dynamicSubjects.value = [];
    if (!newClassId) return;
    loadingSubjects.value = true;
    try {
        const response = await fetch('/admin/class_timetable/subject', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? '',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ class_id: newClassId }),
        });
        const data = await response.json();
        dynamicSubjects.value = data.subjects ?? [];
    } catch {
        dynamicSubjects.value = [];
    } finally {
        loadingSubjects.value = false;
    }
});

watch(() => filters.value.subject_id, (newSubjectId) => {
    if (filters.value.class_id && newSubjectId) applyFilters();
});

watch(() => props.selectedClass, (val) => {
    if (val && props.subjects?.length) dynamicSubjects.value = props.subjects;
}, { immediate: true });

// ── Filtrer ────────────────────────────────────────────────────────────────
const applyFilters = () => {
    router.get('/admin/class_timetable/list', {
        class_id:   filters.value.class_id,
        subject_id: filters.value.subject_id,
    }, { preserveState: true, replace: true });
};

// ── Helpers ────────────────────────────────────────────────────────────────
const clearDay = (index: number) => {
    timetableForm.value[index].start_time  = '';
    timetableForm.value[index].end_time    = '';
    timetableForm.value[index].room_number = '';
};

const clearAll = () => timetableForm.value.forEach((_, i) => clearDay(i));

// ── Sauvegarder ────────────────────────────────────────────────────────────
const saveTimetable = () => {
    saving.value = true;
    router.post('/admin/class_timetable/add', {
        class_id:   filters.value.class_id,
        subject_id: filters.value.subject_id,
        timetable:  timetableForm.value,
    }, {
        onFinish: () => { saving.value = false; },
    });
};
</script>
