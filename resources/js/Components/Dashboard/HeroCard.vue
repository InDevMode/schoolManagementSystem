<template>
    <component
        :is="href ? 'a' : 'div'"
        :href="href"
        :class="[
            'relative overflow-hidden rounded-lg p-4 flex flex-col justify-between min-h-[90px] transition-all duration-200 group',
            href ? 'cursor-pointer hover:shadow-lg hover:-translate-y-0.5' : '',
            bgClass,
        ]"
    >
        <!-- Fond décoratif -->
        <div class="absolute inset-0 opacity-20 pointer-events-none overflow-hidden rounded-lg">
            <div class="absolute -right-6 -top-6 w-28 h-28 rounded-full" :class="circleClass"/>
            <div class="absolute -right-2 -bottom-8 w-20 h-20 rounded-full" :class="circleClass"/>
        </div>

        <!-- Ligne haut : icône + flèche -->
        <div class="relative flex items-start justify-between">
            <div :class="['w-8 h-8 rounded-lg flex items-center justify-center', iconBgClass]">
                <NavIcon :name="icon" class="w-4 h-4 text-white"/>
            </div>
            <svg v-if="href" class="w-4 h-4 text-white/50 group-hover:text-white/90 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </div>

        <!-- Bas : valeur + label + trend -->
        <div class="relative mt-3">
            <div class="flex items-baseline gap-1">
                <span v-if="prefix" class="text-[10px] text-white/70 font-medium">{{ prefix }}</span>
                <span class="text-2xl font-black text-white leading-none">
                    {{ typeof value === 'number' ? value.toLocaleString('fr-FR') : value }}
                </span>
            </div>
            <div class="flex items-center justify-between mt-0.5">
                <p class="text-[11px] text-white/75 font-medium">{{ label }}</p>
                <span v-if="trend" class="text-[10px] font-bold text-white/80 bg-white/15 px-1.5 py-0.5 rounded-full">
                    {{ trend }} ce mois
                </span>
            </div>
        </div>
    </component>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import NavIcon from '@/Components/Layout/NavIcon.vue';

const props = defineProps<{
    label:   string;
    value:   number | string;
    icon:    string;
    color:   'violet' | 'blue' | 'amber' | 'green' | 'rose' | 'teal';
    trend?:  string;
    href?:   string;
    prefix?: string;
}>();

const palettes = {
    violet: { bg: 'bg-gradient-to-br from-violet-500 to-purple-700',  circle: 'bg-white', iconBg: 'bg-white/20' },
    blue:   { bg: 'bg-gradient-to-br from-violet-500 to-purple-700',  circle: 'bg-white', iconBg: 'bg-white/20' },
    amber:  { bg: 'bg-gradient-to-br from-amber-400 to-orange-600',   circle: 'bg-white', iconBg: 'bg-white/20' },
    green:  { bg: 'bg-gradient-to-br from-emerald-500 to-teal-700',   circle: 'bg-white', iconBg: 'bg-white/20' },
    rose:   { bg: 'bg-gradient-to-br from-rose-500 to-pink-700',      circle: 'bg-white', iconBg: 'bg-white/20' },
    teal:   { bg: 'bg-gradient-to-br from-teal-500 to-cyan-700',      circle: 'bg-white', iconBg: 'bg-white/20' },
};

const p         = computed(() => palettes[props.color] ?? palettes.violet);
const bgClass   = computed(() => p.value.bg);
const circleClass = computed(() => p.value.circle);
const iconBgClass = computed(() => p.value.iconBg);
</script>
