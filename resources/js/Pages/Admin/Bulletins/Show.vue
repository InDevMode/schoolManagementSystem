<template>
    <div class="space-y-6">

        <!-- En-tête actions -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Bulletin scolaire</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    {{ detail.bulletin?.student_last_name }} {{ detail.bulletin?.student_name }} — {{ detail.bulletin?.period_name }}
                </p>
            </div>
            <div class="flex gap-2">
                <AppButton variant="secondary" @click="printBulletin">
                    <template #icon>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                    </template>
                    Imprimer
                </AppButton>
                <AppButton v-if="detail.bulletin?.status === 'draft'" variant="success" :loading="publishing" @click="publish">
                    Publier
                </AppButton>
            </div>
        </div>

        <!-- Prévisualisation du bulletin style béninois -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden max-w-4xl mx-auto">

            <!-- En-tête République du Bénin -->
            <div class="bg-gradient-to-r from-green-700 via-yellow-500 to-red-600 p-1"/>
            <div class="px-8 py-6 border-b border-gray-100 dark:border-gray-700">
                <div class="flex items-start justify-between">
                    <!-- Logo école -->
                    <div class="flex flex-col items-center gap-2">
                        <img v-if="settings?.logo_url" :src="settings.logo_url" alt="Logo" class="w-16 h-16 object-contain"/>
                        <div v-else class="w-16 h-16 rounded-lg bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                            <svg class="w-8 h-8 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Infos école -->
                    <div class="flex-1 text-center px-4">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-widest">République du Bénin</p>
                        <p class="text-xs text-gray-400">Ministère des Enseignements Secondaire, Technique et de la Formation Professionnelle</p>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white mt-2 uppercase">
                            {{ settings?.school_name ?? 'École' }}
                        </h2>
                        <p class="text-xs text-gray-500 mt-1">Travail — Discipline — Réussite</p>
                        <div class="mt-3 inline-flex items-center px-4 py-1.5 rounded-full bg-primary-600 text-white text-sm font-semibold">
                            BULLETIN {{ periodTypeLabel }} — {{ detail.bulletin?.school_year ?? '' }}
                        </div>
                    </div>

                    <!-- Photo élève -->
                    <div class="flex flex-col items-center gap-1">
                        <div class="w-20 h-24 rounded-lg border-2 border-gray-200 dark:border-gray-600 overflow-hidden bg-gray-50 dark:bg-gray-700">
                            <img v-if="studentPhotoUrl" :src="studentPhotoUrl" alt="Photo" class="w-full h-full object-cover"/>
                            <div v-else class="w-full h-full flex items-center justify-center">
                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-[10px] text-gray-400">Photo</p>
                    </div>
                </div>

                <!-- Infos élève -->
                <div class="mt-6 grid grid-cols-2 gap-x-8 gap-y-2 text-sm">
                    <div class="flex gap-2">
                        <span class="text-gray-500 dark:text-gray-400 w-36 flex-shrink-0">Nom et Prénom :</span>
                        <span class="font-semibold text-gray-900 dark:text-white">
                            {{ detail.bulletin?.student_last_name }} {{ detail.bulletin?.student_name }}
                        </span>
                    </div>
                    <div class="flex gap-2">
                        <span class="text-gray-500 dark:text-gray-400 w-36 flex-shrink-0">Classe :</span>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ detail.bulletin?.class_name }}</span>
                    </div>
                    <div class="flex gap-2">
                        <span class="text-gray-500 dark:text-gray-400 w-36 flex-shrink-0">Matricule :</span>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ detail.bulletin?.admission_number ?? '—' }}</span>
                    </div>
                    <div class="flex gap-2">
                        <span class="text-gray-500 dark:text-gray-400 w-36 flex-shrink-0">{{ periodTypeLabel }} :</span>
                        <span class="font-semibold text-gray-900 dark:text-white">
                            {{ detail.bulletin?.order_number }}ᵉ {{ detail.bulletin?.period_type === 'semestre' ? 'Semestre' : 'Trimestre' }}
                        </span>
                    </div>
                    <div class="flex gap-2">
                        <span class="text-gray-500 dark:text-gray-400 w-36 flex-shrink-0">Année scolaire :</span>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ detail.bulletin?.school_year ?? '—' }}</span>
                    </div>
                </div>
            </div>

            <!-- Tableau des matières -->
            <div class="px-8 py-4">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-primary-600 text-white">
                            <th class="px-4 py-2.5 text-left rounded-tl-lg">Matières</th>
                            <th class="px-4 py-2.5 text-center">Notes</th>
                            <th class="px-4 py-2.5 text-center">Coef.</th>
                            <th class="px-4 py-2.5 text-center">Moyenne</th>
                            <th class="px-4 py-2.5 text-left rounded-tr-lg">Appréciations</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(sub, i) in detail.subjects" :key="sub.subject_id"
                            :class="[i % 2 === 0 ? 'bg-white dark:bg-gray-800' : 'bg-gray-50 dark:bg-gray-700/50']">
                            <td class="px-4 py-2.5 font-medium text-gray-900 dark:text-white">{{ sub.subject_name }}</td>
                            <td class="px-4 py-2.5 text-center text-gray-600 dark:text-gray-400">
                                {{ sub.average ? Number(sub.average).toFixed(2) + ' / 20' : '—' }}
                            </td>
                            <td class="px-4 py-2.5 text-center font-bold text-primary-600 dark:text-primary-400">{{ sub.coefficient }}</td>
                            <td class="px-4 py-2.5 text-center">
                                <span class="font-bold" :class="avgClass(Number(sub.average))">
                                    {{ sub.average ? Number(sub.average).toFixed(2) : '—' }}
                                </span>
                            </td>
                            <td class="px-4 py-2.5 text-gray-600 dark:text-gray-400 italic">{{ sub.appreciation ?? '—' }}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="border-t-2 border-gray-200 dark:border-gray-600 font-semibold">
                            <td class="px-4 py-2.5 text-gray-700 dark:text-gray-300">Total des Coefficients</td>
                            <td/>
                            <td class="px-4 py-2.5 text-center text-primary-600 dark:text-primary-400 font-bold">{{ totalCoeff }}</td>
                            <td/>
                            <td/>
                        </tr>
                        <tr class="font-semibold">
                            <td class="px-4 py-2.5 text-gray-700 dark:text-gray-300">Total des Points</td>
                            <td/>
                            <td/>
                            <td class="px-4 py-2.5 text-center text-primary-600 dark:text-primary-400 font-bold">{{ totalPoints }}</td>
                            <td/>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Résumé général -->
            <div class="mx-8 mb-4 p-4 rounded-lg bg-primary-600 text-white">
                <div class="flex items-center justify-between flex-wrap gap-3">
                    <div>
                        <p class="text-xs opacity-80 uppercase tracking-wider">Moyenne Générale</p>
                        <p class="text-3xl font-black">
                            {{ detail.bulletin?.average ? Number(detail.bulletin.average).toFixed(2) : '—' }} / 20
                        </p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs opacity-80 uppercase tracking-wider">Rang</p>
                        <p class="text-2xl font-bold">
                            {{ detail.bulletin?.rank ? `${detail.bulletin.rank}ᵉ sur ${detail.bulletin.total_students}` : '—' }}
                        </p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs opacity-80 uppercase tracking-wider">Taux de réussite</p>
                        <p class="text-2xl font-bold">
                            {{ detail.bulletin?.class_success_rate ? detail.bulletin.class_success_rate + '%' : '—' }}
                        </p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs opacity-80 uppercase tracking-wider">Appréciation</p>
                        <p class="text-xl font-bold">{{ detail.bulletin?.appreciation ?? '—' }}</p>
                    </div>
                </div>
            </div>

            <!-- Commentaire du prof + signatures -->
            <div class="px-8 pb-6">
                <!-- Commentaire -->
                <div class="mb-6">
                    <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Appréciation générale</p>
                    <div v-if="!editingComment" class="flex items-start gap-3">
                        <p class="text-sm text-gray-700 dark:text-gray-300 italic flex-1 min-h-[2rem]">
                            {{ detail.bulletin?.teacher_comment || 'Aucun commentaire saisi.' }}
                        </p>
                        <button class="text-xs text-primary-500 hover:underline flex-shrink-0" @click="editingComment = true">
                            Modifier
                        </button>
                    </div>
                    <div v-else class="flex gap-2">
                        <textarea v-model="comment" rows="2"
                            class="flex-1 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-transparent px-3 py-2 dark:text-gray-300"/>
                        <div class="flex flex-col gap-1">
                            <AppButton size="sm" :loading="savingComment" @click="saveComment">OK</AppButton>
                            <AppButton size="sm" variant="ghost" @click="editingComment = false">Annuler</AppButton>
                        </div>
                    </div>
                </div>

                <!-- Signatures -->
                <div class="grid grid-cols-2 gap-8 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <div class="text-center">
                        <p class="text-xs font-semibold text-gray-600 dark:text-gray-400">Le Professeur Principal</p>
                        <div class="mt-6 border-b border-gray-300 dark:border-gray-600"/>
                    </div>
                    <div class="text-center">
                        <p class="text-xs font-semibold text-gray-600 dark:text-gray-400">Le Directeur</p>
                        <div class="mt-6 border-b border-gray-300 dark:border-gray-600"/>
                    </div>
                </div>
            </div>

            <!-- Bande couleur bas -->
            <div class="bg-gradient-to-r from-green-700 via-yellow-500 to-red-600 p-1"/>
        </div>

        <!-- Statut -->
        <div class="flex justify-center">
            <AppBadge :variant="detail.bulletin?.status === 'published' ? 'success' : 'secondary'" class="text-sm" dot>
                {{ detail.bulletin?.status === 'published' ? 'Bulletin publié — visible par l\'élève et ses parents' : 'Brouillon — non visible par l\'élève' }}
            </AppBadge>
        </div>

    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { AppButton, AppBadge } from '@/Components/UI';
