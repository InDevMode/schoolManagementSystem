<template>
    <component
        :is="href ? 'a' : 'div'"
        :href="href"
        :class="[
            'relative overflow-hidden rounded-2xl p-4 bg-white dark:bg-gray-800',
            'border border-gray-100 dark:border-gray-700/60 shadow-sm',
            'transition-all duration-300 ease-out',
            href ? 'cursor-pointer hover:shadow-lg hover:-translate-y-1' : '',
        ]"
    >
        <!-- Déco fond -->
        <div :class="['absolute -top-3 -right-3 w-16 h-16 rounded-full opacity-10 dark:opacity-5 pointer-events-none', bubbleBg]"/>

        <!-- Icône + label + valeur -->
        <div class="relative flex items-start justify-between mb-2">
            <div :class="['w-11 h-11 rounded-xl flex items-center justify-center shadow-md flex-shrink-0', iconContainerBg]">
                <NavIcon :name="icon" class="w-5 h-5 text-white drop-shadow"/>
            </div>
            <svg v-if="href" class="w-4 h-4 text-gray-300 dark:text-gray-600 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </div>

        <p class="text-xl font-black text-gray-900 dark:text-white leading-none tabular-nums">
            {{ typeof value === 'number' ? (value ?? 0).toLocaleString('fr-FR') : (value ?? 0) }}
        </p>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 font-medium truncate">{{ label }}</p>
    </component>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import NavIcon from '@/Components/Layout/NavIcon.vue';

interface Props {
    label: string;
    value?: number | string | null;
    icon: string;
    color?: 'primary' | 'secondary' | 'success' | 'danger' | 'warning' | 'info';
    href?: string;
}

const props = withDefaults(defineProps<Props>(), { color: 'primary', value: 0 });

const colorMap: Record<string, { gradient: string; bubble: string }> = {
    primary:   { gradient: 'bg-gradient-to-br from-primary-400 to-primary-600',   bubble: 'bg-primary-400' },
    secondary: { gradient: 'bg-gradient-to-br from-primary-400 to-primary-600',   bubble: 'bg-primary-400' },
    success:   { gradient: 'bg-gradient-to-br from-emerald-400 to-emerald-600',   bubble: 'bg-emerald-400' },
    danger:    { gradient: 'bg-gradient-to-br from-red-400 to-red-600',           bubble: 'bg-red-400' },
    warning:   { gradient: 'bg-gradient-to-br from-amber-400 to-orange-500',      bubble: 'bg-amber-400' },
    info:      { gradient: 'bg-gradient-to-br from-violet-400 to-violet-600',     bubble: 'bg-violet-400' },
};

const c               = computed(() => colorMap[props.color]);
const iconContainerBg = computed(() => c.value.gradient);
const bubbleBg        = computed(() => c.value.bubble);
</script>
