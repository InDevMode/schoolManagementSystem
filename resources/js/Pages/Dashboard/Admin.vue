<template>
    <div class="space-y-6">

        <!-- ── En-tête ── -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tableau de bord</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    {{ today.toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }) }}
                </p>
            </div>
            <!-- Badge date -->
            <div class="hidden sm:flex items-center gap-2 px-4 py-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-card">
                <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ today.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' }) }}
                </span>
            </div>
        </div>

        <!-- ── 4 grandes stat cards ── -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <BigStatCard
                label="Apprenants"
                :value="totalStudent"
                icon="user-group"
                color="primary"
                trend="+12%"
                href="/admin/student/list"
            />
            <BigStatCard
                label="Professeurs"
                :value="totalTeacher"
                icon="academic-cap"
                color="info"
                trend="+3%"
                href="/admin/teacher/list"
            />
            <BigStatCard
                label="Parents"
                :value="totalParent"
                icon="users"
                color="warning"
                trend="+8%"
                href="/admin/parent/list"
            />
            <BigStatCard
                label="Contributions"
                :value="totalFeesCollections"
                icon="banknotes"
                color="success"
                trend="+21%"
                href="/admin/feescollections/collections/list"
                prefix="FCFA"
            />
        </div>

        <!-- ── Ligne 2 : grille de petites stats ── -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
            <StatCard label="Administrateurs" :value="totalAdmin"    icon="shield"                  color="primary"   href="/admin/admin/list" />
            <StatCard label="Classes"         :value="totalClass"    icon="building-library"        color="secondary" href="/admin/class/list" />
            <StatCard label="Matières"        :value="totalSubject"  icon="book-open"               color="info"      href="/admin/subject/list" />
            <StatCard label="Examens"         :value="totalExam"     icon="clipboard-document-list" color="warning"   href="/admin/examinations/exam/list" />
            <StatCard label="Devoirs"         :value="totalHomework" icon="pencil"                  color="success"   href="/admin/practicalworks/homework/list" />
        </div>

        <!-- ── Ligne RH : Personnel + Congés ── -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <StatCard label="Personnel actif"     :value="totalStaff ?? 0"          icon="user-group"    color="primary"   href="/admin/staff/list" />
            <StatCard label="Congés en attente"   :value="totalPendingLeaves ?? 0"  icon="calendar-days" color="warning"   href="/admin/staff/leaves/list" />
            <StatCard label="Éval. ouvertes"      :value="totalOpenEvals ?? 0"      icon="pencil-square" color="info"      href="/admin/evaluations/list" />
            <StatCard label="Bulletins brouillon" :value="totalDraftBulletins ?? 0" icon="document-text" color="secondary" href="/admin/bulletins/list" />
        </div>

        <!-- ── Présences du jour ── -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <AttendanceBadge label="Présents"    :value="totalAttendanceStudentPresent" color="success" icon="user-check" />
            <AttendanceBadge label="En retard"   :value="totalAttendanceStudentLate"    color="warning" icon="calendar" />
            <AttendanceBadge label="Absents"     :value="totalAttendanceStudentAbsent"  color="danger"  icon="user-check" />
            <AttendanceBadge label="Demi-journée":value="totalAttendanceStudentHalfDay" color="info"    icon="calendar-days" />
        </div>

        <!-- ── Ligne 4 : Événements + Congés actifs ── -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <UpcomingEvents :events="upcomingEvents ?? []" see-all-href="/admin/staff/events/list"/>
            <CurrentLeaves  :leaves="currentLeaves ?? []"  see-all-href="/admin/staff/leaves/list"/>
        </div>

        <!-- ── Ligne 4 : Graphique + Donut ── -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

            <!-- Graphique barres (2/3) -->
            <div class="lg:col-span-2 card p-5">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">Résultats des examens</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Apprenants & Professeurs</p>
                    </div>
                    <div class="flex items-center gap-3 text-xs text-gray-500">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-primary-500 inline-block"/>
                            Professeurs
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-warning-500 inline-block"/>
                            Apprenants
                        </span>
                    </div>
                </div>
                <!-- Graphique SVG simplifié -->
                <div class="relative h-48">
                    <svg viewBox="0 0 600 180" class="w-full h-full" preserveAspectRatio="none">
                        <!-- Grille -->
                        <line x1="0" y1="0"   x2="600" y2="0"   stroke="currentColor" stroke-width="0.5" class="text-gray-100 dark:text-gray-700"/>
                        <line x1="0" y1="45"  x2="600" y2="45"  stroke="currentColor" stroke-width="0.5" class="text-gray-100 dark:text-gray-700"/>
                        <line x1="0" y1="90"  x2="600" y2="90"  stroke="currentColor" stroke-width="0.5" class="text-gray-100 dark:text-gray-700"/>
                        <line x1="0" y1="135" x2="600" y2="135" stroke="currentColor" stroke-width="0.5" class="text-gray-100 dark:text-gray-700"/>
                        <line x1="0" y1="180" x2="600" y2="180" stroke="currentColor" stroke-width="0.5" class="text-gray-100 dark:text-gray-700"/>

                        <!-- Barres Professeurs (violet) -->
                        <g fill="#7B74F0" opacity="0.85">
                            <rect x="18"  y="60"  width="22" height="120" rx="4"/>
                            <rect x="68"  y="40"  width="22" height="140" rx="4"/>
                            <rect x="118" y="20"  width="22" height="160" rx="4"/>
                            <rect x="168" y="50"  width="22" height="130" rx="4"/>
                            <rect x="218" y="10"  width="22" height="170" rx="4"/>
                            <rect x="268" y="35"  width="22" height="145" rx="4"/>
                            <rect x="318" y="55"  width="22" height="125" rx="4"/>
                            <rect x="368" y="25"  width="22" height="155" rx="4"/>
                            <rect x="418" y="45"  width="22" height="135" rx="4"/>
                            <rect x="468" y="15"  width="22" height="165" rx="4"/>
                            <rect x="518" y="30"  width="22" height="150" rx="4"/>
                            <rect x="568" y="50"  width="22" height="130" rx="4"/>
                        </g>
                        <!-- Barres Apprenants (orange) -->
                        <g fill="#f59e0b" opacity="0.75">
                            <rect x="42"  y="80"  width="22" height="100" rx="4"/>
                            <rect x="92"  y="65"  width="22" height="115" rx="4"/>
                            <rect x="142" y="50"  width="22" height="130" rx="4"/>
                            <rect x="192" y="70"  width="22" height="110" rx="4"/>
                            <rect x="242" y="40"  width="22" height="140" rx="4"/>
                            <rect x="292" y="60"  width="22" height="120" rx="4"/>
                            <rect x="342" y="75"  width="22" height="105" rx="4"/>
                            <rect x="392" y="45"  width="22" height="135" rx="4"/>
                            <rect x="442" y="65"  width="22" height="115" rx="4"/>
                            <rect x="492" y="35"  width="22" height="145" rx="4"/>
                            <rect x="542" y="55"  width="22" height="125" rx="4"/>
                            <rect x="592" y="70"  width="22" height="110" rx="4"/>
                        </g>
                    </svg>
                    <!-- Labels mois -->
                    <div class="absolute bottom-0 left-0 right-0 flex justify-between px-2 text-[10px] text-gray-400">
                        <span v-for="m in months" :key="m">{{ m }}</span>
                    </div>
                </div>
            </div>

            <!-- Donut apprenants (1/3) -->
            <div class="card p-5 flex flex-col">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-900 dark:text-white">Apprenants</h3>
                    <button class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-400 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                        </svg>
                    </button>
                </div>
                <!-- Donut SVG -->
                <div class="flex-1 flex items-center justify-center">
                    <div class="relative w-40 h-40">
                        <svg viewBox="0 0 100 100" class="w-full h-full -rotate-90">
                            <!-- Fond -->
                            <circle cx="50" cy="50" r="38" fill="none" stroke="#f3f4f6" stroke-width="14" class="dark:stroke-gray-700"/>
                            <!-- Filles (orange) — 40% -->
                            <circle cx="50" cy="50" r="38" fill="none" stroke="#f59e0b" stroke-width="14"
                                    stroke-dasharray="95.5 143.3" stroke-dashoffset="0" stroke-linecap="round"/>
                            <!-- Garçons (violet) — 60% -->
                            <circle cx="50" cy="50" r="38" fill="none" stroke="#7B74F0" stroke-width="14"
                                    stroke-dasharray="143.3 95.5" stroke-dashoffset="-95.5" stroke-linecap="round"/>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ totalStudent.toLocaleString('fr-FR') }}</span>
                            <span class="text-xs text-gray-400">Total</span>
                        </div>
                    </div>
                </div>
                <!-- Légende -->
                <div class="flex justify-center gap-6 mt-4">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-primary-600 inline-block"/>
                        <span class="text-xs text-gray-600 dark:text-gray-400">Garçons</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-warning-500 inline-block"/>
                        <span class="text-xs text-gray-600 dark:text-gray-400">Filles</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Ligne 5 : Calendrier AppCalendar complet ── -->
        <AppCalendar
            title="Calendrier scolaire"
            subtitle="Cours, événements et activités"
            :course-events="[]"
            :events="calendarEventsFormatted"
        />

    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import StatCard from '@/Components/Dashboard/StatCard.vue';
