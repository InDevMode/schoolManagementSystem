<template>
    <div class="flex items-center gap-1.5 p-1 rounded-xl bg-gray-100 dark:bg-gray-800 w-fit">
        <button
            v-for="opt in options"
            :key="opt.value"
            @click="$emit('update:modelValue', opt.value)"
            :class="[
                'px-3 py-1.5 rounded-md text-xs font-semibold transition-all duration-150',
                modelValue === opt.value
                    ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm'
                    : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200',
            ]"
        >{{ opt.label }}</button>
    </div>
</template>

<script setup lang="ts">
defineProps<{ modelValue: string }>();
defineEmits<{ 'update:modelValue': [value: string] }>();

const options = [
    { label: 'Semaine', value: 'week' },
    { label: 'Mois',    value: 'month' },
    { label: 'Année',   value: 'year' },
];
</script>
