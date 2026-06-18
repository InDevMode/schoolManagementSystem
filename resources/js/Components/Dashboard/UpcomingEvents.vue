<template>
    <div class="card p-5 h-full flex flex-col">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-900 dark:text-white text-sm">Prochains événements</h3>
            <a v-if="seeAllHref" :href="seeAllHref"
                class="text-xs text-primary-600 dark:text-primary-400 hover:underline">
                Voir tout
            </a>
        </div>

        <div v-if="events.length" class="flex flex-col gap-3 flex-1">
            <div v-for="ev in events" :key="ev.id"
                class="flex items-start gap-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors">
                <!-- Badge date -->
                <div class="flex-shrink-0 w-10 h-10 rounded-lg flex flex-col items-center justify-center text-white text-center"
                    :style="{ background: ev.color ?? '#6366f1' }">
                    <span class="text-sm font-bold leading-none">{{ dayLabel(ev.event_date) }}</span>
                    <span class="text-[9px] leading-none mt-0.5 uppercase opacity-90">{{ monthLabel(ev.event_date) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ ev.title }}</p>
                    <p class="text-xs text-gray-400 mt-0.5 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full inline-block flex-shrink-0" :style="{ background: ev.color ?? '#6366f1' }"/>
                        {{ typeLabels[ev.event_type] ?? ev.event_type }}
                        <template v-if="ev.location"> · {{ ev.location }}</template>
                    </p>
                    <p v-if="ev.start_time" class="text-[10px] text-gray-400 mt-0.5">
                        {{ ev.start_time }}{{ ev.end_time ? ` — ${ev.end_time}` : '' }}
                    </p>
                </div>
            </div>
        </div>

        <div v-else class="flex-1 flex items-center justify-center">
            <div class="text-center py-6">
                <svg class="w-8 h-8 text-gray-200 dark:text-gray-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-xs text-gray-400">Aucun événement à venir</p>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
defineProps<{
    events:      any[];
    seeAllHref?: string;
}>();

const typeLabels: Record<string, string> = {
    academic:       'Académique',
    cultural:       'Culturel',
    administrative: 'Administratif',
    exam:           'Examen',
    ceremony:       'Cérémonie',
    trip:           'Sortie',
};

const dayLabel   = (d: string) => new Date(d).toLocaleDateString('fr-FR', { day: '2-digit' });
const monthLabel = (d: string) => new Date(d).toLocaleDateString('fr-FR', { month: 'short' });
</script>
