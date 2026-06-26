<template>
    <div :class="block ? 'w-full' : ''">
        <label v-if="label" :for="selectId" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
            {{ label }}
            <span v-if="required" class="text-danger-500 ml-0.5">*</span>
        </label>
        <div class="relative">
            <select
                :id="selectId"
                v-bind="$attrs"
                :value="modelValue"
                :disabled="disabled"
                :required="required"
                :class="selectClasses"
                @change="$emit('update:modelValue', ($event.target as HTMLSelectElement).value)"
            >
                <option v-if="effectivePlaceholder" value="" disabled :selected="!modelValue || modelValue === ''">{{ effectivePlaceholder }}</option>
                <option
                    v-for="opt in options"
                    :key="opt.value"
                    :value="opt.value"
                    :disabled="opt.disabled"
                >
                    {{ opt.label }}
                </option>
                <slot />
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
        </div>
        <p v-if="error" class="mt-1.5 text-xs text-danger-600 dark:text-danger-400">{{ error }}</p>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import type { SelectOption } from '@/types';

interface Props {
    modelValue?: string | number;
    label?: string;
    options?: SelectOption[];
    placeholder?: string;
    disabled?: boolean;
    required?: boolean;
    error?: string;
    block?: boolean;
    id?: string;
}

const props = withDefaults(defineProps<Props>(), {
    options: () => [],
    block: true,
});

defineEmits<{ 'update:modelValue': [value: string] }>();
defineOptions({ inheritAttrs: false });

const selectId = computed(() => props.id ?? `select-${Math.random().toString(36).slice(2)}`);

/**
 * Placeholder affiché comme première option désactivée.
 * Priorité : prop `placeholder` explicite → généré depuis `label` → valeur par défaut.
 */
const effectivePlaceholder = computed((): string => {
    if (props.placeholder) return props.placeholder;
    if (props.label) {
        const lbl = props.label.toLowerCase().trim();
        // Mots-clés → placeholder contextuel
        if (lbl.includes('classe'))         return 'Sélectionner une classe';
        if (lbl.includes('période'))        return 'Sélectionner une période';
        if (lbl.includes('matière'))        return 'Sélectionner une matière';
        if (lbl.includes('sujet'))          return 'Sélectionner un sujet';
        if (lbl.includes('professeur') || lbl.includes('enseignant')) return 'Sélectionner un professeur';
        if (lbl.includes('apprenant') || lbl.includes('apprenant') || lbl.includes('étudiant')) return 'Sélectionner un apprenant';
        if (lbl.includes('parent'))         return 'Sélectionner un parent';
        if (lbl.includes('rôle') || lbl.includes('role')) return 'Sélectionner un rôle';
        if (lbl.includes('statut') || lbl.includes('status')) return 'Sélectionner un statut';
        if (lbl.includes('type'))           return 'Sélectionner un type';
        if (lbl.includes('année') || lbl.includes('annee')) return 'Sélectionner une année';
        if (lbl.includes('mois'))           return 'Sélectionner un mois';
        if (lbl.includes('sexe') || lbl.includes('genre')) return 'Sélectionner le genre';
        if (lbl.includes('pays'))           return 'Sélectionner un pays';
        if (lbl.includes('ville'))          return 'Sélectionner une ville';
        if (lbl.includes('département') || lbl.includes('service')) return 'Sélectionner un département';
        if (lbl.includes('évaluation') || lbl.includes('evaluation')) return 'Sélectionner une évaluation';
        if (lbl.includes('examen'))         return 'Sélectionner un examen';
        if (lbl.includes('bulletin'))       return 'Sélectionner un bulletin';
        if (lbl.includes('emploi') || lbl.includes('timetable')) return 'Sélectionner un créneau';
        if (lbl.includes('congé') || lbl.includes('conge')) return 'Sélectionner un type de congé';
        if (lbl.includes('groupe'))         return 'Sélectionner un groupe';
        if (lbl.includes('semaine'))        return 'Sélectionner une semaine';
        if (lbl.includes('catégorie') || lbl.includes('categorie')) return 'Sélectionner une catégorie';
        if (lbl.includes('langue'))         return 'Sélectionner une langue';
        if (lbl.includes('permission') || lbl.includes('droit')) return 'Sélectionner une permission';
        // Générique : "Sélectionner + label"
        const article = /^[aeiouéèêëàâùûîïôœæh]/i.test(props.label) ? "Sélectionner l'" : 'Sélectionner le ';
        return `Sélectionner ${props.label.toLowerCase()}`;
    }
    return 'Sélectionner…';
});

const selectClasses = computed(() => [
    'w-full rounded-xl border bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100',
    'pl-3.5 pr-10 py-2.5 text-sm appearance-none',
    'transition-all duration-200',
    'focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent',
    'disabled:opacity-50 disabled:cursor-not-allowed',
    props.error
        ? 'border-danger-500 focus:ring-danger-500'
        : 'border-gray-300 dark:border-gray-600',
]);
</script>
