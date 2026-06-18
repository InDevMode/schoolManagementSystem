<template>
    <component
        :is="href ? 'a' : 'div'"
        :href="href"
        :class="[
            'relative overflow-hidden rounded-2xl p-5 flex items-center gap-4 transition-all duration-200',
            href ? 'hover:shadow-lg hover:-translate-y-0.5 cursor-pointer' : '',
            cardBg,
        ]"
    >
        <!-- Décorations de fond -->
        <div class="absolute -right-8 -top-8 w-32 h-32 rounded-full opacity-10" :class="bubbleBg"/>
        <div class="absolute right-4 -bottom-8 w-20 h-20 rounded-full opacity-10" :class="bubbleBg"/>

        <!-- Icône -->
        <div :class="['relative z-10 w-14 h-14 rounded-2xl flex items-center justify-center flex-shrink-0 shadow-md', iconBg]">
            <NavIcon :name="icon" :class="['w-7 h-7', iconColor]"/>
        </div>

        <!-- Texte -->
        <div class="relative z-10 flex-1 min-w-0">
            <p class="text-sm font-medium opacity-80" :class="textColor">{{ label }}</p>
            <p class="text-3xl font-bold mt-0.5" :class="textColor">
                <span v-if="prefix" class="text-base font-semibold mr-1 opacity-70">{{ prefix }}</span>
                {{ (value ?? 0).toLocaleString('fr-FR') }}
            </p>
            <!-- Trend badge -->
            <div v-if="trend" class="flex items-center gap-1 mt-1.5">
                <span class="inline-flex items-center gap-0.5 text-xs font-semibold px-2 py-0.5 rounded-full"
                      :class="trendBg">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                    </svg>
                    {{ trend }}
                </span>
                <span class="text-xs opacity-60" :class="textColor">ce mois</span>
            </div>
        </div>

        <!-- Flèche -->
        <svg v-if="href" class="relative z-10 w-5 h-5 flex-shrink-0 opacity-50" :class="textColor"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </component>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import NavIcon from '@/Components/Layout/NavIcon.vue';

interface Props {
    label: string;
    value?: number | null;
    icon: string;
    color?: 'primary' | 'secondary' | 'success' | 'danger' | 'warning' | 'info';
    href?: string;
    trend?: string;
    prefix?: string;
}

const props = withDefaults(defineProps<Props>(), { color: 'primary', value: 0 });

const colorMap: Record<string, {
    card: string; bubble: string; iconBg: string; icon: string; text: string; trend: string;
}> = {
    primary: {
        card:   'bg-gradient-to-br from-primary-500 to-primary-700',
        bubble: 'bg-white',
        iconBg: 'bg-white/20',
        icon:   'text-white',
        text:   'text-white',
        trend:  'bg-white/20 text-white',
    },
    info: {
        card:   'bg-gradient-to-br from-violet-400 to-violet-600',
        bubble: 'bg-white',
        iconBg: 'bg-white/20',
        icon:   'text-white',
        text:   'text-white',
        trend:  'bg-white/20 text-white',
    },
    warning: {
        card:   'bg-gradient-to-br from-amber-400 to-orange-500',
        bubble: 'bg-white',
        iconBg: 'bg-white/20',
        icon:   'text-white',
        text:   'text-white',
        trend:  'bg-white/20 text-white',
    },
    success: {
        card:   'bg-gradient-to-br from-emerald-400 to-green-600',
        bubble: 'bg-white',
        iconBg: 'bg-white/20',
        icon:   'text-white',
        text:   'text-white',
        trend:  'bg-white/20 text-white',
    },
    secondary: {
        card:   'bg-gradient-to-br from-primary-400 to-primary-600',
        bubble: 'bg-white',
        iconBg: 'bg-white/20',
        icon:   'text-white',
        text:   'text-white',
        trend:  'bg-white/20 text-white',
    },
    danger: {
        card:   'bg-gradient-to-br from-red-400 to-red-600',
        bubble: 'bg-white',
        iconBg: 'bg-white/20',
        icon:   'text-white',
        text:   'text-white',
        trend:  'bg-white/20 text-white',
    },
};

const c         = computed(() => colorMap[props.color]);
const cardBg    = computed(() => c.value.card);
const bubbleBg  = computed(() => c.value.bubble);
const iconBg    = computed(() => c.value.iconBg);
const iconColor = computed(() => c.value.icon);
const textColor = computed(() => c.value.text);
const trendBg   = computed(() => c.value.trend);
</script>
