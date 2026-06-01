import type { PageProps } from './index';

// Déclare le type global des props Inertia pour que usePage() soit typé
declare module '@inertiajs/vue3' {
    interface PageProps extends import('./index').PageProps {}
}
