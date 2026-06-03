import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { getNavForUserType } from '@/Data/navigation';
import { useCan } from '@/Composables/useCan';
import type { NavItem, PageProps } from '@/types';

export function useNavigation() {
    const page    = usePage<PageProps>();
    const { can, isSuperAdmin } = useCan();

    const user     = computed(() => page.props.auth?.user ?? null);
    const userType = computed(() => user.value?.user_type ?? -1);

    /** Navigation brute selon le user_type (rôles custom >= 5 → adminNav) */
    const baseNav = computed<NavItem[]>(() => getNavForUserType(userType.value));

    /**
     * Navigation filtrée par permissions.
     * - super_admin (user_type=0) : voit tout sans filtrage
     * - Autres : on filtre chaque item et ses enfants selon can(permission)
     * - Un groupe parent est affiché si AU MOINS UN enfant est visible
     */
    const navItems = computed<NavItem[]>(() => {
        if (isSuperAdmin.value) return baseNav.value; // super_admin : tout visible

        return baseNav.value
            .map(item => {
                // Item sans enfants
                if (!item.children) {
                    return can(item.permission) ? item : null;
                }
                // Item avec enfants : filtrer les enfants visibles
                const visibleChildren = item.children.filter(child => can(child.permission));
                if (visibleChildren.length === 0) return null;
                return { ...item, children: visibleChildren };
            })
            .filter((item): item is NavItem => item !== null);
    });

    /** Chemin courant */
    const currentPath = computed<string>(() => {
        try {
            const loc = (page.props as any).ziggy?.location ?? window.location.href;
            return new URL(loc).pathname;
        } catch {
            return window.location.pathname;
        }
    });

    /** Menu de niveau 1 actif */
    const currentMenu = computed<NavItem | null>(() => {
        const path = currentPath.value;
        for (const item of navItems.value) {
            if (item.href && path.startsWith(item.href)) return item;
            if (item.children?.some(c => c.href && path.startsWith(c.href))) return item;
        }
        return null;
    });

    /** Sous-menu actif */
    const currentSubItem = computed<NavItem | null>(() => {
        const path = currentPath.value;
        for (const item of navItems.value) {
            if (item.children) {
                const child = item.children.find(c => c.href && path.startsWith(c.href));
                if (child) return child;
            }
        }
        return null;
    });

    return { navItems, currentMenu, currentSubItem, user, userType };
}
