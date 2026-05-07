<template>
    <div :class="['rounded-full overflow-hidden flex-shrink-0 bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center', sizeClass]">
        <img
            v-if="src"
            :src="src"
            :alt="name"
            class="w-full h-full object-cover"
            @error="imgError = true"
        />
        <span v-else class="font-semibold text-primary-600 dark:text-primary-400 uppercase" :class="textSize">
            {{ initials }}
        </span>
    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';

interface Props {
    src?: string | null;
    name?: string;
    lastName?: string;
    size?: 'xs' | 'sm' | 'md' | 'lg' | 'xl';
}

const props = withDefaults(defineProps<Props>(), { size: 'md' });

const imgError = ref(false);

const sizeClass = computed(() => ({
    xs: 'w-6 h-6',
    sm: 'w-8 h-8',
    md: 'w-10 h-10',
    lg: 'w-12 h-12',
    xl: 'w-16 h-16',
}[props.size]));

const textSize = computed(() => ({
    xs: 'text-[10px]', sm: 'text-xs', md: 'text-sm', lg: 'text-base', xl: 'text-lg',
}[props.size]));

const initials = computed(() => {
    const f = props.name?.[0] ?? '';
    const l = props.lastName?.[0] ?? '';
    return (f + l).toUpperCase() || '?';
});
</script>
