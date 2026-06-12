<template>
    <div class="space-y-5">
        <!-- Tab bar -->
        <div class="flex items-center gap-1 p-1 rounded-xl bg-gray-100/80 dark:bg-gray-800/80 w-fit overflow-x-auto scrollbar-none">
            <button
                v-for="tab in tabs"
                :key="tab.key"
                @click="active = tab.key"
                :class="[
                    'flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 whitespace-nowrap',
                    active === tab.key
                        ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm'
                        : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300',
                ]"
            >
                <!-- Icône SVG via NavIcon -->
                <NavIcon v-if="tab.icon" :name="tab.icon" class="w-4 h-4 flex-shrink-0" />
                {{ tab.label }}
                <span v-if="tab.badge !== undefined && tab.badge > 0"
                    :class="[
                        'ml-1 px-1.5 py-0.5 text-[10px] font-bold rounded-full',
                        active === tab.key
                            ? 'bg-primary-100 dark:bg-primary-900/40 text-primary-700 dark:text-primary-400'
                            : 'bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400',
                    ]"
                >{{ tab.badge }}</span>
            </button>
        </div>

        <!-- Tab content -->
        <div>
            <slot :active="active" />
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
