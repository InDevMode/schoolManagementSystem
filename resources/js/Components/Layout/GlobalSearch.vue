<template>
    <!-- Trigger : barre de recherche factice -->
    <button
        @click="open"
        class="group relative flex items-center gap-2.5 w-56 xl:w-72 px-3.5 py-2 rounded-full
               bg-gray-100 dark:bg-gray-800
               border border-transparent
               hover:border-primary-300 dark:hover:border-primary-700
               hover:bg-white dark:hover:bg-gray-750
               text-gray-400 dark:text-gray-500
               transition-all duration-200 text-left"
        aria-label="Recherche globale (Ctrl+K)"
    >
        <svg class="w-4 h-4 flex-shrink-0 text-gray-400 group-hover:text-primary-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <span class="flex-1 text-sm truncate">Rechercher...</span>
        <kbd class="hidden xl:inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-lg text-[10px] font-medium
                    bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600
                    text-gray-400 dark:text-gray-400 shadow-sm">
            Ctrl K
        </kbd>
    </button>

    <!-- Modale command palette -->
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="isOpen"
                 class="fixed inset-0 z-[9999] flex items-start justify-center pt-[12vh] px-4"
                 @mousedown.self="close"
            >
                <!-- Backdrop -->
                <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" />

                <!-- Panneau principal -->
                <Transition
                    enter-active-class="transition duration-200 ease-out"
                    enter-from-class="opacity-0 scale-95 -translate-y-2"
                    enter-to-class="opacity-100 scale-100 translate-y-0"
                    leave-active-class="transition duration-150 ease-in"
                    leave-from-class="opacity-100 scale-100 translate-y-0"
                    leave-to-class="opacity-0 scale-95 -translate-y-2"
                >
                    <div v-if="isOpen"
                         class="relative w-full max-w-xl bg-white dark:bg-gray-900
                                rounded-2xl shadow-2xl border border-gray-200/60 dark:border-gray-700/60
                                overflow-hidden flex flex-col"
                         style="max-height: 70vh;"
                    >
                        <!-- ── Champ de saisie ── -->
                        <div class="flex items-center gap-3 px-4 py-3.5 border-b border-gray-100 dark:border-gray-800">
                            <svg class="w-5 h-5 flex-shrink-0 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input
                                ref="inputRef"
                                v-model="query"
                                type="text"
                                placeholder="Rechercher une page, une section..."
                                class="flex-1 bg-transparent text-sm text-gray-900 dark:text-white
                                       placeholder-gray-400 dark:placeholder-gray-500
                                       outline-none border-none focus:ring-0"
                                @keydown.down.prevent="moveDown"
                                @keydown.up.prevent="moveUp"
                                @keydown.enter.prevent="selectActive"
                                @keydown.escape="close"
                            />
                            <!-- Compteur résultats -->
                            <span v-if="query && flatResults.length > 0"
                                  class="text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap flex-shrink-0">
                                {{ flatResults.length }} résultat{{ flatResults.length > 1 ? 's' : '' }}
                            </span>
                            <!-- Touche ESC -->
                            <kbd class="hidden sm:inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium
                                        bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700
                                        text-gray-400 dark:text-gray-500 flex-shrink-0">
                                ESC
                            </kbd>
                        </div>

                        <!-- ── Résultats ── -->
                        <div ref="resultsRef" class="overflow-y-auto flex-1 py-2 px-2">

                            <!-- État vide (pas encore de saisie) -->
                            <template v-if="!query">
                                <p class="px-3 pt-2 pb-1 text-[11px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">
                                    Navigation rapide
                                </p>
                                <Link
                                    v-for="(item, idx) in recentOrAll"
                                    :key="item.id"
                                    :href="item.href"
                                    @mouseenter="activeIndex = idx"
                                    @click="close"
                                    :class="[
                                        'group flex items-center gap-3 px-3 py-2.5 rounded-lg cursor-pointer transition-colors',
                                        activeIndex === idx
                                            ? 'bg-primary-50 dark:bg-primary-900/20'
                                            : 'hover:bg-gray-50 dark:hover:bg-gray-800',
                                    ]"
                                >
                                    <span :class="[
                                        'w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 transition-colors',
                                        activeIndex === idx
                                            ? 'bg-primary-100 dark:bg-primary-900/40 text-primary-600 dark:text-primary-400'
                                            : 'bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400'
                                    ]">
                                        <NavIcon :name="item.icon" class="w-4 h-4" />
                                    </span>
                                    <div class="flex-1 min-w-0">
                                        <p :class="['text-sm font-medium truncate', activeIndex === idx ? 'text-primary-700 dark:text-primary-300' : 'text-gray-800 dark:text-gray-200']">
                                            {{ item.label }}
                                        </p>
                                        <p v-if="item.parentLabel" class="text-xs text-gray-400 dark:text-gray-500 truncate mt-0.5">
                                            {{ item.parentLabel }}
                                        </p>
                                    </div>
                                    <svg :class="['w-4 h-4 flex-shrink-0 transition-opacity', activeIndex === idx ? 'opacity-100 text-primary-400' : 'opacity-0 text-gray-300']"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </Link>
                            </template>

                            <!-- Résultats groupés par section -->
                            <template v-else-if="groupedResults.length > 0">
                                <template v-for="group in groupedResults" :key="group.parentId">
                                    <!-- Titre du groupe -->
                                    <div class="flex items-center gap-2 px-3 pt-3 pb-1.5">
                                        <span class="w-5 h-5 rounded-lg flex items-center justify-center bg-gray-100 dark:bg-gray-800 flex-shrink-0">
                                            <NavIcon :name="group.parentIcon" class="w-3 h-3 text-gray-500 dark:text-gray-400" />
                                        </span>
                                        <p class="text-[11px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">
                                            {{ group.parentLabel }}
                                        </p>
                                    </div>

                                    <!-- Items du groupe -->
                                    <Link
                                        v-for="item in group.items"
                                        :key="item.id"
                                        :href="item.href"
                                        @mouseenter="activeIndex = item.flatIndex"
                                        @click="close"
                                        :class="[
                                            'group flex items-center gap-3 px-3 py-2.5 rounded-lg cursor-pointer transition-colors',
                                            activeIndex === item.flatIndex
                                                ? 'bg-primary-50 dark:bg-primary-900/20'
                                                : 'hover:bg-gray-50 dark:hover:bg-gray-800',
                                        ]"
                                    >
                                        <span :class="[
                                            'w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 transition-colors',
                                            activeIndex === item.flatIndex
                                                ? 'bg-primary-100 dark:bg-primary-900/40 text-primary-600 dark:text-primary-400'
                                                : 'bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400'
                                        ]">
                                            <NavIcon :name="item.icon" class="w-4 h-4" />
                                        </span>
                                        <div class="flex-1 min-w-0">
                                            <!-- Label avec partie matchée surlignée -->
                                            <p :class="['text-sm font-medium truncate', activeIndex === item.flatIndex ? 'text-primary-700 dark:text-primary-300' : 'text-gray-800 dark:text-gray-200']"
                                               v-html="highlight(item.label)" />
                                        </div>
                                        <!-- Badge "Aller vers" au survol -->
                                        <span :class="[
                                            'text-[10px] font-medium px-2 py-0.5 rounded-full transition-all whitespace-nowrap flex-shrink-0',
                                            activeIndex === item.flatIndex
                                                ? 'bg-primary-100 dark:bg-primary-900/40 text-primary-600 dark:text-primary-400 opacity-100'
                                                : 'opacity-0'
                                        ]">
                                            Aller →
                                        </span>
                                    </Link>
                                </template>
                            </template>

                            <!-- Aucun résultat -->
                            <div v-else class="flex flex-col items-center py-12 text-center">
                                <div class="w-14 h-14 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-3">
                                    <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Aucun résultat pour</p>
                                <p class="text-sm font-bold text-gray-900 dark:text-white mt-0.5">"{{ query }}"</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">Essayez avec d'autres mots-clés</p>
                            </div>
                        </div>

                        <!-- ── Pied de page : raccourcis clavier ── -->
                        <div class="flex items-center gap-4 px-4 py-2.5 border-t border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50">
                            <div class="flex items-center gap-1.5">
                                <kbd class="kbd-hint">↑</kbd><kbd class="kbd-hint">↓</kbd>
                                <span class="text-[11px] text-gray-400 dark:text-gray-500">Naviguer</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <kbd class="kbd-hint">↵</kbd>
                                <span class="text-[11px] text-gray-400 dark:text-gray-500">Ouvrir</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <kbd class="kbd-hint">Esc</kbd>
                                <span class="text-[11px] text-gray-400 dark:text-gray-500">Fermer</span>
                            </div>
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup lang="ts">
import { ref, computed, watch, nextTick, onMounted, onUnmounted } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import { useNavigation } from '@/Composables/useNavigation';
import NavIcon from '@/Components/Layout/NavIcon.vue';
import type { NavItem } from '@/types';

