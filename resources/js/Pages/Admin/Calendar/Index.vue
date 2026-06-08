<template>
    <AppCalendar
        title="Calendrier scolaire"
        subtitle="Tous les événements et activités de l'établissement"
        :course-events="[]"
        :events="events"
        :legend="legend"
    />
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { AppCalendar } from '@/Components/UI';
import type { CalEvent } from '@/Components/UI';

const props = defineProps<{
    events: CalEvent[];
}>();

// Légende basée sur les types d'événements présents
const legend = computed(() => {
    const seen = new Map<string, string>();
    props.events.forEach(e => {
        const label = e.extendedProps?.type_label ?? e.type_label ?? '';
        if (label && !seen.has(label)) seen.set(label, e.color);
    });
    return Array.from(seen.entries()).map(([label, color]) => ({ label, color }));
});
</script>
