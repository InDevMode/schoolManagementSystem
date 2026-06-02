<template>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Présence des apprenants</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Définissez la présence pour votre classe</p>
        </div>

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
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ students.length }} apprenant(s)</p>
                <AppButton size="sm" :loading="saving" @click="saveAll">Enregistrer tout</AppButton>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800/60">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Apprenant</th>
                            <th v-for="type in attendanceTypes" :key="type.value" class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">{{ type.label }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                        <tr v-for="student in students" :key="student.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                {{ student.last_name }} {{ student.name }}
                            </td>
                            <td v-for="type in attendanceTypes" :key="type.value" class="px-4 py-3 text-center">
                                <input
                                    type="radio"
                                    :name="`attendance_${student.id}`"
                                    :value="type.value"
                                    v-model="attendance[student.id]"
                                    class="w-4 h-4 text-primary-600 border-gray-300 focus:ring-primary-500"
                                />
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
import { ref, computed, reactive } from 'vue';
import { router } from '@inertiajs/vue3';
import { AppButton, AppInput, AppSelect } from '@/Components/UI';

interface Student  { id: number; name: string; last_name: string; }
interface ClassItem { id: number; class_id: number; class_name: string; }

const props = defineProps<{
    classes:       ClassItem[];
    students:      Student[];
    selectedClass: string | null;
    selectedDate:  string | null;
}>();

const filters = reactive({
    class_id:        props.selectedClass ?? '',
    attendance_date: props.selectedDate  ?? '',
});

const filtersApplied = ref(!!(props.selectedClass && props.selectedDate));
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

const attendance = reactive<Record<number, string>>(
    Object.fromEntries(props.students.map(s => [s.id, 'present']))
);

const loadStudents = () => {
    filtersApplied.value = true;
    router.get('/teacher/attendance/student/list', {
        class_id:        filters.class_id,
        attendance_date: filters.attendance_date,
    }, { preserveState: true, replace: true });
};

const saveAll = async () => {
    saving.value = true;
    const promises = props.students.map(student =>
        fetch('/teacher/attendance/student/save', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? '',
            },
            body: JSON.stringify({
                student_id:      student.id,
                class_id:        filters.class_id,
                attendance_date: filters.attendance_date,
                attendance_type: attendance[student.id] ?? 'present',
            }),
        })
    );
    await Promise.all(promises);
    saving.value = false;
};
</script>
