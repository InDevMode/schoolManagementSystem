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
        <div :class="['absolute -top-3 -right-3 w-14 h-14 rounded-full opacity-10 dark:opacity-5 pointer-events-none', bubbleBg]"/>

        <!-- Indicateur animé (alerte) -->
        <span v-if="(variant === 'danger' || variant === 'warning') && Number(value) > 0"
            :class="['absolute top-3 right-3 w-2 h-2 rounded-full animate-pulse z-10', variant === 'danger' ? 'bg-red-500' : 'bg-amber-500']"
        />

        <!-- Icône -->
        <div class="relative mb-2">
            <div :class="['w-10 h-10 rounded-xl flex items-center justify-center shadow-md', iconContainerBg]">
                <NavIcon :name="icon" class="w-4 h-4 text-white drop-shadow"/>
            </div>
        </div>

        <!-- Valeur + label -->
        <p :class="['relative text-xl font-black leading-none tabular-nums', valueColorClass]">
            {{ typeof value === 'number' ? value.toLocaleString('fr-FR') : value }}
        </p>
        <p class="relative text-xs text-gray-500 dark:text-gray-400 mt-1 font-medium truncate">{{ label }}</p>
    </component>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import NavIcon from '@/Components/Layout/NavIcon.vue';

const props = defineProps<{
    label:    string;
    value:    number | string;
    icon:     string;
    variant?: 'default' | 'warning' | 'danger' | 'info';
    href?:    string;
}>();

const palettes = {
    default: {
        gradient:   'bg-gradient-to-br from-primary-400 to-primary-600',
        bubble:     'bg-primary-400',
        valueColor: 'text-gray-900 dark:text-white',
    },
    warning: {
        gradient:   'bg-gradient-to-br from-amber-400 to-orange-500',
        bubble:     'bg-amber-400',
        valueColor: 'text-amber-700 dark:text-amber-400',
    },
    danger: {
        gradient:   'bg-gradient-to-br from-red-400 to-red-600',
        bubble:     'bg-red-400',
        valueColor: 'text-red-700 dark:text-red-400',
    },
    info: {
        gradient:   'bg-gradient-to-br from-blue-400 to-blue-600',
        bubble:     'bg-blue-400',
        valueColor: 'text-gray-900 dark:text-white',
    },
};

const p              = computed(() => palettes[props.variant ?? 'default']);
const iconContainerBg = computed(() => p.value.gradient);
const bubbleBg       = computed(() => p.value.bubble);
const valueColorClass = computed(() => p.value.valueColor);
</script>
