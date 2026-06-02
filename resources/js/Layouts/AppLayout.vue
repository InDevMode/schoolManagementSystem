<template>
    <div :class="['min-h-screen flex bg-gray-50 dark:bg-gray-900 transition-colors duration-300', isDark ? 'dark' : '']">

        <!-- ── Sidebar ── -->
        <AppSidebar
            :collapsed="sidebarCollapsed"
            :mobile-open="mobileSidebarOpen"
            @toggle="sidebarCollapsed = !sidebarCollapsed"
            @close="mobileSidebarOpen = false"
        />

        <!-- ── Zone principale (décalée selon la sidebar) ── -->
        <div
            :class="[
                'flex flex-col flex-1 min-w-0 transition-all duration-300',
                sidebarCollapsed ? 'lg:ml-[72px]' : 'lg:ml-64',
            ]"
        >
            <!-- Topbar -->
            <AppTopbar @open-mobile="mobileSidebarOpen = true" />

            <!-- Flash messages -->
            <div v-if="flash.success || flash.error || flash.warning" class="px-6 pt-4 space-y-2">
                <AppAlert v-if="flash.success" variant="success" :message="flash.success" dismissible />
                <AppAlert v-if="flash.error"   variant="danger"  :message="flash.error"   dismissible />
                <AppAlert v-if="flash.warning" variant="warning" :message="flash.warning" dismissible />
            </div>

            <!-- Contenu de la page -->
            <main class="flex-1 px-6 py-6 overflow-auto">
                <slot />
            </main>
        </div>

        <!-- Toast notifications globales -->
        <ToastContainer />
    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useDark } from '@vueuse/core';
import AppSidebar from '@/Components/Layout/AppSidebar.vue';
import AppTopbar from '@/Components/Layout/AppTopbar.vue';
import AppAlert from '@/Components/UI/AppAlert.vue';
import ToastContainer from '@/Components/UI/ToastContainer.vue';

const isDark = useDark();
const page   = usePage();
const flash  = computed(() => page.props.flash as { success?: string; error?: string; warning?: string });

// ── État sidebar ─────────────────────────────────────────────────────────────
// Persister l'état collapsed dans localStorage
const STORAGE_KEY = 'sidebar_collapsed';
const sidebarCollapsed = ref<boolean>(
    localStorage.getItem(STORAGE_KEY) === 'true'
);

// Sauvegarder à chaque changement
import { watch } from 'vue';
watch(sidebarCollapsed, (val) => {
    localStorage.setItem(STORAGE_KEY, String(val));
});

// Mobile
const mobileSidebarOpen = ref(false);
</script>
