import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap";
import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/inertia-vue3";
import { createPinia } from "pinia";
import "toastr/build/toastr.min.css";
import toastr from "toastr";
import "../css/app.css";

window.toastr = toastr;

toastr.options = {
    positionClass: "toast-top-right",
    newestOnTop: true,
    progressBar: true,
    closeButton: true,
    timeOut: 3000,
    offset: 40,
};

const appName = import.meta.env.VITE_APP_NAME || "Laravel";

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => {
        const pages = import.meta.glob("./Pages/**/*.vue", { eager: true });
        return pages[`./Pages/${name}.vue`];
    },
    setup({ el, App, props, plugin }) {
        const vueApp = createApp({ render: () => h(App, props) });
        vueApp.use(plugin);
        vueApp.use(createPinia());
        vueApp.mount(el);
    },
});
