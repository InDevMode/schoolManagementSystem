<template>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Perceptions des contributions</h1>
            </div>
            <div class="flex items-center gap-2">
                <AppSelect v-model="filters.class_id" :options="classOptions" placeholder="Filtrer par classe" @change="applyFilters" />
            </div>
        </div>

        <div class="card overflow-hidden">
            <AppTable :columns="columns" :rows="feesCollections.data" :pagination="feesCollections" row-key="id">
                <template #cell-student="{ row }">
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ row.student_last_name }} {{ row.student_name }}</p>
                        <p class="text-xs text-gray-500">{{ row.student_admission_number }}</p>
                    </div>
                </template>
                <template #cell-payment_status="{ row }">
                    <AppBadge :variant="row.payment_status === 'Paid' ? 'success' : 'warning'" dot>
                        {{ row.payment_status ?? 'En attente' }}
                    </AppBadge>
                </template>
                <template #actions="{ row }">
                    <a :href="`/admin/feescollections/create/${row.id}`" class="p-1.5 rounded-lg text-gray-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors inline-flex">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    </a>
                </template>
            </AppTable>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { AppSelect, AppTable, AppBadge } from '@/Components/UI';

interface FeesStudent {
    id: number;
    student_name: string;
    student_last_name: string;
    student_admission_number: string;
    class_name: string;
    class_amount: number;
    payment_status?: string;
}

const props = defineProps<{
    classes: { id: number; name: string }[];
    feesCollections: {
        data: FeesStudent[];
        total: number;
        from: number;
        to: number;
        links: { url: string | null; label: string; active: boolean }[];
    };
}>();

const filters = ref({ class_id: '' });

const classOptions = computed(() =>
    props.classes.map(c => ({ value: String(c.id), label: c.name }))
);

const columns = [
    { key: 'student', label: 'Apprenant' },
    { key: 'class_name', label: 'Classe' },
    { key: 'class_amount', label: 'Montant total' },
    { key: 'payment_status', label: 'Statut' },
];

const applyFilters = () => {
    router.get('/admin/feescollections/collections/list', filters.value, { preserveState: true, replace: true });
};
</script>
