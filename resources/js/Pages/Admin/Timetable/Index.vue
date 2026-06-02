<template>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Emploi du temps</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Gestion des horaires de cours par classe</p>
            </div>
        </div>

        <!-- Filtres -->
        <div class="card p-4">
            <form @submit.prevent="applyFilters" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <AppSelect v-model="filters.class_id" :options="classOptions" placeholder="Sélectionner une classe" @change="applyFilters" />
                <AppSelect v-model="filters.subject_id" :options="subjectOptions" placeholder="Sélectionner une matière" @change="applyFilters" />
                <AppButton type="submit">Filtrer</AppButton>
            </form>
        </div>

        <!-- Grille semaine -->
        <div v-if="week.length" class="card overflow-hidden">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Horaires de la semaine</h2>
            </div>
            <form @submit.prevent="saveTimetable" class="p-4 space-y-4">
                <div v-for="(day, index) in week" :key="day.week_id" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end border-b border-gray-100 dark:border-gray-700 pb-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ day.week_name }}</label>
                        <input type="hidden" :name="`timetable[${index}][week_id]`" :value="day.week_id" />
                    </div>
                    <AppInput v-model="timetableForm[index].start_time" label="Heure début" type="time" />
                    <AppInput v-model="timetableForm[index].end_time" label="Heure fin" type="time" />
                    <AppInput v-model="timetableForm[index].room_number" label="Salle" placeholder="Ex: A101" />
                </div>
                <div class="flex justify-end">
                    <AppButton type="submit" :loading="saving">Enregistrer</AppButton>
                </div>
            </form>
        </div>

        <div v-else class="card p-8 text-center text-gray-500 dark:text-gray-400">
            Sélectionnez une classe et une matière pour afficher l'emploi du temps.
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { AppButton, AppInput, AppSelect } from '@/Components/UI';

interface WeekEntry {
    week_id: number;
    week_name: string;
    start_time: string;
    end_time: string;
    room_number: string;
}

const props = defineProps<{
    classes: { id: number; name: string }[];
    subjects: { subject_id: number; subject_name: string }[];
    week: WeekEntry[];
    selectedClass?: string | number;
    selectedSubject?: string | number;
}>();

const filters = ref({
    class_id: props.selectedClass ? String(props.selectedClass) : '',
    subject_id: props.selectedSubject ? String(props.selectedSubject) : '',
});

const saving = ref(false);

const timetableForm = ref<WeekEntry[]>(
    props.week.map(w => ({ ...w }))
);

const classOptions = computed(() =>
    props.classes.map(c => ({ value: String(c.id), label: c.name }))
);

const subjectOptions = computed(() =>
    props.subjects.map(s => ({ value: String(s.subject_id), label: s.subject_name }))
);

const applyFilters = () => {
    router.get('/admin/class_timetable/list', filters.value, { preserveState: true, replace: true });
};

const saveTimetable = () => {
    saving.value = true;
    router.post('/admin/class_timetable/add', {
        class_id: filters.value.class_id,
        subject_id: filters.value.subject_id,
        timetable: timetableForm.value,
    }, {
        onFinish: () => { saving.value = false; },
    });
};
</script>
