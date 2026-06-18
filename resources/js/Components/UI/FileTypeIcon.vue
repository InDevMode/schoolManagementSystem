<template>
    <div :class="['flex items-center justify-center rounded-lg flex-shrink-0', sizeClass, bgClass]">
        <svg :class="['flex-shrink-0', iconSizeClass, colorClass]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" :d="iconPath" />
        </svg>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    filename: string;
    size?: 'sm' | 'md' | 'lg';
}

const props = withDefaults(defineProps<Props>(), { size: 'md' });

const ext = computed(() => (props.filename.split('.').pop() ?? '').toLowerCase());

// ── Chemin SVG selon extension ────────────────────────────────────────────────
const iconPath = computed(() => {
    if (ext.value === 'pdf')
        return 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z';
    if (['doc', 'docx', 'odt', 'txt'].includes(ext.value))
        return 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z';
    if (['xls', 'xlsx', 'csv', 'ods'].includes(ext.value))
        return 'M3 10h18M3 14h18M10 3v18M14 3v18M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z';
    if (['ppt', 'pptx', 'odp'].includes(ext.value))
        return 'M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z';
    if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'ico'].includes(ext.value))
        return 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z';
    if (['mp4', 'mov', 'avi', 'mkv', 'webm', 'flv'].includes(ext.value))
        return 'M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z';
    if (['mp3', 'wav', 'ogg', 'flac', 'aac'].includes(ext.value))
        return 'M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3';
    if (['zip', 'rar', '7z', 'tar', 'gz', 'bz2'].includes(ext.value))
        return 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4';
    // Fichier générique
    return 'M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13';
});

// ── Couleur selon type ────────────────────────────────────────────────────────
const colorClass = computed(() => {
    if (ext.value === 'pdf')                                               return 'text-red-600 dark:text-red-400';
    if (['doc', 'docx', 'odt', 'txt'].includes(ext.value))                return 'text-violet-600 dark:text-violet-400';
    if (['xls', 'xlsx', 'csv', 'ods'].includes(ext.value))                return 'text-emerald-600 dark:text-emerald-400';
    if (['ppt', 'pptx', 'odp'].includes(ext.value))                       return 'text-orange-600 dark:text-orange-400';
    if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'].includes(ext.value)) return 'text-violet-600 dark:text-violet-400';
    if (['mp4', 'mov', 'avi', 'mkv', 'webm'].includes(ext.value))         return 'text-pink-600 dark:text-pink-400';
    if (['mp3', 'wav', 'ogg', 'flac'].includes(ext.value))                return 'text-indigo-600 dark:text-indigo-400';
    if (['zip', 'rar', '7z', 'tar', 'gz'].includes(ext.value))            return 'text-amber-600 dark:text-amber-400';
    return 'text-gray-500 dark:text-gray-400';
});

const bgClass = computed(() => {
    if (ext.value === 'pdf')                                               return 'bg-red-50 dark:bg-red-900/20';
    if (['doc', 'docx', 'odt', 'txt'].includes(ext.value))                return 'bg-violet-50 dark:bg-violet-900/20';
    if (['xls', 'xlsx', 'csv', 'ods'].includes(ext.value))                return 'bg-emerald-50 dark:bg-emerald-900/20';
    if (['ppt', 'pptx', 'odp'].includes(ext.value))                       return 'bg-orange-50 dark:bg-orange-900/20';
    if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'].includes(ext.value)) return 'bg-violet-50 dark:bg-violet-900/20';
    if (['mp4', 'mov', 'avi', 'mkv', 'webm'].includes(ext.value))         return 'bg-pink-50 dark:bg-pink-900/20';
    if (['mp3', 'wav', 'ogg', 'flac'].includes(ext.value))                return 'bg-indigo-50 dark:bg-indigo-900/20';
    if (['zip', 'rar', '7z', 'tar', 'gz'].includes(ext.value))            return 'bg-amber-50 dark:bg-amber-900/20';
    return 'bg-gray-100 dark:bg-gray-700';
});

// ── Tailles ───────────────────────────────────────────────────────────────────
const sizeClass     = computed(() => ({ sm: 'w-8 h-8',  md: 'w-10 h-10', lg: 'w-12 h-12' }[props.size]));
const iconSizeClass = computed(() => ({ sm: 'w-4 h-4',  md: 'w-5 h-5',  lg: 'w-6 h-6'  }[props.size]));
</script>