import BigStatCard from '@/Components/Dashboard/BigStatCard.vue';
import AttendanceBadge from '@/Components/Dashboard/AttendanceBadge.vue';
import UpcomingEvents from '@/Components/Dashboard/UpcomingEvents.vue';
import CurrentLeaves from '@/Components/Dashboard/CurrentLeaves.vue';
import { AppCalendar } from '@/Components/UI';
import type { CalEvent } from '@/Components/UI';

const props = defineProps<{
    totalAdmin: number;
    totalTeacher: number;
    totalStudent: number;
    totalParent: number;
    totalClass: number;
    totalSubject: number;
    totalExam: number;
    totalFeesCollections: number;
    totalAttendance: number;
    totalHomework: number;
    totalAttendanceStudentPresent: number;
    totalAttendanceStudentLate: number;
    totalAttendanceStudentAbsent: number;
    totalAttendanceStudentHalfDay: number;
    totalStaff?: number;
    totalPendingLeaves?: number;
    totalOpenEvals?: number;
    totalDraftBulletins?: number;
    upcomingEvents?: any[];
    currentLeaves?: any[];
    calendarEvents?: any[];
    [key: string]: unknown;
}>();

const today = new Date();
const months = ['Jan','Fév','Mar','Avr','Mai','Jun','Jul','Aoû','Sep','Oct','Nov','Déc'];

