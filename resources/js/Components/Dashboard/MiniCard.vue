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

        <!-- Icône -->
        <div class="relative mb-2">
            <div :class="['w-10 h-10 rounded-xl flex items-center justify-center shadow-md', iconContainerBg]">
                <NavIcon :name="icon" class="w-4 h-4 text-white drop-shadow"/>
            </div>
        </div>

        <!-- Valeur + label -->
        <p class="relative text-xl font-black text-gray-900 dark:text-white leading-none tabular-nums">
            {{ typeof value === 'number' ? value.toLocaleString('fr-FR') : value }}
        </p>
        <p class="relative text-xs text-gray-500 dark:text-gray-400 mt-1 font-medium truncate">{{ label }}</p>

        <!-- Flèche discrète -->
        <svg v-if="href" class="absolute bottom-3 right-3 w-3.5 h-3.5 text-gray-300 dark:text-gray-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
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

const colorMap: Record<string, { gradient: string; bubble: string }> = {
    slate:  { gradient: 'bg-gradient-to-br from-slate-400 to-slate-600',   bubble: 'bg-slate-400' },
    sky:    { gradient: 'bg-gradient-to-br from-sky-400 to-sky-600',       bubble: 'bg-sky-400' },
    teal:   { gradient: 'bg-gradient-to-br from-teal-400 to-teal-600',     bubble: 'bg-teal-400' },
    orange: { gradient: 'bg-gradient-to-br from-orange-400 to-orange-600', bubble: 'bg-orange-400' },
    rose:   { gradient: 'bg-gradient-to-br from-rose-400 to-rose-600',     bubble: 'bg-rose-400' },
    violet: { gradient: 'bg-gradient-to-br from-violet-400 to-violet-600', bubble: 'bg-violet-400' },
    blue:   { gradient: 'bg-gradient-to-br from-blue-400 to-blue-600',     bubble: 'bg-blue-400' },
};

const p               = computed(() => colorMap[props.color ?? 'slate']);
const iconContainerBg = computed(() => p.value.gradient);
const bubbleBg        = computed(() => p.value.bubble);
</script>
