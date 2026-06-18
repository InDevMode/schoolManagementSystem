<template>
    <div class="space-y-6">
        <PageHeader title="Présence des apprenants" subtitle="Définissez la présence pour votre classe" color="cyan">
            <template #icon>
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </template>
        </PageHeader>

        <!-- Filtres -->
        <div class="card p-4">
            <form @submit.prevent="loadStudents" class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                <AppSelect
                    v-model="filters.class_id"
                    label="Classe"
                    :options="classOptions"
                    placeholder="Sélectionner une classe"
                    required
                />
                <AppInput
                    v-model="filters.attendance_date"
                    label="Date"
                    type="date"
                    required
                />
                <AppButton type="submit">Charger les apprenants</AppButton>
            </form>
        </div>

        <!-- Table des apprenants -->
        <div v-if="students.length" class="card overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ students.length }} apprenant(s)</p>
                    <span class="text-xs text-gray-400 dark:text-gray-500">
                        {{ savedCount }} / {{ students.length }} enregistré(s)
                    </span>
                </div>
                <AppButton size="sm" :loading="saving" @click="saveAll" :disabled="pendingCount === 0">
                    Enregistrer {{ pendingCount > 0 ? `(${pendingCount})` : '' }}
                </AppButton>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800/60">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Apprenant</th>
                            <th v-for="type in attendanceTypes" :key="type.value"
                                class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide"
                                :class="typeHeaderColor[type.value]">
                                {{ type.label }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                        <tr v-for="student in students" :key="student.id"
                            :class="[
                                'transition-colors',
                                getAtt(student.id)
                                    ? rowBgColor[getAtt(student.id)!]
                                    : 'hover:bg-gray-50 dark:hover:bg-gray-700/30'
                            ]">
                            <td class="px-4 py-3 text-sm">
                                <span class="font-medium text-gray-800 dark:text-gray-200">
                                    {{ student.last_name }} {{ student.name }}
                                </span>
                                <span v-if="isAlreadySaved(student.id)"
                                      class="ml-2 inline-flex items-center gap-1 text-xs text-emerald-600 dark:text-emerald-400 font-medium">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    enregistré
                                </span>
                                <span v-else-if="!getAtt(student.id)"
                                      class="ml-2 text-xs text-amber-500 dark:text-amber-400 font-medium">
                                    — non saisi
                                </span>
                            </td>
                            <td v-for="type in attendanceTypes" :key="type.value" class="px-4 py-3 text-center">
                                <button
                                    type="button"
                                    @click="toggleAttendance(student.id, type.value)"
                                    :class="[
                                        'w-5 h-5 rounded border-2 transition-all duration-150 flex items-center justify-center mx-auto',
                                        getAtt(student.id) === type.value
                                            ? checkboxActiveClass[type.value]
                                            : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 hover:border-gray-400 dark:hover:border-gray-500'
                                    ]"
                                    :aria-label="`${type.label} pour ${student.last_name} ${student.name}`"
                                    :aria-pressed="getAtt(student.id) === type.value"
                                >
                                    <svg v-if="getAtt(student.id) === type.value"
                                         class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div v-else-if="filtersApplied" class="card p-8 text-center text-gray-400 dark:text-gray-500">
            <p class="text-sm">Aucun apprenant trouvé pour cette classe.</p>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, reactive, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import { PageHeader, AppButton, AppInput, AppSelect } from '@/Components/UI';

interface Student    { id: number; name: string; last_name: string; }
interface ClassItem  { id: number; class_id: number; class_name: string; }

const props = defineProps<{
    classes:            ClassItem[];
    students:           Student[];
    selectedClass:      string | null;
    selectedDate:       string | null;
    existingAttendance: Record<string | number, string | number>;
}>();

const filters = reactive({
    class_id:        props.selectedClass ?? '',
    attendance_date: props.selectedDate  ?? '',
});

const filtersApplied = computed(() => !!(props.selectedClass && props.selectedDate));
const saving = ref(false);

const classOptions = computed(() =>
    props.classes.map(c => ({ value: String(c.class_id), label: c.class_name }))
);

const attendanceTypes = [
    { value: 'present',  label: 'Présent' },
    { value: 'late',     label: 'En retard' },
    { value: 'absent',   label: 'Absent' },
    { value: 'half_day', label: 'Demi-journée' },
];

const typeHeaderColor: Record<string, string> = {
    present:  'text-emerald-600 dark:text-emerald-400',
    late:     'text-amber-600 dark:text-amber-400',
    absent:   'text-red-600 dark:text-red-400',
    half_day: 'text-violet-600 dark:text-violet-400',
};

const checkboxActiveClass: Record<string, string> = {
    present:  'border-emerald-500 bg-emerald-500',
    late:     'border-amber-500  bg-amber-500',
    absent:   'border-red-500    bg-red-500',
    half_day: 'border-violet-500   bg-violet-500',
};

const rowBgColor: Record<string, string> = {
    present:  'bg-emerald-50/40 dark:bg-emerald-900/10',
    late:     'bg-amber-50/40   dark:bg-amber-900/10',
    absent:   'bg-red-50/40     dark:bg-red-900/10',
    half_day: 'bg-violet-50/40    dark:bg-violet-900/10',
};

const normalize = (v: string | number | undefined | null): string | null => {
    if (v === undefined || v === null || v === '') return null;
    const map: Record<string, string> = {
        '0': 'present', '1': 'present', '2': 'late', '3': 'absent', '4': 'half_day',
        'present': 'present', 'late': 'late', 'absent': 'absent', 'half_day': 'half_day',
    };
    return map[String(v).trim()] ?? null;
};

const getRawExisting = (studentId: number): string | number | undefined =>
    props.existingAttendance[studentId] ?? props.existingAttendance[String(studentId)];

const attendance = reactive<Record<string, string | null>>({});

const buildAttendance = () => {
    for (const s of props.students) {
        attendance[String(s.id)] = normalize(getRawExisting(s.id));
    }
};
buildAttendance();

watch(() => [props.students, props.existingAttendance], () => {
    Object.keys(attendance).forEach(k => { delete attendance[k]; });
    buildAttendance();
}, { deep: true });

const getAtt = (studentId: number): string | null =>
    attendance[String(studentId)] ?? null;

const toggleAttendance = (studentId: number, value: string) => {
    const k = String(studentId);
    attendance[k] = attendance[k] === value ? null : value;
};

const isAlreadySaved = (studentId: number): boolean =>
    getRawExisting(studentId) !== undefined;

const savedCount = computed(() =>
    props.students.filter(s => isAlreadySaved(s.id)).length
);
const pendingCount = computed(() =>
    props.students.filter(s => getAtt(s.id) !== null).length
);

const loadStudents = () => {
    router.get('/teacher/attendance/student/list', {
        class_id:        filters.class_id,
        attendance_date: filters.attendance_date,
    }, { preserveState: true, replace: true });
};

const saveAll = async () => {
    const toSave = props.students.filter(s => getAtt(s.id) !== null);
    if (toSave.length === 0) return;

    saving.value = true;

    const entries = toSave.map(student => ({
        student_id:      student.id,
        class_id:        Number(filters.class_id),
        attendance_date: filters.attendance_date,
        attendance_type: getAtt(student.id),
    }));

    try {
        await axios.post('/teacher/attendance/student/save', { entries });
        router.get('/teacher/attendance/student/list', {
            class_id:        filters.class_id,
            attendance_date: filters.attendance_date,
        }, { preserveState: false, replace: true });
    } catch (e) {
        console.error('Erreur enregistrement présences', e);
    } finally {
        saving.value = false;
    }
};
</script>
