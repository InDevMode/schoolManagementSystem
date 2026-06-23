<template>
    <div :class="[
        'relative overflow-hidden rounded-2xl p-4 bg-white dark:bg-gray-800',
        'border border-gray-100 dark:border-gray-700/60 shadow-sm',
    ]">
        <!-- Déco fond -->
        <div :class="['absolute -top-3 -right-3 w-16 h-16 rounded-full opacity-10 dark:opacity-5 pointer-events-none', bubbleBg]"/>

        <!-- Icône -->
        <div class="relative mb-2">
            <div :class="['w-10 h-10 rounded-xl flex items-center justify-center shadow-md', iconContainerBg]">
                <NavIcon :name="icon" class="w-4 h-4 text-white drop-shadow"/>
            </div>
        </div>

        <!-- Valeur + label -->
        <p class="relative text-xl font-black text-gray-900 dark:text-white leading-none tabular-nums">
            {{ value.toLocaleString('fr-FR') }}
        </p>
        <p class="relative text-xs text-gray-500 dark:text-gray-400 mt-1 font-medium truncate">{{ label }}</p>
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

const colorMap: Record<string, { gradient: string; bubble: string }> = {
    success: { gradient: 'bg-gradient-to-br from-emerald-400 to-emerald-600', bubble: 'bg-emerald-400' },
    warning: { gradient: 'bg-gradient-to-br from-amber-400 to-orange-500',    bubble: 'bg-amber-400' },
    danger:  { gradient: 'bg-gradient-to-br from-red-400 to-red-600',         bubble: 'bg-red-400' },
    info:    { gradient: 'bg-gradient-to-br from-blue-400 to-blue-600',       bubble: 'bg-blue-400' },
};

const c               = computed(() => colorMap[props.color]);
const iconContainerBg = computed(() => c.value.gradient);
const bubbleBg        = computed(() => c.value.bubble);
</script>
