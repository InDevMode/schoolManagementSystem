<template>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Emploi du temps</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Gestion des horaires de cours par classe</p>
            </div>
        </div>

        <!-- Filtres -->
        <div class="card p-4">
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
                <AppButton @click="applyFilters" :disabled="!filters.class_id || !filters.subject_id">
                    <template #icon>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                        </svg>
                    </template>
                    Filtrer
                </AppButton>
            </div>
            <p v-if="loadingSubjects" class="text-xs text-gray-400 mt-2 flex items-center gap-1">
                <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                </svg>
                Chargement des matières…
            </p>
        </div>

        <!-- Grille semaine éditable -->
        <div v-if="week.length && filters.class_id && filters.subject_id" class="card overflow-hidden">
            <!-- En-tête -->
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Horaires de la semaine</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        {{ selectedClassName }} — {{ selectedSubjectName }}
                    </p>
                </div>
                <AppBadge variant="info" v-if="hasExistingData">EDT existant</AppBadge>
            </div>

            <!-- Grille visuelle -->
            <div class="p-4">
                <!-- Vue grille colorée par jour -->
                <div class="grid grid-cols-1 gap-3 mb-6">
                    <div
                        v-for="(day, index) in week"
                        :key="day.week_id"
                        class="grid grid-cols-12 gap-3 items-center p-3 rounded-xl border transition-all"
                        :class="timetableForm[index].start_time
                            ? 'border-primary-300 dark:border-primary-700 bg-primary-50/50 dark:bg-primary-900/10'
                            : 'border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/30'"
                    >
                        <!-- Jour -->
                        <div class="col-span-2 flex items-center gap-2">
                            <div
                                class="w-2 h-2 rounded-full flex-shrink-0"
                                :class="timetableForm[index].start_time ? 'bg-primary-500' : 'bg-gray-300 dark:bg-gray-600'"
                            />
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ day.week_name }}</span>
                        </div>

                        <!-- Heure début -->
                        <div class="col-span-3">
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Heure début</label>
                            <input
                                v-model="timetableForm[index].start_time"
                                type="time"
                                class="w-full px-3 py-1.5 text-sm bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            />
                        </div>

                        <!-- Heure fin -->
                        <div class="col-span-3">
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Heure fin</label>
                            <input
                                v-model="timetableForm[index].end_time"
                                type="time"
                                class="w-full px-3 py-1.5 text-sm bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            />
                        </div>

                        <!-- Salle -->
                        <div class="col-span-3">
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Salle</label>
                            <input
                                v-model="timetableForm[index].room_number"
                                type="text"
                                placeholder="Ex: A101"
                                class="w-full px-3 py-1.5 text-sm bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            />
                        </div>

                        <!-- Effacer -->
                        <div class="col-span-1 flex justify-end">
                            <button
                                v-if="timetableForm[index].start_time"
                                @click="clearDay(index)"
                                type="button"
                                title="Effacer ce jour"
                                class="p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Aperçu calendrier semaine -->
                <div v-if="hasExistingData || timetableForm.some(d => d.start_time)" class="mb-4">
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Aperçu de la semaine</h3>
                    <div class="grid grid-cols-7 gap-1">
                        <div
                            v-for="(day, index) in week"
                            :key="'preview-' + day.week_id"
                            class="rounded-lg p-2 text-center min-h-[80px] flex flex-col items-center justify-center border"
                            :class="timetableForm[index].start_time
                                ? 'border-primary-300 dark:border-primary-700 bg-primary-100/60 dark:bg-primary-900/20'
                                : 'border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/30'"
                        >
                            <p class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 mb-1">
                                {{ day.week_name.slice(0, 3) }}
                            </p>
                            <template v-if="timetableForm[index].start_time">
                                <p class="text-xs font-bold text-primary-700 dark:text-primary-300">
                                    {{ timetableForm[index].start_time }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ timetableForm[index].end_time }}</p>
                                <p v-if="timetableForm[index].room_number" class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                                    {{ timetableForm[index].room_number }}
                                </p>
                            </template>
                            <span v-else class="text-gray-300 dark:text-gray-600 text-lg">—</span>
                        </div>
                    </div>
                </div>

                <!-- Boutons action -->
                <div class="flex items-center justify-between pt-2 border-t border-gray-100 dark:border-gray-700">
                    <button
                        type="button"
                        @click="clearAll"
                        class="text-sm text-gray-400 hover:text-red-500 transition-colors"
                    >
                        Tout effacer
                    </button>
                    <AppButton @click="saveTimetable" :loading="saving">
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

        <!-- État vide -->
        <div v-else class="card p-12 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <p class="text-gray-500 dark:text-gray-400 font-medium">
                {{ filters.class_id ? 'Sélectionnez une classe et une matière pour afficher l\'emploi du temps.' : 'Sélectionnez une classe puis une matière.' }}
            </p>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { AppButton, AppSelect, AppBadge } from '@/Components/UI';

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

// ── Watch sur class_id → charger les matières dès que la valeur change ────
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

// ── Watch sur subject_id → recharger la grille dès que la matière est choisie
watch(() => filters.value.subject_id, (newSubjectId) => {
    if (filters.value.class_id && newSubjectId) {
        applyFilters();
    }
});

// ── Au chargement : si une classe + matière étaient déjà sélectionnées via URL
watch(() => props.selectedClass, (val) => {
    if (val && props.subjects?.length) {
        dynamicSubjects.value = props.subjects;
    }
}, { immediate: true });

// ── Filtrer (rechargement page Inertia) ───────────────────────────────────
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

const clearAll = () => {
    timetableForm.value.forEach((_, i) => clearDay(i));
};

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
