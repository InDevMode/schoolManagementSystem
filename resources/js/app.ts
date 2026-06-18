import { createApp, h, type DefineComponent } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { createPinia } from 'pinia';
import AppLayout from '@/Layouts/AppLayout.vue';
import '../css/app.css';

createInertiaApp({
    title: (title) => title ? `${title} — SMS` : 'SMS',

    resolve: async (name) => {
        const page = await resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./Pages/**/*.vue'),
        );
        // Layout par défaut sauf si la page définit layout: null
        if (page.default.layout === undefined) {
            page.default.layout = AppLayout;
        }
        return page;
    },

    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(createPinia())
            .mount(el);
    },

    progress: {
        color: '#7B74F0',
    },
});
