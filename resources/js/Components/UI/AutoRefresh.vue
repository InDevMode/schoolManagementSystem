<template>
    <!-- Widget flottant — coin bas-droit -->
    <div
        class="fixed bottom-5 right-5 z-50 flex flex-col items-end gap-2"
        role="region"
        aria-label="Rafraîchissement automatique"
    >
        <!-- Bouton principal (toujours visible) -->
        <button
            :title="active ? `Rafraîchissement actif (${currentOption.label})` : 'Rafraîchissement automatique'"
            :class="[
                'group w-11 h-11 rounded-full flex items-center justify-center shadow-lg transition-all duration-200',
                active
                    ? 'bg-primary-600 hover:bg-primary-700 text-white ring-2 ring-primary-300 dark:ring-primary-700'
                    : 'bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 hover:text-primary-600 hover:border-primary-400',
            ]"
            @click="panelOpen = !panelOpen"
            aria-haspopup="true"
            :aria-expanded="panelOpen"
        >
            <!-- Icône refresh avec animation rotate quand actif -->
            <svg
                :class="['w-5 h-5 transition-transform', active ? 'animate-spin-slow' : '']"
                fill="none" stroke="currentColor" viewBox="0 0 24 24"
                aria-hidden="true"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>

            <!-- Petit badge de progression circulaire quand actif -->
            <svg
                v-if="active"
                class="absolute inset-0 w-11 h-11 -rotate-90"
                viewBox="0 0 44 44"
                aria-hidden="true"
            >
                <circle
                    cx="22" cy="22" r="19"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2.5"
                    stroke-opacity="0.25"
                    stroke-dasharray="119.4"
                    stroke-dashoffset="0"
                />
                <circle
                    cx="22" cy="22" r="19"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2.5"
                    stroke-dasharray="119.4"
                    :stroke-dashoffset="progressOffset"
                    class="transition-all duration-500 ease-linear"
                    style="stroke: rgba(255,255,255,0.9)"
                />
            </svg>
        </button>

        <!-- Panneau de configuration -->
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0 translate-y-2 scale-95"
            enter-to-class="opacity-100 translate-y-0 scale-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100 translate-y-0 scale-100"
            leave-to-class="opacity-0 translate-y-2 scale-95"
        >
            <div
                v-if="panelOpen"
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700
                       w-64 overflow-hidden"
            >
                <!-- En-tête -->
                <div class="px-4 py-3 bg-gradient-to-r from-primary-50 to-secondary-50
                            dark:from-primary-900/20 dark:to-secondary-900/20
                            border-b border-gray-100 dark:border-gray-700
                            flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <span class="text-sm font-semibold text-gray-800 dark:text-white">Auto-Refresh</span>
                    </div>
                    <button
                        class="w-6 h-6 rounded-full flex items-center justify-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                        @click="panelOpen = false"
                        aria-label="Fermer"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Barre de progression -->
                <div class="h-1 bg-gray-100 dark:bg-gray-700">
                    <div
                        class="h-full bg-primary-500 transition-all ease-linear"
                        :style="{ width: `${progressPercent}%`, transitionDuration: active ? '500ms' : '0ms' }"
                    />
                </div>

                <!-- Contenu -->
                <div class="p-4 space-y-4">

                    <!-- Statut -->
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Statut</p>
                            <p :class="['text-sm font-semibold mt-0.5', active ? 'text-green-600 dark:text-green-400' : 'text-gray-400 dark:text-gray-500']">
                                {{ active ? 'Actif' : 'Inactif' }}
                                <span v-if="active" class="ml-1 text-xs font-normal text-gray-500 dark:text-gray-400">
                                    — {{ countdown }}s
                                </span>
                            </p>
                        </div>
                        <!-- Toggle ON/OFF -->
                        <button
                            :class="[
                                'relative w-11 h-6 rounded-full transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800',
                                active ? 'bg-primary-600' : 'bg-gray-300 dark:bg-gray-600',
                            ]"
                            role="switch"
                            :aria-checked="active"
                            :aria-label="active ? 'Désactiver le rafraîchissement' : 'Activer le rafraîchissement'"
                            @click="toggleActive"
                        >
                            <span
                                :class="[
                                    'absolute top-0.5 left-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform duration-200',
                                    active ? 'translate-x-5' : 'translate-x-0',
                                ]"
                            />
                        </button>
                    </div>

                    <!-- Sélecteur d'intervalle -->
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Intervalle</p>
                        <div class="grid grid-cols-4 gap-1.5">
                            <button
                                v-for="opt in options"
                                :key="opt.value"
                                :class="[
                                    'px-2 py-1.5 rounded-xl text-xs font-medium transition-all duration-150 border',
                                    selectedValue === opt.value
                                        ? 'bg-primary-600 text-white border-primary-600 shadow-sm'
                                        : 'bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 border-gray-200 dark:border-gray-600 hover:border-primary-400 hover:text-primary-600',
                                ]"
                                @click="selectOption(opt.value)"
                            >
                                {{ opt.label }}
                            </button>
                        </div>
                    </div>

                    <!-- Bouton Rafraîchir maintenant -->
                    <button
                        class="w-full py-2 rounded-xl text-xs font-semibold
                               bg-gray-100 dark:bg-gray-700
                               text-gray-600 dark:text-gray-300
                               hover:bg-primary-50 dark:hover:bg-primary-900/20
                               hover:text-primary-700 dark:hover:text-primary-400
                               border border-gray-200 dark:border-gray-600
                               hover:border-primary-300
                               transition-all duration-150 flex items-center justify-center gap-1.5"
                        @click="refreshNow"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Rafraîchir maintenant
                    </button>
                </div>
            </div>
        </Transition>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';

