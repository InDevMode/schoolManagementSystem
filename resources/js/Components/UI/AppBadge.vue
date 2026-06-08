<template>
    <span :class="classes">
        <span v-if="dot" :class="['w-1.5 h-1.5 rounded-full mr-1.5', dotColor]" />
        <slot />
    </span>
</template>

<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    variant?: 'primary' | 'secondary' | 'success' | 'danger' | 'warning' | 'info' | 'gray' | 'purple' | 'cyan' | 'amber';
    size?: 'sm' | 'md';
    dot?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    variant: 'gray',
    size: 'md',
    dot: false,
});

const variantMap: Record<string, string> = {
    primary:   'bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-300',
    secondary: 'bg-secondary-100 text-secondary-700 dark:bg-secondary-900/30 dark:text-secondary-300',
    success:   'bg-success-100 text-success-700 dark:bg-success-900/30 dark:text-success-300',
    danger:    'bg-danger-100 text-danger-700 dark:bg-danger-900/30 dark:text-danger-300',
    warning:   'bg-warning-100 text-warning-700 dark:bg-warning-900/30 dark:text-warning-300',
    info:      'bg-info-100 text-info-700 dark:bg-info-900/30 dark:text-info-300',
    gray:      'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
    purple:    'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300',
    cyan:      'bg-cyan-100 text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-300',
    amber:     'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
};

const dotColorMap: Record<string, string> = {
    primary: 'bg-primary-500', secondary: 'bg-secondary-500',
    success: 'bg-success-500', danger: 'bg-danger-500',
    warning: 'bg-warning-500', info: 'bg-info-500', gray: 'bg-gray-400',
    purple:  'bg-purple-500',  cyan: 'bg-cyan-500',  amber: 'bg-amber-500',
};

const sizeMap: Record<string, string> = {
    sm: 'px-2 py-0.5 text-xs',
    md: 'px-2.5 py-1 text-xs',
};

const classes = computed(() => [
    'badge inline-flex items-center font-medium rounded-full',
    variantMap[props.variant],
    sizeMap[props.size],
]);

const dotColor = computed(() => dotColorMap[props.variant]);
</script>
