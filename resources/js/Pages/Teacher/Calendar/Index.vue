<template>
    <AppCalendar
        title="Mon Calendrier"
        subtitle="Emploi du temps par classe et matière"
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
    COURSE_COLORS,
    type SubjectTimetableRow,
} from '@/Composables/useTimetableEvents';

interface TimetableItem {
    class_name:   string;
    subject_name: string;
    week_id?:     number;
    week_name:    string;
    week_day:     number;   // 1=Lun … 7=Dim
    start_time:   string;
    end_time:     string;
    room_number:  string;
}

const props = defineProps<{
    classTimetable: TimetableItem[];
    events:         CalEvent[];
}>();

/**
 * Convertit le tableau plat classTimetable en SubjectTimetableRow[]
 * (une entrée par "classe – matière" unique, avec weeks reconstituées).
 */
const timetableMatrix = computed<SubjectTimetableRow[]>(() => {
    // Grouper par "classe – matière"
    const map = new Map<string, SubjectTimetableRow>();
    const weekIds = new Map<string, number>(); // week_name → pseudo-id

    // Construire les semaines disponibles depuis les données
    const allWeeks: { week_name: string; day: number }[] = [];
    const seenWeeks = new Set<string>();
    props.classTimetable.forEach(item => {
        if (!seenWeeks.has(item.week_name)) {
            seenWeeks.add(item.week_name);
            allWeeks.push({ week_name: item.week_name, day: item.week_day });
            weekIds.set(item.week_name, allWeeks.length);
        }
    });

    props.classTimetable.forEach(item => {
        const key = `${item.class_name} — ${item.subject_name}`;
        if (!map.has(key)) {
            // Créer l'entrée avec toutes les semaines vides
            map.set(key, {
                name:  key,
                weeks: allWeeks.map(w => ({
                    week_id:     weekIds.get(w.week_name) ?? 0,
                    week_name:   w.week_name,
                    day:         w.day,
                    start_time:  '',
                    end_time:    '',
                    room_number: '',
                })),
            });
        }
        // Remplir le créneau du bon jour
        const entry = map.get(key)!;
        const weekSlot = entry.weeks.find(w => w.week_name === item.week_name);
        if (weekSlot) {
            weekSlot.start_time  = item.start_time;
            weekSlot.end_time    = item.end_time;
            weekSlot.room_number = item.room_number;
        }
    });

    return Array.from(map.values());
});

const courseEvents = computed(() => buildCourseEvents(timetableMatrix.value));

const legend = computed(() =>
    Array.from(new Map(
        timetableMatrix.value.map((s, i) => [s.name, { label: s.name, color: COURSE_COLORS[i % COURSE_COLORS.length] }])
    ).values())
);
</script>