import { useToast } from '@/Composables/useToast';
import axios from 'axios';

const toast = useToast();

const props = defineProps<{
    detail:   { bulletin: any; subjects: any[] };
    settings: any;
}>();

const publishing    = ref(false);
const editingComment = ref(false);
const savingComment  = ref(false);
const comment = ref(props.detail.bulletin?.teacher_comment ?? '');

const periodTypeLabel = computed(() =>
    props.detail.bulletin?.period_type === 'semestre' ? 'BULLETIN SEMESTRIEL' : 'BULLETIN TRIMESTRIEL'
);

const studentPhotoUrl = computed(() => {
    const pic = props.detail.bulletin?.profile_picture;
    if (!pic) return null;
    return `/upload/profile/${pic}`;
});

const totalCoeff = computed(() =>
    (props.detail.subjects ?? []).reduce((s: number, sub: any) => s + (Number(sub.coefficient) || 0), 0)
);
const totalPoints = computed(() =>
    (props.detail.subjects ?? []).reduce((s: number, sub: any) => s + (Number(sub.weighted_points) || 0), 0).toFixed(2)
);

const publish = () => {
    publishing.value = true;
    router.post(`/admin/bulletins/publish/${props.detail.bulletin.id}`, {}, {
        onFinish: () => { publishing.value = false; },
        onSuccess: () => toast.success('Bulletin publié avec succès.'),
    });
};

const printBulletin = () => {
    window.open(`/admin/bulletins/print/${props.detail.bulletin.id}`, '_blank');
};

const saveComment = async () => {
    savingComment.value = true;
    try {
        await axios.post(`/admin/bulletins/comment/${props.detail.bulletin.id}`, { teacher_comment: comment.value });
        toast.success('Commentaire enregistré.');
        editingComment.value = false;
    } catch {
        toast.error('Erreur lors de l\'enregistrement.');
    } finally {
        savingComment.value = false;
    }
};

const avgClass = (avg: number) => {
    if (!avg) return 'text-gray-400';
    if (avg >= 14) return 'text-success-600 dark:text-success-400';
    if (avg >= 10) return 'text-warning-600 dark:text-warning-400';
    return 'text-danger-600 dark:text-danger-400';
};
</script>
