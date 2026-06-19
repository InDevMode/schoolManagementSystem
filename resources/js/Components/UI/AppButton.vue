<template>
    <component
        :is="href ? Link : 'button'"
        :href="href"
        :type="href ? undefined : type"
        :disabled="disabled || loading"
        :class="classes"
        v-bind="$attrs"
    >
        <span v-if="loading" class="mr-2 inline-flex">
            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" />
            </svg>
        </span>
        <span v-if="$slots.icon && !loading" class="mr-2 inline-flex items-center">
            <slot name="icon" />
        </span>
        <slot />
    </component>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

interface Props {
    variant?: 'primary' | 'secondary' | 'danger' | 'success' | 'warning' | 'ghost' | 'outline';
    size?: 'xs' | 'sm' | 'md' | 'lg';
    type?: 'button' | 'submit' | 'reset';
    disabled?: boolean;
    loading?: boolean;
    href?: string;
    block?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    variant: 'primary',
    size: 'md',
    type: 'button',
    disabled: false,
    loading: false,
    block: false,
});

const base = 'inline-flex items-center justify-center font-medium rounded-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-gray-900 disabled:opacity-50 disabled:cursor-not-allowed';

const variants: Record<string, string> = {
    primary:   'bg-primary-600 hover:bg-primary-700 text-white focus:ring-primary-500 shadow-sm',
    secondary: 'bg-secondary-600 hover:bg-secondary-700 text-white focus:ring-secondary-500 shadow-sm',
    danger:    'bg-danger-600 hover:bg-danger-700 text-white focus:ring-danger-500 shadow-sm',
    success:   'bg-success-600 hover:bg-success-700 text-white focus:ring-success-500 shadow-sm',
    warning:   'bg-warning-500 hover:bg-warning-600 text-white focus:ring-warning-500 shadow-sm',
    ghost:     'bg-transparent hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 focus:ring-gray-400',
    outline:   'border border-primary-600 text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 focus:ring-primary-500',
};

const sizes: Record<string, string> = {
    xs: 'px-2.5 py-1.5 text-xs gap-1',
    sm: 'px-3 py-2 text-sm gap-1.5',
    md: 'px-4 py-2.5 text-sm gap-2',
    lg: 'px-6 py-3 text-base gap-2',
};

const classes = computed(() => [
    base,
    variants[props.variant],
    sizes[props.size],
    props.block ? 'w-full' : '',
]);
</script>
