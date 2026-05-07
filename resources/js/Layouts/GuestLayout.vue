<template>
    <div class="min-h-screen bg-gray-50 flex items-center justify-center p-4 relative overflow-hidden">

        <!-- Décoration SVG arrière-plan -->
        <div class="absolute inset-0 pointer-events-none select-none" aria-hidden="true">

            <!-- Cercle grand haut-gauche -->
            <svg class="absolute -top-32 -left-32 opacity-[0.07]" width="600" height="600" viewBox="0 0 600 600" fill="none">
                <circle cx="300" cy="300" r="300" fill="#7c3aed"/>
            </svg>

            <!-- Cercle moyen bas-droite -->
            <svg class="absolute -bottom-24 -right-24 opacity-[0.07]" width="480" height="480" viewBox="0 0 480 480" fill="none">
                <circle cx="240" cy="240" r="240" fill="#7c3aed"/>
            </svg>

            <!-- Anneau haut-droite -->
            <svg class="absolute top-12 right-16 opacity-[0.06]" width="220" height="220" viewBox="0 0 220 220" fill="none">
                <circle cx="110" cy="110" r="100" stroke="#7c3aed" stroke-width="20"/>
            </svg>

            <!-- Anneau bas-gauche -->
            <svg class="absolute bottom-16 left-20 opacity-[0.06]" width="160" height="160" viewBox="0 0 160 160" fill="none">
                <circle cx="80" cy="80" r="70" stroke="#7c3aed" stroke-width="16"/>
            </svg>

            <!-- Grille de points haut-droite -->
            <svg class="absolute top-8 right-8 opacity-[0.08]" width="180" height="180" viewBox="0 0 180 180" fill="none">
                <g fill="#7c3aed">
                    <template v-for="row in 6" :key="row">
                        <template v-for="col in 6" :key="col">
                            <circle :cx="(col - 1) * 30 + 15" :cy="(row - 1) * 30 + 15" r="3"/>
                        </template>
                    </template>
                </g>
            </svg>

            <!-- Grille de points bas-gauche -->
            <svg class="absolute bottom-8 left-8 opacity-[0.08]" width="150" height="150" viewBox="0 0 150 150" fill="none">
                <g fill="#7c3aed">
                    <template v-for="row in 5" :key="row">
                        <template v-for="col in 5" :key="col">
                            <circle :cx="(col - 1) * 30 + 15" :cy="(row - 1) * 30 + 15" r="3"/>
                        </template>
                    </template>
                </g>
            </svg>

            <!-- Ligne décorative diagonale haut -->
            <svg class="absolute top-0 left-0 opacity-[0.04]" width="100%" height="100%" viewBox="0 0 1440 900" preserveAspectRatio="none">
                <line x1="0" y1="0" x2="1440" y2="900" stroke="#7c3aed" stroke-width="2"/>
                <line x1="0" y1="100" x2="1440" y2="1000" stroke="#7c3aed" stroke-width="1"/>
                <line x1="100" y1="0" x2="1540" y2="900" stroke="#7c3aed" stroke-width="1"/>
            </svg>

            <!-- Carré rotatif haut-centre -->
            <svg class="absolute top-16 left-1/2 -translate-x-1/2 opacity-[0.05]" width="120" height="120" viewBox="0 0 120 120" fill="none">
                <rect x="20" y="20" width="80" height="80" rx="8" stroke="#7c3aed" stroke-width="3" transform="rotate(15 60 60)"/>
                <rect x="30" y="30" width="60" height="60" rx="6" stroke="#7c3aed" stroke-width="2" transform="rotate(30 60 60)"/>
            </svg>

            <!-- Triangle bas-droite -->
            <svg class="absolute bottom-20 right-32 opacity-[0.05]" width="100" height="100" viewBox="0 0 100 100" fill="none">
                <polygon points="50,5 95,95 5,95" stroke="#7c3aed" stroke-width="3" fill="none"/>
            </svg>

        </div>

        <!-- Contenu centré -->
        <div class="relative z-10 w-full max-w-md">

            <!-- Logo + titre -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-white shadow-lg border border-gray-100 mb-4">
                    <img src="/upload/logo.png" alt="Logo" class="w-10 h-10 object-contain"
                        @error="(e) => (e.target as HTMLImageElement).style.display = 'none'" />
                    <svg v-if="logoError" class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">School Management System</h1>
                <p class="text-sm text-gray-500 mt-1">Plateforme de gestion scolaire</p>
            </div>

            <!-- Card formulaire -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
                <slot />
            </div>

            <!-- Footer -->
            <p class="text-center text-xs text-gray-400 mt-6">
                © {{ new Date().getFullYear() }} School Management System
            </p>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
const logoError = ref(false);
</script>
