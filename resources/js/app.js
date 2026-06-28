import { createApp, h } from 'vue';
import { createInertiaApp, router } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import Swal from 'sweetalert2';
import { registerSW } from 'virtual:pwa-register';

if ('serviceWorker' in navigator) {
    registerSW({ immediate: true });
}

window.Swal = Swal;

const handleFlashMessages = (page) => {
    if (!page || !page.props) return;
    
    const flash = page.props.flash || {};
    const errors = page.props.errors || {};

    if (flash.success) {
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: flash.success,
            timer: 3000,
            showConfirmButton: false,
        });
        flash.success = null;
    }

    if (flash.error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: flash.error,
        });
        flash.error = null;
    }

    if (Object.keys(errors).length > 0) {
        const errorMessages = Object.values(errors).join('\n');
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: errorMessages,
        });
    }
};

createInertiaApp({
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        // Handle initial load
        setTimeout(() => {
            handleFlashMessages(props.initialPage);
        }, 100);

        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mixin({ methods: { route } }) // Ziggy route helper
            .mount(el);
    },
});

router.on('finish', (event) => {
    handleFlashMessages(event.detail.page);
});
