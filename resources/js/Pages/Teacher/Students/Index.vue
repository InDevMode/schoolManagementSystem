<template>
    <div class="space-y-6">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Mes apprenants</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    {{ students.total }} apprenant(s) dans mes classes
                </p>
            </div>
        </div>

        <!-- Table -->
        <DataTable
            ref="tableRef"
            :columns="columns"
            :rows="tableRows"
            row-key="id"
            export-filename="mes-apprenants"
            :context-menu="true"
        >
            <!-- Apprenant -->
            <template #cell-user="{ row }">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full flex-shrink-0 flex items-center justify-center text-xs font-bold text-white"
                         :style="{ backgroundColor: avatarColor(row.last_name as string) }">
                        {{ (row.last_name as string)?.[0]?.toUpperCase() }}{{ (row.name as string)?.[0]?.toUpperCase() }}
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ row.last_name }} {{ row.name }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 font-mono">
                            {{ row.admission_number ?? '—' }}
                        </p>
                    </div>
                </div>
            </template>

            <!-- Classe -->
            <template #cell-class_name="{ row }">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                             bg-primary-50 text-primary-700 border border-primary-200
                             dark:bg-primary-900/20 dark:text-primary-400 dark:border-primary-700">
                    {{ row.class_name ?? '—' }}
                </span>
            </template>

            <!-- Statut -->
            <template #cell-status="{ row }">
                <AppBadge :variant="row.status == 1 ? 'success' : 'danger'" dot>
                    {{ row.status == 1 ? 'Actif' : 'Inactif' }}
                </AppBadge>
            </template>

            <!-- En ligne -->
            <template #cell-online="{ row }">
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full"
                      :class="row.is_online
                        ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-400'
                        : 'bg-gray-100 text-gray-500 border border-gray-200 dark:bg-gray-800 dark:text-gray-400'">
                    <span class="w-2 h-2 rounded-full flex-shrink-0"
                          :class="row.is_online ? 'bg-emerald-500 animate-pulse' : 'bg-gray-400'"/>
                    {{ row.is_online ? 'En ligne' : 'Hors ligne' }}
                </span>
            </template>

            <!-- Actions -->
            <template #actions="{ row }">
                <div class="flex items-center justify-end gap-1.5">
                    <button title="Voir les détails" @click="openDetails(row as any)"
                            class="p-1.5 rounded-lg transition-all duration-150
                                   text-white bg-violet-500 hover:bg-violet-600
                                   shadow-sm shadow-violet-200 dark:shadow-violet-900/40">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                    <Link :href="`/chat?receiver_id=${(row as any).id_encoded}`"
                       title="Envoyer un message"
                       class="p-1.5 rounded-lg transition-all duration-150
                              text-white bg-blue-500 hover:bg-blue-600
                              shadow-sm shadow-blue-200 dark:shadow-blue-900/40">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </Link>
                </div>
            </template>

            <!-- Menu contextuel -->
            <template #context-menu="{ row }">
                <button @click="openDetails(row as any)"
                        class="flex w-full items-center gap-2.5 px-3.5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-violet-50 dark:hover:bg-gray-700/60 hover:text-violet-700 transition-colors">
                    <svg class="w-4 h-4 text-violet-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Voir les détails
                </button>
                <Link :href="`/chat?receiver_id=${(row as any).id_encoded}`"
                   class="flex items-center gap-2.5 px-3.5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700/60 hover:text-blue-700 transition-colors">
                    <svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    Envoyer un message
                </Link>
            </template>
        </DataTable>

        <!-- Drawer détails apprenant -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition-all duration-300 ease-out"
                leave-active-class="transition-all duration-200 ease-in"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div v-if="showDetails" class="fixed inset-0 z-50 flex justify-end" @click.self="showDetails = false">
                    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="showDetails = false"/>
                    <Transition
                        enter-active-class="transition-transform duration-300 ease-out"
                        leave-active-class="transition-transform duration-200 ease-in"
                        enter-from-class="translate-x-full"
                        enter-to-class="translate-x-0"
                        leave-from-class="translate-x-0"
                        leave-to-class="translate-x-full"
                    >
                        <div v-if="showDetails"
                             class="relative w-full max-w-sm bg-white dark:bg-gray-900 h-full shadow-2xl flex flex-col">

                            <!-- Header drawer -->
                            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center text-sm font-bold text-white flex-shrink-0"
                                         :style="{ backgroundColor: avatarColor(detailsTarget?.last_name ?? '') }">
                                        {{ detailsTarget?.last_name?.[0]?.toUpperCase() }}{{ detailsTarget?.name?.[0]?.toUpperCase() }}
                                    </div>
                                    <div>
                                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">
                                            {{ detailsTarget?.last_name }} {{ detailsTarget?.name }}
                                        </h2>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Apprenant</p>
                                    </div>
                                </div>
                                <button @click="showDetails = false"
                                        class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>

                            <!-- Corps drawer -->
                            <div v-if="detailsTarget" class="flex-1 overflow-y-auto px-6 py-5 space-y-4">

                                <!-- Statut + présence -->
                                <div class="flex items-center gap-2 flex-wrap">
                                    <AppBadge :variant="detailsTarget.status == 1 ? 'success' : 'danger'" dot>
                                        {{ detailsTarget.status == 1 ? 'Actif' : 'Inactif' }}
                                    </AppBadge>
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full"
                                          :class="detailsTarget.is_online
                                            ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400'
                                            : 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400'">
                                        <span class="w-1.5 h-1.5 rounded-full"
                                              :class="detailsTarget.is_online ? 'bg-emerald-500 animate-pulse' : 'bg-gray-400'"/>
                                        {{ detailsTarget.is_online ? 'En ligne' : 'Hors ligne' }}
                                    </span>
                                </div>

                                <!-- Champs -->
                                <div class="space-y-3">
                                    <InfoRow label="Classe"          :value="detailsTarget.class_name" highlight />
                                    <InfoRow label="N° d'admission"  :value="detailsTarget.admission_number" mono />
                                    <InfoRow label="N° de rôle"      :value="detailsTarget.roll_number" mono />
                                    <InfoRow label="Email"           :value="detailsTarget.email" />
                                    <InfoRow label="Téléphone"       :value="detailsTarget.mobile_number" mono />
                                    <InfoRow label="Genre"           :value="genderLabel(detailsTarget.gender)" />
                                    <InfoRow label="Date de naissance" :value="formatDate(detailsTarget.date_of_birth)" />
                                    <InfoRow label="Date d'admission"  :value="formatDate(detailsTarget.admission_date)" />
                                    <InfoRow label="Groupe sanguin"  :value="detailsTarget.blood_group?.toUpperCase()" />
                                </div>
                            </div>

                            <!-- Footer drawer -->
                            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                                <Link v-if="detailsTarget"
                                   :href="`/chat?receiver_id=${detailsTarget.id_encoded}`"
                                   class="flex items-center justify-center gap-2 w-full px-4 py-2.5 rounded-lg bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold transition-colors shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                    </svg>
                                    Envoyer un message
                                </Link>
                            </div>
                        </div>
                    </Transition>
                </div>
            </Transition>
        </Teleport>

    </div>
</template>

<script setup lang="ts">
import { ref, computed, h, defineComponent } from 'vue';
import { Link } from '@inertiajs/vue3';
import { DataTable, AppBadge } from '@/Components/UI';

interface Student {
    id:               number;
    name:             string;
    last_name:        string;
    email:            string;
    status:           number;
    gender?:          string;
    class_id?:        number;
    class_name?:      string;
    admission_number?: string;
    roll_number?:      string;
    profile_picture?:  string | null;
    is_online?:        boolean;
    date_of_birth?:    string;
    admission_date?:   string;
    mobile_number?:    string;
    blood_group?:      string;
    id_encoded?:       string;
}

const props = defineProps<{
    students: {
        data:  Student[];
        total: number;
        from:  number;
        to:    number;
        links: any[];
    };
}>();

// ── Composant interne InfoRow ──────────────────────────────────────────────
const InfoRow = defineComponent({
    props: {
        label:     { type: String, required: true },
        value:     { type: String, default: '' },
        highlight: { type: Boolean, default: false },
        mono:      { type: Boolean, default: false },
    },
    setup(p) {
        return () => h('div', { class: 'flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-800 last:border-0' }, [
            h('span', { class: 'text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide' }, p.label),
            h('span', {
                class: [
                    'text-sm font-semibold',
                    p.highlight ? 'text-primary-600 dark:text-primary-400' : 'text-gray-800 dark:text-gray-200',
                    p.mono      ? 'font-mono' : '',
                ].join(' ')
            }, p.value || '—'),
        ]);
    },
});

// ── State ──────────────────────────────────────────────────────────────────

const tableRef     = ref<InstanceType<typeof DataTable> | null>(null);
const showDetails  = ref(false);
const detailsTarget = ref<Student | null>(null);

// ── Table config ───────────────────────────────────────────────────────────

const columns = [
    { key: 'user',       label: 'Apprenant'    },
    { key: 'class_name', label: 'Classe'        },
    { key: 'email',      label: 'Email'         },
    { key: 'status',     label: 'Statut'        },
    { key: 'online',     label: 'Présence'      },
];

const tableRows = computed(() =>
    props.students.data.map(s => ({
        ...s,
        profile_url: s.profile_picture ? `/upload/profile/${s.profile_picture}` : null,
    }))
);

// ── Helpers ────────────────────────────────────────────────────────────────

const AVATAR_COLORS = [
    '#6366f1','#3b82f6','#8b5cf6','#06b6d4','#10b981',
    '#f59e0b','#ef4444','#ec4899','#84cc16','#f97316',
];

const avatarColor = (name: string): string => {
    const code = [...(name ?? '')].reduce((acc, c) => acc + c.charCodeAt(0), 0);
    return AVATAR_COLORS[code % AVATAR_COLORS.length];
};

const genderLabel = (g?: string) => {
    if (g === 'male')   return 'Masculin';
    if (g === 'female') return 'Féminin';
    return g ?? '—';
};

const formatDate = (d?: string) => {
    if (!d) return '—';
    return new Date(d).toLocaleDateString('fr-FR', { day: '2-digit', month: 'long', year: 'numeric' });
};

// ── Actions ────────────────────────────────────────────────────────────────

const openDetails = (student: Student) => {
    detailsTarget.value = student;
    showDetails.value   = true;
};
</script>
