<template>
    <div :class="['min-h-screen flex overflow-hidden relative', isDark ? 'dark' : '']"
         :style="{ background: isDark ? '#1e1b4b' : '#f5f3ff' }">

        <!--
            ══════════════════════════════════════════════════
            SVG clip-path : séparation ORGANIQUE / imprévisible
            Le panneau gauche est clippé par une courbe
            en S irrégulière plutôt qu'une ligne droite.
            ══════════════════════════════════════════════════
        -->
        <svg width="0" height="0" style="position:absolute">
            <defs>
                <clipPath id="waveClip" clipPathUnits="objectBoundingBox">
                    <!--
                        Courbe imprévisible en S + bosses :
                        démarre en haut à 100%, descend en ondulant
                        avec plusieurs inflexions avant de finir en bas.
                    -->
                    <path d="
                        M 0 0
                        L 0.88 0
                        C 0.92 0,   0.96 0.04, 0.95 0.10
                        C 0.93 0.18, 0.82 0.20, 0.84 0.28
                        C 0.86 0.36, 0.97 0.38, 0.96 0.46
                        C 0.94 0.55, 0.80 0.56, 0.82 0.65
                        C 0.84 0.73, 0.98 0.74, 0.97 0.82
                        C 0.95 0.90, 0.85 0.92, 0.87 1.00
                        L 0 1
                        Z
                    "/>
                </clipPath>
            </defs>
        </svg>

        <!-- ── PANNEAU GAUCHE clippé avec la courbe organique ── -->
        <div class="hidden lg:block absolute inset-y-0 left-0 z-0"
             style="width: 58%; clip-path: url(#waveClip);">

            <!-- Fond : gradient / image / vidéo dynamique -->
            <div v-if="bgType === 'image' && bgValue"
                 class="absolute inset-0 bg-cover bg-center"
                 :style="{ backgroundImage: `url(${bgValue})` }">
                <div class="absolute inset-0" :style="{ background: bgOverlay }"/>
            </div>
            <div v-else-if="bgType === 'video' && bgValue"
                 class="absolute inset-0 overflow-hidden">
                <video autoplay loop muted playsinline class="absolute inset-0 w-full h-full object-cover">
                    <source :src="bgValue"/>
                </video>
                <div class="absolute inset-0" :style="{ background: bgOverlay }"/>
            </div>
            <!-- Gradient violet — couleur principale du bouton de connexion -->
            <div v-else class="absolute inset-0"
                 :style="{ background: bgValue || defaultGradient }"/>

            <!-- Blobs décoratifs animés -->
            <div class="absolute inset-0 pointer-events-none overflow-hidden" aria-hidden="true">
                <div class="absolute -top-24 -left-24 w-80 h-80 rounded-full opacity-20 blur-3xl animate-blob"
                     style="background: radial-gradient(circle, #a78bfa, #6d28d9)"/>
                <div class="absolute bottom-0 left-10 w-96 h-96 rounded-full opacity-15 blur-3xl animate-blob animation-delay-2000"
                     style="background: radial-gradient(circle, #818cf8, #4f46e5)"/>
                <div class="absolute top-1/2 left-1/2 w-64 h-64 rounded-full opacity-10 blur-3xl animate-blob animation-delay-4000"
                     style="background: radial-gradient(circle, #c4b5fd, #7c3aed)"/>
                <!-- Points décoratifs -->
                <svg class="absolute top-10 left-10 opacity-20" width="120" height="120" viewBox="0 0 120 120">
                    <g fill="white">
                        <template v-for="r in 5" :key="r">
                            <template v-for="c in 5" :key="c">
                                <circle :cx="(c-1)*24+12" :cy="(r-1)*24+12" r="2"/>
                            </template>
                        </template>
                    </g>
                </svg>
                <svg class="absolute bottom-14 left-14 opacity-15" width="100" height="100" viewBox="0 0 100 100">
                    <g fill="white">
                        <template v-for="r in 4" :key="r">
                            <template v-for="c in 4" :key="c">
                                <circle :cx="(c-1)*26+13" :cy="(r-1)*26+13" r="1.8"/>
                            </template>
                        </template>
                    </g>
                </svg>
                <!-- Cercle pointillé haut droit -->
                <svg class="absolute top-16 right-16 opacity-15" width="100" height="100" viewBox="0 0 100 100" fill="none">
                    <circle cx="50" cy="50" r="44" stroke="white" stroke-width="1.5" stroke-dasharray="5 4"/>
                </svg>
                <!-- Losange flottant -->
                <div class="absolute top-1/4 right-20 w-8 h-8 rotate-45 opacity-20 animate-float"
                     style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.35)"/>
                <div class="absolute bottom-1/3 left-1/3 w-5 h-5 rounded-full opacity-25 animate-float animation-delay-2000"
                     style="background: rgba(196,181,253,0.5)"/>
            </div>
        </div>

        <!-- ── CONTENU du panneau gauche (au-dessus du clip) ── -->
        <div class="hidden lg:flex relative z-10 flex-col items-center justify-center overflow-hidden"
             style="width: 58%;">

            <!-- Étiquette saisonnière -->
            <div v-if="bgLabel"
                 class="absolute top-6 left-1/2 -translate-x-1/2 px-4 py-1.5 rounded-full text-sm font-semibold text-white z-20 animate-pulse-slow"
                 style="background: rgba(255,255,255,0.18); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.3); white-space: nowrap;">
                {{ bgLabel }}
            </div>

            <div class="relative z-10 flex flex-col items-center px-10 text-center max-w-lg">

                <!-- Logo + Nom -->
                <div class="flex items-center gap-3 mb-7">
                    <div class="w-11 h-11 rounded-2xl flex items-center justify-center shadow-xl overflow-hidden"
                         style="background: rgba(255,255,255,0.18); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.3);">
                        <img v-if="logoUrl" :src="logoUrl" alt="Logo" class="w-8 h-8 object-contain"/>
                        <svg v-else class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 14l9-5-9-5-9 5 9 5z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                        </svg>
                    </div>
                    <span class="text-white text-xl font-bold tracking-wide drop-shadow-md">{{ appName }}</span>
                </div>

                <!-- ══ ILLUSTRATION SCOLAIRE — Tableau + Salle de classe 3D ══ -->
                <div class="w-full max-w-md mb-7">
                    <svg viewBox="0 0 440 320" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto">
                        <defs>
                            <!-- Tableau noir -->
                            <linearGradient id="boardGrad" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%"   stop-color="#134e4a"/>
                                <stop offset="100%" stop-color="#1e3a5f"/>
                            </linearGradient>
                            <!-- Cadre tableau — violet bouton -->
                            <linearGradient id="frameGrad" x1="0" y1="0" x2="1" y2="1">
                                <stop offset="0%"   stop-color="#a78bfa"/>
                                <stop offset="100%" stop-color="#7c3aed"/>
                            </linearGradient>
                            <!-- Lumière halo tableau -->
                            <radialGradient id="boardGlow" cx="50%" cy="45%" r="55%">
                                <stop offset="0%"   stop-color="#c4b5fd" stop-opacity="0.22"/>
                                <stop offset="100%" stop-color="#7c3aed" stop-opacity="0"/>
                            </radialGradient>
                            <!-- Sol en perspective — violet clair -->
                            <linearGradient id="floorGrad" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%"   stop-color="rgba(167,139,250,0.18)"/>
                                <stop offset="100%" stop-color="rgba(124,58,237,0.06)"/>
                            </linearGradient>
                            <!-- Bureau enseignant -->
                            <linearGradient id="deskGrad" x1="0" y1="0" x2="1" y2="1">
                                <stop offset="0%"   stop-color="#c4b5fd"/>
                                <stop offset="100%" stop-color="#8b5cf6"/>
                            </linearGradient>
                            <!-- Peau eleve -->
                            <linearGradient id="skinA" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%"   stop-color="#fbbf24"/>
                                <stop offset="100%" stop-color="#d97706"/>
                            </linearGradient>
                            <!-- Peau prof -->
                            <linearGradient id="skinB" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%"   stop-color="#fed7aa"/>
                                <stop offset="100%" stop-color="#fb923c"/>
                            </linearGradient>
                        </defs>

                        <!-- Pas de fond — illustration transparente sur le gradient violet du panneau -->

                        <!-- Sol translucide -->
                        <path d="M0 200 L440 200 L440 320 L0 320 Z" fill="url(#floorGrad)"/>
                        <line x1="0"   y1="230" x2="440" y2="230" stroke="rgba(196,181,253,0.18)" stroke-width="1"/>
                        <line x1="0"   y1="262" x2="440" y2="262" stroke="rgba(196,181,253,0.13)" stroke-width="1"/>
                        <line x1="0"   y1="295" x2="440" y2="295" stroke="rgba(196,181,253,0.10)" stroke-width="1"/>
                        <!-- Colonnes fuyantes -->
                        <line x1="220" y1="190" x2="0"   y2="320" stroke="rgba(196,181,253,0.08)" stroke-width="1"/>
                        <line x1="220" y1="190" x2="110" y2="320" stroke="rgba(196,181,253,0.08)" stroke-width="1"/>
                        <line x1="220" y1="190" x2="220" y2="320" stroke="rgba(196,181,253,0.08)" stroke-width="1"/>
                        <line x1="220" y1="190" x2="330" y2="320" stroke="rgba(196,181,253,0.08)" stroke-width="1"/>
                        <line x1="220" y1="190" x2="440" y2="320" stroke="rgba(196,181,253,0.08)" stroke-width="1"/>

                        <!-- Bandes lumineuses plafond -->
                        <rect x="80"  y="4" width="120" height="3" rx="1.5" fill="rgba(196,181,253,0.45)"/>
                        <rect x="240" y="4" width="120" height="3" rx="1.5" fill="rgba(196,181,253,0.45)"/>

                        <!-- ══ TABLEAU NOIR en fond ══ -->
                        <!-- Ombre portée -->
                        <rect x="72" y="28" width="297" height="152" rx="6" fill="rgba(0,0,0,0.4)"/>
                        <!-- Cadre extérieur -->
                        <rect x="68" y="24" width="297" height="152" rx="6" fill="url(#frameGrad)"/>
                        <!-- Surface tableau -->
                        <rect x="76" y="31" width="281" height="138" rx="4" fill="url(#boardGrad)"/>
                        <!-- Halo lumineux sur tableau -->
                        <rect x="76" y="31" width="281" height="138" rx="4" fill="url(#boardGlow)"/>

                        <!-- Contenu tableau — formules et schémas -->
                        <!-- Titre -->
                        <text x="217" y="55" font-size="9" fill="rgba(165,243,252,0.9)" text-anchor="middle"
                              font-family="sans-serif" font-weight="bold" letter-spacing="1">MATHÉMATIQUES</text>
                        <line x1="120" y1="59" x2="314" y2="59" stroke="rgba(165,243,252,0.35)" stroke-width="0.8"/>

                        <!-- Équation principale -->
                        <text x="155" y="80" font-size="13" fill="white" font-family="serif" opacity="0.85">f(x) = ax² + bx + c</text>
                        <!-- Flèche annotation -->
                        <path d="M270 88 Q285 92 290 100" stroke="rgba(253,224,71,0.7)" stroke-width="1.2" fill="none" stroke-linecap="round"/>
                        <text x="292" y="104" font-size="7" fill="rgba(253,224,71,0.8)" font-family="sans-serif">Δ = b²-4ac</text>

                        <!-- Schéma parabole -->
                        <path d="M100 148 Q130 90 160 148" stroke="rgba(167,243,208,0.7)" stroke-width="1.5" fill="none"/>
                        <!-- Axes -->
                        <line x1="95"  y1="148" x2="170" y2="148" stroke="rgba(255,255,255,0.4)" stroke-width="1"/>
                        <line x1="130" y1="85"  x2="130" y2="152" stroke="rgba(255,255,255,0.4)" stroke-width="1"/>
                        <!-- Points sur courbe -->
                        <circle cx="130" cy="89"  r="2.5" fill="rgba(253,224,71,0.8)"/>
                        <circle cx="108" cy="143" r="2"   fill="rgba(165,243,252,0.7)"/>
                        <circle cx="152" cy="143" r="2"   fill="rgba(165,243,252,0.7)"/>

                        <!-- Schéma géométrie droite -->
                        <rect x="190" y="92" width="50" height="40" rx="2"
                              stroke="rgba(196,181,253,0.6)" stroke-width="1.2" fill="none"/>
                        <line x1="190" y1="92"  x2="240" y2="132" stroke="rgba(196,181,253,0.4)" stroke-width="0.8"/>
                        <line x1="240" y1="92"  x2="190" y2="132" stroke="rgba(196,181,253,0.4)" stroke-width="0.8"/>
                        <text x="215" y="148" font-size="6.5" fill="rgba(196,181,253,0.7)" text-anchor="middle" font-family="sans-serif">ABCD rectangle</text>

                        <!-- Formule droite -->
                        <text x="260" y="112" font-size="9" fill="rgba(167,243,208,0.75)" font-family="serif">S = ½ × b × h</text>
                        <text x="260" y="130" font-size="8" fill="rgba(253,224,71,0.65)" font-family="serif">P = 2(l + L)</text>

                        <!-- Traits craie décoratifs -->
                        <path d="M85 68 Q92 64 100 67" stroke="rgba(255,255,255,0.2)" stroke-width="1" fill="none" stroke-linecap="round"/>
                        <path d="M305 75 Q312 70 318 73" stroke="rgba(255,255,255,0.15)" stroke-width="1" fill="none" stroke-linecap="round"/>

                        <!-- Barre craie bas tableau -->
                        <rect x="76" y="165" width="281" height="4" rx="2" fill="rgba(255,255,255,0.06)"/>
                        <!-- Morceaux de craie -->
                        <rect x="120" y="166" width="18" height="3" rx="1.5" fill="rgba(255,255,255,0.7)"/>
                        <rect x="145" y="166" width="10" height="3" rx="1.5" fill="rgba(253,224,71,0.6)"/>
                        <rect x="162" y="166" width="14" height="3" rx="1.5" fill="rgba(167,243,208,0.6)"/>

                        <!-- ══ ESTRADE + BUREAU PROFESSEUR ══ -->
                        <path d="M60 198 L380 198 L370 210 L70 210 Z" fill="rgba(167,139,250,0.30)"/>
                        <rect x="60" y="198" width="320" height="4" rx="2" fill="rgba(196,181,253,0.5)"/>
                        <path d="M150 210 L290 210 L286 240 L154 240 Z" fill="url(#deskGrad)"/>
                        <rect x="150" y="208" width="140" height="5" rx="2" fill="rgba(221,214,254,0.6)"/>
                        <rect x="158" y="240" width="8" height="20" rx="4" fill="rgba(109,40,217,0.5)"/>
                        <rect x="274" y="240" width="8" height="20" rx="4" fill="rgba(109,40,217,0.5)"/>
                        <!-- Livres sur bureau -->
                        <rect x="162" y="199" width="14" height="10" rx="2" fill="#ef4444" opacity="0.8"/>
                        <rect x="177" y="200" width="12" height="9"  rx="2" fill="#3b82f6" opacity="0.8"/>
                        <rect x="190" y="201" width="10" height="8"  rx="2" fill="#10b981" opacity="0.8"/>
                        <!-- Ordinateur -->
                        <rect x="210" y="194" width="38" height="26" rx="3" fill="#1e1b4b"/>
                        <rect x="212" y="196" width="34" height="21" rx="2" fill="#312e81"/>
                        <rect x="214" y="198" width="30" height="17" rx="1" fill="#1e40af" opacity="0.85"/>
                        <!-- Écran ordinateur — graphique -->
                        <polyline points="218,213 222,208 228,211 234,205 240,209" stroke="#34d399" stroke-width="1.2" fill="none"/>
                        <!-- Pied écran -->
                        <rect x="225" y="220" width="8" height="3" rx="1" fill="#1e1b4b"/>

                        <!-- ══ PROFESSEUR ══ -->
                        <ellipse cx="220" cy="258" rx="18" ry="5" fill="rgba(124,58,237,0.2)"/>
                        <rect x="212" y="232" width="8" height="26" rx="4" fill="#4f46e5"/>
                        <rect x="222" y="232" width="8" height="26" rx="4" fill="#6366f1"/>
                        <ellipse cx="216" cy="258" rx="7" ry="3.5" fill="rgba(30,27,75,0.7)"/>
                        <ellipse cx="226" cy="258" rx="7" ry="3.5" fill="rgba(30,27,75,0.7)"/>
                        <rect x="207" y="200" width="28" height="34" rx="8" fill="#7c3aed"/>
                        <path d="M220 206 L218 218 L220 222 L222 218 Z" fill="#fbbf24" opacity="0.8"/>
                        <rect x="216" y="200" width="10" height="8" rx="4" fill="#c4b5fd"/>
                        <!-- Tête -->
                        <circle cx="221" cy="192" r="14" fill="url(#skinB)"/>
                        <!-- Yeux -->
                        <circle cx="216" cy="190" r="2" fill="#1e1b4b"/>
                        <circle cx="226" cy="190" r="2" fill="#1e1b4b"/>
                        <!-- Reflet œil -->
                        <circle cx="217" cy="189" r="0.8" fill="white"/>
                        <circle cx="227" cy="189" r="0.8" fill="white"/>
                        <!-- Sourire -->
                        <path d="M215 196 Q221 200 227 196" stroke="#92400e" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                        <!-- Cheveux -->
                        <path d="M207 187 Q209 177 221 178 Q233 177 235 187" fill="#1c1917"/>
                        <!-- Bras gauche — tient pointeur vers tableau -->
                        <path d="M207 212 Q192 218 178 205" stroke="#7c3aed" stroke-width="9" stroke-linecap="round" fill="none"/>
                        <circle cx="176" cy="204" r="5" fill="url(#skinB)"/>
                        <!-- Pointeur -->
                        <line x1="176" y1="204" x2="142" y2="148" stroke="rgba(255,255,255,0.55)" stroke-width="1.5" stroke-linecap="round"/>
                        <circle cx="140" cy="146" r="2.5" fill="#f59e0b"/>
                        <!-- Bras droit -->
                        <path d="M235 215 Q248 222 245 232" stroke="#7c3aed" stroke-width="9" stroke-linecap="round" fill="none"/>
                        <circle cx="245" cy="233" r="5" fill="url(#skinB)"/>

                        <!-- ══ RANGÉES D'ÉLÈVES ══ -->
                        <!-- ─ Rang 1 fond ─ -->
                        <rect x="30"  y="235" width="35" height="22" rx="4" fill="rgba(139,92,246,0.45)"/>
                        <rect x="30"  y="233" width="35" height="4"  rx="2" fill="rgba(196,181,253,0.45)"/>
                        <ellipse cx="47" cy="230" rx="10" ry="5" fill="rgba(124,58,237,0.15)"/>
                        <rect x="38"  y="218" width="18" height="18" rx="6" fill="url(#skinA)"/>
                        <circle cx="42" cy="223" r="1.5" fill="#92400e"/>
                        <circle cx="52" cy="223" r="1.5" fill="#92400e"/>
                        <path d="M40 229 Q47 233 54 229" stroke="#92400e" stroke-width="1.2" fill="none" stroke-linecap="round"/>
                        <rect x="36" y="204" width="22" height="15" rx="4" fill="#6d28d9"/>
                        <!-- Cheveux élève 1 -->
                        <path d="M36 212 Q38 204 47 204 Q56 204 58 212" fill="#1c1917"/>
                        <!-- Livre élève 1 -->
                        <rect x="32" y="236" width="14" height="10" rx="2" fill="#ef4444" opacity="0.85"/>
                        <line x1="39" y1="237" x2="39" y2="245" stroke="rgba(255,255,255,0.4)" stroke-width="0.8"/>

                        <!-- Élève 2 fond centre -->
                        <rect x="195" y="232" width="36" height="22" rx="4" fill="rgba(139,92,246,0.45)"/>
                        <rect x="195" y="230" width="36" height="4"  rx="2" fill="rgba(196,181,253,0.45)"/>
                        <ellipse cx="213" cy="227" rx="10" ry="5" fill="rgba(124,58,237,0.15)"/>
                        <rect x="203" y="215" width="18" height="17" rx="6" fill="#f59e0b"/>
                        <circle cx="207" cy="220" r="1.5" fill="#92400e"/>
                        <circle cx="217" cy="220" r="1.5" fill="#92400e"/>
                        <path d="M205 226 Q212 230 219 226" stroke="#92400e" stroke-width="1.2" fill="none" stroke-linecap="round"/>
                        <rect x="201" y="200" width="22" height="16" rx="4" fill="#be185d"/>
                        <path d="M200 209 Q202 200 213 200 Q224 200 226 209" fill="#374151"/>
                        <!-- Main levée élève 2 -->
                        <path d="M222 208 Q230 200 228 190" stroke="#f59e0b" stroke-width="7" stroke-linecap="round" fill="none"/>
                        <circle cx="228" cy="188" r="4.5" fill="#f59e0b"/>

                        <!-- Élève 3 fond droite -->
                        <rect x="370" y="235" width="36" height="22" rx="4" fill="rgba(139,92,246,0.45)"/>
                        <rect x="370" y="233" width="36" height="4"  rx="2" fill="rgba(196,181,253,0.45)"/>
                        <ellipse cx="388" cy="230" rx="10" ry="5" fill="rgba(124,58,237,0.15)"/>
                        <rect x="378" y="217" width="18" height="18" rx="6" fill="#d97706"/>
                        <circle cx="382" cy="222" r="1.5" fill="#92400e"/>
                        <circle cx="392" cy="222" r="1.5" fill="#92400e"/>
                        <path d="M380 228 Q387 232 394 228" stroke="#92400e" stroke-width="1.2" fill="none" stroke-linecap="round"/>
                        <rect x="376" y="202" width="22" height="16" rx="4" fill="#0284c7"/>
                        <path d="M375 211 Q377 202 388 202 Q399 202 401 211" fill="#1c1917"/>
                        <!-- Ordinateur élève 3 -->
                        <rect x="372" y="236" width="20" height="14" rx="2" fill="#1e1b4b"/>
                        <rect x="373" y="237" width="18" height="11" rx="1" fill="#3b82f6" opacity="0.6"/>

                        <!-- ─ Rang 2 avant ─ -->
                        <rect x="80" y="268" width="46" height="28" rx="5" fill="rgba(109,40,217,0.40)"/>
                        <rect x="80" y="265" width="46" height="6"  rx="3" fill="rgba(167,139,250,0.45)"/>
                        <ellipse cx="103" cy="262" rx="14" ry="6" fill="rgba(124,58,237,0.15)"/>
                        <rect x="90" y="245" width="24" height="22" rx="7" fill="#fbbf24"/>
                        <circle cx="95"  cy="252" r="2" fill="#92400e"/>
                        <circle cx="109" cy="252" r="2" fill="#92400e"/>
                        <circle cx="97"  cy="248" r="0.8" fill="white"/>
                        <circle cx="111" cy="248" r="0.8" fill="white"/>
                        <path d="M93 260 Q102 265 111 260" stroke="#92400e" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                        <rect x="88"  y="225" width="28" height="22" rx="6" fill="#059669"/>
                        <path d="M86 236 Q88 224 103 224 Q118 224 120 236" fill="#111827"/>
                        <!-- Livre -->
                        <rect x="84"  y="270" width="20" height="16" rx="2" fill="#f59e0b" opacity="0.9"/>
                        <line x1="94" y1="271" x2="94" y2="285" stroke="rgba(255,255,255,0.4)" stroke-width="0.8"/>
                        <rect x="105" y="270" width="16" height="16" rx="2" fill="#a78bfa" opacity="0.9"/>

                        <!-- Élève 5 avant droite -->
                        <rect x="314" y="268" width="46" height="28" rx="5" fill="rgba(109,40,217,0.40)"/>
                        <rect x="314" y="265" width="46" height="6"  rx="3" fill="rgba(167,139,250,0.45)"/>
                        <ellipse cx="337" cy="262" rx="14" ry="6" fill="rgba(124,58,237,0.15)"/>
                        <rect x="324" y="245" width="24" height="22" rx="7" fill="#fb923c"/>
                        <circle cx="329" cy="252" r="2" fill="#92400e"/>
                        <circle cx="343" cy="252" r="2" fill="#92400e"/>
                        <path d="M327 260 Q336 265 345 260" stroke="#92400e" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                        <rect x="322" y="225" width="28" height="22" rx="6" fill="#be185d"/>
                        <!-- Tresses -->
                        <path d="M320 237 Q322 224 337 224 Q352 224 354 237" fill="#1c1917"/>
                        <path d="M322 238 Q318 242 316 250" stroke="#1c1917" stroke-width="4" stroke-linecap="round" fill="none"/>
                        <path d="M352 238 Q356 242 358 250" stroke="#1c1917" stroke-width="4" stroke-linecap="round" fill="none"/>
                        <!-- Stylo élève 5 -->
                        <path d="M348 250 Q354 242 350 234" stroke="#fb923c" stroke-width="6" stroke-linecap="round" fill="none"/>
                        <circle cx="350" cy="233" r="4" fill="#fb923c"/>

                        <!-- Halo lumineux au sol -->
                        <ellipse cx="220" cy="198" rx="90" ry="8" fill="rgba(196,181,253,0.12)"/>

                    </svg>
                </div>

                <!-- Texte -->
                <h2 class="text-white text-2xl font-bold leading-tight mb-2 drop-shadow-lg">
                    Gérez votre école<br/>
                    <span style="color: rgba(196,181,253,0.95);">intelligemment</span>
                </h2>
                <p class="text-sm leading-relaxed max-w-sm font-semibold" style="color: rgba(255, 255, 255, 0.85);">
                    Élèves, enseignants, notes, Présences et bien plus — tout en un seul endroit.
                </p>

                <!-- Badges stats -->
                <div class="flex gap-5 mt-5 px-5 py-3 rounded-2xl"
                     style="background: rgba(255,255,255,0.09); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.15);">
                    <div class="text-center">
                        <div class="text-white text-lg font-bold">500+</div>
                        <div class="text-xs" style="color: rgba(196,181,253,0.75);">Écoles</div>
                    </div>
                    <div class="w-px" style="background: rgba(255,255,255,0.18);"></div>
                    <div class="text-center">
                        <div class="text-white text-lg font-bold">50k+</div>
                        <div class="text-xs" style="color: rgba(196,181,253,0.75);">Élèves</div>
                    </div>
                    <div class="w-px" style="background: rgba(255,255,255,0.18);"></div>
                    <div class="text-center">
                        <div class="text-white text-lg font-bold">99%</div>
                        <div class="text-xs" style="color: rgba(196,181,253,0.75);">Satisfaction</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── PANNEAU DROIT : formulaire ── -->
        <div class="flex-1 flex flex-col items-center justify-center px-6 py-10 overflow-y-auto relative z-10 transition-colors duration-300"
             :style="rightPanelStyle">

            <!-- ── Illustrations décoratives en arrière-plan du panneau droit ── -->
            <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">

                <!-- Coin haut-gauche : graduation cap + étoiles -->
                <svg class="absolute -top-8 -left-8" width="220" height="220" viewBox="0 0 220 220" fill="none"
                     :style="{ opacity: isDark ? '0.07' : '0.055' }">
                    <g :fill="isDark ? '#c4b5fd' : '#6d28d9'">
                        <!-- Chapeau de diplômé -->
                        <path d="M110 40 L60 65 L110 90 L160 65 Z"/>
                        <path d="M90 75 L90 105 Q110 115 130 105 L130 75 L110 85 Z" opacity="0.7"/>
                        <line x1="160" y1="65" x2="160" y2="90" stroke-width="3" :stroke="isDark ? '#c4b5fd' : '#6d28d9'"/>
                        <circle cx="160" cy="93" r="5"/>
                        <!-- Étoiles flottantes -->
                        <path d="M30 30 L33 20 L36 30 L46 33 L36 36 L33 46 L30 36 L20 33 Z" opacity="0.6"/>
                        <path d="M170 20 L172 14 L174 20 L180 22 L174 24 L172 30 L170 24 L164 22 Z" opacity="0.5"/>
                        <path d="M50 140 L52 134 L54 140 L60 142 L54 144 L52 150 L50 144 L44 142 Z" opacity="0.4"/>
                        <!-- Grille de points -->
                        <circle cx="20" cy="80" r="2.5" opacity="0.5"/>
                        <circle cx="36" cy="80" r="2.5" opacity="0.5"/>
                        <circle cx="52" cy="80" r="2.5" opacity="0.5"/>
                        <circle cx="20" cy="96" r="2.5" opacity="0.4"/>
                        <circle cx="36" cy="96" r="2.5" opacity="0.4"/>
                        <circle cx="52" cy="96" r="2.5" opacity="0.4"/>
                        <circle cx="20" cy="112" r="2.5" opacity="0.3"/>
                        <circle cx="36" cy="112" r="2.5" opacity="0.3"/>
                        <circle cx="52" cy="112" r="2.5" opacity="0.3"/>
                        <!-- Cercle pointillé -->
                        <circle cx="180" cy="140" r="30" fill="none" :stroke="isDark ? '#c4b5fd' : '#6d28d9'" stroke-width="1.5" stroke-dasharray="5 4" opacity="0.5"/>
                    </g>
                </svg>

                <!-- Haut-droite : crayon + livres empilés -->
                <svg class="absolute top-0 right-0" width="200" height="200" viewBox="0 0 200 200" fill="none"
                     :style="{ opacity: isDark ? '0.07' : '0.055' }">
                    <g :fill="isDark ? '#a78bfa' : '#7c3aed'">
                        <!-- Crayon -->
                        <rect x="130" y="20" width="14" height="60" rx="3" transform="rotate(25 137 50)"/>
                        <path d="M126 78 L140 86 L133 94 Z" opacity="0.8"/>
                        <rect x="130" y="20" width="14" height="8" rx="2" transform="rotate(25 137 50)" :fill="isDark ? '#fbbf24' : '#f59e0b'" opacity="0.7"/>
                        <!-- Livres empilés -->
                        <rect x="20" y="140" width="60" height="12" rx="3" opacity="0.9"/>
                        <rect x="14" y="128" width="68" height="12" rx="3" opacity="0.7"/>
                        <rect x="22" y="116" width="52" height="12" rx="3" opacity="0.5"/>
                        <!-- Trombones -->
                        <path d="M160 100 Q175 90 175 105 Q175 120 160 115 Q148 110 155 100 Z" fill="none" :stroke="isDark ? '#a78bfa' : '#7c3aed'" stroke-width="2.5" opacity="0.6"/>
                        <!-- Étoile -->
                        <path d="M40 50 L44 38 L48 50 L60 54 L48 58 L44 70 L40 58 L28 54 Z" opacity="0.5"/>
                        <!-- Points -->
                        <circle cx="155" cy="155" r="3" opacity="0.4"/>
                        <circle cx="170" cy="155" r="3" opacity="0.4"/>
                        <circle cx="185" cy="155" r="3" opacity="0.4"/>
                        <circle cx="155" cy="170" r="3" opacity="0.3"/>
                        <circle cx="170" cy="170" r="3" opacity="0.3"/>
                        <circle cx="185" cy="170" r="3" opacity="0.3"/>
                    </g>
                </svg>

                <!-- Centre-gauche : loupe + bulle -->
                <svg class="absolute top-1/2 -translate-y-1/2 -left-4" width="160" height="260" viewBox="0 0 160 260" fill="none"
                     :style="{ opacity: isDark ? '0.065' : '0.05' }">
                    <g :fill="isDark ? '#c4b5fd' : '#8b5cf6'">
                        <!-- Loupe -->
                        <circle cx="55" cy="80" r="35" fill="none" :stroke="isDark ? '#c4b5fd' : '#8b5cf6'" stroke-width="8" opacity="0.8"/>
                        <line x1="80" y1="105" x2="105" y2="130" :stroke="isDark ? '#c4b5fd' : '#8b5cf6'" stroke-width="8" stroke-linecap="round" opacity="0.8"/>
                        <!-- Symbole dans la loupe -->
                        <text x="40" y="88" font-size="22" :fill="isDark ? '#c4b5fd' : '#8b5cf6'" font-family="serif" opacity="0.7">+</text>
                        <!-- Bulle de dialogue -->
                        <rect x="10" y="155" width="90" height="55" rx="14" opacity="0.5"/>
                        <path d="M25 210 L15 230 L45 210 Z" opacity="0.5"/>
                        <!-- Lignes dans la bulle -->
                        <rect x="20" y="168" width="60" height="5" rx="2.5" :fill="isDark ? '#ede9fe' : 'white'" opacity="0.6"/>
                        <rect x="20" y="180" width="44" height="5" rx="2.5" :fill="isDark ? '#ede9fe' : 'white'" opacity="0.5"/>
                        <rect x="20" y="192" width="52" height="5" rx="2.5" :fill="isDark ? '#ede9fe' : 'white'" opacity="0.4"/>
                    </g>
                </svg>

                <!-- Bas-gauche : trophée -->
                <svg class="absolute bottom-8 left-0" width="180" height="180" viewBox="0 0 180 180" fill="none"
                     :style="{ opacity: isDark ? '0.065' : '0.05' }">
                    <g :fill="isDark ? '#fbbf24' : '#d97706'" opacity="0.8">
                        <!-- Trophée -->
                        <path d="M60 20 L120 20 L110 80 Q90 100 70 80 Z"/>
                        <path d="M40 20 L60 20 L60 50 Q40 50 35 30 Z" opacity="0.7"/>
                        <path d="M120 20 L140 20 L145 30 Q140 50 120 50 Z" opacity="0.7"/>
                        <rect x="82" y="100" width="16" height="30" rx="4" opacity="0.8"/>
                        <rect x="60" y="130" width="60" height="10" rx="5" opacity="0.8"/>
                        <!-- Étoile sur trophée -->
                        <path d="M90 40 L93 32 L96 40 L104 43 L96 46 L93 54 L90 46 L82 43 Z" :fill="isDark ? '#fde68a' : '#fbbf24'" opacity="0.9"/>
                    </g>
                    <!-- Motif grille -->
                    <g :fill="isDark ? '#c4b5fd' : '#6d28d9'" opacity="0.35">
                        <circle cx="140" cy="50" r="3"/>
                        <circle cx="155" cy="50" r="3"/>
                        <circle cx="140" cy="65" r="3"/>
                        <circle cx="155" cy="65" r="3"/>
                        <circle cx="140" cy="80" r="3"/>
                        <circle cx="155" cy="80" r="3"/>
                    </g>
                </svg>

                <!-- Bas-droite : calculatrice + règle -->
                <svg class="absolute bottom-0 right-0" width="200" height="200" viewBox="0 0 200 200" fill="none"
                     :style="{ opacity: isDark ? '0.065' : '0.05' }">
                    <g :fill="isDark ? '#a78bfa' : '#7c3aed'">
                        <!-- Calculatrice -->
                        <rect x="90" y="60" width="80" height="110" rx="8" opacity="0.8"/>
                        <rect x="98" y="68" width="64" height="28" rx="4" :fill="isDark ? '#312e81' : '#ede9fe'" opacity="0.9"/>
                        <!-- Touches -->
                        <rect x="98"  y="104" width="14" height="14" rx="3" :fill="isDark ? '#c4b5fd' : 'white'" opacity="0.7"/>
                        <rect x="117" y="104" width="14" height="14" rx="3" :fill="isDark ? '#c4b5fd' : 'white'" opacity="0.7"/>
                        <rect x="136" y="104" width="14" height="14" rx="3" :fill="isDark ? '#fbbf24' : '#f59e0b'" opacity="0.8"/>
                        <rect x="98"  y="122" width="14" height="14" rx="3" :fill="isDark ? '#c4b5fd' : 'white'" opacity="0.7"/>
                        <rect x="117" y="122" width="14" height="14" rx="3" :fill="isDark ? '#c4b5fd' : 'white'" opacity="0.7"/>
                        <rect x="136" y="122" width="14" height="32" rx="3" :fill="isDark ? '#7c3aed' : '#6d28d9'" opacity="0.8"/>
                        <rect x="98"  y="140" width="14" height="14" rx="3" :fill="isDark ? '#c4b5fd' : 'white'" opacity="0.7"/>
                        <rect x="117" y="140" width="14" height="14" rx="3" :fill="isDark ? '#c4b5fd' : 'white'" opacity="0.7"/>
                        <!-- Règle diagonale -->
                        <rect x="20" y="30" width="80" height="16" rx="4" transform="rotate(-30 60 38)" opacity="0.6"/>
                        <g :stroke="isDark ? '#312e81' : '#ede9fe'" stroke-width="1" opacity="0.7">
                            <line x1="30" y1="26" x2="30" y2="32" transform="rotate(-30 60 38)"/>
                            <line x1="42" y1="26" x2="42" y2="32" transform="rotate(-30 60 38)"/>
                            <line x1="54" y1="26" x2="54" y2="32" transform="rotate(-30 60 38)"/>
                            <line x1="66" y1="26" x2="66" y2="32" transform="rotate(-30 60 38)"/>
                            <line x1="78" y1="26" x2="78" y2="32" transform="rotate(-30 60 38)"/>
                        </g>
                    </g>
                </svg>

                <!-- Centre-droite : formules mathématiques flottantes -->
                <svg class="absolute top-1/3 right-4" width="140" height="200" viewBox="0 0 140 200" fill="none"
                     :style="{ opacity: isDark ? '0.05' : '0.04' }">
                    <text x="10" y="35" font-size="14" :fill="isDark ? '#c4b5fd' : '#6d28d9'" font-family="serif">E = mc²</text>
                    <text x="10" y="75" font-size="12" :fill="isDark ? '#a78bfa' : '#7c3aed'" font-family="serif">∑(n=1..∞)</text>
                    <text x="10" y="115" font-size="13" :fill="isDark ? '#c4b5fd' : '#6d28d9'" font-family="serif">√(a²+b²)</text>
                    <text x="10" y="155" font-size="12" :fill="isDark ? '#a78bfa' : '#7c3aed'" font-family="serif">∫ f(x) dx</text>
                    <text x="10" y="190" font-size="11" :fill="isDark ? '#c4b5fd' : '#6d28d9'" font-family="serif">π ≈ 3.14159</text>
                </svg>

                <!-- Lignes diagonales discrètes traversant tout -->
                <svg class="absolute inset-0 w-full h-full" preserveAspectRatio="none" fill="none"
                     :style="{ opacity: isDark ? '0.04' : '0.03' }">
                    <g :stroke="isDark ? '#c4b5fd' : '#6d28d9'" stroke-width="1" stroke-dasharray="8 12">
                        <line x1="0%"   y1="15%"  x2="25%"  y2="100%"/>
                        <line x1="75%"  y1="0%"   x2="100%" y2="60%"/>
                        <line x1="0%"   y1="65%"  x2="20%"  y2="100%"/>
                        <line x1="80%"  y1="40%"  x2="100%" y2="90%"/>
                    </g>
                </svg>

            </div>

            <!-- Toggle dark/light -->
            <div class="absolute top-4 right-4 z-20">
                <button @click="toggleDark()"
                    class="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-200 hover:scale-110"
                    :style="toggleBtnStyle"
                    :aria-label="isDark ? 'Passer en mode clair' : 'Passer en mode sombre'">
                    <svg v-if="isDark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#fbbf24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#7B74F0">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                </button>
            </div>

            <!-- Logo mobile -->
            <div class="lg:hidden flex items-center gap-2 mb-8">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center overflow-hidden"
                     style="background: linear-gradient(135deg, #9189f5, #7B74F0);">
                    <img v-if="logoUrl" :src="logoUrl" alt="Logo" class="w-7 h-7 object-contain"/>
                    <svg v-else class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                    </svg>
                </div>
                <span class="text-xl font-bold" :style="{ color: isDark ? '#f3f4f6' : '#1f2937' }">{{ appName }}</span>
            </div>

            <!-- Carte formulaire -->
            <div class="w-full max-w-md rounded-3xl p-8 transition-all duration-300 relative overflow-hidden" :style="cardStyle">

                <!-- ── Illustration décorative dans le formulaire (coins) ── -->
                <div class="pointer-events-none absolute inset-0 rounded-3xl overflow-hidden" aria-hidden="true">

                    <!-- Coin haut-gauche : diamant + étoile -->
                    <svg class="absolute top-0 left-0" width="130" height="130" viewBox="0 0 130 130" fill="none"
                         :style="{ opacity: isDark ? '0.22' : '0.09' }">
                        <g :stroke="isDark ? '#c4b5fd' : '#7c3aed'" stroke-width="1.2" fill="none">
                            <!-- Diamant double -->
                            <rect x="18" y="18" width="44" height="44" transform="rotate(45 40 40)"/>
                            <rect x="27" y="27" width="26" height="26" transform="rotate(45 40 40)"/>
                            <!-- Étoile 4 branches -->
                            <path d="M 88 14 L 92 6 L 96 14 L 104 18 L 96 22 L 92 30 L 88 22 L 80 18 Z" stroke-width="1.1"/>
                            <!-- Points grille -->
                            <circle cx="26" cy="88" r="1.8" :fill="isDark ? '#c4b5fd' : '#7c3aed'"/>
                            <circle cx="42" cy="88" r="1.8" :fill="isDark ? '#c4b5fd' : '#7c3aed'"/>
                            <circle cx="58" cy="88" r="1.8" :fill="isDark ? '#c4b5fd' : '#7c3aed'"/>
                            <circle cx="26" cy="102" r="1.8" :fill="isDark ? '#c4b5fd' : '#7c3aed'"/>
                            <circle cx="42" cy="102" r="1.8" :fill="isDark ? '#c4b5fd' : '#7c3aed'"/>
                            <circle cx="58" cy="102" r="1.8" :fill="isDark ? '#c4b5fd' : '#7c3aed'"/>
                            <!-- Ligne verticale -->
                            <line x1="10" y1="16" x2="10" y2="80" stroke-width="1"/>
                            <!-- Petite étoile -->
                            <path d="M 60 52 L 62 47 L 64 52 L 69 54 L 64 56 L 62 61 L 60 56 L 55 54 Z" stroke-width="0.9"/>
                        </g>
                    </svg>

                    <!-- Coin haut-droit : cercle pointillé + étoile -->
                    <svg class="absolute top-0 right-0" width="120" height="120" viewBox="0 0 120 120" fill="none"
                         :style="{ opacity: isDark ? '0.22' : '0.09' }">
                        <g :stroke="isDark ? '#c4b5fd' : '#7c3aed'" stroke-width="1.2" fill="none">
                            <!-- Cercle pointillé -->
                            <circle cx="80" cy="40" r="36" stroke-dasharray="5 4" stroke-width="1.1"/>
                            <circle cx="80" cy="40" r="22" stroke-dasharray="3 5" stroke-width="0.8"/>
                            <!-- Étoile 4 branches -->
                            <path d="M 22 18 L 26 8 L 30 18 L 40 22 L 30 26 L 26 36 L 22 26 L 12 22 Z" stroke-width="1.1"/>
                            <!-- Petite étoile -->
                            <path d="M 44 52 L 46 46 L 48 52 L 54 54 L 48 56 L 46 62 L 44 56 L 38 54 Z" stroke-width="0.9"/>
                            <!-- Ligne verticale droite -->
                            <line x1="112" y1="10" x2="112" y2="80" stroke-width="1"/>
                        </g>
                    </svg>

                    <!-- Coin bas-gauche : triangle + lune -->
                    <svg class="absolute bottom-0 left-0" width="120" height="110" viewBox="0 0 120 110" fill="none"
                         :style="{ opacity: isDark ? '0.22' : '0.09' }">
                        <g :stroke="isDark ? '#c4b5fd' : '#7c3aed'" stroke-width="1.2" fill="none">
                            <!-- Triangle -->
                            <line x1="14" y1="95" x2="52" y2="42"/>
                            <line x1="52" y1="42" x2="90" y2="95"/>
                            <line x1="14" y1="95" x2="90" y2="95"/>
                            <!-- Triangle intérieur -->
                            <line x1="28" y1="95" x2="52" y2="60"/>
                            <line x1="52" y1="60" x2="76" y2="95"/>
                            <!-- Lune -->
                            <path d="M 14 26 Q 32 14 32 30 Q 20 44 6 36 Q 10 30 14 26 Z"/>
                            <!-- Points grille -->
                            <circle cx="66" cy="28" r="1.8" :fill="isDark ? '#c4b5fd' : '#7c3aed'"/>
                            <circle cx="82" cy="28" r="1.8" :fill="isDark ? '#c4b5fd' : '#7c3aed'"/>
                            <circle cx="66" cy="44" r="1.8" :fill="isDark ? '#c4b5fd' : '#7c3aed'"/>
                            <circle cx="82" cy="44" r="1.8" :fill="isDark ? '#c4b5fd' : '#7c3aed'"/>
                        </g>
                    </svg>

                    <!-- Coin bas-droit : grande étoile + diamant -->
                    <svg class="absolute bottom-0 right-0" width="130" height="130" viewBox="0 0 130 130" fill="none"
                         :style="{ opacity: isDark ? '0.22' : '0.09' }">
                        <g :stroke="isDark ? '#c4b5fd' : '#7c3aed'" stroke-width="1.2" fill="none">
                            <!-- Grande étoile 4 branches -->
                            <path d="M 78 58 L 88 30 L 98 58 L 126 68 L 98 78 L 88 106 L 78 78 L 50 68 Z" stroke-width="1.4"/>
                            <circle cx="88" cy="68" r="5" fill="none"/>
                            <!-- Petite étoile -->
                            <path d="M 28 88 L 31 80 L 34 88 L 42 91 L 34 94 L 31 102 L 28 94 L 20 91 Z" stroke-width="0.9"/>
                            <!-- Diamant petit -->
                            <rect x="8" y="28" width="28" height="28" transform="rotate(45 22 42)"/>
                            <!-- Ligne horizontale -->
                            <line x1="10" y1="122" x2="120" y2="122" stroke-width="1"/>
                        </g>
                    </svg>

                    <!-- Lignes diagonales traversantes discrètes -->
                    <svg class="absolute inset-0 w-full h-full" viewBox="0 0 448 520" preserveAspectRatio="none" fill="none"
                         :style="{ opacity: isDark ? '0.06' : '0.04' }">
                        <g :stroke="isDark ? '#c4b5fd' : '#7c3aed'" stroke-width="1" stroke-dasharray="6 8">
                            <line x1="0" y1="140" x2="180" y2="320"/>
                            <line x1="268" y1="0" x2="448" y2="180"/>
                            <line x1="0" y1="360" x2="160" y2="520"/>
                            <line x1="290" y1="340" x2="448" y2="500"/>
                        </g>
                    </svg>

                </div>

                <!-- Contenu du slot au-dessus des décorations -->
                <div class="relative z-10">
                    <slot />
                </div>
            </div>

            <p class="text-center text-xs mt-6 transition-colors duration-300"
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
const bgValue   = computed(() => authBg.value?.value   ?? defaultGradient);
const bgLabel   = computed(() => authBg.value?.label   ?? null);
const bgOverlay = computed(() => authBg.value?.overlay ?? 'rgba(0,0,0,0.35)');

