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
        <div class="min-w-0">
            <p class="text-[10px] text-gray-500 dark:text-gray-400 leading-none truncate">{{ label }}</p>
            <p class="text-lg font-black text-gray-900 dark:text-white leading-tight mt-0.5">
                {{ typeof value === 'number' ? value.toLocaleString('fr-FR') : value }}
            </p>
        </div>
        <svg v-if="href" class="w-3.5 h-3.5 text-gray-300 dark:text-gray-600 flex-shrink-0 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
    slate:  { bg: 'bg-slate-100 dark:bg-slate-800',   text: 'text-slate-600 dark:text-slate-400' },
    sky:    { bg: 'bg-sky-100 dark:bg-sky-900/30',    text: 'text-sky-600 dark:text-sky-400' },
    teal:   { bg: 'bg-teal-100 dark:bg-teal-900/30',  text: 'text-teal-600 dark:text-teal-400' },
    orange: { bg: 'bg-orange-100 dark:bg-orange-900/30', text: 'text-orange-600 dark:text-orange-400' },
    rose:   { bg: 'bg-rose-100 dark:bg-rose-900/30',  text: 'text-rose-600 dark:text-rose-400' },
    violet: { bg: 'bg-violet-100 dark:bg-violet-900/30', text: 'text-violet-600 dark:text-violet-400' },
    blue:   { bg: 'bg-blue-100 dark:bg-blue-900/30',  text: 'text-blue-600 dark:text-blue-400' },
};

const p            = computed(() => palettes[props.color ?? 'slate']);
const iconBgClass  = computed(() => p.value.bg);
const iconColorClass = computed(() => p.value.text);
</script>
