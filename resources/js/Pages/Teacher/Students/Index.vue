<template>
    <div class="space-y-6">

        <!-- Header -->
        <PageHeader title="Mes apprenants" :subtitle="`${students.total} apprenant(s) au total`" color="primary">
            <template #icon>
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                </svg>
            </template>
        </PageHeader>

        <!-- Résumé par classe -->
        <div v-if="studentsByClass.length" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
            <div
                v-for="cls in studentsByClass"
                :key="cls.class_id"
                class="relative overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700/60
                       bg-white dark:bg-gray-800/60 px-4 py-3 flex items-center gap-3
                       hover:border-primary-400 dark:hover:border-primary-500 hover:shadow-md transition-all duration-200"
            >
                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 text-white text-sm font-bold"
                     :style="{ backgroundColor: avatarColor(cls.class_name) }">
                    {{ cls.class_name?.[0]?.toUpperCase() }}
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-gray-900 dark:text-white truncate">{{ cls.class_name }}</p>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400">
                        <span class="font-bold text-primary-600 dark:text-primary-400">{{ cls.student_count }}</span>
                        élève{{ cls.student_count > 1 ? 's' : '' }}
                    </p>
                </div>
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
                            class="p-1.5 rounded-xl transition-all duration-150
                                   text-white bg-violet-500 hover:bg-violet-600
                                   shadow-sm shadow-violet-200 dark:shadow-violet-900/40">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                    <Link :href="`/chat?receiver_id=${(row as any).id_encoded}`"
                       title="Envoyer un message"
                       class="p-1.5 rounded-xl transition-all duration-150
                              text-white bg-violet-500 hover:bg-violet-600
                              shadow-sm shadow-violet-200 dark:shadow-violet-900/40">
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
                   class="flex items-center gap-2.5 px-3.5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-violet-50 dark:hover:bg-gray-700/60 hover:text-violet-700 transition-colors">
                    <svg class="w-4 h-4 text-violet-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    Envoyer un message
                </Link>
            </template>
        </DataTable>

        <!-- Modal détails apprenant — style settings panel -->
        <DetailModal
            v-model="showDetails"
            :title="detailsTarget ? `${detailsTarget.last_name} ${detailsTarget.name}` : ''"
            :subtitle="detailsTarget?.class_name ?? 'Apprenant'"
            :initials="detailsTarget ? (detailsTarget.last_name?.[0] ?? '') + (detailsTarget.name?.[0] ?? '') : '?'"
            :tabs="studentTabs"
            default-tab="profile"
            size="lg"
        >
            <template #avatar>
                <div class="relative">
                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-xl font-bold text-white shadow-md"
                         :style="{ backgroundColor: avatarColor(detailsTarget?.last_name ?? '') }">
                        {{ detailsTarget?.last_name?.[0]?.toUpperCase() }}{{ detailsTarget?.name?.[0]?.toUpperCase() }}
                    </div>
                    <span :class="['absolute -bottom-1 -right-1 w-4 h-4 rounded-full border-2 border-white dark:border-gray-800 shadow-sm', detailsTarget?.is_online ? 'bg-emerald-400' : 'bg-gray-300']" />
                </div>
            </template>

            <template #sidebar-footer>
                <Link v-if="detailsTarget"
                    :href="`/chat?receiver_id=${detailsTarget.id_encoded}`"
                    class="flex items-center justify-center gap-2 w-full px-3 py-2 rounded-xl bg-primary-600 hover:bg-primary-700 text-white text-xs font-semibold transition-colors shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    Envoyer un message
                </Link>
            </template>

            <template #default="{ activeTab }">
                <div v-if="detailsTarget">
                    <!-- PROFIL -->
                    <div v-show="activeTab === 'profile'" class="space-y-5">
                        <!-- Bannière -->
                        <div class="relative rounded-2xl overflow-hidden p-5"
                             :style="`background: linear-gradient(135deg, ${avatarColor(detailsTarget.last_name)}, ${avatarColor(detailsTarget.last_name)}cc)`">
                            <div class="absolute inset-0 opacity-10" style="background-image:radial-gradient(circle at 80% 20%, white 0%, transparent 60%)"/>
                            <div class="relative flex items-center gap-4">
                                <div class="w-14 h-14 rounded-xl flex items-center justify-center text-lg font-bold text-white ring-4 ring-white/30 shadow-xl"
                                     :style="{ backgroundColor: avatarColor(detailsTarget.last_name) + '80' }">
                                    {{ detailsTarget.last_name?.[0]?.toUpperCase() }}{{ detailsTarget.name?.[0]?.toUpperCase() }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h2 class="text-base font-bold text-white truncate">{{ detailsTarget.last_name }} {{ detailsTarget.name }}</h2>
                                    <p class="text-white/70 text-xs mt-0.5">{{ detailsTarget.email }}</p>
                                    <div class="flex items-center gap-2 mt-2 flex-wrap">
                                        <span :class="['px-2 py-0.5 rounded-full text-xs font-semibold', detailsTarget.status == 1 ? 'bg-emerald-400/30 text-emerald-100' : 'bg-red-400/30 text-red-100']">
                                            {{ detailsTarget.status == 1 ? '✓ Actif' : '✗ Inactif' }}
                                        </span>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-white/10 text-white/80">
                                            <span class="w-1.5 h-1.5 rounded-full" :class="detailsTarget.is_online ? 'bg-emerald-400' : 'bg-gray-400'"/>
                                            {{ detailsTarget.is_online ? 'En ligne' : 'Hors ligne' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Infos grille -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <InfoCard label="Classe" :value="detailsTarget.class_name" highlight />
                            <InfoCard label="N° d'admission" :value="detailsTarget.admission_number" mono />
                            <InfoCard label="N° de rôle" :value="detailsTarget.roll_number" mono />
                            <InfoCard label="Téléphone" :value="detailsTarget.mobile_number" mono />
                            <InfoCard label="Genre" :value="genderLabel(detailsTarget.gender)" />
                            <InfoCard label="Email" :value="detailsTarget.email" />
                        </div>
                    </div>

                    <!-- MÉDICAL -->
                    <div v-show="activeTab === 'medical'" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <InfoCard label="Date de naissance" :value="formatDate(detailsTarget.date_of_birth)" />
                            <InfoCard label="Groupe sanguin" :value="detailsTarget.blood_group?.toUpperCase()" />
                            <InfoCard label="Date d'admission" :value="formatDate(detailsTarget.admission_date)" />
                        </div>
                    </div>
                </div>
            </template>

            <template #footer>
                <AppButton variant="ghost" @click="showDetails = false">Fermer</AppButton>
            </template>
        </DetailModal>

    </div>
</template>

<script setup lang="ts">
import { ref, computed, h, defineComponent } from 'vue';
import { Link } from '@inertiajs/vue3';
import { PageHeader, DataTable, AppBadge, AppButton, DetailModal } from '@/Components/UI';

// ── Composant InfoCard réutilisable ──────────────────────────────────────────
const InfoCard = defineComponent({
    props: {
        label:     { type: String, required: true },
        value:     { type: String, default: '' },
        highlight: { type: Boolean, default: false },
        mono:      { type: Boolean, default: false },
        badge:     { type: String, default: '' },
    },
    setup(p) {
        return () => h('div', {
            class: 'bg-gray-50 dark:bg-gray-800/60 rounded-xl p-4 border border-gray-100 dark:border-gray-700/60',
        }, [
            h('p', { class: 'text-[10px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1.5' }, p.label),
            p.badge
                ? h('span', {
                    class: ['inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold',
                        p.badge === 'success'
                            ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400'
                            : 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400'].join(' '),
                }, [h('span', { class: ['w-1.5 h-1.5 rounded-full', p.badge === 'success' ? 'bg-emerald-500' : 'bg-red-400'].join(' ') }), p.value || '—'])
                : h('p', {
                    class: ['text-sm font-semibold', p.highlight ? 'text-primary-700 dark:text-primary-400' : 'text-gray-800 dark:text-gray-200', p.mono ? 'font-mono' : ''].filter(Boolean).join(' '),
                }, p.value || '—'),
        ]);
    },
});

// ── Onglets du modal de détails ───────────────────────────────────────────────
const studentTabs = [
    {
        id: 'profile',
        label: 'Profil',
        description: 'Informations personnelles',
        icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>',
    },
    {
        id: 'medical',
        label: 'Médical',
        description: 'Santé et dates',
        icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>',
    },
];

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

interface ClassCount {
    class_id:      number;
    class_name:    string;
    student_count: number;
}

const props = defineProps<{
    students: {
        data:  Student[];
        total: number;
        from:  number;
        to:    number;
        links: any[];
    };
    studentsByClass: ClassCount[];
}>();


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
