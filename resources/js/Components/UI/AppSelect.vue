<template>
    <div :class="block ? 'w-full' : ''">
        <label v-if="label" :for="selectId" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
            {{ label }}
            <span v-if="required" class="text-danger-500 ml-0.5">*</span>
        </label>
        <div class="relative">
            <select
                :id="selectId"
                v-bind="$attrs"
                :value="modelValue"
                :disabled="disabled"
                :required="required"
                :class="selectClasses"
                @change="$emit('update:modelValue', ($event.target as HTMLSelectElement).value)"
            >
                <option v-if="placeholder" value="" disabled :selected="!modelValue">{{ placeholder }}</option>
                <option
                    v-for="opt in options"
                    :key="opt.value"
                    :value="opt.value"
                    :disabled="opt.disabled"
                >
                    {{ opt.label }}
                </option>
                <slot />
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
        </div>
        <p v-if="error" class="mt-1.5 text-xs text-danger-600 dark:text-danger-400">{{ error }}</p>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import type { SelectOption } from '@/types';

interface Props {
    modelValue?: string | number;
    label?: string;
    options?: SelectOption[];
    placeholder?: string;
    disabled?: boolean;
    required?: boolean;
    error?: string;
    block?: boolean;
    id?: string;
}

const props = withDefaults(defineProps<Props>(), {
    options: () => [],
    block: true,
});

defineEmits<{ 'update:modelValue': [value: string] }>();
defineOptions({ inheritAttrs: false });

const selectId = computed(() => props.id ?? `select-${Math.random().toString(36).slice(2)}`);

const selectClasses = computed(() => [
    'w-full rounded-lg border bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100',
    'pl-3.5 pr-10 py-2.5 text-sm appearance-none',
    'transition-all duration-200',
    'focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent',
    'disabled:opacity-50 disabled:cursor-not-allowed',
    props.error
        ? 'border-danger-500 focus:ring-danger-500'
        : 'border-gray-300 dark:border-gray-600',
]);
</script>