// ── Options d'intervalle ──────────────────────────────────────────────────────
const options = [
    { label: '10 s',  value: 10   },
    { label: '20 s',  value: 20   },
    { label: '30 s',  value: 30   },
    { label: '1 min', value: 60   },
    { label: '2 min', value: 120  },
    { label: '5 min', value: 300  },
    { label: '10 m',  value: 600  },
    { label: '30 m',  value: 1800 },
] as const;

type OptionValue = typeof options[number]['value'];

// ── Persistence localStorage ──────────────────────────────────────────────────
const STORAGE_KEY_ACTIVE   = 'autorefresh_active';
const STORAGE_KEY_INTERVAL = 'autorefresh_interval';

const selectedValue = ref<OptionValue>(
    (parseInt(localStorage.getItem(STORAGE_KEY_INTERVAL) ?? '60') as OptionValue) || 60
);
const active = ref<boolean>(localStorage.getItem(STORAGE_KEY_ACTIVE) === 'true');
const panelOpen = ref(false);

const currentOption = computed(() => options.find(o => o.value === selectedValue.value) ?? options[1]);

// ── Compteur et progression ───────────────────────────────────────────────────
const elapsed = ref(0);
const countdown = computed(() => Math.max(0, selectedValue.value - elapsed.value));
const progressPercent = computed(() => active.value ? (elapsed.value / selectedValue.value) * 100 : 0);
// Pour le cercle SVG (circonférence r=19 → 2π×19 ≈ 119.4)
const CIRCUMFERENCE = 119.4;
const progressOffset = computed(() =>
    CIRCUMFERENCE - (progressPercent.value / 100) * CIRCUMFERENCE
);

// ── Timer ─────────────────────────────────────────────────────────────────────
let tickInterval: ReturnType<typeof setInterval> | null = null;

function startTimer() {
    stopTimer();
    elapsed.value = 0;
    tickInterval = setInterval(() => {
        elapsed.value += 1;
        if (elapsed.value >= selectedValue.value) {
            doRefresh();
        }
    }, 1000);
}

function stopTimer() {
    if (tickInterval) {
        clearInterval(tickInterval);
        tickInterval = null;
    }
    elapsed.value = 0;
}

function doRefresh() {
    elapsed.value = 0;
    router.reload({ only: [] });
}

function refreshNow() {
    elapsed.value = 0;
    router.reload({ only: [] });
}

// ── Actions ───────────────────────────────────────────────────────────────────
function toggleActive() {
    active.value = !active.value;
    localStorage.setItem(STORAGE_KEY_ACTIVE, String(active.value));
    if (active.value) {
        startTimer();
    } else {
        stopTimer();
    }
}

function selectOption(val: OptionValue) {
    selectedValue.value = val;
    localStorage.setItem(STORAGE_KEY_INTERVAL, String(val));
    if (active.value) {
        startTimer(); // repart depuis zéro avec le nouvel intervalle
    }
}

// ── Fermer le panneau au clic extérieur ───────────────────────────────────────
const containerRef = ref<HTMLElement | null>(null);

function handleClickOutside(e: MouseEvent) {
    const el = (e.target as HTMLElement).closest('[aria-label="Rafraîchissement automatique"]');
    if (!el) panelOpen.value = false;
}

// ── Lifecycle ─────────────────────────────────────────────────────────────────
onMounted(() => {
    if (active.value) startTimer();
    document.addEventListener('mousedown', handleClickOutside);
});

onUnmounted(() => {
    stopTimer();
    document.removeEventListener('mousedown', handleClickOutside);
});

// Redémarrer si on change d'option pendant que c'est actif
watch(selectedValue, () => {
    if (active.value) startTimer();
});
</script>

<style scoped>
@keyframes spin-slow {
    from { transform: rotate(0deg); }
    to   { transform: rotate(360deg); }
}
.animate-spin-slow {
    animation: spin-slow 2s linear infinite;
}
</style>
