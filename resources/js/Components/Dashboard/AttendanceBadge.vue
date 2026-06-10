<template>
    <div :class="[
        'relative overflow-hidden rounded-2xl p-4 flex items-center gap-3',
        'bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-card',
    ]">
        <!-- Barre colorée gauche -->
        <div :class="['absolute left-0 top-0 bottom-0 w-1 rounded-l-2xl', barColor]"/>

        <!-- Icône -->
        <div :class="['w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0', iconBg]">
            <NavIcon :name="icon" :class="['w-5 h-5', iconColor]"/>
        </div>

        <!-- Texte -->
        <div class="min-w-0">
            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ label }}</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ value.toLocaleString('fr-FR') }}</p>
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
    color?: 'success' | 'warning' | 'danger' | 'info';
}

const props = withDefaults(defineProps<Props>(), { color: 'success' });

const colorMap: Record<string, { bar: string; iconBg: string; icon: string }> = {
    success: { bar: 'bg-success-500',  iconBg: 'bg-success-100 dark:bg-success-900/30',  icon: 'text-success-600 dark:text-success-400' },
    warning: { bar: 'bg-warning-500',  iconBg: 'bg-warning-100 dark:bg-warning-900/30',  icon: 'text-warning-600 dark:text-warning-400' },
    danger:  { bar: 'bg-danger-500',   iconBg: 'bg-danger-100 dark:bg-danger-900/30',    icon: 'text-danger-600 dark:text-danger-400' },
    info:    { bar: 'bg-info-500',     iconBg: 'bg-info-100 dark:bg-info-900/30',        icon: 'text-info-600 dark:text-info-400' },
};

const c         = computed(() => colorMap[props.color]);
const barColor  = computed(() => c.value.bar);
const iconBg    = computed(() => c.value.iconBg);
const iconColor = computed(() => c.value.icon);
</script>
