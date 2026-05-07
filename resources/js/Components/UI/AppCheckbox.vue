<template>
    <label class="inline-flex items-center gap-2.5 cursor-pointer group">
        <input
            type="checkbox"
            :checked="modelValue"
            :disabled="disabled"
            class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500 focus:ring-offset-0 dark:bg-gray-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
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
