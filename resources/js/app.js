import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import "@mdi/font/css/materialdesignicons.css";

// Vuetify
import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'

//konva
import VueKonva from 'vue-konva';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

const light = {
    dark: false,
    colors: {
        background: '#bebebe',
    },
}

const vuetify = createVuetify({
    components,
    directives,
    theme: {
        light: 'light',
        themes: {
            light,
        },
    },
})

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(vuetify)
            .use(VueKonva)
            .mixin({ methods: { route } })
            .mount(el);
    },
    progress: {
        color: '#000000',
    },
});
