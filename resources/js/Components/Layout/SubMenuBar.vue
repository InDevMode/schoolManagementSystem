<template>
    <div v-if="menu?.children?.length" class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-1 overflow-x-auto no-scrollbar py-1">
                <a
                    v-for="child in menu.children"
                    :key="child.id"
                    :href="child.href"
                    :class="[
                        'flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium whitespace-nowrap transition-colors duration-150',
                        isActive(child)
                            ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300'
                            : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-200',
                    ]"
                >
                    <NavIcon :name="child.icon" class="w-4 h-4 flex-shrink-0" />
                    {{ child.label }}
                </a>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useNavigation } from '@/Composables/useNavigation';
import NavIcon from '@/Components/Layout/NavIcon.vue';
import type { NavItem } from '@/types';

defineProps<{ menu: NavItem | null }>();

const { currentSubItem } = useNavigation();

const isActive = (item: NavItem) => currentSubItem.value?.id === item.id;
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
