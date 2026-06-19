<template>
    <div :class="block ? 'w-full' : ''">
        <label v-if="label" :for="inputId" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
            {{ label }}
            <span v-if="required" class="text-danger-500 ml-0.5">*</span>
        </label>
        <div class="relative">
            <div v-if="$slots.prefix" class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                <slot name="prefix" />
            </div>
            <input
                :id="inputId"
                v-bind="$attrs"
                :type="type"
                :value="modelValue"
                :placeholder="placeholder"
                :disabled="disabled"
                :required="required"
                :class="inputClasses"
                @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)"
            />
            <div v-if="$slots.suffix" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400">
                <slot name="suffix" />
            </div>
        </div>
        <p v-if="error" class="mt-1.5 text-xs text-danger-600 dark:text-danger-400 flex items-center gap-1">
            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            {{ error }}
        </p>
        <p v-else-if="hint" class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">{{ hint }}</p>
    </div>
</template>

<script setup lang="ts">
import { computed, useSlots } from 'vue';

interface Props {
    modelValue?: string | number;
    label?: string;
    type?: string;
    placeholder?: string;
    disabled?: boolean;
    required?: boolean;
    error?: string;
    hint?: string;
    block?: boolean;
    id?: string;
}

const props = withDefaults(defineProps<Props>(), {
    type: 'text',
    block: true,
});

defineEmits<{ 'update:modelValue': [value: string] }>();
defineOptions({ inheritAttrs: false });

const slots   = useSlots();
const inputId = computed(() => props.id ?? `input-${Math.random().toString(36).slice(2)}`);

const inputClasses = computed(() => [
    'w-full rounded-xl border bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100',
    'placeholder-gray-400 dark:placeholder-gray-500',
    'transition-all duration-200',
    'focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent',
    'disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-50 dark:disabled:bg-gray-700',
    slots.prefix ? 'pl-10' : 'pl-3.5',
    slots.suffix ? 'pr-10' : 'pr-3.5',
    'py-2.5 text-sm',
    props.error
        ? 'border-danger-500 focus:ring-danger-500'
        : 'border-gray-300 dark:border-gray-600',
]);
</script>
