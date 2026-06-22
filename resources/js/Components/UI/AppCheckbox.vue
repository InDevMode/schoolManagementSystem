<template>
    <label class="inline-flex items-center gap-2.5 cursor-pointer group">
        <input
            type="checkbox"
            :checked="modelValue"
            :disabled="disabled"
            class="w-4 h-4 rounded border-4 border-gray-200 dark:border-gray-500 text-primary-600 focus:ring-primary-500 focus:ring-offset-0 bg-white dark:bg-gray-800 checked:bg-primary-600 dark:checked:bg-primary-500 checked:border-primary-600 dark:checked:border-primary-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            @change="$emit('update:modelValue', ($event.target as HTMLInputElement).checked)"
        />
        <span v-if="label" class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white transition-colors">
            {{ label }}
        </span>
        <slot />
    </label>
</template>

<script setup lang="ts">
interface Props {
    modelValue?: boolean;
    label?: string;
    disabled?: boolean;
}
withDefaults(defineProps<Props>(), { disabled: false });
defineEmits<{ 'update:modelValue': [value: boolean] }>();
</script>
