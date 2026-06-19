<template>
    <div class="space-y-5">
        <!-- Tab bar — centré, bien aligné, dégradé sur actif -->
        <div class="flex justify-center">
            <div class="inline-flex items-center gap-1 p-1 rounded-2xl
                        bg-gray-100 dark:bg-gray-800/90
                        border border-gray-200 dark:border-gray-700
                        shadow-inner overflow-x-auto scrollbar-none max-w-full">
                <button
                    v-for="tab in tabs"
                    :key="tab.key"
                    @click="active = tab.key"
                    :class="[
                        'relative flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium',
                        'transition-all duration-300 ease-in-out whitespace-nowrap select-none',
                        active === tab.key
                            ? 'text-white shadow-lg shadow-primary-500/30'
                            : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200',
                    ]"
                    :style="active === tab.key
                        ? 'background: linear-gradient(135deg, #7B74F0, #9189f5)'
                        : ''"
                >
                    <!-- Icône -->
                    <NavIcon v-if="tab.icon" :name="tab.icon" class="w-3.5 h-3.5 flex-shrink-0" />
                    <span>{{ tab.label }}</span>
                    <!-- Badge -->
                    <span v-if="tab.badge !== undefined && tab.badge > 0"
                        :class="[
                            'ml-0.5 min-w-[18px] h-[18px] px-1 text-[10px] font-bold rounded-full flex items-center justify-center',
                            active === tab.key
                                ? 'bg-white/25 text-white'
                                : 'bg-primary-100 dark:bg-primary-900/40 text-primary-700 dark:text-primary-400',
                        ]"
                    >{{ tab.badge }}</span>
                </button>
            </div>
        </div>

        <!-- Tab content avec transition douce -->
        <div>
            <Transition
                enter-active-class="transition-all duration-400 ease-out"
                enter-from-class="opacity-0 translate-y-2"
                enter-to-class="opacity-100 translate-y-0"
                mode="out-in"
            >
                <div :key="active">
                    <slot :active="active" />
                </div>
            </Transition>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import NavIcon from '@/Components/Layout/NavIcon.vue';

interface Tab { key: string; label: string; icon?: string; badge?: number }

const props = defineProps<{ tabs: Tab[]; defaultTab?: string }>();
const active = ref(props.defaultTab ?? props.tabs[0]?.key ?? '');
</script>
