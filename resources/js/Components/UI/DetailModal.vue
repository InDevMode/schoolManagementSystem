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
                        :class="['relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl overflow-hidden flex flex-col', sizeClass]"
                        style="max-height: 88vh;"
                        role="dialog"
                        :aria-label="title">

                        <!-- ══════════════════════════════════════════
                             HEADER ILLUSTRÉ (style "Board Meeting")
                        ═══════════════════════════════════════════ -->
                        <div :class="['relative flex-shrink-0 overflow-hidden', headerGradient]"
                             style="min-height: 108px;">

                            <!-- Pattern géométrique décoratif (losanges) — opacité réduite -->
                            <div class="pointer-events-none absolute inset-0" aria-hidden="true">
                                <svg class="absolute inset-0 w-full h-full opacity-[0.12]" xmlns="http://www.w3.org/2000/svg">
                                    <defs>
                                        <pattern id="detail-diamond" x="0" y="0" width="24" height="24" patternUnits="userSpaceOnUse">
                                            <path d="M12 0 L24 12 L12 24 L0 12 Z" fill="none" stroke="white" stroke-width="1"/>
                                        </pattern>
                                    </defs>
                                    <rect width="100%" height="100%" fill="url(#detail-diamond)"/>
                                </svg>
                                <!-- Radial highlight top-right -->
                                <div class="absolute -top-8 -right-8 w-48 h-48 rounded-full bg-white/10 blur-2xl" />
                                <!-- Gradient fade vers le bas -->
                                <div class="absolute bottom-0 left-0 right-0 h-12 bg-gradient-to-t from-black/10 to-transparent" />
                            </div>

                            <!-- Bouton fermer -->
                            <button
                                @click="close"
                                class="absolute top-3 right-3 z-20 p-1.5 rounded-xl bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white transition-colors shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>

                            <!-- Contenu du header : avatar + nom uniquement -->
                            <div class="relative z-10 flex items-center gap-4 px-6 pt-5 pb-4">
                                <!-- Avatar slot -->
                                <div class="flex-shrink-0">
                                    <slot name="avatar">
                                        <div :class="['w-14 h-14 rounded-2xl flex items-center justify-center text-white text-xl font-bold shadow-lg ring-4 ring-white/25', avatarFallbackBg]">
                                            {{ initials }}
                                        </div>
                                    </slot>
                                </div>

                                <!-- Nom + subtitle -->
                                <div class="flex-1 min-w-0">
                                    <h2 class="text-lg font-bold text-white truncate leading-tight">{{ title }}</h2>
                                    <p v-if="subtitle" class="text-white/70 text-sm mt-0.5 truncate">{{ subtitle }}</p>
                                </div>
                            </div>

                            <!-- ── Tabs horizontaux ── -->
                            <div v-if="tabs && tabs.length > 0"
                                 class="relative z-10 flex items-end gap-1 px-6 pb-0">
                                <button
                                    v-for="tab in tabs"
                                    :key="tab.id"
                                    @click="activeTab = tab.id"
                                    :class="[
                                        'flex items-center gap-1.5 px-4 py-2.5 text-sm font-medium rounded-t-xl transition-all duration-150 border-b-2 whitespace-nowrap',
                                        activeTab === tab.id
                                            ? 'bg-white dark:bg-gray-900 text-gray-900 dark:text-white border-transparent shadow-sm'
                                            : 'text-white/75 border-transparent hover:text-white hover:bg-white/10'
                                    ]">
                                    <span v-if="tab.icon" class="w-3.5 h-3.5 flex-shrink-0" v-html="tab.icon" />
                                    {{ tab.label }}
                                </button>
                            </div>
                        </div>

                        <!-- ══════════════════════════════════════════
                             BODY (contenu de l'onglet actif)
                        ═══════════════════════════════════════════ -->
                        <div class="relative flex-1 overflow-y-auto px-6 py-5 bg-white dark:bg-gray-900">
                            <!-- Illustration background sur le body -->
                            <div class="pointer-events-none absolute inset-0 modal-bg-illustration" aria-hidden="true" />
                            <div class="relative z-10">
                                <slot :active-tab="activeTab" />
                            </div>
                        </div>

                        <!-- ══════════════════════════════════════════
                             FOOTER actions
                        ═══════════════════════════════════════════ -->
                        <div v-if="$slots.footer || $slots['sidebar-footer']"
                            class="relative flex items-center justify-between gap-3 px-6 py-4 border-t border-gray-100 dark:border-gray-700/60 flex-shrink-0 bg-white/80 dark:bg-gray-900/80 backdrop-blur-sm">
                            <!-- Illustration background sur le footer -->
                            <div class="pointer-events-none absolute inset-0 modal-bg-illustration" aria-hidden="true" />
                            <!-- Gauche : bouton Message (slot sidebar-footer) -->
                            <div class="relative z-10 flex items-center gap-2">
                                <slot name="sidebar-footer" />
                            </div>
                            <!-- Droite : Fermer + Modifier -->
                            <div class="relative z-10 flex items-center gap-2">
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

type ColorVariant = 'primary' | 'violet' | 'emerald' | 'blue' | 'amber' | 'rose' | 'indigo' | 'teal' | 'purple';

interface Props {
    modelValue: boolean;
    title?: string;
    subtitle?: string;
    initials?: string;
    color?: ColorVariant;
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
    color: 'primary',
});

const emit = defineEmits<{ 'update:modelValue': [value: boolean] }>();

const activeTab = ref(props.defaultTab ?? props.tabs?.[0]?.id ?? '');

watch(() => props.modelValue, (val) => {
    if (val) activeTab.value = props.defaultTab ?? props.tabs?.[0]?.id ?? '';
});

const headerGradient = computed(() => ({
    primary: 'bg-gradient-to-br from-primary-500 via-primary-600 to-violet-700',
    violet:  'bg-gradient-to-br from-violet-500 via-violet-600 to-purple-700',
    emerald: 'bg-gradient-to-br from-emerald-500 via-emerald-600 to-teal-700',
    blue:    'bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-700',
    amber:   'bg-gradient-to-br from-amber-400 via-amber-500 to-orange-600',
    rose:    'bg-gradient-to-br from-rose-500 via-rose-600 to-pink-700',
    indigo:  'bg-gradient-to-br from-indigo-500 via-indigo-600 to-violet-700',
    teal:    'bg-gradient-to-br from-teal-500 via-teal-600 to-cyan-700',
    purple:  'bg-gradient-to-br from-purple-500 via-purple-600 to-violet-800',
}[props.color]));

const avatarFallbackBg = computed(() => ({
    primary: 'bg-white/20',
    violet:  'bg-white/20',
    emerald: 'bg-white/20',
    blue:    'bg-white/20',
    amber:   'bg-white/20',
    rose:    'bg-white/20',
    indigo:  'bg-white/20',
    teal:    'bg-white/20',
    purple:  'bg-white/20',
}[props.color]));

const sizeClass = computed(() => ({
    md:    'w-full max-w-2xl',
    lg:    'w-full max-w-3xl',
    xl:    'w-full max-w-4xl',
    '2xl': 'w-full max-w-5xl',
}[props.size]));

const close = () => {
    if (!props.persistent) emit('update:modelValue', false);
};
</script>
