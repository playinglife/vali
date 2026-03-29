import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/scss/app.scss', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: { 
        host: true, 
        port: 5173, 
        hmr: { 
            host: 'localhost', 
            port: 5173 
        } 
    },
	build: {
        outDir: 'public/build', // <- ensures build goes here
        emptyOutDir: true,
    },
	base: '/build/',
});
