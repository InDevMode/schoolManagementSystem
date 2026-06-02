<template>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Registre des notes</h1>
        </div>

        <div class="card p-4">
            <form @submit.prevent="applyFilters" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <AppSelect v-model="filters.exam_id" :options="examOptions" placeholder="Sélectionner une évaluation" />
                <AppSelect v-model="filters.class_id" :options="classOptions" placeholder="Sélectionner une classe" />
                <AppButton type="submit">Filtrer</AppButton>
            </form>
        </div>

        <div v-if="data.students?.length" class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Apprenant</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <tr v-for="student in data.students" :key="student.id" class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ student.last_name }} {{ student.name }}</td>
                            <td class="px-4 py-3">
                                <AppButton size="sm" variant="ghost">Saisir les notes</AppButton>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div v-else class="card p-8 text-center text-gray-500 dark:text-gray-400">
            Sélectionnez une évaluation et une classe pour afficher les apprenants.
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { AppButton, AppSelect } from '@/Components/UI';

interface TeacherMarksData {
    classes?: { id: number; name: string; class_id?: number; class_name?: string }[];
    exams?: { id: number; name: string }[];
    subjects?: unknown[];
    students?: { id: number; name: string; last_name: string }[];
}

const props = defineProps<{
    data: TeacherMarksData;
}>();

const filters = ref({ exam_id: '', class_id: '' });

const examOptions = computed(() =>
    (props.data.exams ?? []).map(e => ({ value: String(e.id), label: e.name }))
);

const classOptions = computed(() =>
    (props.data.classes ?? []).map(c => ({
        value: String(c.class_id ?? c.id),
        label: c.class_name ?? c.name,
    }))
);

const applyFilters = () => {
    router.get('/teacher/examinations/marks_register/list', filters.value, { preserveState: true, replace: true });
};
</script>
