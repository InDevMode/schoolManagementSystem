<template>
    <div class="min-h-screen flex" :class="isDark ? 'dark' : ''"
         :style="{ background: isDark ? '#1e1b4b' : '#f8f7ff' }">

        <!-- ═══════════════════════════════════════════
             PANNEAU GAUCHE — Violet avec bord droit ondulé
        ═══════════════════════════════════════════ -->
        <!-- PANNEAU GAUCHE -->
        <div class="hidden lg:flex flex-col relative flex-shrink-0"
             style="width:48%;min-height:100vh;overflow:visible;z-index:10;">

            <!-- Fond violet de base avec clip-path ondulé -->
            <div v-if="bgType === 'image' && bgValue"
                 class="absolute inset-0 bg-cover bg-center"
                 :style="{ backgroundImage: `url(${bgValue})` }">
                <div class="absolute inset-0" :style="{ background: bgOverlay }"/>
            </div>
            <div v-else-if="bgType === 'video' && bgValue"
                 class="absolute inset-0 overflow-hidden">
                <video autoplay loop muted playsinline
                       class="absolute inset-0 w-full h-full object-cover">
                    <source :src="bgValue"/>
                </video>
                <div class="absolute inset-0" :style="{ background: bgOverlay }"/>
            </div>

            <!-- SVG unique : fond violet ondulé + vagues, tout en un, déborde à droite -->
            <div class="absolute inset-y-0 pointer-events-none"
                 style="left:0;right:-220px;z-index:20;">
                <svg width="100%" height="100%"
                     viewBox="0 0 720 900" preserveAspectRatio="none"
                     xmlns="http://www.w3.org/2000/svg">

                    <!-- FOND VIOLET — bord droit ondulé, pas de ligne droite -->
                    <path d="M0 0
                             C0 0 490 0 490 0
                             C490 0 530 50 510 150
                             C490 250 525 300 505 400
                             C485 500 522 550 502 650
                             C482 750 518 800 498 900
                             L0 900 Z"
                          fill="#7F00FF"/>

                    <!-- Vague 2 — légèrement plus à droite, plus claire -->
                    <path d="M0 0
                             C0 0 530 0 530 0
                             C530 0 575 55 552 158
                             C529 261 568 314 545 417
                             C522 520 562 573 539 676
                             C516 779 556 832 533 900
                             L0 900 Z"
                          fill="rgba(123,116,240,0.55)"/>

                    <!-- Vague 3 -->
                    <path d="M0 0
                             C0 0 570 0 570 0
                             C570 0 618 60 593 165
                             C568 270 610 325 585 430
                             C560 535 603 590 578 695
                             C553 800 596 855 571 900
                             L0 900 Z"
                          fill="rgba(123,116,240,0.35)"/>

                    <!-- Vague 4 -->
                    <path d="M0 0
                             C0 0 610 0 610 0
                             C610 0 662 65 635 173
                             C608 281 653 336 626 444
                             C599 552 645 607 618 715
                             C591 823 637 878 610 900
                             L0 900 Z"
                          fill="rgba(155,135,245,0.22)"/>

                    <!-- Vague 5 — la plus large, quasi transparente -->
                    <path d="M0 0
                             C0 0 650 0 650 0
                             C650 0 706 70 677 181
                             C648 292 696 348 667 459
                             C638 570 688 626 659 737
                             C630 848 680 904 651 900
                             L0 900 Z"
                          fill="rgba(196,181,253,0.14)"/>
                </svg>
            </div>

            <!-- Contenu — justify-between: Welcome en haut, centre au milieu, liens en bas -->
            <div class="relative flex flex-col items-center justify-between h-full py-12 px-10 text-center"
                 style="z-index:20;">

                <!-- Haut : Welcome to -->
                <p class="text-white font-bold drop-shadow-lg"
                   style="font-size:16px;letter-spacing:0.06em;text-shadow:0 2px 8px rgba(0,0,0,0.30);">
                    Welcome to
                </p>

                <!-- Centre : Logo + Nom + Description -->
                <div class="flex flex-col items-center gap-0">

                    <!-- Grand cercle blanc avec icône dedans -->
                    <div class="rounded-full flex items-center justify-center mb-5 shadow-2xl relative"
                         style="width:110px;height:110px;
                                background:rgba(255,255,255,0.22);
                                border:3px solid rgba(255,255,255,0.60);">
                        <!-- SVG toujours présent (chapeau de diplômé) -->
                        <svg viewBox="0 0 64 64" fill="none"
                             style="width:56px;height:56px;position:absolute;">
                            <path d="M32 10 L4 26 L32 42 L60 26 Z"
                                  fill="white" opacity="0.96"/>
                            <path d="M17 31 L17 47 Q32 56 47 47 L47 31 L32 41 Z"
                                  fill="white" opacity="0.78"/>
                            <line x1="60" y1="26" x2="60" y2="41"
                                  stroke="white" stroke-width="2.5"
                                  stroke-linecap="round" opacity="0.88"/>
                            <circle cx="60" cy="44" r="3.5" fill="white" opacity="0.90"/>
                            <path d="M32 15 L34.2 21.5 L41 21.5 L35.5 25.8 L37.7 32.3 L32 28.2 L26.3 32.3 L28.5 25.8 L23 21.5 L29.8 21.5 Z"
                                  fill="#fbbf24"/>
                        </svg>
                        <!-- Image logo si disponible — overflow:hidden cache le texte alt -->
                        <img v-if="logoUrl"
                             :src="logoUrl" alt=""
                             class="rounded-full object-cover overflow-hidden"
                             style="width:100%;height:100%;position:absolute;inset:0;z-index:2;
                                    text-indent:-9999px;color:transparent;"/>
                    </div>

                    <!-- Nom de l app -->
                    <h1 class="text-white font-bold mb-5"
                        style="font-size:18px;letter-spacing:0.01em;text-shadow:0 2px 10px rgba(0,0,0,0.25);">
                        {{ appName }}
                    </h1>

                    <!-- Description -->
                    <p class="font-medium leading-relaxed"
                       style="font-size:12px;color:rgba(255,255,255,0.92);max-width:240px;text-shadow:0 1px 4px rgba(0,0,0,0.20);">
                        Gérez vos apprenants, enseignants, notes et présences.
                        Rejoignez notre communauté et embarquez dans un
                        voyage scolaire intelligent !
                    </p>
                </div>

                <!-- Liens bas -->
                <div class="flex flex-row items-center gap-5">
                    <a href="/login"
                       class="font-bold uppercase whitespace-nowrap transition-opacity hover:opacity-100"
                       style="font-size:11px;color:rgba(255,255,255,0.90);letter-spacing:0.10em;text-shadow:0 1px 4px rgba(0,0,0,0.25);">
                        Se connecter
                    </a>
                    <span style="color:rgba(255,255,255,0.50);">|</span>
                    <a href="/"
                       class="font-bold uppercase whitespace-nowrap transition-opacity hover:opacity-100"
                       style="font-size:11px;color:rgba(255,255,255,0.90);letter-spacing:0.10em;text-shadow:0 1px 4px rgba(0,0,0,0.25);">
                        Accueil
                    </a>
                </div>
            </div>
        </div><!-- fin panneau gauche -->

        <!-- ═══════════════════════════════════════════
             PANNEAU DROIT — Formulaire
        ═══════════════════════════════════════════ -->
        <div class="flex-1 flex flex-col items-center justify-center px-6 py-10 overflow-y-auto relative transition-colors duration-300"
             style="z-index:5;"
             :style="rightPanelStyle">

            <!-- Décorations géométriques en arrière-plan -->
            <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">

                <!-- Coin haut-gauche : chapeau + étoiles -->
                <svg class="absolute -top-8 -left-8" width="200" height="200" viewBox="0 0 200 200" fill="none"
                     :style="{ opacity: isDark ? '0.07' : '0.06' }">
                    <g :fill="isDark ? '#c4b5fd' : '#7B74F0'">
                        <path d="M110 40 L60 65 L110 90 L160 65 Z"/>
                        <path d="M90 75 L90 105 Q110 115 130 105 L130 75 L110 85 Z" opacity="0.7"/>
                        <path d="M30 30 L33 20 L36 30 L46 33 L36 36 L33 46 L30 36 L20 33 Z" opacity="0.55"/>
                        <path d="M170 15 L172 9 L174 15 L180 17 L174 19 L172 25 L170 19 L164 17 Z" opacity="0.5"/>
                        <circle cx="20" cy="80" r="2.5" opacity="0.5"/>
                        <circle cx="36" cy="80" r="2.5" opacity="0.5"/>
                        <circle cx="52" cy="80" r="2.5" opacity="0.5"/>
                        <circle cx="20" cy="96" r="2.5" opacity="0.4"/>
                        <circle cx="36" cy="96" r="2.5" opacity="0.4"/>
                        <circle cx="52" cy="96" r="2.5" opacity="0.4"/>
                        <circle cx="180" cy="140" r="28" fill="none"
                                :stroke="isDark ? '#c4b5fd' : '#7B74F0'"
                                stroke-width="1.5" stroke-dasharray="5 4" opacity="0.45"/>
                    </g>
                </svg>

                <!-- Haut-droite : crayon + livres -->
                <svg class="absolute top-0 right-0" width="180" height="180" viewBox="0 0 180 180" fill="none"
                     :style="{ opacity: isDark ? '0.07' : '0.06' }">
                    <g :fill="isDark ? '#a78bfa' : '#7B74F0'">
                        <rect x="120" y="18" width="12" height="52" rx="3" transform="rotate(25 126 44)"/>
                        <path d="M116 68 L128 76 L122 84 Z" opacity="0.8"/>
                        <rect x="18" y="130" width="56" height="11" rx="3" opacity="0.85"/>
                        <rect x="12" y="119" width="62" height="11" rx="3" opacity="0.65"/>
                        <rect x="20" y="108" width="46" height="11" rx="3" opacity="0.45"/>
                        <path d="M40 46 L44 34 L48 46 L60 50 L48 54 L44 66 L40 54 L28 50 Z" opacity="0.5"/>
                        <circle cx="145" cy="145" r="3" opacity="0.4"/>
                        <circle cx="160" cy="145" r="3" opacity="0.4"/>
                        <circle cx="145" cy="160" r="3" opacity="0.3"/>
                        <circle cx="160" cy="160" r="3" opacity="0.3"/>
                    </g>
                </svg>

                <!-- Bas-gauche : trophée -->
                <svg class="absolute bottom-6 left-0" width="160" height="160" viewBox="0 0 160 160" fill="none"
                     :style="{ opacity: isDark ? '0.06' : '0.05' }">
                    <g :fill="isDark ? '#fbbf24' : '#d97706'" opacity="0.8">
                        <path d="M52 16 L108 16 L98 68 Q80 86 62 68 Z"/>
                        <path d="M34 16 L52 16 L52 44 Q34 44 30 26 Z" opacity="0.65"/>
                        <path d="M108 16 L126 16 L130 26 Q126 44 108 44 Z" opacity="0.65"/>
                        <rect x="72" y="88" width="16" height="28" rx="4" opacity="0.8"/>
                        <rect x="52" y="116" width="56" height="9" rx="4" opacity="0.8"/>
                        <path d="M80 36 L83 28 L86 36 L94 39 L86 42 L83 50 L80 42 L72 39 Z"
                              :fill="isDark ? '#fde68a' : '#fbbf24'" opacity="0.95"/>
                    </g>
                </svg>

                <!-- Bas-droite : calculatrice -->
                <svg class="absolute bottom-0 right-0" width="180" height="180" viewBox="0 0 180 180" fill="none"
                     :style="{ opacity: isDark ? '0.06' : '0.05' }">
                    <g :fill="isDark ? '#a78bfa' : '#7B74F0'">
                        <rect x="80" y="50" width="76" height="106" rx="8" opacity="0.75"/>
                        <rect x="88" y="58" width="60" height="26" rx="4"
                              :fill="isDark ? '#312e81' : '#ede9fe'" opacity="0.9"/>
                        <rect x="88"  y="92"  width="13" height="13" rx="3"
                              :fill="isDark ? '#c4b5fd' : 'white'" opacity="0.7"/>
                        <rect x="106" y="92"  width="13" height="13" rx="3"
                              :fill="isDark ? '#c4b5fd' : 'white'" opacity="0.7"/>
                        <rect x="124" y="92"  width="13" height="13" rx="3"
                              :fill="isDark ? '#fbbf24' : '#f59e0b'" opacity="0.8"/>
                        <rect x="88"  y="110" width="13" height="13" rx="3"
                              :fill="isDark ? '#c4b5fd' : 'white'" opacity="0.7"/>
                        <rect x="106" y="110" width="13" height="13" rx="3"
                              :fill="isDark ? '#c4b5fd' : 'white'" opacity="0.7"/>
                        <rect x="124" y="110" width="13" height="30" rx="3"
                              :fill="isDark ? '#7c3aed' : '#6d28d9'" opacity="0.8"/>
                        <rect x="88"  y="128" width="13" height="13" rx="3"
                              :fill="isDark ? '#c4b5fd' : 'white'" opacity="0.7"/>
                        <rect x="106" y="128" width="13" height="13" rx="3"
                              :fill="isDark ? '#c4b5fd' : 'white'" opacity="0.7"/>
                        <rect x="16" y="26"  width="70" height="14" rx="4"
                              transform="rotate(-28 51 33)" opacity="0.55"/>
                    </g>
                </svg>

                <!-- Formules mathématiques -->
                <svg class="absolute top-1/3 right-3" width="130" height="180" viewBox="0 0 130 180" fill="none"
                     :style="{ opacity: isDark ? '0.05' : '0.04' }">
                    <text x="8"  y="30"  font-size="13" :fill="isDark ? '#c4b5fd' : '#7B74F0'" font-family="serif">E = mc²</text>
                    <text x="8"  y="65"  font-size="11" :fill="isDark ? '#a78bfa' : '#7B74F0'" font-family="serif">∑(n=1..∞)</text>
                    <text x="8"  y="100" font-size="12" :fill="isDark ? '#c4b5fd' : '#7B74F0'" font-family="serif">√(a²+b²)</text>
                    <text x="8"  y="135" font-size="11" :fill="isDark ? '#a78bfa' : '#7B74F0'" font-family="serif">∫ f(x) dx</text>
                    <text x="8"  y="168" font-size="10" :fill="isDark ? '#c4b5fd' : '#7B74F0'" font-family="serif">π ≈ 3.14159</text>
                </svg>

                <!-- Lignes diagonales -->
                <svg class="absolute inset-0 w-full h-full" preserveAspectRatio="none" fill="none"
                     :style="{ opacity: isDark ? '0.04' : '0.025' }">
                    <g :stroke="isDark ? '#c4b5fd' : '#7B74F0'" stroke-width="1" stroke-dasharray="8 12">
                        <line x1="0%"  y1="15%"  x2="25%"  y2="100%"/>
                        <line x1="75%" y1="0%"   x2="100%" y2="60%"/>
                        <line x1="0%"  y1="65%"  x2="20%"  y2="100%"/>
                        <line x1="80%" y1="40%"  x2="100%" y2="90%"/>
                    </g>
                </svg>
            </div><!-- fin décorations -->

            <!-- Bouton toggle dark/light -->
            <div class="absolute top-4 right-4 z-20">
                <button @click="toggleDark()"
                        class="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-200 hover:scale-110"
                        :style="toggleBtnStyle"
                        :aria-label="isDark ? 'Mode clair' : 'Mode sombre'">
                    <svg v-if="isDark" class="w-5 h-5" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24" style="color:#fbbf24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <svg v-else class="w-5 h-5" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24" style="color:#7B74F0">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                </button>
            </div>

            <!-- Logo mobile — uniquement sur petits écrans -->
            <div class="lg:hidden flex flex-col items-center gap-3 mb-8">
                <div class="w-20 h-20 rounded-full flex items-center justify-center shadow-xl"
                     style="background: linear-gradient(135deg, #7B74F0, #5b4fd4);">
                    <img v-if="logoUrl" :src="logoUrl" alt="Logo" class="w-14 h-14 object-contain"/>
                    <svg v-else viewBox="0 0 80 80" style="width:56px;height:56px;" fill="none">
                        <path d="M40 16 L8 33 L40 50 L72 33 Z" fill="white" opacity="0.96"/>
                        <path d="M23 39 L23 56 Q40 66 57 56 L57 39 L40 48 Z" fill="white" opacity="0.80"/>
                        <line x1="72" y1="33" x2="72" y2="50" stroke="white" stroke-width="3" stroke-linecap="round"/>
                        <circle cx="72" cy="54" r="4" fill="white"/>
                        <path d="M40 22 L42.5 29 L50 29 L44 33.5 L46.5 40.5 L40 36.5 L33.5 40.5 L36 33.5 L30 29 L37.5 29 Z"
                              fill="#fbbf24"/>
                    </svg>
                </div>
                <span class="text-xl font-bold"
                      :style="{ color: isDark ? '#f3f4f6' : '#1e1b4b' }">
                    {{ appName }}
                </span>
            </div>

            <!-- Carte formulaire -->
            <div class="w-full max-w-md rounded-3xl transition-all duration-300 relative overflow-hidden"
                 :style="cardStyle">

                <!-- Décos coins carte -->
                <div class="pointer-events-none absolute inset-0 rounded-3xl overflow-hidden" aria-hidden="true">
                    <svg class="absolute top-0 left-0" width="110" height="110" viewBox="0 0 110 110" fill="none"
                         :style="{ opacity: isDark ? '0.18' : '0.08' }">
                        <g :stroke="isDark ? '#c4b5fd' : '#7B74F0'" stroke-width="1.2" fill="none">
                            <rect x="14" y="14" width="38" height="38" transform="rotate(45 33 33)"/>
                            <rect x="22" y="22" width="22" height="22" transform="rotate(45 33 33)"/>
                            <path d="M74 10 L77 3 L80 10 L88 13 L80 16 L77 23 L74 16 L66 13 Z" stroke-width="1"/>
                            <circle cx="20" cy="76" r="1.6" :fill="isDark ? '#c4b5fd' : '#7B74F0'"/>
                            <circle cx="34" cy="76" r="1.6" :fill="isDark ? '#c4b5fd' : '#7B74F0'"/>
                            <circle cx="48" cy="76" r="1.6" :fill="isDark ? '#c4b5fd' : '#7B74F0'"/>
                            <circle cx="20" cy="90" r="1.6" :fill="isDark ? '#c4b5fd' : '#7B74F0'"/>
                            <circle cx="34" cy="90" r="1.6" :fill="isDark ? '#c4b5fd' : '#7B74F0'"/>
                        </g>
                    </svg>
                    <svg class="absolute top-0 right-0" width="110" height="110" viewBox="0 0 110 110" fill="none"
                         :style="{ opacity: isDark ? '0.18' : '0.08' }">
                        <g :stroke="isDark ? '#c4b5fd' : '#7B74F0'" stroke-width="1.2" fill="none">
                            <circle cx="75" cy="35" r="32" stroke-dasharray="5 4" stroke-width="1"/>
                            <circle cx="75" cy="35" r="18" stroke-dasharray="3 5" stroke-width="0.8"/>
                            <path d="M18 16 L22 6 L26 16 L36 20 L26 24 L22 34 L18 24 L8 20 Z" stroke-width="1"/>
                        </g>
                    </svg>
                </div>

                <!-- Slot formulaire -->
                <div class="relative z-10 p-8">
                    <slot />
                </div>
            </div>

            <!-- Copyright bas -->
            <p class="text-center text-xs mt-6 transition-colors duration-300"
               :style="{ color: isDark ? '#6b7280' : '#9ca3af' }">
                © {{ new Date().getFullYear() }} {{ appName }}
            </p>
        </div><!-- fin panneau droit -->
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useDark, useToggle } from '@vueuse/core';