// Violet identique au bouton "Se connecter" — couleur principale de l'app
const defaultGradient = 'linear-gradient(160deg, #6d28d9 0%, #7c3aed 40%, #8b5cf6 100%)';

const rightPanelStyle = computed(() => ({
    // Transparent — laisse voir le violet-50 (#f5f3ff) de la div racine
    background: 'transparent',
}));

const cardStyle = computed(() => ({
    background:          isDark.value ? 'rgba(30, 27, 75, 0.95)' : '#ffffff',
    backdropFilter:      'blur(24px)',
    WebkitBackdropFilter:'blur(24px)',
    border:  isDark.value ? '1px solid rgba(139,92,246,0.2)' : '1px solid rgba(139,92,246,0.12)',
    boxShadow: isDark.value
        ? '0 28px 64px rgba(0,0,0,0.6)'
        : '0 20px 60px rgba(109,40,217,0.12), 0 4px 16px rgba(109,40,217,0.08)',
}));

const toggleBtnStyle = computed(() => ({
    background: isDark.value ? 'rgba(139,92,246,0.15)' : 'rgba(109,40,217,0.07)',
    border:     isDark.value ? '1px solid rgba(139,92,246,0.3)' : '1px solid rgba(109,40,217,0.15)',
}));
</script>

<style scoped>
@keyframes blob {
    0%, 100% { transform: translate(0,0) scale(1); }
    33%       { transform: translate(18px,-18px) scale(1.04); }
    66%       { transform: translate(-12px,14px) scale(0.97); }
}
@keyframes float {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    50%       { transform: translateY(-10px) rotate(5deg); }
}
@keyframes pulse-slow {
    0%, 100% { opacity: 1; }
    50%       { opacity: 0.72; }
}
.animate-blob       { animation: blob 9s ease-in-out infinite; }
.animate-float      { animation: float 5s ease-in-out infinite; }
.animate-pulse-slow { animation: pulse-slow 3s ease-in-out infinite; }
.animation-delay-2000 { animation-delay: 2s; }
.animation-delay-4000 { animation-delay: 4s; }
</style>
