<template>
    <div :class="[
        'relative overflow-hidden rounded-2xl p-5 bg-white dark:bg-gray-800',
        'border border-gray-100 dark:border-gray-700',
        href ? 'cursor-pointer hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200' : '',
    ]" @click="href ? (window.location.href = href) : null">
        <!-- Background decoration -->
        <div class="absolute -right-6 -top-6 w-24 h-24 rounded-full opacity-[0.06]" :class="bubbleBg"/>

        <div class="relative flex items-start justify-between gap-3">
            <!-- Icon -->
            <div :class="['w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0', iconBg]">
                <svg class="w-5 h-5" :class="iconColor" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="iconPath"/>
                </svg>
            </div>
            <!-- Trend badge -->
            <span v-if="trend"
                :class="[
                    'text-[10px] font-bold px-2 py-0.5 rounded-full flex items-center gap-0.5',
                    trendPositive ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400'
                                  : 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400',
                ]"
            >
                <span>{{ trendPositive ? '↑' : '↓' }}</span>{{ trend }}
            </span>
        </div>

        <div class="mt-3">
            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">{{ label }}</p>
            <p class="text-2xl font-black text-gray-900 dark:text-white mt-0.5 leading-none">
                <span v-if="prefix" class="text-sm font-semibold text-gray-400 mr-1">{{ prefix }}</span>
                {{ typeof value === 'number' ? value.toLocaleString('fr-FR') : value }}
                <span v-if="suffix" class="text-sm font-semibold text-gray-400 ml-1">{{ suffix }}</span>
            </p>
            <p v-if="sub" class="text-[10px] text-gray-400 mt-1">{{ sub }}</p>
        </div>

        <!-- Mini sparkline area (optional inline bar) -->
        <div v-if="sparkline && sparkline.length > 1" class="mt-3 h-8 relative">
            <svg viewBox="0 0 100 32" preserveAspectRatio="none" class="w-full h-full">
                <polyline
                    :points="sparkPoints"
                    fill="none"
                    :stroke="sparkColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    opacity="0.7"
                />
            </svg>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    label:    string;
    value:    number | string;
    icon?:    string; // SVG path d=
    color?:   'primary' | 'success' | 'warning' | 'danger' | 'info' | 'violet' | 'amber';
    trend?:   string;
    trendPositive?: boolean;
    href?:    string;
    prefix?:  string;
    suffix?:  string;
    sub?:     string;
    sparkline?: number[];
}

const props = withDefaults(defineProps<Props>(), {
    color: 'primary',
    trendPositive: true,
    icon: 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6', // trending up
});

const colorMap: Record<string, { bubble: string; iconBg: string; icon: string; spark: string }> = {
    primary: { bubble: 'bg-primary-500',   iconBg: 'bg-primary-50 dark:bg-primary-900/30',   icon: 'text-primary-600 dark:text-primary-400',   spark: '#7C3AED' },
    success: { bubble: 'bg-emerald-500',   iconBg: 'bg-emerald-50 dark:bg-emerald-900/30',   icon: 'text-emerald-600 dark:text-emerald-400',   spark: '#10B981' },
    warning: { bubble: 'bg-amber-500',     iconBg: 'bg-amber-50 dark:bg-amber-900/30',       icon: 'text-amber-600 dark:text-amber-400',       spark: '#F59E0B' },
    danger:  { bubble: 'bg-red-500',       iconBg: 'bg-red-50 dark:bg-red-900/30',           icon: 'text-red-600 dark:text-red-400',           spark: '#EF4444' },
    info:    { bubble: 'bg-violet-500',      iconBg: 'bg-violet-50 dark:bg-violet-900/30',         icon: 'text-violet-600 dark:text-violet-400',         spark: '#8B5CF6' },
    violet:  { bubble: 'bg-violet-500',    iconBg: 'bg-violet-50 dark:bg-violet-900/30',     icon: 'text-violet-600 dark:text-violet-400',     spark: '#8B5CF6' },
    amber:   { bubble: 'bg-orange-500',    iconBg: 'bg-orange-50 dark:bg-orange-900/30',     icon: 'text-orange-600 dark:text-orange-400',     spark: '#F97316' },
};

const c = computed(() => colorMap[props.color]);
const bubbleBg   = computed(() => c.value.bubble);
const iconBg     = computed(() => c.value.iconBg);
const iconColor  = computed(() => c.value.icon);
const sparkColor = computed(() => c.value.spark);
const iconPath   = computed(() => props.icon);

const sparkPoints = computed(() => {
    if (!props.sparkline || props.sparkline.length < 2) return '';
    const data = props.sparkline;
    const min  = Math.min(...data);
    const max  = Math.max(...data);
    const range = max - min || 1;
    const step  = 100 / (data.length - 1);
    return data.map((v, i) => `${i * step},${32 - ((v - min) / range) * 28}`).join(' ');
});

declare const window: Window;
</script>