const isDark     = useDark();
const toggleDark = useToggle(isDark);

const page    = usePage();
const appName = computed(() => {
    const s = (page.props as any).settings;
    return s?.school_name || 'School Management System';
});
const logoUrl = computed(() => (page.props as any).settings?.logo_url ?? null);

const authBg    = computed(() => (page.props as any).settings?.auth_background ?? null);
const bgType    = computed(() => authBg.value?.type    ?? 'gradient');
const bgValue   = computed(() => authBg.value?.value   ?? null);
const bgLabel   = computed(() => authBg.value?.label   ?? null);
const bgOverlay = computed(() => authBg.value?.overlay ?? 'rgba(0,0,0,0.25)');

// Gradient violet principal #7B74F0
const defaultGradient = 'linear-gradient(160deg, #5b4fd4 0%, #7B74F0 55%, #9188f5 100%)';

const rightPanelStyle = computed(() => ({
    background: isDark.value
        ? 'linear-gradient(160deg, #1e1b4b 0%, #2e1065 100%)'
        : '#f8f7ff',
}));

const cardStyle = computed(() => ({
    background:           isDark.value ? 'rgba(30,27,75,0.96)' : '#ffffff',
    backdropFilter:       'blur(24px)',
    WebkitBackdropFilter: 'blur(24px)',
    border:     isDark.value
        ? '1px solid rgba(123,116,240,0.20)'
        : '1px solid rgba(123,116,240,0.12)',
    boxShadow:  isDark.value
        ? '0 28px 64px rgba(0,0,0,0.60)'
        : '0 20px 60px rgba(123,116,240,0.14), 0 4px 16px rgba(123,116,240,0.08)',
}));

const toggleBtnStyle = computed(() => ({
    background: isDark.value ? 'rgba(123,116,240,0.15)' : 'rgba(123,116,240,0.07)',
    border:     isDark.value ? '1px solid rgba(123,116,240,0.30)' : '1px solid rgba(123,116,240,0.18)',
}));
</script>

<style scoped>
@keyframes pulse-slow {
    0%, 100% { opacity: 1; }
    50%       { opacity: 0.72; }
}
.animate-pulse-slow { animation: pulse-slow 3s ease-in-out infinite; }
</style>
