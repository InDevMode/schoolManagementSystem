<template>
    <div :class="['min-h-screen flex overflow-hidden transition-colors duration-300', isDark ? 'dark' : '']">

        <!-- ===== PANNEAU GAUCHE : Illustration + Branding ===== -->
        <div class="hidden lg:flex lg:w-1/2 xl:w-[55%] relative flex-col items-center justify-center overflow-hidden"
             :style="leftPanelStyle">

            <!-- Décorations : cercles en pointillés + points dispersés -->
            <div class="absolute inset-0 pointer-events-none select-none" aria-hidden="true">

                <!-- Cercles en pointillés (comme sur la capture) -->
                <svg class="absolute top-10 left-10 opacity-25" width="120" height="120" viewBox="0 0 120 120" fill="none">
                    <circle cx="60" cy="60" r="55" stroke="white" stroke-width="1.5" stroke-dasharray="6 5"/>
                </svg>
                <svg class="absolute bottom-16 right-12 opacity-20" width="160" height="160" viewBox="0 0 160 160" fill="none">
                    <circle cx="80" cy="80" r="74" stroke="white" stroke-width="1.5" stroke-dasharray="6 5"/>
                </svg>
                <svg class="absolute top-1/3 right-8 opacity-15" width="90" height="90" viewBox="0 0 90 90" fill="none">
                    <circle cx="45" cy="45" r="40" stroke="white" stroke-width="1.5" stroke-dasharray="5 4"/>
                </svg>
                <svg class="absolute bottom-1/3 left-6 opacity-15" width="70" height="70" viewBox="0 0 70 70" fill="none">
                    <circle cx="35" cy="35" r="30" stroke="white" stroke-width="1.5" stroke-dasharray="4 4"/>
                </svg>

                <!-- Points dispersés (comme sur la capture) -->
                <svg class="absolute top-8 right-16 opacity-30" width="180" height="180" viewBox="0 0 180 180">
                    <g fill="white">
                        <template v-for="r in 6" :key="r">
                            <template v-for="c in 6" :key="c">
                                <circle :cx="(c-1)*30+15" :cy="(r-1)*30+15" r="2"/>
                            </template>
                        </template>
                    </g>
                </svg>
                <svg class="absolute bottom-10 left-10 opacity-25" width="140" height="140" viewBox="0 0 140 140">
                    <g fill="white">
                        <template v-for="r in 5" :key="r">
                            <template v-for="c in 5" :key="c">
                                <circle :cx="(c-1)*28+14" :cy="(r-1)*28+14" r="2"/>
                            </template>
                        </template>
                    </g>
                </svg>

                <!-- Quelques points isolés -->
                <circle cx="0" cy="0" r="4" fill="white" opacity="0.3" style="position:absolute" class="absolute top-1/4 left-1/3"/>
                <svg class="absolute" style="top:22%;left:35%" width="8" height="8"><circle cx="4" cy="4" r="3" fill="white" opacity="0.4"/></svg>
                <svg class="absolute" style="top:65%;right:25%" width="6" height="6"><circle cx="3" cy="3" r="2.5" fill="white" opacity="0.35"/></svg>
                <svg class="absolute" style="top:15%;right:35%" width="7" height="7"><circle cx="3.5" cy="3.5" r="3" fill="white" opacity="0.3"/></svg>
                <svg class="absolute" style="bottom:20%;left:40%" width="5" height="5"><circle cx="2.5" cy="2.5" r="2" fill="white" opacity="0.4"/></svg>
            </div>

            <!-- Contenu principal gauche -->
            <div class="relative z-10 flex flex-col items-center px-12 text-center">

                <!-- Logo + Nom de l'école -->
                <div class="flex items-center gap-3 mb-10">
                    <div class="w-11 h-11 rounded-lg flex items-center justify-center shadow-lg"
                         style="background: rgba(255,255,255,0.2); backdrop-filter: blur(8px);">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 14l9-5-9-5-9 5 9 5z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                        </svg>
                    </div>
                    <span class="text-white text-xl font-bold tracking-wide">{{ appName }}</span>
                </div>

                <!-- Illustration 3D SVG -->
                <div class="w-full max-w-xs mb-10 drop-shadow-2xl">
                    <svg viewBox="0 0 400 320" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto">
                        <!-- Ombre sol -->
                        <ellipse cx="200" cy="300" rx="140" ry="16" fill="rgba(0,0,0,0.15)"/>

                        <!-- Écran principal -->
                        <rect x="55" y="60" width="230" height="185" rx="14" fill="#1e1b4b"/>
                        <!-- Bord gauche 3D -->
                        <path d="M55 60 L38 78 L38 262 L55 245 Z" fill="#312e81"/>
                        <!-- Bord bas 3D -->
                        <path d="M55 245 L38 262 L268 262 L285 245 Z" fill="#3730a3"/>
                        <!-- Écran intérieur -->
                        <rect x="66" y="72" width="208" height="162" rx="8" fill="#0d0d1f" opacity="0.92"/>

                        <!-- Barre titre -->
                        <rect x="74" y="80" width="192" height="16" rx="4" fill="#4f46e5" opacity="0.85"/>
                        <circle cx="84" cy="88" r="3.5" fill="#ef4444"/>
                        <circle cx="95" cy="88" r="3.5" fill="#f59e0b"/>
                        <circle cx="106" cy="88" r="3.5" fill="#22c55e"/>
                        <text x="168" y="92" font-size="6.5" fill="white" opacity="0.7" text-anchor="middle" font-family="sans-serif">School Dashboard</text>

                        <!-- Cartes stats -->
                        <rect x="76" y="104" width="52" height="32" rx="5" fill="#4f46e5" opacity="0.65"/>
                        <text x="102" y="117" font-size="10" fill="white" font-weight="bold" text-anchor="middle" font-family="sans-serif">248</text>
                        <text x="102" y="129" font-size="5.5" fill="#c4b5fd" text-anchor="middle" font-family="sans-serif">Élèves</text>

                        <rect x="136" y="104" width="52" height="32" rx="5" fill="#7B74F0" opacity="0.65"/>
                        <text x="162" y="117" font-size="10" fill="white" font-weight="bold" text-anchor="middle" font-family="sans-serif">32</text>
                        <text x="162" y="129" font-size="5.5" fill="#c4b5fd" text-anchor="middle" font-family="sans-serif">Profs</text>

                        <rect x="196" y="104" width="52" height="32" rx="5" fill="#6660d4" opacity="0.65"/>
                        <text x="222" y="117" font-size="10" fill="white" font-weight="bold" text-anchor="middle" font-family="sans-serif">12</text>
                        <text x="222" y="129" font-size="5.5" fill="#c4b5fd" text-anchor="middle" font-family="sans-serif">Classes</text>

                        <!-- Graphique barres -->
                        <rect x="76" y="144" width="172" height="54" rx="5" fill="rgba(79,70,229,0.18)"/>
                        <text x="162" y="155" font-size="5.5" fill="#a78bfa" text-anchor="middle" font-family="sans-serif">Présences ce mois</text>
                        <rect x="86" y="183" width="12" height="12" rx="2" fill="#7B74F0" opacity="0.9"/>
                        <rect x="102" y="175" width="12" height="20" rx="2" fill="#6660d4" opacity="0.9"/>
                        <rect x="118" y="169" width="12" height="26" rx="2" fill="#9189f5" opacity="0.9"/>
                        <rect x="134" y="178" width="12" height="17" rx="2" fill="#7B74F0" opacity="0.9"/>
                        <rect x="150" y="172" width="12" height="23" rx="2" fill="#4f46e5" opacity="0.9"/>
                        <rect x="166" y="165" width="12" height="30" rx="2" fill="#6660d4" opacity="0.9"/>
                        <rect x="182" y="176" width="12" height="19" rx="2" fill="#9189f5" opacity="0.9"/>
                        <rect x="198" y="170" width="12" height="25" rx="2" fill="#7B74F0" opacity="0.9"/>
                        <rect x="214" y="167" width="12" height="28" rx="2" fill="#4f46e5" opacity="0.9"/>

                        <!-- Barre progression -->
                        <rect x="76" y="206" width="172" height="6" rx="3" fill="rgba(255,255,255,0.1)"/>
                        <rect x="76" y="206" width="122" height="6" rx="3" fill="url(#pg)"/>
                        <text x="76" y="220" font-size="5.5" fill="#a78bfa" font-family="sans-serif">Taux de réussite : 71%</text>

                        <!-- Pied écran -->
                        <rect x="170" y="248" width="36" height="7" rx="3.5" fill="#312e81"/>
                        <rect x="134" y="255" width="108" height="5" rx="2.5" fill="#3730a3"/>

                        <!-- Personnage -->
                        <ellipse cx="318" cy="258" rx="24" ry="7" fill="rgba(0,0,0,0.12)"/>
                        <!-- Jambes -->
                        <rect x="307" y="225" width="9" height="34" rx="4.5" fill="#1e40af"/>
                        <rect x="320" y="225" width="9" height="34" rx="4.5" fill="#1d4ed8"/>
                        <!-- Chaussures -->
                        <ellipse cx="311.5" cy="259" rx="8" ry="4.5" fill="#1e293b"/>
                        <ellipse cx="324.5" cy="259" rx="8" ry="4.5" fill="#0f172a"/>
                        <!-- Corps -->
                        <rect x="300" y="178" width="36" height="50" rx="9" fill="#3b82f6"/>
                        <!-- Col -->
                        <rect x="312" y="178" width="14" height="10" rx="4" fill="#60a5fa"/>
                        <!-- Tête -->
                        <circle cx="318" cy="163" r="20" fill="#fbbf24"/>
                        <!-- Visage -->
                        <circle cx="312" cy="160" r="2.5" fill="#92400e"/>
                        <circle cx="324" cy="160" r="2.5" fill="#92400e"/>
                        <path d="M312 170 Q318 175 324 170" stroke="#92400e" stroke-width="1.8" fill="none" stroke-linecap="round"/>
                        <!-- Cheveux -->
                        <path d="M298 157 Q300 143 318 146 Q336 143 338 157" fill="#1c1917"/>
                        <!-- Bras gauche pointant -->
                        <path d="M300 195 Q278 208 262 218" stroke="#3b82f6" stroke-width="11" stroke-linecap="round" fill="none"/>
                        <circle cx="260" cy="219" r="6" fill="#fbbf24"/>
                        <!-- Bras droit -->
                        <path d="M336 195 Q350 208 346 224" stroke="#3b82f6" stroke-width="11" stroke-linecap="round" fill="none"/>
                        <circle cx="345" cy="225" r="6" fill="#fbbf24"/>

                        <!-- Clé flottante -->
                        <g transform="translate(44, 238) rotate(-30)">
                            <circle cx="0" cy="0" r="12" stroke="#fbbf24" stroke-width="3.5" fill="none"/>
                            <circle cx="0" cy="0" r="5" fill="#fbbf24"/>
                            <rect x="12" y="-2.5" width="24" height="5" rx="2.5" fill="#fbbf24"/>
                            <rect x="30" y="2.5" width="5" height="7" rx="1.5" fill="#fbbf24"/>
                            <rect x="23" y="2.5" width="4" height="5" rx="1.5" fill="#fbbf24"/>
                        </g>

                        <!-- Cadenas flottant -->
                        <g transform="translate(340, 82)">
                            <rect x="-12" y="0" width="24" height="19" rx="4" fill="rgba(255,255,255,0.25)"/>
                            <path d="M-7 0 Q-7 -13 0 -13 Q7 -13 7 0" stroke="rgba(255,255,255,0.6)" stroke-width="3.5" fill="none" stroke-linecap="round"/>
                            <circle cx="0" cy="9.5" r="3.5" fill="rgba(255,255,255,0.5)"/>
                        </g>

                        <!-- Dégradés -->
                        <defs>
                            <linearGradient id="pg" x1="0" y1="0" x2="1" y2="0">
                                <stop offset="0%" stop-color="#7B74F0"/>
                                <stop offset="100%" stop-color="#a78bfa"/>
                            </linearGradient>
                        </defs>
                    </svg>
                </div>

                <!-- Texte -->
                <h2 class="text-white text-3xl font-bold leading-tight mb-3">
                    Gérez votre école<br/>
                    <span style="color: rgba(255,255,255,0.85);">intelligemment</span>
                </h2>
                <p class="text-sm leading-relaxed max-w-xs" style="color: rgba(255,255,255,0.7);">
                    Élèves, enseignants, notes, présences et bien plus — tout en un seul endroit.
                </p>

                <!-- Badges stats -->
                <div class="flex gap-6 mt-8">
                    <div class="text-center">
                        <div class="text-white text-xl font-bold">500+</div>
                        <div class="text-xs" style="color: rgba(255,255,255,0.65);">Écoles</div>
                    </div>
                    <div class="w-px" style="background: rgba(255,255,255,0.25);"></div>
                    <div class="text-center">
                        <div class="text-white text-xl font-bold">50k+</div>
                        <div class="text-xs" style="color: rgba(255,255,255,0.65);">Élèves</div>
                    </div>
                    <div class="w-px" style="background: rgba(255,255,255,0.25);"></div>
                    <div class="text-center">
                        <div class="text-white text-xl font-bold">99%</div>
                        <div class="text-xs" style="color: rgba(255,255,255,0.65);">Satisfaction</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== PANNEAU DROIT : Formulaire ===== -->
        <div class="flex-1 flex flex-col items-center justify-center px-6 py-10 overflow-y-auto transition-colors duration-300"
             :style="rightPanelStyle">

            <!-- Toggle dark/light — coin supérieur droit -->
            <div class="absolute top-4 right-4 z-20">
                <button
                    @click="toggleDark()"
                    class="w-10 h-10 rounded-lg flex items-center justify-center transition-all duration-200 hover:scale-110"
                    :style="toggleBtnStyle"
                    :aria-label="isDark ? 'Passer en mode clair' : 'Passer en mode sombre'"
                >
                    <!-- Soleil (mode clair) -->
                    <svg v-if="isDark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                         :style="{ color: isDark ? '#fbbf24' : '#6660d4' }">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <!-- Lune (mode sombre) -->
                    <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                         style="color: #7B74F0;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                </button>
            </div>

            <!-- Logo mobile -->
            <div class="lg:hidden flex items-center gap-2 mb-8">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center"
                     style="background: linear-gradient(135deg, #9189f5, #7B74F0);">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                    </svg>
                </div>
                <span class="text-xl font-bold" :style="{ color: isDark ? '#f3f4f6' : '#1f2937' }">{{ appName }}</span>
            </div>

            <!-- Carte formulaire -->
            <div class="w-full max-w-md">
                <slot />
            </div>

            <!-- Footer -->
            <p class="text-center text-xs mt-8 transition-colors duration-300"
               :style="{ color: isDark ? '#6b7280' : '#9ca3af' }">
                © {{ new Date().getFullYear() }} {{ appName }}
            </p>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useDark, useToggle } from '@vueuse/core';

const isDark    = useDark();
const toggleDark = useToggle(isDark);

const page    = usePage();
const appName = computed(() => {
    const s = (page.props as any).settings;
    return s?.school_name || 'School Management';
});

// Panneau gauche : violet periwinkle doux comme le bouton "Goals"
const leftPanelStyle = computed(() => ({
    background: 'linear-gradient(145deg, #9189f5 0%, #7B74F0 45%, #6660d4 100%)',
}));

// Panneau droit : adapté dark/light
const rightPanelStyle = computed(() => ({
    background: isDark.value ? '#111827' : '#f9fafb',
}));

// Bouton toggle
const toggleBtnStyle = computed(() => ({
    background: isDark.value ? 'rgba(255,255,255,0.08)' : 'rgba(123,116,240,0.08)',
    border: isDark.value ? '1px solid rgba(255,255,255,0.12)' : '1px solid rgba(123,116,240,0.15)',
}));
</script>
