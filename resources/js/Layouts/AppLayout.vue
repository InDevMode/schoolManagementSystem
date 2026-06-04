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

            <!-- Contenu de la page -->
            <main class="flex-1 px-6 py-6 overflow-auto">
                <slot />
            </main>
        </div>

        <!-- Toast notifications globales -->
        <ToastContainer />

        <!-- Auto-refresh flottant -->
        <AutoRefresh />
    </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import { useDark } from '@vueuse/core';
import AppSidebar from '@/Components/Layout/AppSidebar.vue';
import AppTopbar from '@/Components/Layout/AppTopbar.vue';
import ToastContainer from '@/Components/UI/ToastContainer.vue';
import AutoRefresh from '@/Components/UI/AutoRefresh.vue';
import { useToast } from '@/Composables/useToast';

const isDark = useDark();
const page   = usePage();
const toast  = useToast();

// ── Bridge flash → toast ──────────────────────────────────────────────────────
// À chaque navigation Inertia, on lit les flash et on les convertit en toast
router.on('navigate', () => {
    const flash = page.props.flash as { success?: string; error?: string; warning?: string } | undefined;
    if (!flash) return;
    if (flash.success) toast.success(flash.success, 5000);
    if (flash.error)   toast.error(flash.error,     6000);
    if (flash.warning) toast.warning(flash.warning, 5000);
});

// Aussi au premier chargement (page refresh) — les flash peuvent être présents dès le mount
const initialFlash = page.props.flash as { success?: string; error?: string; warning?: string } | undefined;
if (initialFlash?.success) toast.success(initialFlash.success, 5000);
if (initialFlash?.error)   toast.error(initialFlash.error,     6000);
if (initialFlash?.warning) toast.warning(initialFlash.warning, 5000);

// ── État sidebar ─────────────────────────────────────────────────────────────
const STORAGE_KEY = 'sidebar_collapsed';
const sidebarCollapsed = ref<boolean>(
    localStorage.getItem(STORAGE_KEY) === 'true'
);

watch(sidebarCollapsed, (val) => {
    localStorage.setItem(STORAGE_KEY, String(val));
});

// Mobile
const mobileSidebarOpen = ref(false);
</script>