// ── État ─────────────────────────────────────────────────────────────────────
const isOpen    = ref(false);
const query     = ref('');
const activeIndex = ref(0);
const inputRef  = ref<HTMLInputElement | null>(null);
const resultsRef = ref<HTMLElement | null>(null);

const { navItems } = useNavigation();

// ── Aplatissement de tous les liens navigables ────────────────────────────────
interface FlatItem {
    id: string;
    label: string;
    icon: string;
    href: string;
    parentLabel: string;
    parentIcon: string;
    parentId: string;
    flatIndex: number;
}

const allItems = computed<FlatItem[]>(() => {
    const result: FlatItem[] = [];
    let idx = 0;

    for (const item of navItems.value) {
        if (item.type === 'separator') continue;

        if (!item.children) {
            // Lien simple
            if (item.href) {
                result.push({
                    id: item.id,
                    label: item.label,
                    icon: item.icon,
                    href: item.href,
                    parentLabel: 'Principal',
                    parentIcon: 'home',
                    parentId: 'root',
                    flatIndex: idx++,
                });
            }
        } else {
            // Sous-liens
            for (const child of item.children) {
                if (child.href) {
                    result.push({
                        id: child.id,
                        label: child.label,
                        icon: child.icon,
                        href: child.href,
                        parentLabel: item.label,
                        parentIcon: item.icon,
                        parentId: item.id,
                        flatIndex: idx++,
                    });
                }
            }
        }
    }
    return result;
});

