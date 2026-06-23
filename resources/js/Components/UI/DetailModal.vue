<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="modelValue"
                class="fixed inset-0 z-[10000] flex items-center justify-center p-4"
                @mousedown.self="close">

                <!-- Backdrop -->
                <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @mousedown="close" />

                <!-- Modal panel -->
                <Transition
                    enter-active-class="transition-[opacity,transform] duration-300 ease-[cubic-bezier(0.34,1.2,0.64,1)]"
                    enter-from-class="opacity-0 scale-[0.97] translate-y-2"
                    enter-to-class="opacity-100 scale-100 translate-y-0"
                    leave-active-class="transition-[opacity,transform] duration-200 ease-in"
                    leave-from-class="opacity-100 scale-100"
                    leave-to-class="opacity-0 scale-[0.97]"
                >
                    <div v-if="modelValue"
                        :class="['relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl overflow-hidden flex', sizeClass]"
                        style="max-height: 88vh;"
                        role="dialog"
                        :aria-label="title">

                        <!-- Illustration background subtile -->
                        <div class="pointer-events-none absolute inset-0 modal-bg-illustration" aria-hidden="true" />

                        <!-- ── Sidebar navigation (gauche) ── -->
                        <aside v-if="tabs && tabs.length > 0"
                            class="relative z-10 w-52 flex-shrink-0 bg-gray-50 dark:bg-gray-800/60 border-r border-gray-200 dark:border-gray-700/60 flex flex-col">

                            <!-- Profil mini dans la sidebar -->
                            <div class="px-4 pt-5 pb-4 border-b border-gray-200 dark:border-gray-700/60">
                                <slot name="sidebar-header">
                                    <div class="flex flex-col items-center text-center gap-2">
                                        <slot name="avatar">
                                            <div class="w-14 h-14 rounded-full bg-gradient-to-br from-primary-500 to-violet-600 flex items-center justify-center text-white text-lg font-bold shadow-md">
                                                {{ initials }}
                                            </div>
                                        </slot>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900 dark:text-white leading-snug">{{ title }}</p>
                                            <p v-if="subtitle" class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ subtitle }}</p>
                                        </div>
                                    </div>
                                </slot>
                            </div>

                            <!-- Navigation tabs -->
                            <nav class="flex-1 px-2 py-3 space-y-0.5">
                                <button
                                    v-for="tab in tabs"
                                    :key="tab.id"
                                    @click="activeTab = tab.id"
                                    :class="[
                                        'w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150',
                                        activeTab === tab.id
                                            ? 'bg-white dark:bg-gray-700 text-primary-700 dark:text-primary-300 shadow-sm'
                                            : 'text-gray-600 dark:text-gray-400 hover:bg-white/60 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-white'
                                    ]">
                                    <span v-if="tab.icon" class="w-4 h-4 flex-shrink-0" v-html="tab.icon" />
                                    {{ tab.label }}
                                </button>
                            </nav>

                            <!-- Actions sidebar bas -->
                            <div v-if="$slots['sidebar-footer']" class="px-3 py-3 border-t border-gray-200 dark:border-gray-700/60">
                                <slot name="sidebar-footer" />
                            </div>
                        </aside>

                        <!-- ── Contenu principal (droite) ── -->
                        <div class="relative z-10 flex flex-col flex-1 min-w-0 overflow-hidden">

                            <!-- Header -->
                            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700/60 flex-shrink-0 bg-white/80 dark:bg-gray-900/80 backdrop-blur-sm">
                                <div>
                                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                                        {{ activeTabLabel || title }}
                                    </h3>
                                    <p v-if="activeTabDesc || subtitle" class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                                        {{ activeTabDesc || subtitle }}
                                    </p>
                                </div>
                                <button
                                    @click="close"
                                    class="p-1.5 rounded-xl bg-red-500 hover:bg-red-600 text-white transition-colors flex-shrink-0 shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>

                            <!-- Body -->
                            <div class="flex-1 overflow-y-auto px-6 py-5">
                                <slot :active-tab="activeTab" />
                            </div>

                            <!-- Footer actions -->
                            <div v-if="$slots.footer"
                                class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700/60 flex-shrink-0 bg-white/80 dark:bg-gray-900/80 backdrop-blur-sm">
                                <slot name="footer" />
                            </div>
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';

interface Tab {
    id: string;
    label: string;
    icon?: string;
    description?: string;
}

interface Props {
    modelValue: boolean;
    title?: string;
    subtitle?: string;
    initials?: string;
    size?: 'md' | 'lg' | 'xl' | '2xl';
    tabs?: Tab[];
    defaultTab?: string;
    persistent?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    title: '',
    size: 'xl',
    tabs: () => [],
    persistent: false,
    initials: '?',
});

const emit = defineEmits<{ 'update:modelValue': [value: boolean] }>();

const activeTab = ref(props.defaultTab ?? props.tabs?.[0]?.id ?? '');

watch(() => props.modelValue, (val) => {
    if (val) activeTab.value = props.defaultTab ?? props.tabs?.[0]?.id ?? '';
});

const activeTabLabel = computed(() => props.tabs?.find(t => t.id === activeTab.value)?.label ?? '');
const activeTabDesc  = computed(() => props.tabs?.find(t => t.id === activeTab.value)?.description ?? '');

const sizeClass = computed(() => ({
    md:  'w-full max-w-2xl',
    lg:  'w-full max-w-3xl',
    xl:  'w-full max-w-4xl',
    '2xl': 'w-full max-w-5xl',
}[props.size]));

const close = () => {
    if (!props.persistent) emit('update:modelValue', false);
};
</script>
