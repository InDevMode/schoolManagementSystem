import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import type { PageProps } from '@/types';

/**
 * useCan — Vérification des permissions Spatie dans Vue
 *
 * Usage :
 *   const { can, canAny, canAll, isSuperAdmin, isAdmin } = useCan();
 *   v-if="can('view.users.students')"
 *   v-if="canAny(['view.fees.collect', 'view.fees.reports'])"
 */
export function useCan() {
    const page = usePage<PageProps>();

    const user = computed(() => page.props.auth?.user ?? null);

    const userType = computed(() => user.value?.user_type ?? -1);

    /** Le super_admin (user_type=0) passe toujours toutes les vérifications */
    const isSuperAdmin = computed(() => userType.value === 0);

    const isAdmin = computed(() => userType.value === 0 || userType.value === 1);

    /** Set des permissions de l'utilisateur (roles + directes) */
    const permissions = computed(() => new Set<string>(user.value?.permissions ?? []));

    /**
     * Vérifie si l'utilisateur a une permission.
     * Le super_admin (user_type=0) a toujours accès.
     * Si permission est undefined/null/vide → accès autorisé.
     */
    const can = (permission?: string | null): boolean => {
        if (!permission) return true;          // pas de restriction → visible
        if (isSuperAdmin.value) return true;   // super_admin → tout voir
        return permissions.value.has(permission);
    };

    /** Retourne true si l'utilisateur a AU MOINS UNE des permissions listées */
    const canAny = (perms: string[]): boolean => {
        if (isSuperAdmin.value) return true;
        return perms.some(p => permissions.value.has(p));
    };

    /** Retourne true si l'utilisateur a TOUTES les permissions listées */
    const canAll = (perms: string[]): boolean => {
        if (isSuperAdmin.value) return true;
        return perms.every(p => permissions.value.has(p));
    };

    /** Vérifie si l'utilisateur a un rôle Spatie */
    const hasRole = (roleName: string): boolean => {
        return user.value?.roles?.includes(roleName) ?? false;
    };

    return { can, canAny, canAll, hasRole, isSuperAdmin, isAdmin, userType, permissions };
}
