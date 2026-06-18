<template>
    <Teleport to="body">
        <!-- Backdrop séparé avec fondu -->
        <Transition
            enter-active-class="transition duration-400 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-300 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="modelValue" class="fixed inset-0 z-50 flex items-center justify-center p-4" @mousedown.self="closeOnBackdrop">
                <!-- Backdrop -->
                <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" />

                <!-- Panel — animation "ouverture en œil" : scale scaleY part de 0.05 → 1 avec opacité -->
                <Transition
                    enter-active-class="transition-[opacity,transform] duration-500 ease-[cubic-bezier(0.34,1.56,0.64,1)]"
                    enter-from-class="opacity-0 scale-x-100 scale-y-[0.04]"
                    enter-to-class="opacity-100 scale-x-100 scale-y-100"
                    leave-active-class="transition-[opacity,transform] duration-300 ease-in"
                    leave-from-class="opacity-100 scale-x-100 scale-y-100"
                    leave-to-class="opacity-0 scale-x-100 scale-y-[0.04]"
                >
                    <div
                        v-if="modelValue"
                        :class="['relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full flex flex-col max-h-[90vh] origin-center', sizeClass]"
                        role="dialog"
                        :aria-label="title"
                    >
                        <!-- Header -->
                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex-shrink-0">
                            <div>
                                <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ title }}</h3>
                                <p v-if="subtitle" class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ subtitle }}</p>
                            </div>
                            <button
                                type="button"
                                class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                @click="$emit('update:modelValue', false)"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Body -->
                        <div class="flex-1 overflow-y-auto px-6 py-5">
                            <slot />
                        </div>

                        <!-- Footer -->
                        <div v-if="$slots.footer" class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 flex-shrink-0 flex items-center justify-end gap-3">
                            <slot name="footer" />
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    modelValue: boolean;
    title?: string;
    subtitle?: string;
    size?: 'sm' | 'md' | 'lg' | 'xl' | '2xl' | 'full';
    persistent?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    title: '',
    size: 'md',
    persistent: false,
});

const emit = defineEmits<{ 'update:modelValue': [value: boolean] }>();

const sizeClass = computed(() => ({
    sm:   'max-w-sm',
    md:   'max-w-lg',
    lg:   'max-w-2xl',
    xl:   'max-w-4xl',
    '2xl':'max-w-6xl',
    full: 'max-w-full mx-4',
}[props.size]));

const closeOnBackdrop = () => {
    if (!props.persistent) emit('update:modelValue', false);
};
</script>
