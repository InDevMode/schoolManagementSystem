<template>
    <div
        :class="[
            'relative overflow-hidden rounded-2xl p-5',
            'bg-white dark:bg-gray-800',
            'border border-gray-100 dark:border-gray-700/60',
            'shadow-sm',
            href ? 'cursor-pointer transition-all duration-300 ease-out hover:shadow-xl hover:-translate-y-1 hover:border-primary-200 dark:hover:border-primary-700/50' : '',
        ]"
        @click="href ? (window.location.href = href) : null"
    >
        <!-- Decoration blob en fond -->
        <div class="absolute -right-6 -top-6 w-24 h-24 rounded-full blur-2xl opacity-20 pointer-events-none"
             :class="bubbleBg"/>
        <div class="absolute -left-3 -bottom-3 w-14 h-14 rounded-full blur-xl opacity-10 pointer-events-none"
             :class="bubbleBg"/>

        <!-- Trend badge — ancré en haut à droite de la card -->
        <span v-if="trend"
            :class="[
                'absolute top-3 right-3 z-10',
                'text-[11px] font-bold px-2.5 py-1 rounded-full flex items-center gap-1',
                trendPositive
                    ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 ring-1 ring-emerald-200 dark:ring-emerald-700/50'
                    : 'bg-red-50 dark:bg-red-900/30 text-red-500 dark:text-red-400 ring-1 ring-red-200 dark:ring-red-700/50',
            ]"
        >
            <svg class="w-2.5 h-2.5 flex-shrink-0" :class="trendPositive ? 'rotate-0' : 'rotate-180'"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
            </svg>
            {{ trend }}
        </span>

        <!-- Ligne icône + label -->
        <div class="relative flex items-center gap-3">
            <div :class="['w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm', iconBg]">
                <svg class="w-5 h-5" :class="iconColor" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" :d="iconPath"/>
                </svg>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium tracking-wide leading-tight pr-12">{{ label }}</p>
        </div>

        <!-- Valeur principale -->
        <div class="relative mt-3">
            <p class="text-3xl font-black text-gray-900 dark:text-white leading-none tabular-nums">
                <span v-if="prefix" class="text-base font-semibold text-gray-400 mr-1">{{ prefix }}</span>
                {{ typeof value === 'number' ? value.toLocaleString('fr-FR') : value }}
                <span v-if="suffix" class="text-base font-semibold text-gray-400 ml-1">{{ suffix }}</span>
            </p>

            <!-- Gender breakdown -->
            <div v-if="genderMale !== undefined && genderFemale !== undefined"
                 class="flex items-center gap-2 mt-2">
                <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-blue-600 dark:text-blue-400">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2a4 4 0 100 8 4 4 0 000-8zm-2 10a5 5 0 00-5 5v1h14v-1a5 5 0 00-5-5h-4z"/></svg>
                    {{ genderMale }}H
                </span>
                <span class="text-[10px] text-gray-300 dark:text-gray-600">·</span>
                <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-pink-500 dark:text-pink-400">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2a4 4 0 100 8 4 4 0 000-8zm-2 10a5 5 0 00-5 5v1h14v-1a5 5 0 00-5-5h-4z"/></svg>
                    {{ genderFemale }}F
                </span>
            </div>
            <p v-else-if="sub" class="text-[11px] text-gray-400 mt-1.5 leading-tight">{{ sub }}</p>
        </div>

        <!-- Mini sparkline -->
        <div v-if="sparkline && sparkline.length > 1" class="mt-3 h-9 relative">
            <svg viewBox="0 0 100 32" preserveAspectRatio="none" class="w-full h-full">
                <polyline
                    :points="sparkPoints"
                    fill="none"
                    :stroke="sparkColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    opacity="0.6"
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

const colorMap: Record<string, { bubble: string; iconBg: string; icon: string; spark: string }> = {
    primary: { bubble: 'bg-primary-400',   iconBg: 'bg-gradient-to-br from-primary-100 to-primary-50 dark:from-primary-900/40 dark:to-primary-900/20',   icon: 'text-primary-600 dark:text-primary-400',   spark: '#7C3AED' },
    success: { bubble: 'bg-emerald-400',   iconBg: 'bg-gradient-to-br from-emerald-100 to-emerald-50 dark:from-emerald-900/40 dark:to-emerald-900/20',   icon: 'text-emerald-600 dark:text-emerald-400',   spark: '#10B981' },
    warning: { bubble: 'bg-amber-400',     iconBg: 'bg-gradient-to-br from-amber-100 to-amber-50 dark:from-amber-900/40 dark:to-amber-900/20',           icon: 'text-amber-600 dark:text-amber-400',       spark: '#F59E0B' },
    danger:  { bubble: 'bg-red-400',       iconBg: 'bg-gradient-to-br from-red-100 to-red-50 dark:from-red-900/40 dark:to-red-900/20',                   icon: 'text-red-600 dark:text-red-400',           spark: '#EF4444' },
    info:    { bubble: 'bg-blue-400',      iconBg: 'bg-gradient-to-br from-blue-100 to-blue-50 dark:from-blue-900/40 dark:to-blue-900/20',               icon: 'text-blue-600 dark:text-blue-400',         spark: '#3B82F6' },
    violet:  { bubble: 'bg-violet-400',    iconBg: 'bg-gradient-to-br from-violet-100 to-violet-50 dark:from-violet-900/40 dark:to-violet-900/20',       icon: 'text-violet-600 dark:text-violet-400',     spark: '#8B5CF6' },
    amber:   { bubble: 'bg-orange-400',    iconBg: 'bg-gradient-to-br from-orange-100 to-orange-50 dark:from-orange-900/40 dark:to-orange-900/20',       icon: 'text-orange-600 dark:text-orange-400',     spark: '#F97316' },
};

const c          = computed(() => colorMap[props.color]);
const bubbleBg   = computed(() => c.value.bubble);
const iconBg     = computed(() => c.value.iconBg);
const iconColor  = computed(() => c.value.icon);
const sparkColor = computed(() => c.value.spark);
const iconPath   = computed(() => props.icon);

const sparkPoints = computed(() => {
    if (!props.sparkline || props.sparkline.length < 2) return '';
    const data  = props.sparkline;
    const min   = Math.min(...data);
    const max   = Math.max(...data);
    const range = max - min || 1;
    const step  = 100 / (data.length - 1);
    return data.map((v, i) => `${i * step},${32 - ((v - min) / range) * 28}`).join(' ');
});

declare const window: Window;
</script>