// Convertit les événements du backend en CalEvent[] pour AppCalendar
const typeColors: Record<string, string> = {
    academic: '#3b82f6', cultural: '#8b5cf6', administrative: '#f59e0b',
    exam: '#ef4444', ceremony: '#10b981', trip: '#06b6d4',
};
const typeLabels: Record<string, string> = {
    academic: 'Académique', cultural: 'Culturel', administrative: 'Administratif',
    exam: 'Examen', ceremony: 'Cérémonie', trip: 'Sortie',
};

const calendarEventsFormatted = computed<CalEvent[]>(() => {
    if (props.calendarEvents?.length) {
        return (props.calendarEvents as any[]).map(ev => ({
            id:    ev.id,
            title: ev.title,
            start: ev.start ?? ev.event_date,
            color: ev.color ?? typeColors[ev.extendedProps?.type ?? ev.event_type] ?? '#7B74F0',
            start_time:  ev.start_time ?? ev.extendedProps?.start_time ?? '',
            end_time:    ev.end_time   ?? ev.extendedProps?.end_time   ?? '',
            extendedProps: {
                type_label:  typeLabels[ev.extendedProps?.type ?? ev.event_type] ?? 'Événement',
                description: ev.description ?? ev.extendedProps?.description ?? '',
                location:    ev.location    ?? ev.extendedProps?.location    ?? '',
                start_time:  ev.start_time  ?? ev.extendedProps?.start_time  ?? '',
                end_time:    ev.end_time    ?? ev.extendedProps?.end_time    ?? '',
            },
        }));
    }
    // Données de démonstration si pas d'événements backend
    const m = today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2, '0');
    return [
        { id: 1, title: 'Réunion parents',      start: `${m}-03`, color: '#f472b6', start_time: '08:00', end_time: '10:00', extendedProps: { type_label: 'Réunion',   location: 'Salle 1' } },
        { id: 2, title: 'Examen Maths',          start: `${m}-08`, color: '#a78bfa', start_time: '09:00', end_time: '12:00', extendedProps: { type_label: 'Examen',    location: 'Grande salle' } },
        { id: 3, title: 'Sortie scolaire',       start: `${m}-15`, color: '#34d399', start_time: '07:00', end_time: '18:00', extendedProps: { type_label: 'Sortie',    location: 'Extérieur' } },
        { id: 4, title: 'Conseil de classe',     start: `${m}-22`, color: '#fb923c', start_time: '14:00', end_time: '16:00', extendedProps: { type_label: 'Conseil',   location: 'Salle des profs' } },
        { id: 5, title: 'Remise des bulletins',  start: `${m}-28`, color: '#60a5fa', start_time: '10:00', end_time: '12:00', extendedProps: { type_label: 'Cérémonie', location: 'Préau' } },
    ] as CalEvent[];
});
</script>
