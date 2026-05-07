<template>
    <div class="card p-4 flex items-center gap-3 hover:shadow-card-md transition-shadow duration-200">
        <div :class="['w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0', iconBg]">
            <NavIcon :name="icon" :class="['w-5 h-5', iconColor]" />
        </div>
        <div class="min-w-0">
            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ label }}</p>
            <p class="text-xl font-bold text-gray-900 dark:text-white">{{ value.toLocaleString() }}</p>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import NavIcon from '@/Components/Layout/NavIcon.vue';

interface Props {
    label: string;
    value: number;
    icon: string;
    color?: 'primary' | 'secondary' | 'success' | 'danger' | 'warning' | 'info';
}

const props = withDefaults(defineProps<Props>(), { color: 'primary' });

const colorMap: Record<string, { bg: string; text: string }> = {
    primary:   { bg: 'bg-primary-100 dark:bg-primary-900/30',   text: 'text-primary-600 dark:text-primary-400' },
    secondary: { bg: 'bg-secondary-100 dark:bg-secondary-900/30', text: 'text-secondary-600 dark:text-secondary-400' },
    success:   { bg: 'bg-success-100 dark:bg-success-900/30',   text: 'text-success-600 dark:text-success-400' },
    danger:    { bg: 'bg-danger-100 dark:bg-danger-900/30',     text: 'text-danger-600 dark:text-danger-400' },
    warning:   { bg: 'bg-warning-100 dark:bg-warning-900/30',   text: 'text-warning-600 dark:text-warning-400' },
    info:      { bg: 'bg-info-100 dark:bg-info-900/30',         text: 'text-info-600 dark:text-info-400' },
};

const iconBg   = computed(() => colorMap[props.color].bg);
const iconColor = computed(() => colorMap[props.color].text);
</script>
