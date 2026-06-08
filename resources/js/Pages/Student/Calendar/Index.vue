<template>
    <AppCalendar
        title="Mon Calendrier"
        subtitle="Cours, événements et activités scolaires"
        :course-events="courseEvents"
        :events="events"
        :legend="legend"
    />
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { AppCalendar } from '@/Components/UI';
import type { CalEvent } from '@/Components/UI';
import {
    buildCourseEvents,
    buildCourseLegend,
    type SubjectTimetableRow,
} from '@/Composables/useTimetableEvents';

const props = defineProps<{
    timetable: SubjectTimetableRow[];
    events:    CalEvent[];
}>();

const courseEvents = computed(() => buildCourseEvents(props.timetable));
const legend       = computed(() => buildCourseLegend(props.timetable));
</script>
