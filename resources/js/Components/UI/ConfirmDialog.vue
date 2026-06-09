<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-150 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-100 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="modelValue" class="fixed inset-0 z-[9998] flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="$emit('update:modelValue', false); $emit('cancel')" />
                <Transition
                    enter-active-class="transition duration-150 ease-out"
                    enter-from-class="opacity-0 scale-95"
                    enter-to-class="opacity-100 scale-100"
                >
                    <div v-if="modelValue" class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-sm p-6">
                        <!-- Icône -->
                        <div :class="['w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-4', iconBg]">
                            <svg class="w-6 h-6" :class="iconColor" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>

                        <!-- Titre -->
                        <h3 class="text-base font-bold text-gray-900 dark:text-white text-center mb-2">{{ title }}</h3>

                        <!-- Message -->
                        <p class="text-sm text-gray-500 dark:text-gray-400 text-center mb-6">{{ message }}</p>

                        <!-- Boutons -->
                        <div class="flex items-center gap-3">
                            <button
                                class="flex-1 px-4 py-2.5 text-sm font-medium rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                                @click="$emit('update:modelValue', false); $emit('cancel')"
                            >
                                {{ cancelLabel }}
                            </button>
                            <button
                                :class="['flex-1 px-4 py-2.5 text-sm font-medium rounded-xl text-white transition-colors', confirmBg]"
                                @click="$emit('update:modelValue', false); $emit('confirm')"
                            >
                                {{ confirmLabel }}
                            </button>
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
    message?: string;
    confirmLabel?: string;
    cancelLabel?: string;
    variant?: 'danger' | 'warning' | 'info';
}

const props = withDefaults(defineProps<Props>(), {
    title:        'Confirmer l\'action',
    message:      'Êtes-vous sûr de vouloir effectuer cette action ?',
    confirmLabel: 'Confirmer',
    cancelLabel:  'Annuler',
    variant:      'danger',
});

defineEmits<{ confirm: []; cancel: []; 'update:modelValue': [value: boolean] }>();

const iconBg    = computed(() => ({ danger: 'bg-danger-100 dark:bg-danger-900/30', warning: 'bg-warning-100 dark:bg-warning-900/30', info: 'bg-info-100 dark:bg-info-900/30' }[props.variant]));
const iconColor = computed(() => ({ danger: 'text-danger-600', warning: 'text-warning-600', info: 'text-info-600' }[props.variant]));
const confirmBg = computed(() => ({ danger: 'bg-danger-600 hover:bg-danger-700', warning: 'bg-warning-600 hover:bg-warning-700', info: 'bg-info-600 hover:bg-info-700' }[props.variant]));
</script>
