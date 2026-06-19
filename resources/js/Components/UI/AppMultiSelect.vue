<template>
    <div ref="containerRef" class="relative w-full">
        <label v-if="label" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
            {{ label }}
            <span v-if="required" class="text-danger-500 ml-0.5">*</span>
        </label>

        <!-- Trigger -->
        <div
            :class="[
                'min-h-[42px] w-full rounded-xl border bg-white dark:bg-gray-800 px-3 py-2 cursor-pointer flex flex-wrap gap-1.5 items-center transition-all duration-200',
                isOpen ? 'ring-2 ring-primary-500 border-transparent' : 'border-gray-300 dark:border-gray-600',
                error ? 'border-danger-500' : '',
            ]"
            @click="isOpen = !isOpen"
        >
            <template v-if="selected.length">
                <span
                    v-for="item in selected"
                    :key="item.value"
                    class="inline-flex items-center gap-1 bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 text-xs font-medium px-2 py-0.5 rounded-full"
                >
                    {{ item.label }}
                    <button type="button" class="hover:text-primary-900 dark:hover:text-primary-100" @click.stop="remove(item.value)">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </span>
            </template>
            <span v-else class="text-sm text-gray-400 dark:text-gray-500">{{ placeholder }}</span>
            <div class="ml-auto text-gray-400">
                <svg :class="['w-4 h-4 transition-transform', isOpen ? 'rotate-180' : '']" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
        </div>

        <!-- Dropdown -->
        <Transition enter-active-class="animate-slide-down" leave-active-class="transition duration-100 ease-in" leave-to-class="opacity-0 scale-95">
            <div v-if="isOpen" class="absolute z-50 mt-1 w-full bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-card-lg overflow-hidden">
                <!-- Search -->
                <div class="p-2 border-b border-gray-100 dark:border-gray-700">
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Rechercher..."
                        class="w-full text-sm px-3 py-1.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-primary-500"
                        @click.stop
                    />
                </div>
                <ul class="max-h-48 overflow-y-auto py-1">
                    <li v-if="!filteredOptions.length" class="px-3 py-2 text-sm text-gray-400 text-center">Aucun résultat</li>
                    <li
                        v-for="opt in filteredOptions"
                        :key="opt.value"
                        :class="[
                            'flex items-center gap-2.5 px-3 py-2 text-sm transition-colors',
                            opt.disabled
                                ? 'opacity-50 cursor-not-allowed bg-gray-50 dark:bg-gray-700/30'
                                : isSelected(opt.value)
                                    ? 'cursor-pointer bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300'
                                    : 'cursor-pointer text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700',
                        ]"
                        @click.stop="!opt.disabled && toggle(opt)"
                    >
                        <div :class="['w-4 h-4 rounded border-2 flex items-center justify-center flex-shrink-0 transition-colors', isSelected(opt.value) ? 'bg-primary-600 border-primary-600' : 'border-gray-300 dark:border-gray-500']">
                            <svg v-if="isSelected(opt.value)" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <span class="flex-1">{{ opt.label }}</span>
                        <span v-if="opt.disabled" class="text-xs text-gray-400 bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded-full">déjà assignée</span>
                    </li>
                </ul>
            </div>
        </Transition>

        <p v-if="error" class="mt-1.5 text-xs text-danger-600 dark:text-danger-400">{{ error }}</p>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import type { SelectOption } from '@/types';

interface Props {
    modelValue?: (string | number)[];
    options?: SelectOption[];
    label?: string;
    placeholder?: string;
    required?: boolean;
    error?: string;
}

const props = withDefaults(defineProps<Props>(), {
    modelValue: () => [],
    options: () => [],
    placeholder: 'Sélectionner...',
});

const emit = defineEmits<{ 'update:modelValue': [value: (string | number)[]] }>();

const isOpen = ref(false);
const search = ref('');
const containerRef = ref<HTMLElement>();

const filteredOptions = computed(() =>
    props.options.filter(o => o.label.toLowerCase().includes(search.value.toLowerCase()))
);

const selected = computed(() =>
    props.options.filter(o => props.modelValue?.includes(o.value))
);

const isSelected = (val: string | number) => props.modelValue?.includes(val) ?? false;

const toggle = (opt: SelectOption) => {
    const current = [...(props.modelValue ?? [])];
    const idx = current.indexOf(opt.value);
    if (idx === -1) current.push(opt.value);
    else current.splice(idx, 1);
    emit('update:modelValue', current);
};

const remove = (val: string | number) => {
    emit('update:modelValue', (props.modelValue ?? []).filter(v => v !== val));
};

const handleClickOutside = (e: MouseEvent) => {
    if (containerRef.value && !containerRef.value.contains(e.target as Node)) {
        isOpen.value = false;
    }
};

onMounted(() => document.addEventListener('mousedown', handleClickOutside));
onUnmounted(() => document.removeEventListener('mousedown', handleClickOutside));
</script>
