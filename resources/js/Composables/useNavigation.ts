import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { navByRole } from '@/Data/navigation';
import type { NavItem, PageProps } from '@/types';

export function useNavigation() {
    const page = usePage<PageProps>();

    const user     = computed(() => page.props.auth?.user ?? null);
    const userType = computed(() => user.value?.user_type ?? 0);

    const navItems = computed<NavItem[]>(() => navByRole[userType.value] ?? []);

    /** Chemin courant — sécurisé même sans Ziggy */
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
