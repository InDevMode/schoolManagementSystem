<template>
    <div class="space-y-5">

        <!-- ── Header ──────────────────────────────────────────────────── -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ title }}</h1>
                <p v-if="subtitle" class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ subtitle }}</p>
            </div>
            <div class="flex items-center bg-gray-100 dark:bg-gray-800 rounded-xl p-1 gap-1 self-start sm:self-auto">
                <button
                    v-for="v in views"
                    :key="v.key"
                    @click="currentView = v.key"
                    class="px-3.5 py-1.5 rounded-lg text-sm font-medium transition-all duration-150"
                    :class="currentView === v.key
                        ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm'
                        : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'"
                >{{ v.label }}</button>
            </div>
        </div>

        <!-- ── Barre navigation ─────────────────────────────────────────── -->
        <div class="card p-3 flex items-center justify-between gap-4">
            <button @click="prev"
                class="p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700/60 transition-colors text-gray-500 dark:text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            <div class="text-center">
                <h2 class="text-base font-bold text-gray-900 dark:text-white capitalize">{{ navLabel }}</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ currentView === 'month' ? 'Vue mensuelle' : 'Vue hebdomadaire' }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <button @click="goToday"
                    class="px-3 py-1.5 rounded-xl text-xs font-medium bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 hover:bg-primary-200 dark:hover:bg-primary-900/50 transition-colors">
                    Aujourd'hui
                </button>
                <button @click="next"
                    class="p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700/60 transition-colors text-gray-500 dark:text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- ═══ VUE MENSUELLE ════════════════════════════════════════════ -->
        <div v-if="currentView === 'month'" class="card overflow-hidden">
            <div class="grid grid-cols-7 border-b border-gray-200 dark:border-gray-700 bg-gray-50/80 dark:bg-gray-800/60">
                <div v-for="d in DAY_HEADERS" :key="d"
                    class="py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                    {{ d }}
                </div>
            </div>
            <div class="grid grid-cols-7">
                <div
                    v-for="(cell, i) in monthCells" :key="i"
                    class="min-h-[110px] border-b border-r border-gray-100 dark:border-gray-800 p-1.5 transition-colors"
                    :class="[
                        !cell.inMonth ? 'bg-gray-50/40 dark:bg-gray-900/40' : 'bg-white dark:bg-gray-900',
                        cell.isToday  ? '!bg-primary-50/40 dark:!bg-primary-900/10' : '',
                        (i + 1) % 7 === 0 ? '!border-r-0' : '',
                    ]"
                >
                    <div class="flex justify-end mb-1.5">
                        <span class="w-6 h-6 flex items-center justify-center text-xs font-bold rounded-full transition-colors"
                            :class="cell.isToday
                                ? 'bg-primary-600 text-white shadow-sm shadow-primary-300 dark:shadow-primary-900'
                                : cell.inMonth ? 'text-gray-700 dark:text-gray-200' : 'text-gray-400 dark:text-gray-600'"
                        >{{ cell.day }}</span>
                    </div>
                    <div class="space-y-0.5">
                        <div
                            v-for="ev in cell.events.slice(0, 3)" :key="ev.id"
                            @click="openEvent(ev)"
                            class="text-xs px-1.5 py-0.5 rounded-md cursor-pointer truncate font-medium leading-5 transition-all hover:opacity-80"
                            :style="{ backgroundColor: ev.color+'20', color: ev.color, borderLeft: `2px solid ${ev.color}` }"
                        >
                            <span v-if="ev.start_time" class="opacity-60 mr-0.5 font-normal">{{ ev.start_time }}</span>
                            {{ ev.title }}
                        </div>
                        <button v-if="cell.events.length > 3" @click="expandDay(cell)"
                            class="text-xs text-gray-400 dark:text-gray-500 hover:text-primary-500 dark:hover:text-primary-400 pl-1 transition-colors w-full text-left">
                            +{{ cell.events.length - 3 }} de plus
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ═══ VUE HEBDOMADAIRE ══════════════════════════════════════════ -->
        <div v-else class="card overflow-hidden">
            <div class="grid border-b border-gray-200 dark:border-gray-700 bg-gray-50/80 dark:bg-gray-800/60"
                 :style="{ gridTemplateColumns: `64px repeat(7, 1fr)` }">
                <div class="py-3 border-r border-gray-200 dark:border-gray-700"/>
                <div v-for="wd in weekDays" :key="wd.date"
                    class="py-3 text-center border-r border-gray-100 dark:border-gray-700 last:border-r-0 transition-colors"
                    :class="wd.isToday ? 'bg-primary-50/60 dark:bg-primary-900/15' : ''">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ wd.dayName }}</p>
                    <span class="inline-flex items-center justify-center w-7 h-7 mt-1 text-sm font-bold rounded-full transition-colors"
                        :class="wd.isToday ? 'bg-primary-600 text-white shadow-sm' : 'text-gray-700 dark:text-gray-200'"
                    >{{ wd.dayNum }}</span>
                </div>
            </div>
            <div class="overflow-y-auto" style="max-height: 580px;">
                <div v-for="slot in timeSlots" :key="slot"
                    class="grid"
                    :style="{ gridTemplateColumns: `64px repeat(7, 1fr)` }"
                    :class="slot.endsWith(':00') ? 'border-t border-gray-100 dark:border-gray-800' : ''"
                >
                    <div class="px-2 py-0.5 text-right border-r border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 sticky left-0">
                        <span v-if="slot.endsWith(':00')" class="text-xs font-mono text-gray-400 dark:text-gray-600">{{ slot }}</span>
                    </div>
                    <div v-for="wd in weekDays" :key="wd.date+slot"
                        class="relative px-0.5 py-0.5 min-h-[28px] border-r border-gray-100 dark:border-gray-800 last:border-r-0"
                        :class="wd.isToday ? 'bg-primary-50/15 dark:bg-primary-900/5' : 'bg-white dark:bg-gray-900'"
                    >
                        <template v-for="ev in getWeekEvents(wd.date, slot)" :key="ev.id">
                            <div @click="openEvent(ev)"
                                class="rounded-lg px-1.5 py-1 text-xs font-medium cursor-pointer hover:opacity-90 hover:shadow-md transition-all mb-0.5 overflow-hidden"
                                :style="{
                                    backgroundColor: ev.color+'20',
                                    color: ev.color,
                                    borderLeft: `3px solid ${ev.color}`,
                                    minHeight: Math.max(ev.durationSlots ?? 2, 2) * 28 + 'px',
                                }"
                            >
                                <p class="font-semibold truncate leading-tight">{{ ev.title }}</p>
                                <p class="opacity-70 text-[10px] leading-tight mt-0.5">{{ ev.start_time }}–{{ ev.end_time }}</p>
                                <p v-if="ev.room_number" class="opacity-60 text-[10px] leading-tight truncate">Salle {{ ev.room_number }}</p>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Légende + Prochains événements ──────────────────────────── -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <!-- Légende -->
            <div class="card p-4 space-y-3">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Légende</h3>
                <div class="space-y-1.5">
                    <div v-for="item in legend" :key="item.label" class="flex items-center gap-2.5">
                        <span class="w-2.5 h-2.5 rounded-sm flex-shrink-0" :style="{ backgroundColor: item.color }"/>
                        <span class="text-xs text-gray-600 dark:text-gray-400">{{ item.label }}</span>
                    </div>
                    <p v-if="!legend.length" class="text-xs text-gray-400 dark:text-gray-500">Aucune matière configurée.</p>
                </div>
            </div>

            <!-- Prochains événements -->
            <div class="card overflow-hidden lg:col-span-2">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Prochains événements</h3>
                </div>
                <div v-if="upcomingEvents.length" class="divide-y divide-gray-100 dark:divide-gray-700/60">
                    <div v-for="ev in upcomingEvents" :key="ev.id"
                        @click="openEvent(ev)"
                        class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-800/40 cursor-pointer transition-colors">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0"
                            :style="{ backgroundColor: ev.color+'20' }">
                            <span class="w-2 h-2 rounded-full" :style="{ backgroundColor: ev.color }"/>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ ev.title }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ formatEventDate(ev.start) }}<span v-if="ev.start_time"> · {{ ev.start_time }}</span>
                            </p>
                        </div>
                        <span class="text-xs font-medium px-2.5 py-1 rounded-full flex-shrink-0"
                            :style="{ backgroundColor: ev.color+'20', color: ev.color }">
                            {{ ev.extendedProps?.type_label ?? ev.type_label ?? '—' }}
                        </span>
                    </div>
                </div>
                <div v-else class="p-8 text-center">
                    <p class="text-sm text-gray-400 dark:text-gray-500">Aucun événement à venir.</p>
                </div>
            </div>
        </div>

        <!-- ── Drawer détail ────────────────────────────────────────────── -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition-all duration-200 ease-out"
                leave-active-class="transition-all duration-150 ease-in"
                enter-from-class="opacity-0" enter-to-class="opacity-100"
                leave-from-class="opacity-100" leave-to-class="opacity-0"
            >
                <div v-if="selectedEvent" class="fixed inset-0 z-50 flex items-center justify-center p-4" @click.self="selectedEvent = null">
                    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="selectedEvent = null"/>
                    <div class="relative w-full max-w-sm bg-white dark:bg-gray-900 rounded-2xl shadow-2xl overflow-hidden z-10">
                        <div class="h-1.5 w-full" :style="{ backgroundColor: selectedEvent.color }"/>
                        <div class="p-5 space-y-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide mb-1" :style="{ color: selectedEvent.color }">
                                        {{ selectedEvent.extendedProps?.type_label ?? selectedEvent.type_label ?? 'Événement' }}
                                    </p>
                                    <h3 class="text-base font-bold text-gray-900 dark:text-white">{{ selectedEvent.title }}</h3>
                                </div>
                                <button @click="selectedEvent = null"
                                    class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="space-y-2.5">
                                <div class="flex items-center gap-2.5 text-sm text-gray-600 dark:text-gray-400">
                                    <svg class="w-4 h-4 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ formatEventDate(selectedEvent.start) }}
                                </div>
                                <div v-if="selectedEvent.start_time" class="flex items-center gap-2.5 text-sm text-gray-600 dark:text-gray-400">
                                    <svg class="w-4 h-4 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ selectedEvent.start_time }}<template v-if="selectedEvent.end_time"> – {{ selectedEvent.end_time }}</template>
                                </div>
                                <div v-if="selectedEvent.room_number || selectedEvent.extendedProps?.location"
                                    class="flex items-center gap-2.5 text-sm text-gray-600 dark:text-gray-400">
                                    <svg class="w-4 h-4 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    {{ selectedEvent.room_number || selectedEvent.extendedProps?.location }}
                                </div>
                                <p v-if="selectedEvent.extendedProps?.description"
                                    class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed pt-1 border-t border-gray-100 dark:border-gray-700">
                                    {{ selectedEvent.extendedProps.description }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>

    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';

// ── Types ──────────────────────────────────────────────────────────────────

export interface CalEvent {
    id:            string | number;
    title:         string;
    start:         string;          // 'YYYY-MM-DD'
    color:         string;
    start_time?:   string;
    end_time?:     string;
    room_number?:  string;
    type_label?:   string;
    durationSlots?: number;
    extendedProps?: {
        type_label?:  string;
        description?: string;
        location?:    string;
        start_time?:  string;
        end_time?:    string;
    };
}

interface LegendItem { label: string; color: string; }

interface MonthCell {
    day: number; date: string; inMonth: boolean; isToday: boolean;
    events: CalEvent[];
}

// ── Props ──────────────────────────────────────────────────────────────────

const props = withDefaults(defineProps<{
    /** Événements cours/EDT déjà normalisés en CalEvent[] */
    courseEvents: CalEvent[];
    /** Événements ponctuels de l'école */
    events:       CalEvent[];
    title?:       string;
    subtitle?:    string;
    legend?:      LegendItem[];
}>(), {
    title:    'Calendrier',
    subtitle: 'Cours, événements et activités scolaires',
    legend:   () => [],
});

// ── State ──────────────────────────────────────────────────────────────────

const currentView   = ref<'month' | 'week'>('month');
const cursor        = ref(new Date());
const selectedEvent = ref<CalEvent | null>(null);

const views = [
    { key: 'month' as const, label: 'Mois'    },
    { key: 'week'  as const, label: 'Semaine' },
];

// ── Navigation ─────────────────────────────────────────────────────────────

const prev = () => {
    const d = new Date(cursor.value);
    currentView.value === 'month' ? d.setMonth(d.getMonth() - 1) : d.setDate(d.getDate() - 7);
    cursor.value = d;
};
const next = () => {
    const d = new Date(cursor.value);
    currentView.value === 'month' ? d.setMonth(d.getMonth() + 1) : d.setDate(d.getDate() + 7);
    cursor.value = d;
};
const goToday = () => { cursor.value = new Date(); };

const navLabel = computed(() => {
    if (currentView.value === 'month')
        return cursor.value.toLocaleDateString('fr-FR', { month: 'long', year: 'numeric' });
    const mon = mondayOfWeek(cursor.value);
    const sun = new Date(mon); sun.setDate(sun.getDate() + 6);
    return `${mon.getDate()} – ${sun.getDate()} ${sun.toLocaleDateString('fr-FR', { month: 'long', year: 'numeric' })}`;
});

// ── Helpers date ───────────────────────────────────────────────────────────

const toYMD = (d: Date) =>
    `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;

const todayYMD = toYMD(new Date());

const mondayOfWeek = (d: Date): Date => {
    const c = new Date(d);
    const day = c.getDay();
    c.setDate(c.getDate() - (day === 0 ? 6 : day - 1));
    c.setHours(0, 0, 0, 0);
    return c;
};

// ── Tous les événements pour une plage ────────────────────────────────────

const allEventsForRange = (from: Date, to: Date): CalEvent[] => {
    const fromYMD = toYMD(from), toYMD_ = toYMD(to);
    const courses = props.courseEvents.filter(e => e.start >= fromYMD && e.start <= toYMD_);
    const ponct   = props.events.filter(e => {
        const d = new Date(e.start);
        return d >= from && d <= to;
    }).map(e => ({
        ...e,
        start_time:    e.extendedProps?.start_time ?? e.start_time ?? '',
        end_time:      e.extendedProps?.end_time   ?? e.end_time   ?? '',
        durationSlots: durationInSlots(
            e.extendedProps?.start_time ?? e.start_time ?? '',
            e.extendedProps?.end_time   ?? e.end_time   ?? ''
        ),
    }));
    return [...courses, ...ponct];
};

const durationInSlots = (s: string, e: string): number => {
    if (!s || !e) return 2;
    const [sh, sm] = s.split(':').map(Number);
    const [eh, em] = e.split(':').map(Number);
    return Math.max(1, Math.round(((eh*60+em)-(sh*60+sm))/30));
};

// ── VUE MENSUELLE ──────────────────────────────────────────────────────────

const DAY_HEADERS = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];

const monthCells = computed<MonthCell[]>(() => {
    const yr = cursor.value.getFullYear(), mo = cursor.value.getMonth();
    const first = new Date(yr, mo, 1), last = new Date(yr, mo+1, 0);

    const startCell = new Date(first);
    startCell.setDate(startCell.getDate() - ((first.getDay()===0?7:first.getDay())-1));
    const endCell = new Date(last);
    endCell.setDate(endCell.getDate() + (last.getDay()===0?0:7-last.getDay()));

    const allEv = allEventsForRange(startCell, endCell);
    const cells: MonthCell[] = [];
    const cur = new Date(startCell);

    while (cur <= endCell) {
        const ymd = toYMD(cur);
        cells.push({
            day:     cur.getDate(),
            date:    ymd,
            inMonth: cur.getMonth() === mo,
            isToday: ymd === todayYMD,
            events:  allEv.filter(e => e.start === ymd)
                         .sort((a,b) => (a.start_time??'').localeCompare(b.start_time??'')),
        });
        cur.setDate(cur.getDate()+1);
    }
    return cells;
});

// ── VUE HEBDOMADAIRE ───────────────────────────────────────────────────────

const DAY_NAMES = ['Lun','Mar','Mer','Jeu','Ven','Sam','Dim'];

const weekDays = computed(() => {
    const mon = mondayOfWeek(cursor.value);
    return Array.from({length:7}, (_,i) => {
        const d = new Date(mon); d.setDate(d.getDate()+i);
        return { date: toYMD(d), dayName: DAY_NAMES[i], dayNum: d.getDate(), isToday: toYMD(d)===todayYMD };
    });
});

const timeSlots = computed(() => {
    const s: string[] = [];
    for (let h = 7; h < 21; h++) {
        s.push(`${String(h).padStart(2,'0')}:00`);
        s.push(`${String(h).padStart(2,'0')}:30`);
    }
    return s;
});

const weekEventsAll = computed(() => {
    const mon = mondayOfWeek(cursor.value);
    const sun = new Date(mon); sun.setDate(sun.getDate()+6);
    return allEventsForRange(mon, sun);
});

const getWeekEvents = (date: string, slot: string): CalEvent[] =>
    weekEventsAll.value.filter(e => e.start === date && e.start_time === slot);

// ── Prochains événements ──────────────────────────────────────────────────

const upcomingEvents = computed(() =>
    props.events
        .filter(e => new Date(e.start) >= new Date(todayYMD))
        .slice(0, 5)
);

// ── Légende dynamique ─────────────────────────────────────────────────────

const legend = computed<LegendItem[]>(() => {
    const items = [...props.legend];
    const seen  = new Set(items.map(i => i.label));
    props.events.forEach(e => {
        const lbl = e.extendedProps?.type_label ?? e.type_label ?? '';
        if (lbl && !seen.has(lbl)) { seen.add(lbl); items.push({ label: lbl, color: e.color }); }
    });
    return items;
});

// ── Misc ───────────────────────────────────────────────────────────────────

const formatEventDate = (d: string) =>
    new Date(d).toLocaleDateString('fr-FR', { weekday:'long', day:'numeric', month:'long' });

const openEvent  = (ev: CalEvent) => { selectedEvent.value = ev; };
const expandDay  = (cell: MonthCell) => { cursor.value = new Date(cell.date); currentView.value = 'week'; };
</script>
