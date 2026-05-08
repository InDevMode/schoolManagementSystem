<template>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Programmation des examens</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Planifiez les dates et horaires des épreuves par classe</p>
        </div>

        <!-- Filtres -->
        <div class="card p-5">
            <form @submit.prevent="applyFilters" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <AppSelect
                    v-model="filters.exam_id"
                    label="Évaluation"
                    :options="examOptions"
                    placeholder="Sélectionner une évaluation"
                />
                <AppSelect
                    v-model="filters.class_id"
                    label="Classe"
                    :options="classOptions"
                    placeholder="Sélectionner une classe"
                />
                <div class="flex items-end">
                    <AppButton type="submit" block>
                        <template #icon>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z" />
                            </svg>
                        </template>
                        Filtrer
                    </AppButton>
                </div>
            </form>
        </div>

        <!-- Tableau de programmation -->
        <div v-if="examSchedule.length" class="card overflow-hidden">
            <form @submit.prevent="saveSchedule">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800/60">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Matière</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Début</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Fin</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Salle</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Note max</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Note min</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                            <tr v-for="(item, index) in scheduleForm" :key="index" class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                    {{ item.subject_name }}
                                    <span class="ml-1 text-xs text-gray-400">({{ item.subject_type }})</span>
                                </td>
                                <td class="px-4 py-2">
                                    <input type="date" v-model="item.exam_date"
                                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                                </td>
                                <td class="px-4 py-2">
                                    <input type="time" v-model="item.start_time"
                                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                                </td>
                                <td class="px-4 py-2">
                                    <input type="time" v-model="item.end_time"
                                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                                </td>
                                <td class="px-4 py-2">
                                    <input type="text" v-model="item.room_number" placeholder="Ex: Salle A"
                                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                                </td>
                                <td class="px-4 py-2">
                                    <input type="number" v-model="item.full_marks" placeholder="20"
                                        class="w-24 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                                </td>
                                <td class="px-4 py-2">
                                    <input type="number" v-model="item.passing_marks" placeholder="10"
                                        class="w-24 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700 flex justify-end">
                    <AppButton type="submit" :loading="saving">
                        <template #icon>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </template>
                        Enregistrer la programmation
                    </AppButton>
                </div>
            </form>
        </div>

        <!-- État vide -->
        <div v-else class="card p-12 text-center">
            <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <p class="text-gray-500 dark:text-gray-400 font-medium">Sélectionnez une évaluation et une classe</p>
            <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Les matières programmées apparaîtront ici</p>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { AppButton, AppSelect } from '@/Components/UI';

interface ScheduleItem {
    subject_id: number;
    class_id: number;
    subject_name: string;
    subject_type: string;
    exam_date: string;
    start_time: string;
    end_time: string;
    room_number: string;
    full_marks: string;
    passing_marks: string;
}

interface Exam { id: number; name: string; }
interface ClassItem { id: number; name: string; }

const props = defineProps<{
    examSchedule:  ScheduleItem[];
    exams:         Exam[];
    classes:       ClassItem[];
    selectedExam?:  string | null;
    selectedClass?: string | null;
}>();

const filters  = ref({
    exam_id:  props.selectedExam  ?? '',
    class_id: props.selectedClass ?? '',
});
const saving   = ref(false);
const scheduleForm = ref<ScheduleItem[]>(props.examSchedule.map(s => ({ ...s })));

// Resynchroniser quand les props changent (après filtre)
watch(() => props.examSchedule, (val) => {
    scheduleForm.value = val.map(s => ({ ...s }));
});

const examOptions = computed(() =>
    (props.exams ?? []).map(e => ({ value: String(e.id), label: e.name }))
);

const classOptions = computed(() =>
    (props.classes ?? []).map(c => ({ value: String(c.id), label: c.name }))
);

const applyFilters = () => {
    router.get('/admin/examinations/schedule/list', filters.value, {
        preserveState: true,
        replace: true,
    });
};

const saveSchedule = () => {
    saving.value = true;
    router.post('/admin/examinations/schedule/add', {
        exam_id:  filters.value.exam_id,
        class_id: filters.value.class_id,
        schedule: scheduleForm.value,
    }, {
        onFinish: () => { saving.value = false; },
    });
};
</script>
