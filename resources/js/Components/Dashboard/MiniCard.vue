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
            <p class="text-lg font-black text-gray-900 dark:text-white leading-tight mt-0.5 tabular-nums">
                {{ typeof value === 'number' ? value.toLocaleString('fr-FR') : value }}
            </p>
        </div>
        <svg v-if="href" class="w-3.5 h-3.5 text-gray-300 dark:text-gray-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </component>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import NavIcon from '@/Components/Layout/NavIcon.vue';

const props = defineProps<{
    label:  string;
    value:  number | string;
    icon:   string;
    color?: 'slate' | 'sky' | 'teal' | 'orange' | 'rose' | 'violet' | 'blue';
    href?:  string;
}>();

const palettes: Record<string, { bg: string; text: string }> = {
    slate:  { bg: 'bg-gradient-to-br from-slate-100 to-slate-50 dark:from-slate-800 dark:to-slate-700',    text: 'text-slate-600 dark:text-slate-300' },
    sky:    { bg: 'bg-gradient-to-br from-sky-100 to-sky-50 dark:from-sky-900/40 dark:to-sky-900/20',      text: 'text-sky-600 dark:text-sky-400' },
    teal:   { bg: 'bg-gradient-to-br from-teal-100 to-teal-50 dark:from-teal-900/40 dark:to-teal-900/20', text: 'text-teal-600 dark:text-teal-400' },
    orange: { bg: 'bg-gradient-to-br from-orange-100 to-orange-50 dark:from-orange-900/40 dark:to-orange-900/20', text: 'text-orange-600 dark:text-orange-400' },
    rose:   { bg: 'bg-gradient-to-br from-rose-100 to-rose-50 dark:from-rose-900/40 dark:to-rose-900/20', text: 'text-rose-600 dark:text-rose-400' },
    violet: { bg: 'bg-gradient-to-br from-violet-100 to-violet-50 dark:from-violet-900/40 dark:to-violet-900/20', text: 'text-violet-600 dark:text-violet-400' },
    blue:   { bg: 'bg-gradient-to-br from-blue-100 to-blue-50 dark:from-blue-900/40 dark:to-blue-900/20', text: 'text-blue-600 dark:text-blue-400' },
};

const p              = computed(() => palettes[props.color ?? 'slate']);
const iconBgClass    = computed(() => p.value.bg);
const iconColorClass = computed(() => p.value.text);
</script>
