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
// On garde trace du dernier message affiché pour éviter les doublons.
// Le problème vient du fait que sur une redirection Inertia, le layout est
// monté (mount lit initialFlash) ET l'event "navigate" se déclenche aussi.
let lastShownFlash = '';

const showFlash = (flash: { success?: string; error?: string; warning?: string } | undefined) => {
    if (!flash) return;

    const key = [flash.success, flash.error, flash.warning].filter(Boolean).join('|');
    if (!key || key === lastShownFlash) return;
    lastShownFlash = key;

    if (flash.success) toast.success(flash.success, 5000);
    if (flash.error)   toast.error(flash.error,     6000);
    if (flash.warning) toast.warning(flash.warning, 5000);

    // Réinitialiser après un délai pour permettre le même message sur une action future
    setTimeout(() => { if (lastShownFlash === key) lastShownFlash = ''; }, 1000);
};

// Premier chargement (full page reload)
showFlash(page.props.flash as any);

// Navigations Inertia côté client
router.on('navigate', () => {
    showFlash(page.props.flash as any);
});

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
