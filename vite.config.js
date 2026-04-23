import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/scss/app.scss',
                'resources/js/app.js',
                'resources/js/admin/dashboard.js',
                'resources/js/admin/orders.js',
                'resources/js/admin/order-detail.js',
                'resources/js/admin/product-detail.js',
            ],
            refresh: true,
        }),
    ],
    server: {
        host: true,
        port: 5173,
        hmr: {
            host: 'localhost',
            port: 5173,
        },
        watch: {
            usePolling: true,
            interval: 1000,
        },       
    },
    build: {
        outDir: 'public/build',
        emptyOutDir: true,
    },
});
