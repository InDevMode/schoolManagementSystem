<template>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div
                class="w-12 h-12 rounded-2xl flex items-center justify-center shadow-lg flex-shrink-0"
                :class="gradientClass"
            >
                <slot name="icon">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </slot>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ title }}</h1>
                <p v-if="subtitle" class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ subtitle }}</p>
            </div>
        </div>
        <div v-if="$slots.actions" class="flex items-center gap-2 flex-wrap">
            <slot name="actions"/>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';

const props = withDefaults(defineProps<{
    title: string;
    subtitle?: string;
    /** Color theme: primary | emerald | violet | amber | red | cyan | indigo */
    color?: string;
}>(), {
    color: 'primary',
});

const gradientClass = computed(() => {
    const map: Record<string, string> = {
        primary: 'bg-gradient-to-br from-primary-500 to-primary-700 shadow-primary-500/30',
        emerald: 'bg-gradient-to-br from-emerald-500 to-teal-600 shadow-emerald-500/30',
        violet:  'bg-gradient-to-br from-violet-500 to-purple-700 shadow-violet-500/30',
        amber:   'bg-gradient-to-br from-amber-500 to-orange-600 shadow-amber-500/30',
        red:     'bg-gradient-to-br from-red-500 to-rose-700 shadow-red-500/30',
        cyan:    'bg-gradient-to-br from-cyan-500 to-sky-600 shadow-cyan-500/30',
        indigo:  'bg-gradient-to-br from-indigo-500 to-blue-700 shadow-indigo-500/30',
        blue:    'bg-gradient-to-br from-blue-500 to-indigo-600 shadow-blue-500/30',
    };
    return map[props.color] ?? map['primary'];
});
</script>
