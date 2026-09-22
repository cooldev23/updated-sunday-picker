import { createInertiaApp } from '@inertiajs/vue3';
import { createApp, h } from 'vue';
import { initializeTheme } from '@/composables/useAppearance';
import AppLayout from '@/layouts/AppLayout.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { initializeFlashToast } from '@/lib/flashToast';
import { createPinia } from 'pinia';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

void createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });
        const page = pages[`./Pages/${name}.vue`].default;
        
        // Apply your dynamic layouts
        if (name === 'Welcome') {
            page.layout = null;
        } else if (name.startsWith('auth/')) {
            page.layout = AuthLayout;
        } else if (name.startsWith('settings/')) {
            page.layout = [AppLayout, SettingsLayout];
        } else {
            page.layout = page.layout || AppLayout;
        }
        
        return page;
    },
    setup({ el, App, props, plugin }) {
        console.log(el);
        const pinia = createPinia();

        const app = createApp({ render: () => h(App, props) })
            app.use(plugin);
            app.use(pinia);
            app.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

if (!import.meta.env.SSR) {
// This will set light / dark mode on page load...
initializeTheme();

// This will listen for flash toast data from the server...
initializeFlashToast();
}