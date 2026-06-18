<template>
    <div class="flex items-center gap-3">
        <div class="flex-1 min-w-0">
            <div class="flex justify-between items-center mb-1">
                <span class="text-xs font-medium text-gray-700 dark:text-gray-300 truncate">{{ label }}</span>
                <span class="text-xs font-bold ml-2 flex-shrink-0" :class="valueClass">{{ displayValue }}</span>
            </div>
            <div class="w-full h-2 rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                <div
                    class="h-2 rounded-full transition-all duration-700"
                    :class="barClass"
                    :style="{ width: clampedPercent + '%' }"
                />
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    label: string;
    value: number;
    max?: number;
    percent?: number;
    color?: 'primary' | 'success' | 'warning' | 'danger' | 'info' | 'violet';
    suffix?: string;
}

const props = withDefaults(defineProps<Props>(), { color: 'primary', max: 100 });

const clampedPercent = computed(() => {
    const p = props.percent ?? (props.max > 0 ? (props.value / props.max) * 100 : 0);
    return Math.min(100, Math.max(0, Math.round(p)));
});

const displayValue = computed(() =>
    (props.percent !== undefined ? clampedPercent.value + '%' : props.value.toLocaleString('fr-FR') + (props.suffix ?? ''))
);

const barMap: Record<string, string> = {
    primary: 'bg-primary-500',
    success: 'bg-emerald-500',
    warning: 'bg-amber-500',
    danger:  'bg-red-500',
    info:    'bg-violet-500',
    violet:  'bg-violet-500',
};
const textMap: Record<string, string> = {
    primary: 'text-primary-600 dark:text-primary-400',
    success: 'text-emerald-600 dark:text-emerald-400',
    warning: 'text-amber-600 dark:text-amber-400',
    danger:  'text-red-600 dark:text-red-400',
    info:    'text-violet-600 dark:text-violet-400',
    violet:  'text-violet-600 dark:text-violet-400',
};

const barClass   = computed(() => barMap[props.color]);
const valueClass = computed(() => textMap[props.color]);
</script>
