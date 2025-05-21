import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import Toast, { POSITION, TYPE } from "vue-toastification";
import "vue-toastification/dist/index.css";
import H1 from '@/Components/H1.vue';
import P from '@/Components/P.vue';
import {Link, Head} from '@inertiajs/vue3';
// Vuetify
import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import PrimeVue from "primevue/config";
import Aura from "@primeuix/themes/aura";
import { formatPrice } from './utils/format';
import { formatDate } from './utils/formatDate';
const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
const vuetify = createVuetify({
    components,
    directives,
});
createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(PrimeVue, {

                theme: {
                    preset: Aura,
                },
            })
            .component('formatDate', formatDate)
            .component('formatPrice', formatPrice)
            .component('H1', H1)
            .component('P', P)
            .component('Head', Head)
            .component('Link', Link)
            .use(vuetify)
            .use(Toast, {
                position: POSITION.TOP_RIGHT,
                timeout: 5000,
                closeOnClick: true,
                pauseOnFocusLoss: true,
                pauseOnHover: true,
                draggable: true,
                draggablePercent: 0.6,
                showCloseButtonOnHover: false,
                hideProgressBar: false,
                closeButton: "button",
                icon: true,
                rtl: false,
                transition: "Vue-Toastification__bounce",
                maxToasts: 5,
                newestOnTop: true,
            })
            .mount(el);
    },

});

