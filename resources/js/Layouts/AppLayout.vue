<template>
    <div :class="['min-h-screen flex bg-gray-50 dark:bg-gray-900 transition-colors duration-300', isDark ? 'dark' : '']">

        <!-- ── Page Loader ── -->
        <Transition
            enter-active-class="transition-opacity duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-500"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="isLoading" class="fixed inset-0 z-[9999] flex flex-col items-center justify-center bg-white/80 dark:bg-gray-900/80 backdrop-blur-sm">
                <!-- Spinner dégradé -->
                <div class="relative w-16 h-16">
                    <svg class="w-16 h-16 animate-spin" viewBox="0 0 64 64" fill="none">
                        <defs>
                            <linearGradient id="loader-gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" style="stop-color:#7B74F0;stop-opacity:1" />
                                <stop offset="50%" style="stop-color:#9189f5;stop-opacity:0.8" />
                                <stop offset="100%" style="stop-color:#c4b5fd;stop-opacity:0.1" />
                            </linearGradient>
                        </defs>
                        <circle cx="32" cy="32" r="28" stroke="url(#loader-gradient)" stroke-width="5" stroke-linecap="round"
                            stroke-dasharray="130" stroke-dashoffset="30" />
                    </svg>
                    <!-- Logo centre -->
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center"
                             style="background: linear-gradient(135deg, #7B74F0, #9189f5);">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <p class="mt-4 text-sm font-medium text-gray-500 dark:text-gray-400 animate-pulse">Chargement…</p>
            </div>
        </Transition>

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
                'flex flex-col flex-1 min-w-0 transition-all duration-300 ease-in-out',
                sidebarCollapsed ? 'lg:ml-[72px]' : 'lg:ml-64',
            ]"
        >
            <!-- Topbar -->
            <AppTopbar @open-mobile="mobileSidebarOpen = true" />

            <!-- Contenu de la page avec transition douce -->
            <main class="flex-1 px-6 py-6 overflow-auto">
                <Transition
                    enter-active-class="transition-all duration-500 ease-out"
                    enter-from-class="opacity-0 translate-y-3"
                    enter-to-class="opacity-100 translate-y-0"
                    mode="out-in"
                >
                    <div :key="pageKey">
                        <slot />
                    </div>
                </Transition>
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

// ── Loader de page ────────────────────────────────────────────────────────────
const isLoading = ref(false);
const pageKey   = ref(0);

router.on('start', () => { isLoading.value = true; });
router.on('finish', () => {
    // Délai minime pour éviter un flash sur les navigations rapides
    setTimeout(() => {
        isLoading.value = false;
        pageKey.value++;
    }, 150);
});

// ── Bridge flash → toast ──────────────────────────────────────────────────────
// On garde trace du dernier message affiché pour éviter les doublons.
// Le problème vient du fait que sur une redirection Inertia, le layout est
// monté (mount lit initialFlash) ET l'event "navigate" se déclenche aussi.
let lastShownFlash = '';
let lastShownFlashTimer: ReturnType<typeof setTimeout> | null = null;

const showFlash = (flash: { success?: string; error?: string; warning?: string } | undefined) => {
    if (!flash) return;

    const key = [flash.success, flash.error, flash.warning].filter(Boolean).join('|');
    if (!key || key === lastShownFlash) return;
    lastShownFlash = key;

    if (flash.success) toast.success(flash.success, 5000);
    if (flash.error)   toast.error(flash.error,     6000);
    if (flash.warning) toast.warning(flash.warning, 5000);

    // Réinitialiser après un délai pour permettre le même message sur une action future
    if (lastShownFlashTimer) clearTimeout(lastShownFlashTimer);
    lastShownFlashTimer = setTimeout(() => { lastShownFlash = ''; }, 2000);
};

// Premier chargement (full page reload)
showFlash(page.props.flash as any);

// Navigations Inertia côté client (full visit) ET partial reloads (back() avec preserveScroll)
router.on('navigate', () => {
    showFlash(page.props.flash as any);
});

router.on('success', (event) => {
    // Sur un back() avec preserveScroll, lire le flash depuis l'event
    // car page.props peut ne pas encore être synchronisé
    const flash = (event as any).detail?.page?.props?.flash ?? page.props.flash;
    showFlash(flash as any);
});

// ── Rechargement automatique si les permissions ont été modifiées ────────────
// Quand perm_refreshed=true arrive dans les props, on force un reload partiel
// pour que le menu se mette à jour avec les nouvelles permissions.
watch(
    () => (page.props.auth as any)?.user?.perm_refreshed,
    (refreshed) => {
        if (refreshed) {
            router.reload({ only: ['auth'] });
        }
    }
);
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
