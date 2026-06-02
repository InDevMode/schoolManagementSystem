<template>
    <label class="inline-flex items-center gap-2.5 cursor-pointer group">
        <input
            type="radio"
            :value="value"
            :checked="modelValue === value"
            :disabled="disabled"
            class="w-4 h-4 border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500 focus:ring-offset-0 dark:bg-gray-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            @change="$emit('update:modelValue', value)"
        />
        <span v-if="label" class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white transition-colors">
            {{ label }}
        </span>
        <slot />
    </label>
</template>

<script setup lang="ts">
interface Props {
    modelValue?: string | number;
    value: string | number;
    label?: string;
    disabled?: boolean;
}
withDefaults(defineProps<Props>(), { disabled: false });
defineEmits<{ 'update:modelValue': [value: string | number] }>();
</script>
