<template>
    <div class="card p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-900 dark:text-white text-sm">Personnel en congé</h3>
            <a v-if="seeAllHref" :href="seeAllHref" class="text-xs text-primary-600 dark:text-primary-400 hover:underline">
                Gérer les congés
            </a>
        </div>

        <div v-if="leaves.length" class="flex flex-col gap-2">
            <div v-for="l in leaves" :key="l.id"
                class="flex items-center gap-3 p-2.5 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                <!-- Avatar -->
                <div class="w-8 h-8 rounded-full flex-shrink-0 flex items-center justify-center text-white text-xs font-bold"
                    :style="{ background: l.color ?? '#6366f1' }">
                    {{ (l.last_name?.[0] ?? l.name?.[0] ?? '?').toUpperCase() }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-gray-900 dark:text-white truncate">{{ l.last_name }} {{ l.name }}</p>
                    <p class="text-[10px] text-gray-400">
                        {{ l.leave_type_name }}
                        <template v-if="l.end_date"> · jusqu'au {{ formatDate(l.end_date) }}</template>
                        <template v-else> · durée indéterminée</template>
                    </p>
                </div>
                <!-- Badge rôle -->
                <span class="text-[9px] font-semibold px-1.5 py-0.5 rounded bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-300 flex-shrink-0">
                    {{ roleLabels[l.role] ?? l.role }}
                </span>
            </div>
        </div>

        <div v-else class="text-center py-4">
            <p class="text-xs text-gray-400">Aucun membre en congé actuellement</p>
        </div>
    </div>
</template>

<script setup lang="ts">
defineProps<{
    leaves:      any[];
    seeAllHref?: string;
}>();

const roleLabels: Record<string, string> = {
    teacher: 'Prof', director: 'Dir.', accountant: 'Compt.',
    supervisor: 'Surv.', secretary: 'Secr.', other: 'Autre',
};

const formatDate = (d: string) => d ? new Date(d).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short' }) : '—';
</script>
