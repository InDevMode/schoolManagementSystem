<template>
    <component
        :is="href ? 'a' : 'div'"
        :href="href"
        :class="[
            'card px-3 py-3 flex items-center gap-3 transition-all duration-300 ease-out',
            href ? 'cursor-pointer hover:shadow-md hover:-translate-y-0.5 hover:border-primary-200 dark:hover:border-primary-700/50' : '',
        ]"
    >
        <div :class="['w-9 h-9 rounded-xl flex-shrink-0 flex items-center justify-center shadow-sm', iconBgClass]">
            <NavIcon :name="icon" :class="['w-4 h-4', iconColorClass]"/>
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-[11px] text-gray-500 dark:text-gray-400 leading-none truncate font-medium">{{ label }}</p>
            <p :class="['text-lg font-black leading-tight mt-0.5 tabular-nums', valueColorClass]">
                {{ typeof value === 'number' ? value.toLocaleString('fr-FR') : value }}
            </p>
        </div>
        <!-- Indicateur animé si alerte -->
        <span v-if="(variant === 'danger' || variant === 'warning') && Number(value) > 0"
            :class="['w-2 h-2 rounded-full flex-shrink-0 animate-pulse', variant === 'danger' ? 'bg-danger-500' : 'bg-warning-500']"/>
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
        bg:    'bg-gradient-to-br from-primary-100 to-primary-50 dark:from-primary-900/40 dark:to-primary-900/20',
        text:  'text-primary-600 dark:text-primary-400',
        value: 'text-gray-900 dark:text-white',
    },
    warning: {
        bg:    'bg-gradient-to-br from-warning-100 to-amber-50 dark:from-warning-900/40 dark:to-warning-900/20',
        text:  'text-warning-600 dark:text-warning-400',
        value: 'text-warning-700 dark:text-warning-400',
    },
    danger: {
        bg:    'bg-gradient-to-br from-danger-100 to-red-50 dark:from-danger-900/40 dark:to-danger-900/20',
        text:  'text-danger-600 dark:text-danger-400',
        value: 'text-danger-700 dark:text-danger-400',
    },
    info: {
        bg:    'bg-gradient-to-br from-blue-100 to-blue-50 dark:from-blue-900/40 dark:to-blue-900/20',
        text:  'text-blue-600 dark:text-blue-400',
        value: 'text-gray-900 dark:text-white',
    },
};

const p              = computed(() => palettes[props.variant ?? 'default']);
const iconBgClass    = computed(() => p.value.bg);
const iconColorClass = computed(() => p.value.text);
const valueColorClass = computed(() => p.value.value);
</script>