// ── Navigation rapide (sans saisie) : max 8 items ────────────────────────────
const recentOrAll = computed(() => allItems.value.slice(0, 8));

// ── Recherche filtrée ─────────────────────────────────────────────────────────
const normalize = (s: string) =>
    s.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();

const flatResults = computed<FlatItem[]>(() => {
    const q = normalize(query.value.trim());
    if (!q) return [];
    return allItems.value.filter(item =>
        normalize(item.label).includes(q) ||
        normalize(item.parentLabel).includes(q)
    );
});

// ── Grouper par section parent ────────────────────────────────────────────────
interface Group {
    parentId: string;
    parentLabel: string;
    parentIcon: string;
    items: FlatItem[];
}

const groupedResults = computed<Group[]>(() => {
    const map = new Map<string, Group>();
    for (const item of flatResults.value) {
        if (!map.has(item.parentId)) {
            map.set(item.parentId, {
                parentId: item.parentId,
                parentLabel: item.parentLabel,
                parentIcon: item.parentIcon,
                items: [],
            });
        }
        map.get(item.parentId)!.items.push(item);
    }
    return Array.from(map.values());
});

// ── Surlignage du terme recherché ─────────────────────────────────────────────
const highlight = (text: string): string => {
    if (!query.value.trim()) return text;
    const q = query.value.trim().replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    const regex = new RegExp(`(${q})`, 'gi');
    return text.replace(regex, '<mark class="bg-primary-100 dark:bg-primary-900/40 text-primary-700 dark:text-primary-300 rounded px-0.5 not-italic font-semibold">$1</mark>');
};

// ── Navigation clavier ────────────────────────────────────────────────────────
const currentList = computed(() => query.value ? flatResults.value : recentOrAll.value);

const moveDown = () => {
    activeIndex.value = (activeIndex.value + 1) % Math.max(currentList.value.length, 1);
    scrollToActive();
};
const moveUp = () => {
    activeIndex.value = (activeIndex.value - 1 + Math.max(currentList.value.length, 1)) % Math.max(currentList.value.length, 1);
    scrollToActive();
};
const selectActive = () => {
    const list = currentList.value;
    const item = list[activeIndex.value];
    if (item?.href) {
        router.visit(item.href);
        close();
    }
};

const scrollToActive = async () => {
    await nextTick();
    const el = resultsRef.value?.querySelector('[data-active="true"]') as HTMLElement | null;
    el?.scrollIntoView({ block: 'nearest' });
};

// ── Reset activeIndex quand la query change ───────────────────────────────────
watch(query, () => { activeIndex.value = 0; });

// ── Ouvrir / Fermer ───────────────────────────────────────────────────────────
const open = async () => {
    isOpen.value = true;
    query.value  = '';
    activeIndex.value = 0;
    await nextTick();
    inputRef.value?.focus();
};

const close = () => {
    isOpen.value = false;
    query.value  = '';
};

// ── Raccourci clavier Ctrl+K / Cmd+K ─────────────────────────────────────────
const onKeydown = (e: KeyboardEvent) => {
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        isOpen.value ? close() : open();
    }
};

onMounted(()  => document.addEventListener('keydown', onKeydown));
onUnmounted(() => document.removeEventListener('keydown', onKeydown));
</script>

<style scoped>
.kbd-hint {
    @apply inline-flex items-center justify-center min-w-[22px] h-[22px] px-1
           rounded-lg text-[10px] font-medium
           bg-white dark:bg-gray-700
           border border-gray-200 dark:border-gray-600
           text-gray-500 dark:text-gray-400
           shadow-sm;
}
</style>
