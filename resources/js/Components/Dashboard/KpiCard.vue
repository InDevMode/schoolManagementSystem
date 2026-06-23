<template>
    <component
        :is="href ? 'a' : 'div'"
        :href="href"
        :class="[
            'relative overflow-hidden rounded-2xl p-4 bg-white dark:bg-gray-800',
            'border border-gray-100 dark:border-gray-700/60 shadow-sm',
            'transition-all duration-300 ease-out',
            href ? 'cursor-pointer hover:shadow-lg hover:-translate-y-1 hover:border-opacity-60' : '',
        ]"
    >
        <!-- Fond coloré pastel en haut à droite (déco) -->
        <div
            :class="['absolute -top-4 -right-4 w-24 h-24 rounded-full opacity-10 dark:opacity-5 pointer-events-none', bubbleBg]"
        />

        <!-- Ligne supérieure : icône 3D + trend -->
        <div class="relative flex items-start justify-between mb-3">
            <!-- Icône avec fond dégradé coloré -->
            <div :class="['w-12 h-12 rounded-xl flex items-center justify-center shadow-md flex-shrink-0', iconContainerBg]">
                <svg class="w-6 h-6 text-white drop-shadow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" :d="iconPath"/>
                </svg>
            </div>

            <!-- Trend badge -->
            <span v-if="trend"
                :class="[
                    'text-[10px] font-bold px-2 py-0.5 rounded-full flex items-center gap-0.5',
                    trendPositive
                        ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400'
                        : 'bg-red-50 dark:bg-red-900/30 text-red-500 dark:text-red-400',
                ]"
            >
                <svg class="w-2.5 h-2.5" :class="trendPositive ? '' : 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                </svg>
                {{ trend }}
            </span>
        </div>

        <!-- Valeur principale -->
        <div class="relative">
            <p class="text-2xl font-black text-gray-900 dark:text-white leading-none tabular-nums">
                <span v-if="prefix" class="text-sm font-semibold text-gray-400 mr-0.5">{{ prefix }}</span>
                {{ typeof value === 'number' ? value.toLocaleString('fr-FR') : value }}
                <span v-if="suffix" class="text-sm font-semibold text-gray-400 ml-0.5">{{ suffix }}</span>
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 font-medium truncate">{{ label }}</p>

            <!-- Gender breakdown -->
            <div v-if="genderMale !== undefined && genderFemale !== undefined" class="flex items-center gap-2 mt-1.5">
                <span class="inline-flex items-center gap-0.5 text-[10px] font-semibold text-blue-600 dark:text-blue-400">
                    <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2a4 4 0 100 8 4 4 0 000-8zm-2 10a5 5 0 00-5 5v1h14v-1a5 5 0 00-5-5h-4z"/>
                    </svg>
                    {{ genderMale }}H
                </span>
                <span class="text-[9px] text-gray-300 dark:text-gray-600">·</span>
                <span class="inline-flex items-center gap-0.5 text-[10px] font-semibold text-pink-500 dark:text-pink-400">
                    <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2a4 4 0 100 8 4 4 0 000-8zm-2 10a5 5 0 00-5 5v1h14v-1a5 5 0 00-5-5h-4z"/>
                    </svg>
                    {{ genderFemale }}F
                </span>
            </div>
            <p v-else-if="sub" class="text-[10px] text-gray-400 mt-0.5 leading-tight">{{ sub }}</p>
        </div>

        <!-- Mini sparkline -->
        <div v-if="sparkline && sparkline.length > 1" class="mt-2 h-8 relative">
            <svg viewBox="0 0 100 28" preserveAspectRatio="none" class="w-full h-full">
                <polyline
                    :points="sparkPoints"
                    fill="none"
                    :stroke="sparkColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    opacity="0.5"
                />
            </svg>
        </div>
    </component>
</template>

<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    label:    string;
    value:    number | string;
    icon?:    string;
    color?:   'primary' | 'success' | 'warning' | 'danger' | 'info' | 'violet' | 'amber';
    trend?:   string;
    trendPositive?: boolean;
    href?:    string;
    prefix?:  string;
    suffix?:  string;
    sub?:     string;
    sparkline?: number[];
    genderMale?: number;
    genderFemale?: number;
}

const props = withDefaults(defineProps<Props>(), {
    color: 'primary',
    trendPositive: true,
    icon: 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6',
});

// Dégradés colorés pour l'icône (style "3D/isométrique")
const colorMap: Record<string, { gradient: string; bubble: string; spark: string }> = {
    primary: {
        gradient: 'bg-gradient-to-br from-primary-400 to-primary-600',
        bubble:   'bg-primary-400',
        spark:    '#7C3AED',
    },
    success: {
        gradient: 'bg-gradient-to-br from-emerald-400 to-emerald-600',
        bubble:   'bg-emerald-400',
        spark:    '#10B981',
    },
    warning: {
        gradient: 'bg-gradient-to-br from-amber-400 to-orange-500',
        bubble:   'bg-amber-400',
        spark:    '#F59E0B',
    },
    danger: {
        gradient: 'bg-gradient-to-br from-red-400 to-red-600',
        bubble:   'bg-red-400',
        spark:    '#EF4444',
    },
    info: {
        gradient: 'bg-gradient-to-br from-blue-400 to-blue-600',
        bubble:   'bg-blue-400',
        spark:    '#3B82F6',
    },
    violet: {
        gradient: 'bg-gradient-to-br from-violet-400 to-violet-600',
        bubble:   'bg-violet-400',
        spark:    '#8B5CF6',
    },
    amber: {
        gradient: 'bg-gradient-to-br from-orange-400 to-orange-600',
        bubble:   'bg-orange-400',
        spark:    '#F97316',
    },
};

const c               = computed(() => colorMap[props.color]);
const iconContainerBg = computed(() => c.value.gradient);
const bubbleBg        = computed(() => c.value.bubble);
const sparkColor      = computed(() => c.value.spark);
const iconPath        = computed(() => props.icon);

const sparkPoints = computed(() => {
    if (!props.sparkline || props.sparkline.length < 2) return '';
    const data  = props.sparkline;
    const min   = Math.min(...data);
    const max   = Math.max(...data);
    const range = max - min || 1;
    const step  = 100 / (data.length - 1);
    return data.map((v, i) => `${i * step},${28 - ((v - min) / range) * 24}`).join(' ');
});
</script>
