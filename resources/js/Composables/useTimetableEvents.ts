/**
 * useTimetableEvents
 * Convertit la matrice EDT (SubjectTimetable[]) en CalEvent[] récurrents
 * pour toutes les dates d'une plage donnée.
 */
import type { CalEvent } from '@/Components/UI/AppCalendar.vue';

export interface SubjectTimetableRow {
    name:  string;
    weeks: {
        week_id:     number;
        week_name:   string;
        day:         number;   // 1=Lun … 7=Dim (ISO)
        start_time:  string;
        end_time:    string;
        room_number: string;
    }[];
}

export const COURSE_COLORS = [
    '#6366f1','#3b82f6','#8b5cf6','#06b6d4','#10b981',
    '#f59e0b','#ef4444','#ec4899','#84cc16','#f97316',
];

const toYMD = (d: Date) =>
    `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;

const isoDay = (d: Date) => { const n = d.getDay(); return n === 0 ? 7 : n; };

const durationInSlots = (s: string, e: string): number => {
    if (!s || !e) return 2;
    const [sh, sm] = s.split(':').map(Number);
    const [eh, em] = e.split(':').map(Number);
    return Math.max(1, Math.round(((eh*60+em)-(sh*60+sm))/30));
};

/**
 * Génère les occurrences de cours pour une plage [from, to].
 * Utilisé par le composant AppCalendar via courseEvents prop.
 */
export function buildCourseEventsForRange(
    timetable: SubjectTimetableRow[],
    from: Date,
    to: Date
): CalEvent[] {
    const out: CalEvent[] = [];

    timetable.forEach((subject, si) => {
        const color = COURSE_COLORS[si % COURSE_COLORS.length];
        subject.weeks.forEach(w => {
            if (!w.start_time || !w.day) return;
            const cur = new Date(from);
            while (cur <= to) {
                if (isoDay(cur) === Number(w.day)) {
                    out.push({
                        id:           `course-${subject.name}-${w.week_id}-${toYMD(cur)}`,
                        title:        subject.name,
                        start:        toYMD(cur),
                        color,
                        start_time:   w.start_time,
                        end_time:     w.end_time,
                        room_number:  w.room_number,
                        type_label:   'Cours',
                        durationSlots: durationInSlots(w.start_time, w.end_time),
                    });
                }
                cur.setDate(cur.getDate()+1);
            }
        });
    });

    return out;
}

/**
 * Pré-génère les cours sur 3 mois glissants (mois précédent + 2 suivants).
 * Retourne un CalEvent[] prêt à passer à AppCalendar.
 */
export function buildCourseEvents(timetable: SubjectTimetableRow[]): CalEvent[] {
    const from = new Date(); from.setMonth(from.getMonth() - 1); from.setDate(1);
    const to   = new Date(); to.setMonth(to.getMonth() + 2);   to.setDate(31);
    return buildCourseEventsForRange(timetable, from, to);
}

/**
 * Construit la liste de légende pour les cours.
 */
export function buildCourseLegend(timetable: SubjectTimetableRow[]) {
    return timetable.map((s, i) => ({
        label: s.name,
        color: COURSE_COLORS[i % COURSE_COLORS.length],
    }));
}
