<template>
    <component
        :is="href ? 'a' : 'div'"
        :href="href"
        :class="[
            'relative overflow-hidden rounded-2xl p-4 flex items-center gap-3 transition-all duration-200',
            'bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700',
            href ? 'hover:shadow-md hover:-translate-y-0.5 cursor-pointer' : '',
        ]"
    >
        <!-- Bulle décorative — opacité réduite, visible light uniquement -->
        <div :class="['absolute -right-5 -top-5 w-20 h-20 rounded-full dark:opacity-0', bubbleBg]" style="opacity:0.12" />
        <div :class="['absolute right-2 -bottom-6 w-12 h-12 rounded-full dark:opacity-0', bubbleBg]" style="opacity:0.07" />

        <!-- Icône -->
        <div :class="['relative z-10 w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0', iconBg]">
            <NavIcon :name="icon" :class="['w-4 h-4', iconColor]" />
        </div>

        <!-- Texte -->
        <div class="relative z-10 min-w-0 flex-1">
            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ label }}</p>
            <p class="text-xl font-bold text-gray-900 dark:text-white">{{ value.toLocaleString('fr-FR') }}</p>
        </div>

        <!-- Flèche -->
        <svg v-if="href" class="relative z-10 w-3.5 h-3.5 flex-shrink-0 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
        </svg>
    </component>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import NavIcon from '@/Components/Layout/NavIcon.vue';

interface Props {
    label: string;
    value: number;
    icon: string;
    color?: 'primary' | 'secondary' | 'success' | 'danger' | 'warning' | 'info';
    href?: string;
}

const props = withDefaults(defineProps<Props>(), { color: 'primary' });

const colorMap: Record<string, { bubble: string; iconBg: string; icon: string }> = {
    primary:   { bubble: 'bg-primary-500',   iconBg: 'bg-primary-100 dark:bg-primary-900/40',   icon: 'text-primary-600 dark:text-primary-400' },
    secondary: { bubble: 'bg-indigo-500',    iconBg: 'bg-indigo-100 dark:bg-indigo-900/40',     icon: 'text-indigo-600 dark:text-indigo-400' },
    success:   { bubble: 'bg-green-500',     iconBg: 'bg-green-100 dark:bg-green-900/40',       icon: 'text-green-600 dark:text-green-400' },
    danger:    { bubble: 'bg-red-500',       iconBg: 'bg-red-100 dark:bg-red-900/40',           icon: 'text-red-600 dark:text-red-400' },
    warning:   { bubble: 'bg-amber-500',     iconBg: 'bg-amber-100 dark:bg-amber-900/40',       icon: 'text-amber-600 dark:text-amber-400' },
    info:      { bubble: 'bg-blue-500',      iconBg: 'bg-blue-100 dark:bg-blue-900/40',         icon: 'text-blue-600 dark:text-blue-400' },
};

const c         = computed(() => colorMap[props.color]);
const bubbleBg  = computed(() => c.value.bubble);
const iconBg    = computed(() => c.value.iconBg);
const iconColor = computed(() => c.value.icon);
</script>
