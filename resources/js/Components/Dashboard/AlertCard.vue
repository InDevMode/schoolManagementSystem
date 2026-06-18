<template>
    <component
        :is="href ? 'a' : 'div'"
        :href="href"
        :class="[
            'card px-3 py-2.5 flex items-center gap-2.5 transition-all duration-200',
            href ? 'cursor-pointer hover:shadow-md hover:-translate-y-0.5' : '',
        ]"
    >
        <div :class="['w-8 h-8 rounded-lg flex-shrink-0 flex items-center justify-center', iconBgClass]">
            <NavIcon :name="icon" :class="['w-4 h-4', iconColorClass]"/>
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-[10px] text-gray-500 dark:text-gray-400 leading-none truncate">{{ label }}</p>
            <p :class="['text-lg font-black leading-tight mt-0.5', valueColorClass]">
                {{ typeof value === 'number' ? value.toLocaleString('fr-FR') : value }}
            </p>
        </div>
        <!-- Badge alerte si valeur > 0 et variant danger/warning -->
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
    default: { bg: 'bg-primary-100 dark:bg-primary-900/30',  text: 'text-primary-600 dark:text-primary-400', value: 'text-gray-900 dark:text-white' },
    warning: { bg: 'bg-warning-100 dark:bg-warning-900/30',  text: 'text-warning-600 dark:text-warning-400', value: 'text-warning-700 dark:text-warning-400' },
    danger:  { bg: 'bg-danger-100 dark:bg-danger-900/30',    text: 'text-danger-600 dark:text-danger-400',   value: 'text-danger-700 dark:text-danger-400' },
    info:    { bg: 'bg-info-100 dark:bg-info-900/30',        text: 'text-info-600 dark:text-info-400',       value: 'text-gray-900 dark:text-white' },
};

const p             = computed(() => palettes[props.variant ?? 'default']);
const iconBgClass   = computed(() => p.value.bg);
const iconColorClass = computed(() => p.value.text);
const valueColorClass = computed(() => p.value.value);
</script>
